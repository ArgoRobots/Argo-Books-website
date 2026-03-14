<?php
/**
 * AI Completions Proxy Endpoint
 *
 * POST /api/ai/completions - Proxy OpenAI chat completion requests
 *
 * Receives prompts from the Argo Books app, forwards them to OpenAI,
 * and returns the response. All API keys are stored server-side.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

// Rate limiting: 60 requests per 15 minutes per company
$ip = get_client_ip();
if (is_rate_limited($ip, 60, 900, 'ai_' . $company['id'])) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($ip, 'ai_' . $company['id']);

// Validate server configuration
$openaiKey = $_ENV['OPENAI_API_KEY'] ?? '';
if (empty($openaiKey)) {
    send_error_response(500, 'AI service not configured on server.', 'CONFIG_ERROR');
}

$defaultModel = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

// Validate required fields
if (empty($data['systemPrompt']) && empty($data['userPrompt'])) {
    send_error_response(400, 'At least one of systemPrompt or userPrompt is required.', 'MISSING_FIELDS');
}

$systemPrompt = $data['systemPrompt'] ?? '';
$userPrompt = $data['userPrompt'] ?? '';
$model = $data['model'] ?? $defaultModel;
$maxTokens = min((int)($data['maxTokens'] ?? 4000), 16000); // Cap at 16k
$temperature = max(0, min(2, (float)($data['temperature'] ?? 0.1)));

// Build OpenAI request
$messages = [];
if (!empty($systemPrompt)) {
    $messages[] = ['role' => 'system', 'content' => $systemPrompt];
}
if (!empty($userPrompt)) {
    $messages[] = ['role' => 'user', 'content' => $userPrompt];
}

$openaiPayload = json_encode([
    'model' => $model,
    'messages' => $messages,
    'temperature' => $temperature,
    'max_tokens' => $maxTokens,
]);

// Forward to OpenAI
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $openaiPayload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaiKey,
    ],
    CURLOPT_TIMEOUT => 120,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    error_log('OpenAI proxy cURL error: ' . $curlError);
    send_error_response(502, 'Failed to connect to AI service.', 'UPSTREAM_ERROR');
}

$responseData = json_decode($response, true);

if ($httpCode !== 200) {
    $errorMessage = $responseData['error']['message'] ?? 'Unknown upstream error';
    error_log("OpenAI proxy error ({$httpCode}): {$errorMessage}");

    if ($httpCode === 429) {
        send_error_response(429, 'AI service rate limit exceeded. Please try again later.', 'UPSTREAM_RATE_LIMITED');
    }

    send_error_response(502, 'AI service returned an error.', 'UPSTREAM_ERROR');
}

// Extract content from OpenAI response
$content = $responseData['choices'][0]['message']['content'] ?? null;
if ($content === null) {
    send_error_response(502, 'Invalid response from AI service.', 'UPSTREAM_ERROR');
}

$usage = $responseData['usage'] ?? null;

send_json_response(200, [
    'success' => true,
    'content' => $content,
    'model' => $responseData['model'] ?? $model,
    'usage' => $usage,
    'timestamp' => date('c'),
]);
