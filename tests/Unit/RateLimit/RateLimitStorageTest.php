<?php
declare(strict_types=1);

namespace Tests\Unit\RateLimit;

use PHPUnit\Framework\TestCase;

/**
 * Tests against the file-backed rate-limit storage. Each test uses a UNIQUE
 * IP from the documentation reserved range (192.0.2.0/24) so concurrent dev
 * traffic cannot collide with test buckets, and tearDown explicitly clears
 * every IP/prefix combo a test touched.
 *
 * The storage file lives at resources/rate_limits/rate_limits.json — same
 * file the local dev server uses, so contamination would matter if these
 * tests touched real IPs. They don't.
 */
final class RateLimitStorageTest extends TestCase
{
    private const PREFIX = 'unit_test_rl';
    private array $touchedIps = [];

    protected function tearDown(): void
    {
        foreach ($this->touchedIps as $ip) {
            clear_rate_limit_attempts($ip, self::PREFIX);
        }
        parent::tearDown();
    }

    private function uniqueIp(string $tag): string
    {
        // 192.0.2.0/24 is reserved for documentation (RFC 5737)
        $hash = crc32($tag . microtime(true));
        $ip = '192.0.2.' . (($hash % 254) + 1);
        $this->touchedIps[] = $ip;
        return $ip;
    }

    public function test_is_rate_limited_returns_false_when_no_attempts(): void
    {
        $ip = $this->uniqueIp('no_attempts');
        $this->assertFalse(is_rate_limited($ip, 5, 900, self::PREFIX));
    }

    public function test_is_rate_limited_returns_false_below_limit(): void
    {
        $ip = $this->uniqueIp('below_limit');
        record_rate_limit_attempt($ip, self::PREFIX);
        record_rate_limit_attempt($ip, self::PREFIX);
        $this->assertFalse(is_rate_limited($ip, 5, 900, self::PREFIX));
    }

    public function test_is_rate_limited_returns_true_at_limit(): void
    {
        $ip = $this->uniqueIp('at_limit');
        for ($i = 0; $i < 5; $i++) {
            record_rate_limit_attempt($ip, self::PREFIX);
        }
        $this->assertTrue(is_rate_limited($ip, 5, 900, self::PREFIX));
    }

    public function test_record_rate_limit_attempt_creates_bucket_on_first_call(): void
    {
        $ip = $this->uniqueIp('first_call');
        $this->assertFalse(is_rate_limited($ip, 1, 900, self::PREFIX));
        record_rate_limit_attempt($ip, self::PREFIX);
        $this->assertTrue(is_rate_limited($ip, 1, 900, self::PREFIX));
    }

    public function test_check_and_record_returns_false_under_limit_and_increments(): void
    {
        $ip = $this->uniqueIp('check_under');
        // First call: count goes 0 -> 1, returns false (not limited)
        $this->assertFalse(check_and_record_rate_limit($ip, 3, 900, self::PREFIX));
        // After 1 increment, count=1; limit=2 means we still have one more
        // record before being limited. check_and_record at count=1 with
        // limit=2: not yet limited, increments to 2.
        $this->assertFalse(check_and_record_rate_limit($ip, 2, 900, self::PREFIX));
    }

    public function test_check_and_record_returns_true_at_limit_and_does_not_increment(): void
    {
        $ip = $this->uniqueIp('check_at');
        record_rate_limit_attempt($ip, self::PREFIX);
        record_rate_limit_attempt($ip, self::PREFIX);
        // Already at 2; with limit=2 the next check_and_record sees the
        // bucket already at the cap and refuses (returns true) without
        // incrementing.
        $this->assertTrue(check_and_record_rate_limit($ip, 2, 900, self::PREFIX));
        // Confirm count stayed at 2 (still limited at limit=2)
        $this->assertTrue(is_rate_limited($ip, 2, 900, self::PREFIX));
        // And confirm the bucket would NOT trip at limit=3 (so count is
        // exactly 2, not 3).
        $this->assertFalse(is_rate_limited($ip, 3, 900, self::PREFIX));
    }

    public function test_clear_rate_limit_attempts_removes_bucket(): void
    {
        $ip = $this->uniqueIp('clear');
        record_rate_limit_attempt($ip, self::PREFIX);
        record_rate_limit_attempt($ip, self::PREFIX);
        $this->assertTrue(is_rate_limited($ip, 2, 900, self::PREFIX));

        clear_rate_limit_attempts($ip, self::PREFIX);
        $this->assertFalse(is_rate_limited($ip, 1, 900, self::PREFIX));
    }

    public function test_different_prefixes_do_not_share_buckets(): void
    {
        $ip = $this->uniqueIp('different_prefix');
        record_rate_limit_attempt($ip, self::PREFIX);
        record_rate_limit_attempt($ip, self::PREFIX);
        record_rate_limit_attempt($ip, self::PREFIX);

        // Different prefix should be empty for the same IP
        $this->assertFalse(is_rate_limited($ip, 1, 900, 'other_test_prefix'));

        // Cleanup the OTHER prefix bucket too in case anything leaked
        clear_rate_limit_attempts($ip, 'other_test_prefix');
    }

    public function test_different_ips_do_not_share_buckets(): void
    {
        $ipA = $this->uniqueIp('ip_a');
        $ipB = $this->uniqueIp('ip_b');
        for ($i = 0; $i < 5; $i++) {
            record_rate_limit_attempt($ipA, self::PREFIX);
        }
        $this->assertTrue(is_rate_limited($ipA, 5, 900, self::PREFIX));
        $this->assertFalse(is_rate_limited($ipB, 1, 900, self::PREFIX));
    }

    public function test_stale_entries_pruned_during_read(): void
    {
        $ip = $this->uniqueIp('stale');
        record_rate_limit_attempt($ip, self::PREFIX, 1);
        // Wait past the window, then read with the same window. Stale entry
        // should be pruned during read_rate_limits_locked.
        sleep(2);
        $this->assertFalse(is_rate_limited($ip, 1, 1, self::PREFIX));
    }
}
