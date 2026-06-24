<?php
// Start a session if one doesn't exist so we can check admin status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

/**
 * Detects search engines, AI scrapers, social-preview fetchers, and HTTP-library
 * traffic via the User-Agent string. Empty UA is treated as a bot.
 *
 * @param string $user_agent Raw User-Agent header
 * @return bool True if the UA looks like a bot
 */
function is_likely_bot($user_agent)
{
    $ua = trim($user_agent);
    if ($ua === '') {
        return true;
    }

    // We list specific bot names rather than matching "bot" alone, because some
    // legitimate UAs contain "bot" as a substring (e.g. Cubot phones).
    static $patterns = [
        // Search / SEO crawlers
        'Googlebot', 'bingbot', 'DuckDuckBot', 'YandexBot', 'Baiduspider', 'Sogou',
        'Slurp', 'Applebot', 'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'rogerbot',
        // Google's non-"Googlebot" crawlers. These don't contain the substring
        // "Googlebot", so they slipped the line above and flooded referral_visits
        // (esp. Google-InspectionTool, which fires on Search Console URL inspection).
        'GoogleOther', 'Google-InspectionTool', 'Storebot-Google', 'Feedfetcher-Google',
        'APIs-Google', 'GoogleProducer',
        // AI / dataset crawlers
        'GPTBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web', 'anthropic-ai',
        'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended',
        'CCBot', 'Bytespider', 'Diffbot', 'Amazonbot', 'cohere-ai',
        // Social / link-preview fetchers
        'facebookexternalhit', 'meta-externalagent', 'Twitterbot', 'LinkedInBot',
        'Slackbot', 'Discordbot', 'TelegramBot', 'WhatsApp',
        // HTTP client libraries (real browsers don't send these)
        'curl/', 'wget/', 'python-requests', 'python-urllib', 'Go-http-client',
        'Java/', 'okhttp', 'libwww-perl', 'Apache-HttpClient', 'node-fetch',
        // Headless / automation
        'HeadlessChrome', 'PhantomJS', 'Selenium',
        // Generic crawler verbs
        'crawler', 'spider', 'scraper',
        // Archives
        'archive.org', 'Wayback',
    ];

    foreach ($patterns as $pattern) {
        if (stripos($ua, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * True if $ip falls inside $range. $range is either a plain address (exact
 * match, works for IPv4 and IPv6) or an IPv4 CIDR like "66.249.64.0/19".
 */
function ip_in_cidr($ip, $range)
{
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }

    list($subnet, $bits) = explode('/', $range, 2);
    $ip_long     = ip2long($ip);
    $subnet_long = ip2long($subnet);
    // ip2long returns false for non-IPv4 input; the CIDR math below is IPv4-only.
    if ($ip_long === false || $subnet_long === false) {
        return false;
    }

    $bits = (int)$bits;
    if ($bits < 0 || $bits > 32) {
        return false;
    }

    $mask = ($bits === 0) ? 0 : ((0xFFFFFFFF << (32 - $bits)) & 0xFFFFFFFF);
    return (($ip_long & $mask) === ($subnet_long & $mask));
}

/**
 * True if $ip belongs to a datacenter / cloud-hosting network rather than a
 * residential or mobile ISP. Real customers browse from consumer ISPs; traffic
 * originating inside AWS, Google Cloud, Azure, DigitalOcean, etc. is almost
 * always automated (scrapers, uptime monitors, AI crawlers) wearing a spoofed
 * browser User-Agent that is_likely_bot() can't catch by name. The June 2026
 * invoice-generator scrape came almost entirely from Google Cloud (34/8, 35/8).
 *
 * This is a STATIC list of the largest published cloud aggregates, not a live
 * IP-to-ASN lookup, so it is deliberately broad rather than exhaustive: a few
 * uncounted real visitors costs us nothing, but a missed scraper pollutes the
 * stats. Extend it via the DATACENTER_IP_RANGES env var (same format as
 * EXCLUDED_TRACKING_IPS: comma-separated plain IPs or CIDR ranges). IPv4 only.
 */
function is_datacenter_ip($ip)
{
    if (empty($ip)) {
        return false;
    }

    static $ranges = [
        // Google Cloud Platform. 34/8 and 35/8 are predominantly GCP.
        '34.0.0.0/8', '35.0.0.0/8',
        // Amazon Web Services (largest aggregates).
        '3.0.0.0/8', '13.32.0.0/15', '15.177.0.0/18', '18.0.0.0/8',
        '52.0.0.0/8', '54.0.0.0/8',
        // Microsoft Azure (largest aggregates).
        '13.64.0.0/11', '20.0.0.0/8', '40.64.0.0/10', '104.40.0.0/13',
        // DigitalOcean.
        '104.131.0.0/16', '138.197.0.0/16', '159.65.0.0/16', '165.227.0.0/16',
        '167.71.0.0/16', '167.99.0.0/16', '178.62.0.0/16', '188.166.0.0/16',
        // OVH.
        '51.68.0.0/16', '51.75.0.0/16', '51.83.0.0/16', '51.91.0.0/16',
        '54.36.0.0/16', '145.239.0.0/16', '147.135.0.0/16',
        // Hetzner.
        '5.9.0.0/16', '88.99.0.0/16', '116.202.0.0/16', '116.203.0.0/16',
        '135.181.0.0/16', '157.90.0.0/16', '162.55.0.0/16', '167.235.0.0/16',
        // Linode / Akamai.
        '45.33.0.0/16', '45.56.0.0/16', '50.116.0.0/16', '66.175.208.0/20',
        '96.126.96.0/19', '139.144.0.0/16', '172.104.0.0/15', '173.255.192.0/18',
    ];

    $configured = $_ENV['DATACENTER_IP_RANGES'] ?? getenv('DATACENTER_IP_RANGES');
    if (!empty($configured)) {
        foreach (explode(',', $configured) as $entry) {
            $entry = trim($entry);
            if ($entry !== '') {
                $ranges[] = $entry;
            }
        }
    }

    foreach ($ranges as $range) {
        if (ip_in_cidr($ip, $range)) {
            return true;
        }
    }
    return false;
}

/**
 * IPs we never record analytics for: the site owner's own connection(s), known
 * crawler netblocks whose UAs can't be trusted, and datacenter/cloud hosts.
 * Keeps the owner's casual (logged-out) browsing, Google's crawler, and
 * UA-spoofing scrapers out of page-view, referral-visit, and funnel-event tables.
 *
 * Configure owner/internal addresses via the EXCLUDED_TRACKING_IPS env var:
 * comma-separated, plain IPs or CIDR ranges (e.g. "64.201.195.108,203.0.113.0/24").
 */
function is_nontracked_ip($ip)
{
    if (empty($ip)) {
        return false;
    }

    // Cloud/datacenter source = automated traffic with a spoofed browser UA.
    if (is_datacenter_ip($ip)) {
        return true;
    }

    // Google's published crawler range. UA filtering catches the named bots
    // above; this is the backstop since a spoofed UA can't fake the source IP.
    static $ranges = ['66.249.64.0/19'];

    $configured = $_ENV['EXCLUDED_TRACKING_IPS'] ?? getenv('EXCLUDED_TRACKING_IPS');
    if (!empty($configured)) {
        foreach (explode(',', $configured) as $entry) {
            $entry = trim($entry);
            if ($entry !== '') {
                $ranges[] = $entry;
            }
        }
    }

    foreach ($ranges as $range) {
        if (ip_in_cidr($ip, $range)) {
            return true;
        }
    }
    return false;
}

/**
 * Track a statistical event
 *
 * @param string $event_type Type of event (download, page_view, etc.)
 * @param string $event_data Additional event data
 * @return bool Success status
 */
function track_event($event_type, $event_data = '')
{
    // Don't track statistics for logged in admins
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return false;
    }

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (is_likely_bot($user_agent)) {
        return false;
    }

    if (is_nontracked_ip($_SERVER['REMOTE_ADDR'] ?? null)) {
        return false;
    }

    global $pdo;
    if (!$pdo) {
        return false;
    }
    $ip_address = $_SERVER['REMOTE_ADDR'];

    try {
        // Only record one occurrence of an event per IP per day
        $today_start = date('Y-m-d 00:00:00');
        $exists_stmt = $pdo->prepare('SELECT 1 FROM statistics WHERE event_type = ? AND event_data = ? AND ip_address = ? AND created_at >= ? LIMIT 1');
        $exists_stmt->execute([$event_type, $event_data, $ip_address, $today_start]);
        if ($exists_stmt->fetch() !== false) {
            return false;
        }

        $country_code = null;

        // Check if we already have this IP's country code in our database
        $check_stmt = $pdo->prepare('SELECT country_code FROM statistics WHERE ip_address = ? AND country_code IS NOT NULL AND country_code != "" LIMIT 1');
        $check_stmt->execute([$ip_address]);
        $row = $check_stmt->fetch();

        if ($row !== false) {
            // We already have this IP's country code
            $country_code = $row['country_code'];
        } else {
            // New IP or no country code yet, use cURL to contact the API
            if (function_exists('curl_init')) {
                $ch = curl_init("https://ipinfo.io/{$ip_address}/country");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_USERAGENT, 'ArgoSalesTracker/1.0');
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($http_code == 200 && !empty($response)) {
                    $country_code = trim($response);
                }
            }
        }

        // Insert event
        $stmt = $pdo->prepare('INSERT INTO statistics (event_type, event_data, ip_address, user_agent, country_code) VALUES (?, ?, ?, ?, ?)');
        $result = $stmt->execute([$event_type, $event_data, $ip_address, $user_agent, $country_code]);

        return $result;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Track a page view
 *
 * @param string $page The page being viewed (e.g., 'homepage', 'download', 'documentation')
 * @return bool Success status
 */
function track_page_view($page)
{
    $result = track_event('page_view', $page);
    // Also emit a separate 'reddit_referrer' event if this visit came from
    // a Reddit URL. Used by the admin Reddit dashboard to count profile-link
    // clicks (the only Reddit traffic browsers expose a referrer for).
    track_reddit_referrer_if_present($page);
    return $result;
}

/**
 * If the inbound request carries a reddit.com Referer header, emit a
 * 'reddit_referrer' statistics event. Called from track_page_view().
 * Same one-per-IP-per-day dedup behaviour as track_event() since it
 * piggybacks on that function.
 */
function track_reddit_referrer_if_present($page)
{
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    if ($referrer === '') return;
    if (!preg_match('#^https?://(www\.|old\.|new\.|m\.|i\.)?reddit\.com/?#i', $referrer)) return;
    // event_data is the inbound page (the landing URL the Reddit visitor hit).
    // We deliberately don't store the referrer itself, since it adds little
    // beyond "reddit.com" and risks logging tracking params from share links.
    track_event('reddit_referrer', $page);
}

/**
 * Track a referral visit from a source parameter
 *
 * @param string $source_code The source code from URL parameter (e.g., 'google-ad', 'twitter-sponsor')
 * @param string $page_url The current page URL
 * @return bool Success status
 */
function track_referral_visit($source_code, $page_url = '')
{
    // Don't track statistics for logged in admins
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return false;
    }

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (is_likely_bot($user_agent)) {
        return false;
    }

    if (is_nontracked_ip($_SERVER['REMOTE_ADDR'] ?? null)) {
        return false;
    }

    global $pdo;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Store source in session for conversion tracking (runs even if DB is down)
    if (!isset($_SESSION['referral_source'])) {
        $_SESSION['referral_source'] = $source_code;
    }

    if (!$pdo) {
        return false;
    }

    try {
        // Check if this IP already visited from this source today
        $today_start = date('Y-m-d 00:00:00');
        $exists_stmt = $pdo->prepare('SELECT 1 FROM referral_visits WHERE source_code = ? AND ip_address = ? AND visited_at >= ? LIMIT 1');
        $exists_stmt->execute([$source_code, $ip_address, $today_start]);
        if ($exists_stmt->fetch() !== false) {
            return false; // Already tracked this IP for this source today
        }

        $country_code = null;

        // Check if we already have this IP's country code
        $check_stmt = $pdo->prepare('SELECT country_code FROM referral_visits WHERE ip_address = ? AND country_code IS NOT NULL AND country_code != "" LIMIT 1');
        $check_stmt->execute([$ip_address]);
        $row = $check_stmt->fetch();

        if ($row !== false) {
            $country_code = $row['country_code'];
        } else {
            // New IP, get country code from API
            if (function_exists('curl_init')) {
                $ch = curl_init("https://ipinfo.io/{$ip_address}/country");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_USERAGENT, 'ArgoSalesTracker/1.0');
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($http_code == 200 && !empty($response)) {
                    $country_code = trim($response);
                }
            }
        }

        // Insert referral visit
        $stmt = $pdo->prepare('INSERT INTO referral_visits (source_code, page_url, ip_address, user_agent, country_code) VALUES (?, ?, ?, ?, ?)');
        $result = $stmt->execute([$source_code, $page_url, $ip_address, $user_agent, $country_code]);

        return $result;
    } catch (PDOException $e) {
        return false;
    }
}
