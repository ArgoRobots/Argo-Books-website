<?php
/**
 * Referral tracking middleware
 * Include this file at the top of pages where you want to track referral sources
 *
 * Source resolution order:
 *   1. ?source= URL param (manual codes: sponsors, outreach campaigns, ads)
 *   2. ?utm_source= URL param mapped to a known channel
 *   3. HTTP Referer header mapped to a known channel (AI chats, social sites)
 */

require_once __DIR__ . '/statistics.php';

/**
 * Map of referrer hosts and utm_source values to source codes.
 * Add new entries here to start auto-tracking traffic from additional channels.
 */
function get_auto_referral_sources()
{
    return [
        // AI chats
        'chatgpt.com'           => ['code' => 'ai-chatgpt',     'name' => 'ChatGPT'],
        'chat.openai.com'       => ['code' => 'ai-chatgpt',     'name' => 'ChatGPT'],
        'chatgpt'               => ['code' => 'ai-chatgpt',     'name' => 'ChatGPT'],
        'claude.ai'             => ['code' => 'ai-claude',      'name' => 'Claude'],
        'claude'                => ['code' => 'ai-claude',      'name' => 'Claude'],
        'perplexity.ai'         => ['code' => 'ai-perplexity',  'name' => 'Perplexity'],
        'www.perplexity.ai'     => ['code' => 'ai-perplexity',  'name' => 'Perplexity'],
        'perplexity'            => ['code' => 'ai-perplexity',  'name' => 'Perplexity'],
        'gemini.google.com'     => ['code' => 'ai-gemini',      'name' => 'Gemini'],
        'gemini'                => ['code' => 'ai-gemini',      'name' => 'Gemini'],
        'copilot.microsoft.com' => ['code' => 'ai-copilot',     'name' => 'Microsoft Copilot'],
        'copilot'               => ['code' => 'ai-copilot',     'name' => 'Microsoft Copilot'],
        'you.com'               => ['code' => 'ai-you',         'name' => 'You.com'],
        'phind.com'             => ['code' => 'ai-phind',       'name' => 'Phind'],
        'poe.com'               => ['code' => 'ai-poe',         'name' => 'Poe'],
        'meta.ai'               => ['code' => 'ai-meta',        'name' => 'Meta AI'],
        'duckduckgo.com'        => ['code' => 'ai-duckduckgo',  'name' => 'DuckDuckGo'],

        // Social
        'reddit.com'            => ['code' => 'social-reddit',      'name' => 'Reddit'],
        'www.reddit.com'        => ['code' => 'social-reddit',      'name' => 'Reddit'],
        'old.reddit.com'        => ['code' => 'social-reddit',      'name' => 'Reddit'],
        'reddit'                => ['code' => 'social-reddit',      'name' => 'Reddit'],
        'news.ycombinator.com'  => ['code' => 'social-hn',          'name' => 'Hacker News'],
        'hackernews'            => ['code' => 'social-hn',          'name' => 'Hacker News'],
        'producthunt.com'       => ['code' => 'social-producthunt', 'name' => 'Product Hunt'],
        'www.producthunt.com'   => ['code' => 'social-producthunt', 'name' => 'Product Hunt'],
        'producthunt'           => ['code' => 'social-producthunt', 'name' => 'Product Hunt'],
        'twitter.com'           => ['code' => 'social-x',           'name' => 'X (Twitter)'],
        'x.com'                 => ['code' => 'social-x',           'name' => 'X (Twitter)'],
        't.co'                  => ['code' => 'social-x',           'name' => 'X (Twitter)'],
        'twitter'               => ['code' => 'social-x',           'name' => 'X (Twitter)'],
        'linkedin.com'          => ['code' => 'social-linkedin',    'name' => 'LinkedIn'],
        'www.linkedin.com'      => ['code' => 'social-linkedin',    'name' => 'LinkedIn'],
        'lnkd.in'               => ['code' => 'social-linkedin',    'name' => 'LinkedIn'],
        'linkedin'              => ['code' => 'social-linkedin',    'name' => 'LinkedIn'],
        'facebook.com'          => ['code' => 'social-facebook',    'name' => 'Facebook'],
        'www.facebook.com'      => ['code' => 'social-facebook',    'name' => 'Facebook'],
        'm.facebook.com'        => ['code' => 'social-facebook',    'name' => 'Facebook'],
        'facebook'              => ['code' => 'social-facebook',    'name' => 'Facebook'],
        'youtube.com'           => ['code' => 'social-youtube',     'name' => 'YouTube'],
        'www.youtube.com'       => ['code' => 'social-youtube',     'name' => 'YouTube'],
        'youtu.be'              => ['code' => 'social-youtube',     'name' => 'YouTube'],
        'youtube'               => ['code' => 'social-youtube',     'name' => 'YouTube'],
        'instagram.com'         => ['code' => 'social-instagram',   'name' => 'Instagram'],
        'www.instagram.com'     => ['code' => 'social-instagram',   'name' => 'Instagram'],
        'tiktok.com'            => ['code' => 'social-tiktok',      'name' => 'TikTok'],
        'www.tiktok.com'        => ['code' => 'social-tiktok',      'name' => 'TikTok'],
    ];
}

/**
 * Ensure a referral_links row exists for an auto-detected source.
 * The admin dashboard joins on referral_links, so the row must exist
 * for visits to appear in the charts.
 */
function ensure_auto_referral_link($source_code, $name)
{
    global $pdo;
    if (!$pdo) {
        return;
    }
    try {
        $check = $pdo->prepare('SELECT 1 FROM referral_links WHERE source_code = ? LIMIT 1');
        $check->execute([$source_code]);
        if ($check->fetch() !== false) {
            return;
        }
        $insert = $pdo->prepare(
            'INSERT INTO referral_links (source_code, name, description, target_url, is_active)
             VALUES (?, ?, ?, ?, 1)'
        );
        $insert->execute([
            $source_code,
            $name,
            'Auto-detected from referrer',
            'https://argorobots.com/'
        ]);
    } catch (PDOException $e) {
        // Don't break tracking on a transient DB error
    }
}

$resolved_source = null;

// 1. Explicit ?source= param (sponsors, outreach campaigns, ads)
if (isset($_GET['source']) && !empty($_GET['source'])) {
    $candidate = trim($_GET['source']);
    if (preg_match('/^[a-zA-Z0-9_-]+$/', $candidate)) {
        $resolved_source = $candidate;
    }
}

// 2. ?utm_source= param mapped to a known channel
if ($resolved_source === null && !empty($_GET['utm_source'])) {
    $utm = strtolower(trim($_GET['utm_source']));
    $sources = get_auto_referral_sources();
    if (isset($sources[$utm])) {
        $mapping = $sources[$utm];
        ensure_auto_referral_link($mapping['code'], $mapping['name']);
        $resolved_source = $mapping['code'];
    }
}

// 3. HTTP Referer header mapped to a known channel
if ($resolved_source === null && !empty($_SERVER['HTTP_REFERER'])) {
    $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    if ($referer_host) {
        $referer_host = strtolower($referer_host);
        $sources = get_auto_referral_sources();
        if (isset($sources[$referer_host])) {
            $mapping = $sources[$referer_host];
            ensure_auto_referral_link($mapping['code'], $mapping['name']);
            $resolved_source = $mapping['code'];
        }
    }
}

if ($resolved_source !== null) {
    track_referral_visit($resolved_source, $_SERVER['REQUEST_URI']);
}
