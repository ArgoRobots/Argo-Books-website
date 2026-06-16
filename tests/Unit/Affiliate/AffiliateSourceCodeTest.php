<?php
declare(strict_types=1);

namespace Tests\Unit\Affiliate;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/community/affiliate/affiliate_functions.php';

/**
 * Source-code generation. A predicate is injected so these run without a DB.
 */
final class AffiliateSourceCodeTest extends TestCase
{
    /** Predicate that treats nothing as taken. */
    private function none(): callable
    {
        return fn(string $code): bool => false;
    }

    public function test_basic_username_becomes_aff_prefixed_slug(): void
    {
        $code = generate_affiliate_source_code('Marc', $this->none());
        $this->assertSame('aff-marc', $code);
    }

    public function test_strips_disallowed_characters(): void
    {
        $code = generate_affiliate_source_code('My Cool Name!', $this->none());
        // Spaces and "!" stripped; remaining is lowercased.
        $this->assertSame('aff-mycoolname', $code);
    }

    public function test_result_is_url_safe_and_within_length(): void
    {
        $code = generate_affiliate_source_code(str_repeat('a', 200), $this->none());
        $this->assertLessThanOrEqual(50, strlen($code));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $code);
    }

    public function test_empty_username_falls_back_to_user(): void
    {
        $code = generate_affiliate_source_code('!!!', $this->none());
        $this->assertSame('aff-user', $code);
    }

    public function test_collision_appends_suffix_and_stays_unique(): void
    {
        // First candidate is taken; generator must return something different.
        $taken = ['aff-marc'];
        $exists = fn(string $code): bool => in_array($code, $taken, true);

        $code = generate_affiliate_source_code('Marc', $exists);

        $this->assertNotSame('aff-marc', $code);
        $this->assertStringStartsWith('aff-marc-', $code);
        $this->assertLessThanOrEqual(50, strlen($code));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $code);
    }
}
