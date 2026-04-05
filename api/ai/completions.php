<?php
/**
 * AI Completions Proxy Endpoint
 *
 * POST /api/ai/completions - Proxy AI chat completion requests
 *
 * Receives prompts from the Argo Books app, forwards them to the appropriate
 * AI provider (Gemini or OpenAI), and returns the response.
 * All API keys are stored server-side.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate using license key
$license = authenticate_license_request();
if (!$license) {
    send_error_response(401, 'Invalid or missing license key.', 'UNAUTHORIZED');
}

// Rate limiting: 60 requests per 15 minutes per license (global per license, not per IP)
$rateLimitId = substr($license['license_key_hash'], 0, 16);
$rateLimitKey = 'ai_license';
if (is_rate_limited($rateLimitId, 60, 900, $rateLimitKey)) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($rateLimitId, $rateLimitKey);

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
$maxTokens = max(1, min((int)($data['maxTokens'] ?? 4000), 16000)); // Clamp 1-16k
$temperature = max(0, min(2, (float)($data['temperature'] ?? 0.1)));
$base64Image = $data['base64Image'] ?? null;
$mimeType = $data['mimeType'] ?? 'image/jpeg';

// Determine provider based on model name
$geminiModels = ['gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.0-flash'];
$openaiModels = ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo', 'gpt-3.5-turbo'];
$isGemini = in_array($requestedModel, $geminiModels, true);

if ($isGemini) {
    // --- Gemini API path ---
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

            $uploadUrl = "https://generativelanguage.googleapis.com/upload/v1beta/files?key={$geminiKey}";
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
            curl_close($ch);

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
            $fileStatusUrl = "https://generativelanguage.googleapis.com/v1beta/{$uploadedFileName}?key={$geminiKey}";
            $maxPolls = 15;
            for ($i = 0; $i < $maxPolls; $i++) {
                $ch = curl_init($fileStatusUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                ]);
                $statusResponse = curl_exec($ch);
                curl_close($ch);

                $statusData = json_decode($statusResponse, true);
                $state = $statusData['state'] ?? '';
                if ($state === 'ACTIVE') {
                    break;
                }
                if ($state === 'FAILED') {
                    error_log('Gemini file processing failed: ' . $statusResponse);
                    send_error_response(502, 'AI service failed to process the PDF.', 'UPSTREAM_ERROR');
                }
                // Still PROCESSING — wait and retry
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

    $geminiPayload = [
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => $temperature,
            'maxOutputTokens' => $maxTokens,
            'responseMimeType' => 'application/json',
        ],
    ];
    if ($systemInstruction) {
        $geminiPayload['system_instruction'] = $systemInstruction;
    }

    $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}";

    $ch = curl_init($geminiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($geminiPayload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 120,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

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
        $deleteUrl = "https://generativelanguage.googleapis.com/v1beta/{$uploadedFileName}?key={$geminiKey}";
        $ch = curl_init($deleteUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_TIMEOUT => 10,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    // Extract content from Gemini response
    $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
    if ($content === null) {
        send_error_response(502, 'Invalid response from AI service.', 'UPSTREAM_ERROR');
    }

    $usage = null;
    if (isset($responseData['usageMetadata'])) {
        $usage = [
            'prompt_tokens' => $responseData['usageMetadata']['promptTokenCount'] ?? 0,
            'completion_tokens' => $responseData['usageMetadata']['candidatesTokenCount'] ?? 0,
            'total_tokens' => $responseData['usageMetadata']['totalTokenCount'] ?? 0,
        ];
    }

    send_json_response(200, [
        'success' => true,
        'content' => $content,
        'model' => $model,
        'usage' => $usage,
        'timestamp' => date('c'),
    ]);

} else {
    // --- OpenAI API path ---
    $openaiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    if (empty($openaiKey)) {
        send_error_response(500, 'AI service not configured on server.', 'CONFIG_ERROR');
    }

    $defaultModel = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';
    $model = in_array($requestedModel, $openaiModels, true) ? $requestedModel : $defaultModel;

    $messages = [];
    if (!empty($systemPrompt)) {
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
    }
    if (!empty($userPrompt)) {
        if (!empty($base64Image)) {
            // Vision request: include image in user message
            $messages[] = [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $userPrompt],
                    ['type' => 'image_url', 'image_url' => [
                        'url' => "data:{$mimeType};base64,{$base64Image}",
                        'detail' => 'high',
                    ]],
                ],
            ];
        } else {
            $messages[] = ['role' => 'user', 'content' => $userPrompt];
        }
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
}
