<?php
// profit-analyzer/lib/import/gemini.php
//
// Direct Gemini client for the Profit Analyzer's server-side analysis.
//
// The app calls Gemini through api/ai/completions.php, but that proxy requires a
// license key or device ID. The Profit Analyzer is anonymous public traffic with
// neither, so it calls Gemini directly here using the server-side GEMINI_API_KEY.
// Abuse is bounded by the per-IP rate limit + file caps in upload.php.
//
// The request/response shape mirrors completions.php exactly (system_instruction +
// user parts, responseMimeType application/json, content at candidates[0]...text)
// so the two stay behaviourally identical and easy to merge later.

if (!defined('PA_GEMINI_MODEL')) {
    define('PA_GEMINI_MODEL', 'gemini-2.5-flash');
}

/** Loads GEMINI_API_KEY from the project .env if it isn't already in the environment. */
function pa_gemini_key(): string
{
    $key = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: '';
    if ($key !== '') {
        return $key;
    }
    $autoload = __DIR__ . '/../../../vendor/autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        if (class_exists('Dotenv\\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
            $dotenv->safeLoad();
            $key = $_ENV['GEMINI_API_KEY'] ?? '';
        }
    }
    return $key ?: '';
}

/**
 * Sends a system+user chat to Gemini and returns the model's text content
 * (expected to be JSON, since responseMimeType is application/json). Returns null
 * on any failure — callers treat null as "this batch/chunk could not be analyzed".
 *
 * Mirrors GeminiService.SendChatAsync: maxTokens default 4000, temperature 0.1.
 */
function pa_gemini_chat(
    string $systemPrompt,
    string $userPrompt,
    int $maxTokens = 4000,
    float $temperature = 0.1
): ?string {
    $key = pa_gemini_key();
    if ($key === '') {
        error_log('profit-analyzer: GEMINI_API_KEY not configured');
        return null;
    }

    $model = PA_GEMINI_MODEL;
    $maxTokens = max(1, min($maxTokens, 16000));
    $temperature = max(0.0, min(2.0, $temperature));

    $payload = [
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
        ],
        'generationConfig' => [
            'temperature' => $temperature,
            'maxOutputTokens' => $maxTokens,
            'responseMimeType' => 'application/json',
        ],
    ];
    if ($systemPrompt !== '') {
        $payload['system_instruction'] = ['parts' => [['text' => $systemPrompt]]];
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 120,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log('profit-analyzer Gemini cURL error: ' . $curlError);
        return null;
    }

    $data = json_decode($response, true);

    if ($httpCode !== 200) {
        $msg = $data['error']['message'] ?? 'unknown error';
        error_log("profit-analyzer Gemini error ({$httpCode}): {$msg}");
        return null;
    }

    $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    return is_string($content) && $content !== '' ? $content : null;
}

/**
 * Strips a ```json ... ``` markdown fence if the model wrapped its JSON in one.
 * Mirrors JsonResponseHelper.StripMarkdownCodeBlock.
 */
function pa_strip_markdown_json(string $response): string
{
    $s = trim($response);
    if (strncmp($s, '```', 3) === 0) {
        // Drop the opening fence line (``` or ```json) and the closing fence.
        $s = preg_replace('/^```[a-zA-Z]*\s*\n?/', '', $s);
        $s = preg_replace('/\n?```\s*$/', '', $s);
        $s = trim($s);
    }
    return $s;
}
