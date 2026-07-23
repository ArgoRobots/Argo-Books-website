<?php
/**
 * Map of referrer hosts and utm_source values to source codes.
 *
 * The single source of truth for auto-detected channels. Shared by:
 *   - track_referral.php: resolves ?utm_source= / ?ref= / Referer into a
 *     source_code on landing.
 *   - admin/marketing-funnel/analytics.php: derives the AI / social host lists
 *     for channel classification (funnel_classify_channel), so adding an entry
 *     here updates both source attribution and the channel donut together.
 *
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
