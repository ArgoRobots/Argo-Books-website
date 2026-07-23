<?php
declare(strict_types=1);

namespace Tests\Integration\Waitlist;

use Tests\Helpers\DatabaseTestCase;

final class WaitlistSubscribeTest extends DatabaseTestCase
{
    private function countRows(string $email): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM platform_waitlist WHERE email = ?');
        $stmt->execute([$email]);
        return (int) $stmt->fetchColumn();
    }

    public function test_valid_signup_inserts_row_with_attribution(): void
    {
        $email = 'mac_fan_' . bin2hex(random_bytes(4)) . '@example.test';
        $result = waitlist_subscribe(
            ['email' => $email, 'platform' => 'macos', 'website' => ''],
            [
                'ip_address'  => '203.0.113.10',
                'user_agent'  => 'TestAgent/1.0',
                'visitor_id'  => '11111111-2222-4333-8444-555555555555',
                'source_code' => 'guide-test',
            ]
        );

        $this->assertSame(200, $result['status']);
        $this->assertTrue($result['body']['success']);

        $stmt = $this->pdo->prepare('SELECT * FROM platform_waitlist WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        $this->assertNotFalse($row);
        $this->assertSame('macos', $row['platform']);
        $this->assertSame('11111111-2222-4333-8444-555555555555', $row['visitor_id']);
        $this->assertSame('guide-test', $row['source_code']);
        $this->assertSame('203.0.113.10', $row['ip_address']);
        $this->assertNull($row['notified_at']);
    }

    public function test_email_is_normalized_to_lowercase_and_trimmed(): void
    {
        $result = waitlist_subscribe(['email' => '  Mac.User@Example.TEST  ', 'platform' => 'macos']);
        $this->assertSame(200, $result['status']);
        $this->assertSame(1, $this->countRows('mac.user@example.test'));
    }

    public function test_invalid_email_is_rejected(): void
    {
        foreach (['', 'not-an-email', 'a@b', str_repeat('x', 250) . '@example.test'] as $bad) {
            $result = waitlist_subscribe(['email' => $bad, 'platform' => 'macos']);
            $this->assertSame(400, $result['status'], "expected 400 for: $bad");
            $this->assertFalse($result['body']['success']);
        }
    }

    public function test_invalid_platform_is_rejected(): void
    {
        $result = waitlist_subscribe(['email' => 'valid@example.test', 'platform' => 'windows']);
        $this->assertSame(400, $result['status']);
        $this->assertSame(0, $this->countRows('valid@example.test'));
    }

    public function test_honeypot_returns_fake_success_without_inserting(): void
    {
        $email = 'bot_' . bin2hex(random_bytes(4)) . '@example.test';
        $result = waitlist_subscribe(['email' => $email, 'platform' => 'macos', 'website' => 'http://spam.example']);

        $this->assertSame(200, $result['status']);
        $this->assertTrue($result['body']['success'], 'honeypot response must look like success');
        $this->assertSame(0, $this->countRows($email), 'honeypot submission must not be stored');
    }

    public function test_duplicate_email_stays_single_row_and_still_succeeds(): void
    {
        $email = 'dupe_' . bin2hex(random_bytes(4)) . '@example.test';
        $first  = waitlist_subscribe(['email' => $email, 'platform' => 'macos']);
        $second = waitlist_subscribe(['email' => $email, 'platform' => 'macos']);

        $this->assertSame(200, $first['status']);
        $this->assertSame(200, $second['status']);
        $this->assertTrue($second['body']['success'], 'duplicate must not leak subscription status');
        $this->assertSame(1, $this->countRows($email));
    }

    public function test_rate_limit_blocks_sixth_signup_from_same_ip(): void
    {
        $ip = '198.51.100.77';
        for ($i = 1; $i <= 5; $i++) {
            $result = waitlist_subscribe(
                ['email' => "rate{$i}_" . bin2hex(random_bytes(3)) . '@example.test', 'platform' => 'macos'],
                ['ip_address' => $ip]
            );
            $this->assertSame(200, $result['status'], "signup $i should pass");
        }

        $blocked = waitlist_subscribe(
            ['email' => 'rate6_' . bin2hex(random_bytes(3)) . '@example.test', 'platform' => 'macos'],
            ['ip_address' => $ip]
        );
        $this->assertSame(429, $blocked['status']);
        $this->assertFalse($blocked['body']['success']);
    }
}
