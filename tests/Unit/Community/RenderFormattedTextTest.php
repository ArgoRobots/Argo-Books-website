<?php
declare(strict_types=1);

namespace Tests\Unit\Community;

use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/community/formatting/formatting_functions.php';

/**
 * render_formatted_text() output is echoed raw into forum pages, so the only
 * HTML in it may be the formatter's own markup. Checked by parsing the output
 * and allowing nothing but those elements and attributes.
 */
final class RenderFormattedTextTest extends TestCase
{
    private const ALLOWED = [
        'div' => ['class'],
        'p' => [],
        'br' => [],
        'strong' => [],
        'em' => [],
        'code' => [],
        'blockquote' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'a' => ['href', 'target', 'rel'],
        'span' => ['class'],
    ];

    public static function attackPayloads(): array
    {
        return [
            'restored anchor prefix' => ['<a href="x<a href=" autofocus onfocus=alert(document.domain) x='],
            'restored anchor prefix in blockquote' => ['> <a href="x<a href=" autofocus onfocus=alert(1) x='],
            'restored anchor prefix in list' => ["- <a href=\"x<a href=\" onmouseover=alert(1) x=\n- two"],
            'restored anchor suffix' => ['<img src=x " target="_blank" rel="noopener noreferrer"> onerror=alert(1)>'],
            'restored span' => ['<span class="invalid-link-warning" onclick=alert(1)>x</span>'],
            'restored strong' => ['<strong onmouseover=alert(1)>x</strong>'],
            'script tag' => ['<script>alert(1)</script>'],
            'img in bold' => ['**<img src=x onerror=alert(1)>**'],
            'pre-escaped entities' => ['&lt;a href=&quot;javascript:alert(1)&quot; target=&quot;_blank&quot; rel=&quot;noopener noreferrer&quot;&gt;x&lt;/a&gt;'],
            'javascript link on allowed host' => ['[x](javascript://argorobots.com/%0Aalert%281%29)'],
            'javascript link mixed case' => ['[x](JaVaScRiPt://github.com/%0aalert`1`)'],
            'data link on allowed host' => ['[x](data://github.com/text/html,<script>alert(1)</script>)'],
            'vbscript link' => ['[x](vbscript://github.com/msgbox)'],
            'quote in url' => ['[x](https://github.com/" onmouseover="alert`1`)'],
            'quote in link text' => ['[a" onmouseover="alert`1`](https://github.com)'],
            'tag in link text' => ['[<img src=x onerror=alert(1)>](https://github.com)'],
        ];
    }

    #[DataProvider('attackPayloads')]
    public function test_attack_payload_produces_only_formatter_markup(string $payload): void
    {
        $this->assertOnlyFormatterMarkup(render_formatted_text($payload));
    }

    public function test_javascript_scheme_on_allowed_host_is_not_a_link(): void
    {
        $html = render_formatted_text('[x](javascript://argorobots.com/%0Aalert%281%29)');
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringContainsString('invalid-link-warning', $html);
    }

    public function test_is_allowed_url_requires_http_or_https(): void
    {
        $this->assertFalse(is_allowed_url('javascript://argorobots.com/%0Aalert(1)'));
        $this->assertFalse(is_allowed_url('data://github.com/x'));
        $this->assertFalse(is_allowed_url('ftp://github.com/x'));
        $this->assertFalse(is_allowed_url('//github.com/x'));
        $this->assertTrue(is_allowed_url('https://github.com/x'));
        $this->assertTrue(is_allowed_url('http://en.wikipedia.org/wiki/PHP'));
        $this->assertTrue(is_allowed_url('HTTPS://argorobots.com/'));
    }

    public function test_typed_markup_is_shown_as_text(): void
    {
        $html = render_formatted_text('<a href="x<a href=" autofocus onfocus=alert(document.domain) x=');
        $this->assertStringContainsString('&lt;a href=&quot;x&lt;a href=&quot;', $html);
    }

    public function test_bold_italic_and_code_still_render(): void
    {
        $html = render_formatted_text('**bold** _italic_ `a < b`');
        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<em>italic</em>', $html);
        $this->assertStringContainsString('<code>a &lt; b</code>', $html);
    }

    public function test_lists_and_blockquotes_still_render(): void
    {
        $html = render_formatted_text("- one\n- two\n\n1. first\n2. second\n\n> quoted\n> more");
        foreach (['<ul>', '<li>one</li>', '<li>two</li>', '</ul>', '<ol>', '<li>first</li>', '<li>second</li>', '</ol>', '<blockquote>quoted', 'more</blockquote>'] as $fragment) {
            $this->assertStringContainsString($fragment, $html);
        }
    }

    public function test_allowed_link_renders(): void
    {
        $html = render_formatted_text('[Link text](https://argorobots.com/a?b=1&c=2)');
        $this->assertStringContainsString(
            '<a href="https://argorobots.com/a?b=1&amp;c=2" target="_blank" rel="noopener noreferrer">Link text</a>',
            $html
        );
    }

    public function test_underscores_in_a_url_are_not_italicised(): void
    {
        $html = render_formatted_text('[docs](https://github.com/foo_bar_baz)');
        $this->assertStringContainsString('href="https://github.com/foo_bar_baz"', $html);
    }

    public function test_disallowed_link_shows_warning(): void
    {
        $html = render_formatted_text('[Disallowed Link](https://example.com)');
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringContainsString('Disallowed Link <span class="invalid-link-warning">(Link to disallowed domain removed)</span>', $html);
    }

    private function assertOnlyFormatterMarkup(string $html): void
    {
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $doc->getElementsByTagName('body')->item(0);
        $this->assertNotNull($body);

        foreach ($body->getElementsByTagName('*') as $el) {
            /** @var DOMElement $el */
            $tag = strtolower($el->tagName);
            $this->assertArrayHasKey($tag, self::ALLOWED, "unexpected <{$tag}> in: {$html}");
            foreach ($el->attributes as $attr) {
                $name = strtolower($attr->name);
                $this->assertContains($name, self::ALLOWED[$tag], "unexpected {$name}= on <{$tag}> in: {$html}");
                if ($name === 'href') {
                    $this->assertMatchesRegularExpression('#^https?://#i', $attr->value, "unsafe href in: {$html}");
                }
                if ($name === 'class') {
                    $this->assertContains($attr->value, ['formatted-text', 'invalid-link-warning'], "unexpected class in: {$html}");
                }
                if ($name === 'target') {
                    $this->assertSame('_blank', $attr->value);
                }
                if ($name === 'rel') {
                    $this->assertSame('noopener noreferrer', $attr->value);
                }
            }
        }
    }
}
