<?php
/**
 * Smoke tests for the outreach auto-filter helpers added after the
 * Air Canada Maple Leaf Lounge lead incident.
 *
 * Run via: php tests/Smoke/test_outreach_filters_smoke.php
 *
 * Pure-logic tests, no network, no DB. The AI gate (Layer 3) is not
 * exercised here because it actually calls Gemini; that's covered by
 * the integration path running the backfill against a sandbox DB.
 *
 * DO NOT RUN AGAINST PRODUCTION. Aborts immediately if APP_ENV='production'.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

if (($_ENV['APP_ENV'] ?? '') === 'production') {
    fwrite(STDERR, "REFUSING TO RUN: APP_ENV='production'.\n");
    exit(2);
}

require_once __DIR__ . '/../../cron/lib/outreach_helpers.php';

$pass = 0;
$fail = 0;

function assert_true($cond, $msg)
{
    global $pass, $fail;
    if ($cond) { echo "  ✓ $msg\n"; $pass++; }
    else       { echo "  ✗ $msg\n"; $fail++; }
}

// ─── Section 1: filter_gatekept_email (regression + new tokens) ───
echo "Section 1: filter_gatekept_email (regression + new tokens)\n";

// Existing Shopify-channel cases must still pass.
assert_true(filter_gatekept_email('support@store.ca')       === true,  "support@ still caught");
assert_true(filter_gatekept_email('SUPPORT@store.ca')       === true,  "case-insensitive");
assert_true(filter_gatekept_email('no-reply@store.ca')      === true,  "no-reply@ caught as full local-part");
assert_true(filter_gatekept_email('partnerships@store.ca')  === true,  "partnerships@ caught");
assert_true(filter_gatekept_email('hello@store.ca')         === false, "hello@ NOT caught (legit owner mailbox)");
assert_true(filter_gatekept_email('jane@store.ca')          === false, "jane@ NOT caught (personal name)");
assert_true(filter_gatekept_email('contact@store.ca')       === false, "contact@ NOT caught (legit small-biz)");
assert_true(filter_gatekept_email('')                       === true,  "empty is gatekept (defensive)");
assert_true(filter_gatekept_email('  support@store.ca  ')   === true,  "whitespace-padded support@");

// The Air Canada case: segment-wise matching catches the embedded "phishing".
assert_true(filter_gatekept_email('phishing.hameconnage@aircanada.ca') === true,
    "phishing.hameconnage@ caught via segment match");
assert_true(filter_gatekept_email('hameconnage.phishing@bank.ca') === true,
    "hameconnage segment also caught");
assert_true(filter_gatekept_email('fraud-report@bank.ca') === true,
    "fraud-report segment match");
assert_true(filter_gatekept_email('privacy.officer@telco.ca') === true,
    "privacy segment match");
assert_true(filter_gatekept_email('accounts.payable@bigco.com') === true,
    "accounts segment match");
assert_true(filter_gatekept_email('customerservice@airline.com') === true,
    "customerservice@ prefix match (no separator)");
assert_true(filter_gatekept_email('customercare-canada@telco.ca') === true,
    "customercare-canada@ prefix match");

// Don't accidentally over-reject: words that merely contain a token as a substring
// (without separator) shouldn't trip the filter. Personal names matter most here.
assert_true(filter_gatekept_email('marketa.smith@store.ca') === false,
    "marketa is not 'marketing' (segment exact match required)");
assert_true(filter_gatekept_email('helper@store.ca')        === false,
    "helper is not 'help' (segment exact match required)");
assert_true(filter_gatekept_email('alertha@store.ca')       === false,
    "alertha is not 'alert'");

// ─── Section 2: filter_chain_domain (mega-brand blocklist) ───
echo "Section 2: filter_chain_domain\n";

assert_true(filter_chain_domain('phishing.hameconnage@aircanada.ca') === true,
    "aircanada.ca email caught");
assert_true(filter_chain_domain('https://aircanada.ca/saskatoon-lounge') === true,
    "aircanada.ca URL caught");
assert_true(filter_chain_domain('https://lounges.aircanada.ca/saskatoon') === true,
    "subdomain of aircanada.ca caught");
assert_true(filter_chain_domain('manager@tims.ca') === true, "tims.ca caught");
assert_true(filter_chain_domain('http://www.timhortons.com') === true,
    "www. prefix stripped before lookup");
assert_true(filter_chain_domain('https://www.skillsamuraiportal.com/login') === true,
    "user-flagged skillsamuraiportal.com caught");
assert_true(filter_chain_domain('hello@skillsamurai.com') === true,
    "user-flagged skillsamurai.com caught");
assert_true(filter_chain_domain('info@joesplumbing.ca') === false,
    "unknown small-biz domain passes through");
assert_true(filter_chain_domain('') === false,
    "empty input returns false (do not over-reject)");
assert_true(filter_chain_domain('not-a-valid-thing') === false,
    "malformed input returns false");

// ─── Section 3: filter_blocklisted_place_type ───
echo "Section 3: filter_blocklisted_place_type\n";

assert_true(filter_blocklisted_place_type(['airport', 'establishment']) === 'airport',
    "airport blocked");
assert_true(filter_blocklisted_place_type(['hospital']) === 'hospital',
    "hospital blocked");
assert_true(filter_blocklisted_place_type(['city_hall', 'point_of_interest']) === 'city_hall',
    "city_hall blocked");
assert_true(filter_blocklisted_place_type(['restaurant', 'food', 'establishment']) === '',
    "restaurant passes through");
assert_true(filter_blocklisted_place_type([]) === '',
    "empty types passes through");
assert_true(filter_blocklisted_place_type(null) === '',
    "null is defensive-safe");

// ─── Section 4: filter_category_type_mismatch ───
echo "Section 4: filter_category_type_mismatch\n";

assert_true(filter_category_type_mismatch('travel_agency', ['airport', 'establishment']) === true,
    "expected travel_agency but got airport: mismatch");
assert_true(filter_category_type_mismatch('travel_agency', ['travel_agency', 'point_of_interest']) === false,
    "expected travel_agency, present in types: OK");
assert_true(filter_category_type_mismatch('', ['airport']) === false,
    "no expected type: skip the check (returns false)");
assert_true(filter_category_type_mismatch('plumber', null) === true,
    "null types with expectation: mismatch (defensive)");
assert_true(filter_category_type_mismatch('plumber', []) === true,
    "empty types with expectation: mismatch");

// ─── Section 5: filter_review_count_too_high (default lowered to 15) ───
echo "Section 5: filter_review_count_too_high\n";

assert_true(filter_review_count_too_high(10) === false,    "10 reviews: pass (under default 15)");
assert_true(filter_review_count_too_high(15) === false,    "15 reviews: pass (at default threshold, not over)");
assert_true(filter_review_count_too_high(16) === true,     "16 reviews: reject (over default 15)");
assert_true(filter_review_count_too_high(50) === true,     "50 reviews: reject (default lowered)");
assert_true(filter_review_count_too_high(5000) === true,   "5000 reviews: reject (chain)");
assert_true(filter_review_count_too_high(null) === false,  "null: pass (no signal)");
assert_true(filter_review_count_too_high(0) === false,     "0: pass (new business)");
assert_true(filter_review_count_too_high('') === false,    "empty string: pass");
assert_true(filter_review_count_too_high('400') === true,  "stringified 400: still rejects");

// Custom threshold still honoured
assert_true(filter_review_count_too_high(150, 100) === true,
    "custom threshold: 150 > 100");
assert_true(filter_review_count_too_high(50, 100) === false,
    "custom threshold: 50 <= 100");

// ─── Section 5b: outreach_max_review_count precedence (env -> default) ───
// The DB-state branch isn't exercised here (no $pdo in this smoke harness);
// it falls through to env, then to the built-in default of 15.
echo "Section 5b: outreach_max_review_count\n";

$savedEnv = $_ENV['OUTREACH_MAX_REVIEW_COUNT'] ?? null;

$_ENV['OUTREACH_MAX_REVIEW_COUNT'] = '40';
assert_true(outreach_max_review_count() === 40, "env override honoured (40)");

$_ENV['OUTREACH_MAX_REVIEW_COUNT'] = '0';
assert_true(outreach_max_review_count() === 15, "env 0 is invalid -> default 15");

$_ENV['OUTREACH_MAX_REVIEW_COUNT'] = 'abc';
assert_true(outreach_max_review_count() === 15, "env non-numeric -> default 15");

unset($_ENV['OUTREACH_MAX_REVIEW_COUNT']);
assert_true(outreach_max_review_count() === 15, "env unset -> default 15");

// Restore whatever the environment had so later sections are unaffected.
if ($savedEnv === null) { unset($_ENV['OUTREACH_MAX_REVIEW_COUNT']); }
else { $_ENV['OUTREACH_MAX_REVIEW_COUNT'] = $savedEnv; }

// ─── Section 5c: detect_established_signals (newness gate, pure regex) ───
echo "Section 5c: detect_established_signals\n";

$currentYear = (int) date('Y');
$oldYear = $currentYear - 20;

$sig = detect_established_signals("<p>Proudly serving the community since {$oldYear}.</p>");
assert_true($sig['founded_year'] === $oldYear, "founded year detected from 'since YYYY'");

$sig = detect_established_signals('<footer>&copy; ' . $oldYear . ' Acme Co. All rights reserved.</footer>');
assert_true($sig['founded_year'] === $oldYear, "founded year detected from copyright");

$sig = detect_established_signals('<h2>With over 25 years of experience in plumbing</h2>');
assert_true($sig['years_experience'] === 25, "years experience detected ('over 25 years of experience')");

$sig = detect_established_signals('<p>20+ years in business serving Saskatoon</p>');
assert_true($sig['years_experience'] === 20, "years experience detected ('20+ years in business')");

$sig = detect_established_signals('<h1>Now open! Fresh new bakery launching this spring.</h1>');
assert_true($sig['founded_year'] === null && $sig['years_experience'] === null,
    "brand-new site has no established signals (passes through)");

$sig = detect_established_signals('<p>We sell handmade candles. Contact us today.</p>');
assert_true($sig['founded_year'] === null && $sig['years_experience'] === null,
    "sparse site has no established signals (passes through)");

// detect_storefront_founded_year (shared with the Shopify evaluator)
assert_true(detect_storefront_founded_year("<p>Founded in {$oldYear}.</p>") === $oldYear,
    "detect_storefront_founded_year still works after move to shared helpers");
assert_true(detect_storefront_founded_year('<p>no year here</p>') === null,
    "detect_storefront_founded_year returns null when no signal");

// ─── Section 6: _outreach_host_from (helper for chain-domain logic) ───
echo "Section 6: _outreach_host_from\n";

assert_true(_outreach_host_from('user@example.com') === 'example.com',
    "email host extracted");
assert_true(_outreach_host_from('USER@EXAMPLE.COM') === 'example.com',
    "email lowercased");
assert_true(_outreach_host_from('https://www.example.com/path') === 'example.com',
    "www. stripped");
assert_true(_outreach_host_from('http://sub.example.com') === 'sub.example.com',
    "subdomain preserved");
assert_true(_outreach_host_from('example.com/no-scheme') === 'example.com',
    "missing scheme handled");
assert_true(_outreach_host_from('') === '',
    "empty input returns empty");

// ─── Summary ───
echo "\n=== Results ===\n";
echo "Passed: $pass\n";
echo "Failed: $fail\n";
exit($fail === 0 ? 0 : 1);
