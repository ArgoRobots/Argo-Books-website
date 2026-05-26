<?php
/**
 * Shopify discovery smoke tests.
 *
 * Run via: php cron/test_shopify_discovery_smoke.php
 *
 * Pure-logic tests for the helpers in cron/lib/shopify_discovery.php using
 * injected HTML and product-JSON fixtures. No network, no DB.
 *
 * DO NOT RUN AGAINST PRODUCTION. Aborts immediately if APP_ENV='production'.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (($_ENV['APP_ENV'] ?? '') === 'production') {
    fwrite(STDERR, "REFUSING TO RUN: APP_ENV='production'.\n");
    exit(2);
}

require_once __DIR__ . '/lib/shopify_discovery.php';  // pulls in outreach_helpers too

$pass = 0;
$fail = 0;

function assert_true($cond, $msg) {
    global $pass, $fail;
    if ($cond) { echo "  ✓ $msg\n"; $pass++; }
    else       { echo "  ✗ $msg\n"; $fail++; }
}

// ─── Shared fixture ───
// Realistic actively-trading store: 9 older products (oldest 14 months ago,
// inside the 3-24 month window) + 1 fresh product (1 month ago) so the
// dormancy check (rejects if newest > 6 months old) passes.
$goodDate    = (new DateTime('-14 months'))->format(DateTime::RFC3339);
$freshDate   = (new DateTime('-1 month'))->format(DateTime::RFC3339);
$caProducts  = ['products' => array_merge(
    array_fill(0, 9, ['created_at' => $goodDate]),
    [['created_at' => $freshDate]]
)];

// ─── Section 1: filter_gatekept_email ───
echo "Section 1: filter_gatekept_email\n";
assert_true(filter_gatekept_email('support@store.ca')       === true,  "support@store.ca is gatekept");
assert_true(filter_gatekept_email('SUPPORT@store.ca')       === true,  "SUPPORT@store.ca is gatekept (case-insensitive)");
assert_true(filter_gatekept_email('no-reply@store.ca')      === true,  "no-reply@store.ca is gatekept");
assert_true(filter_gatekept_email('partnerships@store.ca')  === true,  "partnerships@store.ca is gatekept");
assert_true(filter_gatekept_email('hello@store.ca')         === false, "hello@store.ca is not gatekept");
assert_true(filter_gatekept_email('jane@store.ca')          === false, "jane@store.ca is not gatekept");
assert_true(filter_gatekept_email('contact@store.ca')       === false, "contact@store.ca is not gatekept");
assert_true(filter_gatekept_email('')                        === true,  "empty string is gatekept (defensive)");
assert_true(filter_gatekept_email('  support@store.ca  ')  === true,  "whitespace-padded support@store.ca is gatekept (trim)");

// ─── Section 2: shopify_canonical_url ───
echo "Section 2: shopify_canonical_url\n";
assert_true(
    shopify_canonical_url('https://Foo.MyShopify.com/?utm=1#x') === 'https://foo.myshopify.com',
    "Uppercased host + query + fragment stripped"
);
assert_true(
    shopify_canonical_url('http://example.ca/path/') === 'http://example.ca',
    "Path stripped (origin only)"
);
assert_true(
    shopify_canonical_url('https://example.ca/path') === 'https://example.ca',
    "Deep-link path stripped to origin"
);
assert_true(
    shopify_canonical_url('  ') === '',
    "Whitespace-only returns empty string"
);

// ─── Section 3: evaluate_shopify_candidate: fit path ───
echo "Section 3: evaluate_shopify_candidate: fit path\n";
$caHtml = <<<HTML
<html>
<head><title>Cool Maple Co</title></head>
<body>
<header>Welcome to Cool Maple Co</header>
<footer>Made in Canada. Powered by Shopify. <a href="mailto:hello@coolmaple.ca">hello@coolmaple.ca</a></footer>
</body>
</html>
HTML;

$result = evaluate_shopify_candidate('https://coolmaple.myshopify.com', $caHtml, $caProducts);

assert_true($result['fit'] === true,                              "fit === true for happy-path CA store");
assert_true(($result['metadata']['email'] ?? '') === 'hello@coolmaple.ca', "metadata.email is hello@coolmaple.ca");
assert_true(($result['metadata']['country'] ?? '') === 'CA',     "metadata.country is CA");
assert_true(($result['metadata']['products_count'] ?? 0) === 10, "metadata.products_count is 10");
assert_true(
    str_contains($result['metadata']['business_name'] ?? '', 'Cool Maple Co'),
    "metadata.business_name contains 'Cool Maple Co'"
);
$parsedDate = DateTime::createFromFormat('Y-m-d H:i:s', $result['metadata']['first_product_created_at'] ?? '');
assert_true($parsedDate !== false, "metadata.first_product_created_at is a valid Y-m-d H:i:s datetime");

// ─── Section 4: evaluate_shopify_candidate: reject paths ───
echo "Section 4: evaluate_shopify_candidate: reject paths\n";

// agency_operated
$agencyHtml = '<html><body>Made in Canada hello@x.ca. Powered by Awesome Agency.</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $agencyHtml, $caProducts);
assert_true($r['reason'] === 'agency_operated', "agency_operated: reason correct");
assert_true(stripos($r['detail'] ?? '', 'Awesome Agency') !== false, "agency_operated: detail contains 'Awesome Agency'");

// not_shopify, NOTE: not reachable via $productsOverride because any non-null array skips
// the $productsData === null check. To trigger not_shopify cleanly would require a live
// network call (or mocking fetch_shopify_products_json). This path is intentionally omitted
// from offline smoke testing; see report for explanation.

// too_few_products
$fewDate     = (new DateTime('-14 months'))->format(DateTime::RFC3339);
$fewProducts = ['products' => array_fill(0, 3, ['created_at' => $fewDate])];
$r = evaluate_shopify_candidate('https://test.myshopify.com', $caHtml, $fewProducts);
assert_true($r['reason'] === 'too_few_products', "too_few_products: reason correct");
assert_true(str_contains($r['detail'] ?? '', '3'), "too_few_products: detail mentions count 3");

// too_new (~1 month ago, always within the too_new window)
$newDate     = (new DateTime('-1 month'))->format(DateTime::RFC3339);
$newProducts = ['products' => array_fill(0, 10, ['created_at' => $newDate])];
$r = evaluate_shopify_candidate('https://test.myshopify.com', $caHtml, $newProducts);
assert_true($r['reason'] === 'too_new', "too_new: reason correct");

// too_old (~27 months ago, past the 24-month ceiling)
$oldDate     = (new DateTime('-27 months'))->format(DateTime::RFC3339);
$oldProducts = ['products' => array_fill(0, 10, ['created_at' => $oldDate])];
$r = evaluate_shopify_candidate('https://test.myshopify.com', $caHtml, $oldProducts);
assert_true($r['reason'] === 'too_old', "too_old: reason correct");

// age_unknown (malformed created_at)
$badAgeProducts = ['products' => array_fill(0, 10, ['created_at' => 'completely-malformed-string'])];
$r = evaluate_shopify_candidate('https://test.myshopify.com', $caHtml, $badAgeProducts);
assert_true($r['reason'] === 'age_unknown', "age_unknown: reason correct");

// US lead now accepted (the channel was Canadian-only originally; US was
// added so the same evaluator accepts either country).
$usaHtml = '<html><body>Made in USA hello@x.com</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $usaHtml, $caProducts);
assert_true($r['fit'] === true, "US 'Made in USA' signal passes country gate");
assert_true(($r['metadata']['country'] ?? null) === 'US', "US lead metadata.country === 'US'");

// Non-target country (no CA or US signal at all)
$frHtml = '<html><body>Fabriqué en France hello@x.fr</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $frHtml, $caProducts);
assert_true($r['reason'] === 'not_target_country', "not_target_country: reason correct for non-CA/US");

// no_contact_email
$noEmailHtml = '<html><body>Made in Canada (no email anywhere)</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $noEmailHtml, $caProducts);
assert_true($r['reason'] === 'no_contact_email', "no_contact_email: reason correct");

// gatekept_email
$gatekeptHtml = '<html><body>Made in Canada support@store.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $gatekeptHtml, $caProducts);
assert_true($r['reason'] === 'gatekept_email', "gatekept_email: reason correct");
assert_true(str_contains($r['detail'] ?? '', 'support@store.ca'), "gatekept_email: detail contains 'support@store.ca'");

// ─── Section 5: Canada-signal detection ───
echo "Section 5: Canada-signal detection\n";

// Postal code only (no other Canada phrases). K1A 0B1 is a valid Canadian postal code
$postalOnlyHtml = '<html><body>Pickup at K1A 0B1 hello@x.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $postalOnlyHtml, $caProducts);
assert_true($r['fit'] === true, "Postal code alone (K1A 0B1) passes Canada gate");

// Postal code without space: M5V3A8
$postalNoSpaceHtml = '<html><body>Location: M5V3A8. hello@x.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $postalNoSpaceHtml, $caProducts);
assert_true($r['fit'] === true, "Postal code without space (M5V3A8) passes Canada gate");

// Postal code with space: M5V 3A8
$postalWithSpaceHtml = '<html><body>Location: M5V 3A8. hello@x.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $postalWithSpaceHtml, $caProducts);
assert_true($r['fit'] === true, "Postal code with space (M5V 3A8) passes Canada gate");

// Phrase "based in canada" without postal code
$basedInHtml = '<html><body>Based in Canada. hello@maple.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $basedInHtml, $caProducts);
assert_true($r['fit'] === true, "'based in canada' phrase passes Canada gate");

// Phrase "proudly canadian" without postal code
$proudlyHtml = '<html><body>Proudly Canadian. hello@maple.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $proudlyHtml, $caProducts);
assert_true($r['fit'] === true, "'proudly canadian' phrase passes Canada gate");

// Just the word "Canada" alone (no postal code, no qualifying phrase) should FAIL
$justCanadaHtml = '<html><body>We ship to Canada. hello@x.ca</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $justCanadaHtml, $caProducts);
assert_true($r['reason'] === 'not_target_country', "Bare 'Canada' without postal code or signal phrase → not_target_country");

// ─── Section 6: Powered-by-Shopify exclusion ───
echo "Section 6: Powered-by-Shopify exclusion\n";

// "Powered by Shopify" must NOT trigger agency_operated
$shopifyPoweredHtml = '<html><body>Made in Canada hello@x.ca. Powered by Shopify</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $shopifyPoweredHtml, $caProducts);
assert_true($r['fit'] === true, "'Powered by Shopify' does not trigger agency_operated");

// Caps variant
$shopifyPoweredCapsHtml = '<html><body>Made in Canada hello@x.ca. POWERED BY SHOPIFY</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $shopifyPoweredCapsHtml, $caProducts);
assert_true($r['fit'] === true, "'POWERED BY SHOPIFY' (all caps) does not trigger agency_operated");

// Any other "Powered by X" must reject
$acmeHtml = '<html><body>Made in Canada hello@x.ca. Powered by Acme</body></html>';
$r = evaluate_shopify_candidate('https://test.myshopify.com', $acmeHtml, $caProducts);
assert_true($r['reason'] === 'agency_operated', "'Powered by Acme' → agency_operated");

echo "\n========================================\n";
echo "Result: $pass passed, $fail failed\n";
echo "========================================\n";
exit($fail === 0 ? 0 : 1);
