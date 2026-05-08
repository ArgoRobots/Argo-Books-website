<?php
declare(strict_types=1);

namespace Tests\Unit\Email;

use PHPUnit\Framework\TestCase;

final class EmailHelpersTest extends TestCase
{
    public function test_sanitize_header_value_strips_crlf(): void
    {
        $injected = "user@example.com\r\nBcc: attacker@evil.com";
        $sanitized = sanitize_header_value($injected);
        $this->assertStringNotContainsString("\r", $sanitized);
        $this->assertStringNotContainsString("\n", $sanitized);
    }

    public function test_sanitize_header_value_strips_control_chars(): void
    {
        $injected = "subject\x00with\x07\x1fcontrol\x08chars";
        $sanitized = sanitize_header_value($injected);
        // Replacement collapses runs of control chars to a single space.
        $this->assertSame('subject with control chars', $sanitized);
    }

    public function test_sanitize_header_value_returns_null_for_null(): void
    {
        $this->assertNull(sanitize_header_value(null));
    }

    public function test_sanitize_header_value_passes_through_clean_input(): void
    {
        $this->assertSame('Hello World', sanitize_header_value('Hello World'));
    }

    public function test_community_excerpt_strips_html_and_truncates_with_ellipsis(): void
    {
        $input = '<p>This is a <strong>long</strong> post body that should be truncated to a short excerpt for the email notification.</p>';
        $excerpt = _community_excerpt($input, 40);
        $this->assertStringNotContainsString('<', $excerpt);
        $this->assertStringNotContainsString('>', $excerpt);
        $this->assertSame(40, mb_strlen($excerpt));
        $this->assertStringEndsWith('…', $excerpt);
    }

    public function test_community_excerpt_collapses_whitespace(): void
    {
        $input = "line one\n\n\nline   two\t\ttabs";
        $excerpt = _community_excerpt($input, 100);
        $this->assertStringNotContainsString("\n", $excerpt);
        $this->assertStringNotContainsString("\t", $excerpt);
        $this->assertStringNotContainsString('  ', $excerpt);
    }

    public function test_community_excerpt_below_max_returns_unchanged(): void
    {
        $input = 'Short.';
        $this->assertSame('Short.', _community_excerpt($input, 240));
    }
}
