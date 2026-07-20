<?php
/**
 * AI Completions Proxy Endpoint
 *
 * POST /api/ai/completions - Proxy AI chat completion requests
 *
 * Receives prompts from the Argo Books app, forwards them to Gemini,
 * and returns the response. The Gemini API key is stored server-side.
 */

require_once __DIR__ . '/../portal/portal-helper.php';
require_once __DIR__ . '/_timing.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate using license key (premium) or device ID (free)
$license = authenticate_license_request();
$deviceIdHash = null;
if (!$license) {
    $deviceIdHash = authenticate_device_request();
    if (!$deviceIdHash) {
        send_error_response(401, 'Invalid or missing license key.', 'UNAUTHORIZED');
    }
}

// Rate limiting: 60 requests per 15 minutes per identity
$rateLimitId = $license ? substr($license['license_key_hash'], 0, 16) : substr($deviceIdHash, 0, 16);
$rateLimitKey = 'ai_license';
if (is_rate_limited($rateLimitId, 60, 900, $rateLimitKey)) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($rateLimitId, $rateLimitKey);

// Per-IP ceiling for the free (device) path only.
// The X-Device-Id of a free request is self-asserted and not checked against any
// record, so the per-identity limit above can be bypassed by rotating the header.
// A per-IP cap closes that hole: the source IP can't be rotated like a header, so
// it bounds total free AI usage from one origin regardless of how many device IDs
// are sent. Premium (license-validated) requests are exempt because their key is
// verified in the database and is not the abuse vector. This reuses the same
// get_client_ip() that the license-validation and payment endpoints already rely
// on in production. The ceiling is generous (well above the 60/identity limit) so
// genuine shared networks (offices/households behind one NAT) are not affected;
// raise AI_IP_MAX if a large shared deployment ever legitimately hits it.
if (!$license) {
    $clientIp = get_client_ip();
    $ipRateKey = 'ai_ip';
    $aiIpMax = 200; // requests per 15 minutes per IP
    if (is_rate_limited($clientIp, $aiIpMax, 900, $ipRateKey)) {
        send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
    }
    record_rate_limit_attempt($clientIp, $ipRateKey);
}

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
$requestedModel = $data['model'] ?? '';
$maxTokens = max(1, min((int)($data['maxTokens'] ?? 4000), 32000)); // Clamp 1-32k
$temperature = max(0, min(2, (float)($data['temperature'] ?? 0.1)));
$base64Image = $data['base64Image'] ?? null;
$mimeType = $data['mimeType'] ?? 'image/jpeg';

// Optional timing metadata sent by the desktop app so pooled duration priors can be
// kept per operation (receipt scan vs spreadsheet analysis vs bank categorize, which
// are otherwise indistinguishable here). Absent/older clients default to 'completion'.
$operation = isset($data['operation']) ? (string) $data['operation'] : 'completion';
$sizeFeature = isset($data['sizeFeature']) && is_numeric($data['sizeFeature']) ? (int) $data['sizeFeature'] : null;
$appPlatform = isset($data['platform']) ? (string) $data['platform'] : null;

// Receipt scans on the gemini-3.x thinking models spend a large, variable chunk of
// maxOutputTokens on hidden reasoning before writing the JSON answer, so budgets that
// were fine on the old model now truncate mid-JSON. The receipt budget is authoritative
// from .env (RECEIPT_SCAN_MAX_OUTPUT_TOKENS) and ignores whatever the client sent, so it
// can be tuned for all client versions instantly (older builds still send 16000) without
// an app release. Overrides the generic clamp above for this operation only.
$isReceiptExtraction = ($operation === 'receipt_scan' && !empty($base64Image));
if ($isReceiptExtraction) {
    $maxTokens = max(1, (int)($_ENV['RECEIPT_SCAN_MAX_OUTPUT_TOKENS'] ?? 32000));
}

// Installed desktop builds pin a model id that Google has since retired (e.g.
// gemini-2.5-flash); empty requests also need a default. Remap both to a current
// model so existing installs keep working without a forced app update instead of
// failing every AI call. Vision requests (receipt scans include an image) get the
// accuracy tier; text-only calls get the cheaper general model.
$retiredModels = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-2.0-flash-lite', 'gemini-2.0-flash-001'];
if ($requestedModel === '' || in_array($requestedModel, $retiredModels, true)) {
    $requestedModel = $base64Image
        ? ($_ENV['GEMINI_MODEL_EXTRACTION'] ?? 'gemini-3.5-flash')
        : ($_ENV['GEMINI_MODEL'] ?? 'gemini-3.1-flash-lite');
}

// Validate model: Gemini is the only supported provider
$geminiModels = ['gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-2.5-pro'];
if (!in_array($requestedModel, $geminiModels, true)) {
    send_error_response(400, 'Unsupported model. Supported: ' . implode(', ', $geminiModels), 'INVALID_MODEL');
}

$geminiKey = $_ENV['GEMINI_API_KEY'] ?? '';
if (empty($geminiKey)) {
    send_error_response(500, 'Gemini AI service not configured on server.', 'CONFIG_ERROR');
}

$model = $requestedModel;

// Build Gemini request: https://ai.google.dev/api/generate-content
$contents = [];

// Gemini uses system_instruction for system prompts (not in contents array)
$systemInstruction = null;
if (!empty($systemPrompt)) {
    $systemInstruction = ['parts' => [['text' => $systemPrompt]]];
}

// Build user message parts
$userParts = [];
$uploadedFileUri = null;
$uploadedFileName = null;
if (!empty($base64Image)) {
    if ($mimeType === 'application/pdf') {
        // PDFs must be uploaded via the Gemini Files API, then referenced by URI.
        // inline_data only works for image formats.
        $pdfBytes = base64_decode($base64Image, true);
        if ($pdfBytes === false) {
            send_error_response(400, 'Invalid base64 PDF data.', 'INVALID_DATA');
        }

        $uploadUrl = "https://generativelanguage.googleapis.com/upload/v1beta/files";
        $boundary = bin2hex(random_bytes(16));

        // Build multipart/related body: JSON metadata + raw file bytes
        $metadataJson = json_encode(['file' => ['displayName' => 'receipt.pdf']]);
        $multipartBody = "--{$boundary}\r\n"
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . $metadataJson . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: application/pdf\r\n\r\n"
            . $pdfBytes . "\r\n"
            . "--{$boundary}--\r\n";

        $ch = curl_init($uploadUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $multipartBody,
            CURLOPT_HTTPHEADER => [
                "x-goog-api-key: {$geminiKey}",
                "Content-Type: multipart/related; boundary={$boundary}",
                'X-Goog-Upload-Protocol: multipart',
                'Content-Length: ' . strlen($multipartBody),
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $uploadResponse = curl_exec($ch);
        $uploadHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $uploadError = curl_error($ch);

        if ($uploadResponse === false || $uploadHttpCode !== 200) {
            error_log("Gemini Files API upload failed ({$uploadHttpCode}): {$uploadError} - Response: {$uploadResponse}");
            send_error_response(502, 'Failed to upload PDF to AI service.', 'UPSTREAM_ERROR');
        }

        $uploadData = json_decode($uploadResponse, true);
        $uploadedFileUri = $uploadData['file']['uri'] ?? null;
        $uploadedFileName = $uploadData['file']['name'] ?? null;
        if (empty($uploadedFileUri) || empty($uploadedFileName)) {
            error_log('Gemini Files API returned no file URI: ' . $uploadResponse);
            send_error_response(502, 'Failed to process uploaded PDF.', 'UPSTREAM_ERROR');
        }

        // Poll until the file is ACTIVE (Gemini processes uploads asynchronously)
        // Polls up to 15 times at 500ms intervals (~7.5s max). If still PROCESSING after
        // all polls, the file is used as-is (Gemini will reject it if not ready).
        $fileStatusUrl = "https://generativelanguage.googleapis.com/v1beta/{$uploadedFileName}";
        $maxPolls = 15;
        for ($i = 0; $i < $maxPolls; $i++) {
            $ch = curl_init($fileStatusUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["x-goog-api-key: {$geminiKey}"],
                CURLOPT_TIMEOUT => 10,
            ]);
            $statusResponse = curl_exec($ch);

            $statusData = json_decode($statusResponse, true);
            $state = $statusData['state'] ?? '';
            if ($state === 'ACTIVE') {
                break;
            }
            if ($state === 'FAILED') {
                error_log('Gemini file processing failed: ' . $statusResponse);
                send_error_response(502, 'AI service failed to process the PDF.', 'UPSTREAM_ERROR');
            }
            // Still PROCESSING: wait and retry
            usleep(500000); // 500ms
        }

        $userParts[] = [
            'file_data' => [
                'file_uri' => $uploadedFileUri,
                'mime_type' => 'application/pdf',
            ],
        ];
    } else {
        $userParts[] = [
            'inline_data' => [
                'mime_type' => $mimeType,
                'data' => $base64Image,
            ],
        ];
    }
}
if (!empty($userPrompt)) {
    $userParts[] = ['text' => $userPrompt];
}
if (!empty($userParts)) {
    $contents[] = ['role' => 'user', 'parts' => $userParts];
}

$generationConfig = [
    'temperature' => $temperature,
    'maxOutputTokens' => $maxTokens,
    'responseMimeType' => 'application/json',
];

if ($isReceiptExtraction) {
    // Cap thinking to "low": gemini-3.x defaults to dynamic thinking that draws from
    // maxOutputTokens; extraction is a structured OCR task, not a reasoning task.
    $generationConfig['thinkingConfig'] = ['thinkingLevel' => 'low'];

    // Constrain generation to a strict schema. Without it, gemini-3.5-flash intermittently
    // emits invalid JSON even in JSON mode (extra brackets, objects cut off mid-field) with
    // finishReason=STOP - the model thinks it finished but the payload won't parse. A
    // responseSchema forces constrained decoding, so the output is always well-formed JSON
    // matching this shape. Fields are optional/nullable so the "not a receipt" error path and
    // any missing values still work; the app reads every field defensively.
    $numberOrNull = ['type' => 'number', 'nullable' => true];
    $stringOrNull = ['type' => 'string', 'nullable' => true];
    $nameAmountItem = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'amount' => ['type' => 'number'],
        ],
        'propertyOrdering' => ['name', 'amount'],
    ];
    $generationConfig['responseSchema'] = [
        'type' => 'object',
        'properties' => [
            'supplierName' => $stringOrNull,
            'transactionDate' => $stringOrNull,
            'subtotal' => $numberOrNull,
            'taxes' => ['type' => 'array', 'items' => $nameAmountItem, 'nullable' => true],
            'discounts' => ['type' => 'array', 'items' => $nameAmountItem, 'nullable' => true],
            'shipping' => $numberOrNull,
            'totalAmount' => $numberOrNull,
            'currencyCode' => $stringOrNull,
            'paymentMethod' => $stringOrNull,
            'confidence' => $numberOrNull,
            'lineItems' => [
                'type' => 'array',
                'nullable' => true,
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'description' => ['type' => 'string'],
                        'quantity' => ['type' => 'number'],
                        'unitPrice' => ['type' => 'number'],
                        'totalPrice' => ['type' => 'number'],
                        'confidence' => ['type' => 'number'],
                    ],
                    'propertyOrdering' => ['description', 'quantity', 'unitPrice', 'totalPrice', 'confidence'],
                ],
            ],
            'error' => $stringOrNull,
        ],
        'propertyOrdering' => [
            'supplierName', 'transactionDate', 'subtotal', 'taxes', 'discounts', 'shipping',
            'totalAmount', 'currencyCode', 'paymentMethod', 'confidence', 'lineItems', 'error',
        ],
    ];
}

$geminiPayload = [
    'contents' => $contents,
    'generationConfig' => $generationConfig,
];
if ($systemInstruction) {
    $geminiPayload['system_instruction'] = $systemInstruction;
}

$geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

// Server-measured Gemini wall time (isolated from the user's network) feeds the
// timing priors and the response 'timing' block.
$aiTimingStart = microtime(true);

$ch = curl_init($geminiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($geminiPayload),
    CURLOPT_HTTPHEADER => [
        "x-goog-api-key: {$geminiKey}",
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 120,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = curl_exec($ch);
$aiElapsedMs = (int) round((microtime(true) - $aiTimingStart) * 1000);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

if ($response === false) {
    error_log('Gemini proxy cURL error: ' . $curlError);
    send_error_response(502, 'Failed to connect to AI service.', 'UPSTREAM_ERROR');
}

$responseData = json_decode($response, true);

if ($httpCode !== 200) {
    $errorMessage = $responseData['error']['message'] ?? 'Unknown upstream error';
    error_log("Gemini proxy error ({$httpCode}): {$errorMessage}");

    if ($httpCode === 429) {
        send_error_response(429, 'AI service rate limit exceeded. Please try again later.', 'UPSTREAM_RATE_LIMITED');
    }

    send_error_response(502, 'AI service returned an error.', 'UPSTREAM_ERROR');
}

// Clean up uploaded PDF file from Gemini storage
if (!empty($uploadedFileName)) {
    $deleteUrl = "https://generativelanguage.googleapis.com/v1beta/{$uploadedFileName}";
    $ch = curl_init($deleteUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => ["x-goog-api-key: {$geminiKey}"],
        CURLOPT_TIMEOUT => 10,
    ]);
    curl_exec($ch);
}

// Extract content + finish reason from the Gemini response. The gemini-3.x models can
// split the answer across several parts (and emit separate "thought" parts), so read only
// the earlier parts[0] would drop the tail and yield truncated JSON. Concatenate every
// non-thought text part instead.
$candidate = $responseData['candidates'][0] ?? [];
$content = null;
$parts = $candidate['content']['parts'] ?? [];
if (is_array($parts)) {
    $textSegments = [];
    foreach ($parts as $part) {
        if (!empty($part['thought'])) {
            continue; // reasoning trace, not part of the answer
        }
        if (isset($part['text']) && is_string($part['text'])) {
            $textSegments[] = $part['text'];
        }
    }
    if (!empty($textSegments)) {
        $content = implode('', $textSegments);
    }
}
$partCount = is_array($parts) ? count($parts) : 0;
$finishReason = $candidate['finishReason'] ?? null;

$usage = null;
if (isset($responseData['usageMetadata'])) {
    $usage = [
        'prompt_tokens' => $responseData['usageMetadata']['promptTokenCount'] ?? 0,
        'completion_tokens' => $responseData['usageMetadata']['candidatesTokenCount'] ?? 0,
        'total_tokens' => $responseData['usageMetadata']['totalTokenCount'] ?? 0,
    ];
}

// Diagnostic: a finishReason other than STOP (most often MAX_TOKENS) means the
// model stopped before completing, typically leaving truncated or empty JSON,
// which is the likely cause of downstream "JsonReaderException" parse failures.
// gemini-2.5-flash spends hidden "thinking" tokens out of maxOutputTokens, so a
// small token budget can be exhausted before the JSON answer is written. Logged
// only (response behaviour is unchanged) so truncation shows up in the PHP
// error log. Grep the error log for "[gemini]" to find these.
if ($finishReason !== null && $finishReason !== 'STOP') {
    error_log(sprintf(
        '[gemini] non-STOP finishReason=%s model=%s maxOutputTokens=%d tokens(prompt/out/total)=%d/%d/%d content=%s',
        $finishReason,
        $model,
        $maxTokens,
        $usage['prompt_tokens'] ?? 0,
        $usage['completion_tokens'] ?? 0,
        $usage['total_tokens'] ?? 0,
        $content === null ? 'null' : strlen($content) . ' chars'
    ));
}

if ($content === null) {
    send_error_response(502, 'Invalid response from AI service.', 'UPSTREAM_ERROR');
}

// TEMPORARY DIAGNOSTIC: for receipt extraction, verify the JSON parses server-side. When it
// doesn't, surface the real cause (finishReason, token usage, part count, and the tail of the
// content where it cut off) as a normal {"error": ...} 200 response, which is the only shape the
// desktop app displays verbatim. This replaces the cryptic client-side "truncated JSON" parse
// error with something we can actually read, and confirms whether this deploy is even live.
if ($isReceiptExtraction) {
    json_decode($content);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $diag = sprintf(
            'DIAG truncated: finishReason=%s model=%s budget=%d tokens(p/o/t)=%d/%d/%d parts=%d len=%d err=%s tail=[%s]',
            $finishReason ?? 'null',
            $model,
            $maxTokens,
            $usage['prompt_tokens'] ?? 0,
            $usage['completion_tokens'] ?? 0,
            $usage['total_tokens'] ?? 0,
            $partCount,
            strlen($content),
            json_last_error_msg(),
            substr($content, -140)
        );
        error_log('[gemini] ' . $diag);
        $content = json_encode(['error' => $diag]);
    }
}

// Record the server-measured timing (best-effort; never breaks the response).
ai_timing_record([
    'operation' => $operation,
    'model' => $model,
    'size_feature' => $sizeFeature,
    'input_bytes' => !empty($base64Image) ? (int) (strlen($base64Image) * 0.75) : strlen($userPrompt),
    'mime' => !empty($base64Image) ? $mimeType : null,
    'prompt_tokens' => $usage['prompt_tokens'] ?? null,
    'output_tokens' => $usage['completion_tokens'] ?? null,
    'max_output_tokens' => $maxTokens,
    'finish_reason' => $finishReason,
    'elapsed_ms' => $aiElapsedMs,
    'success' => true,
    'app_platform' => $appPlatform,
]);

send_json_response(200, [
    'success' => true,
    'content' => $content,
    'model' => $model,
    'usage' => $usage,
    'finishReason' => $finishReason,
    'timing' => [
        'elapsed_ms' => $aiElapsedMs,
        'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
        'output_tokens' => $usage['completion_tokens'] ?? 0,
        'load_factor' => ai_timing_load_factor(),
    ],
    'timestamp' => date('c'),
]);
