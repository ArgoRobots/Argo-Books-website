<?php
declare(strict_types=1);

namespace Tests\Unit\Niches;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NicheDataShapeTest extends TestCase
{
    /** @return array<string,array{0:string,1:array<string,mixed>}> */
    public static function nicheDataProvider(): array
    {
        $rows = [];
        $files = glob(__DIR__ . '/../../../niches/data/*.php');
        foreach ($files as $file) {
            $slug = basename($file, '.php');
            if ($slug === '_template') continue;
            $data = require $file;
            $rows[$slug] = [$slug, $data];
        }
        return $rows;
    }

    #[DataProvider('nicheDataProvider')]
    public function test_required_keys_exist(string $slug, $data): void
    {
        $this->assertIsArray($data, "Niche '$slug' must return an array");
        foreach (['slug', 'h1', 'meta_title', 'meta_description',
                  'intro_html', 'sample_line_items',
                  'typical_payment_terms_html', 'tax_notes_html',
                  'faqs', 'related_slugs', 'cta_text'] as $key) {
            $this->assertArrayHasKey(
                $key, $data, "Niche '$slug' missing key '$key'"
            );
        }
    }

    #[DataProvider('nicheDataProvider')]
    public function test_slug_matches_filename(string $slug, $data): void
    {
        $this->assertSame(
            $slug, $data['slug'],
            "Niche '$slug' has mismatched 'slug' field"
        );
        $this->assertMatchesRegularExpression(
            '/^[a-z0-9-]+$/', $slug,
            "Niche slug '$slug' must be lowercase letters, digits, hyphens"
        );
    }

    #[DataProvider('nicheDataProvider')]
    public function test_no_em_dashes_anywhere(string $slug, $data): void
    {
        $serialized = var_export($data, true);
        $this->assertStringNotContainsString(
            "\u{2014}", $serialized,
            "Niche '$slug' contains an em dash. Replace with a comma, colon, or period."
        );
        $this->assertStringNotContainsString(
            '&mdash;', $serialized,
            "Niche '$slug' contains '&mdash;'. Replace with a comma, colon, or period."
        );
    }

    #[DataProvider('nicheDataProvider')]
    public function test_no_banned_words(string $slug, $data): void
    {
        $serialized = strtolower(var_export($data, true));
        foreach (['reconciliation', 'reconcile'] as $banned) {
            $this->assertStringNotContainsString(
                $banned, $serialized,
                "Niche '$slug' contains banned word '$banned'."
            );
        }
    }

    #[DataProvider('nicheDataProvider')]
    public function test_faqs_are_well_formed(string $slug, $data): void
    {
        $faqs = $data['faqs'] ?? [];
        $this->assertIsArray($faqs, "Niche '$slug' faqs must be array");
        $this->assertGreaterThanOrEqual(
            4, count($faqs),
            "Niche '$slug' needs at least 4 FAQs (got " . count($faqs) . ")"
        );
        foreach ($faqs as $i => $faq) {
            $this->assertArrayHasKey('q', $faq, "Niche '$slug' faq #$i missing 'q'");
            $this->assertArrayHasKey('a', $faq, "Niche '$slug' faq #$i missing 'a'");
            $this->assertNotSame('', trim((string)$faq['q']));
            $this->assertNotSame('', trim((string)$faq['a']));
            $this->assertStringNotContainsString(
                '<', (string)$faq['q'], "FAQ question must be plain text, no HTML"
            );
            $this->assertStringNotContainsString(
                '<', (string)$faq['a'], "FAQ answer must be plain text, no HTML"
            );
        }
    }

    #[DataProvider('nicheDataProvider')]
    public function test_related_slugs_exist(string $slug, $data): void
    {
        if ($slug === 'generic') {
            $this->assertTrue(true, 'generic page is exempt from minimum-link requirement at the test level');
            return;
        }
        $related = $data['related_slugs'] ?? [];
        $this->assertGreaterThanOrEqual(
            3, count($related),
            "Niche '$slug' must link to at least 3 related niches"
        );
        foreach ($related as $rs) {
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $rs);
            $this->assertFileExists(
                __DIR__ . "/../../../niches/data/$rs.php",
                "Niche '$slug' references missing related slug '$rs'"
            );
        }
    }

    #[DataProvider('nicheDataProvider')]
    public function test_country_pages_have_concept(string $slug, $data): void
    {
        $country = $data['country'] ?? null;
        $concept = $data['concept'] ?? null;
        if ($country !== null) {
            $this->assertSame(
                'invoice-generator', $concept,
                "Country page '$slug' must set concept='invoice-generator' for hreflang"
            );
        } else {
            $this->assertTrue(true, 'profession page country-check vacuously passes');
        }
    }

    #[DataProvider('nicheDataProvider')]
    public function test_intro_word_count_is_healthy(string $slug, $data): void
    {
        // The generic page is intentionally niche-agnostic and has shorter
        // copy. Skip the 300-word floor for it but enforce on all niches.
        if ($slug === 'generic') {
            $this->assertTrue(true, 'generic page exempt from 300-word floor');
            return;
        }
        $intro = strip_tags((string)($data['intro_html'] ?? ''));
        $terms = strip_tags((string)($data['typical_payment_terms_html'] ?? ''));
        $tax   = strip_tags((string)($data['tax_notes_html'] ?? ''));
        $faqs_text = '';
        foreach (($data['faqs'] ?? []) as $faq) {
            $faqs_text .= ' ' . ($faq['a'] ?? '');
        }
        $total = $intro . ' ' . $terms . ' ' . $tax . ' ' . $faqs_text;
        $word_count = str_word_count($total);
        $this->assertGreaterThanOrEqual(
            300, $word_count,
            "Niche '$slug' has only $word_count words of unique content (need >= 300)"
        );
    }
}
