<?php
declare(strict_types=1);

namespace Tests\Unit\Outreach;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/cron/lib/editorial_discovery.php';

/**
 * editorial_detect_affiliate() decides whether a roundup page runs affiliate
 * links, which gates the affiliate-program offer in the editorial pitch. It is a
 * pure scan over raw HTML, so it needs no database. It must err specific: a clean
 * editorial page returns false (we stay a neutral pitch), and only real affiliate
 * infrastructure or disclosure wording flips it true.
 */
final class EditorialAffiliateDetectionTest extends TestCase
{
    public function test_clean_editorial_page_is_not_affiliate(): void
    {
        $html = '<html><body><h1>The 8 Best Free Accounting Tools</h1>'
            . '<p>By Jane Doe. We tested Wave, Zoho Books, and FreshBooks.</p>'
            . '<a href="https://wave.com">Wave</a>'
            . '<a href="/about">About us</a>'
            . '<a href="/affiliate-program">Affiliate Program</a></body></html>';
        $res = editorial_detect_affiliate($html);
        $this->assertFalse($res['is_affiliate'], 'A neutral page (even with a nav link to its own affiliate program) must not trip.');
    }

    public function test_affiliate_network_link_is_detected(): void
    {
        $html = '<a href="https://www.shareasale.com/r.cfm?b=123&u=456&m=789">FreshBooks</a>';
        $this->assertTrue(editorial_detect_affiliate($html)['is_affiliate']);
    }

    public function test_amazon_associate_tag_is_detected(): void
    {
        $html = '<a href="https://www.amazon.com/dp/B000000?tag=myoutlet-20">Buy</a>';
        $this->assertTrue(editorial_detect_affiliate($html)['is_affiliate']);
    }

    public function test_rel_sponsored_is_detected(): void
    {
        $html = '<a href="https://zoho.com/books" rel="nofollow sponsored">Zoho Books</a>';
        $this->assertTrue(editorial_detect_affiliate($html)['is_affiliate']);
    }

    public function test_affiliate_query_param_is_detected(): void
    {
        $html = '<a href="https://freshbooks.com/pricing?aff=outlet123">FreshBooks</a>';
        $this->assertTrue(editorial_detect_affiliate($html)['is_affiliate']);
    }

    public function test_disclosure_phrase_is_detected(): void
    {
        $html = '<p class="disclosure">Some links are affiliate links. We may earn a commission at no additional cost to you.</p>';
        $this->assertTrue(editorial_detect_affiliate($html)['is_affiliate']);
    }

    public function test_empty_html_is_not_affiliate(): void
    {
        $this->assertFalse(editorial_detect_affiliate('')['is_affiliate']);
    }

    public function test_generic_ref_param_does_not_trip(): void
    {
        // ref= is common on non-affiliate links (e.g. GitHub), so it must not
        // be treated as an affiliate signal on its own.
        $html = '<a href="https://github.com/wave?ref=homepage">Wave on GitHub</a>';
        $this->assertFalse(editorial_detect_affiliate($html)['is_affiliate']);
    }
}
