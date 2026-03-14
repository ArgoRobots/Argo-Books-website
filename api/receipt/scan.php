<?php
/**
 * Receipt Scan Proxy Endpoint
 *
 * POST /api/receipt/scan - Proxy Azure Document Intelligence receipt scanning
 *
 * Receives receipt images from the Argo Books app, forwards them to Azure
 * Document Intelligence, parses the result, and returns structured receipt data.
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

// Rate limiting: 30 scans per 15 minutes per company
$ip = get_client_ip();
if (is_rate_limited($ip, 30, 900, 'receipt_' . $company['id'])) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($ip, 'receipt_' . $company['id']);

// Validate server configuration
$azureEndpoint = $_ENV['AZURE_DOCUMENT_INTELLIGENCE_ENDPOINT'] ?? '';
$azureKey = $_ENV['AZURE_DOCUMENT_INTELLIGENCE_API_KEY'] ?? '';
if (empty($azureEndpoint) || empty($azureKey)) {
    send_error_response(500, 'Receipt scanning service not configured on server.', 'CONFIG_ERROR');
}

// Validate uploaded file
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = $_FILES['image']['error'] ?? 'no_file';
    send_error_response(400, 'No image file uploaded or upload error.', 'UPLOAD_ERROR');
}

$file = $_FILES['image'];
$maxFileSize = 4 * 1024 * 1024; // 4MB

if ($file['size'] > $maxFileSize) {
    send_error_response(413, 'File too large. Maximum size is 4MB.', 'FILE_TOO_LARGE');
}

if ($file['size'] === 0) {
    send_error_response(400, 'Empty file uploaded.', 'EMPTY_FILE');
}

// Validate MIME type
$allowedTypes = [
    'image/jpeg' => 'jpeg',
    'image/png' => 'png',
    'application/pdf' => 'pdf',
    'image/bmp' => 'bmp',
    'image/tiff' => 'tiff',
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowedTypes[$mimeType])) {
    send_error_response(400, 'Unsupported file type. Allowed: JPEG, PNG, PDF, BMP, TIFF.', 'INVALID_FILE_TYPE');
}

// Read file content
$imageData = file_get_contents($file['tmp_name']);
if ($imageData === false) {
    send_error_response(500, 'Failed to read uploaded file.', 'READ_ERROR');
}

// Normalize Azure endpoint
$azureEndpoint = rtrim($azureEndpoint, '/');
if (!str_starts_with($azureEndpoint, 'https://')) {
    $azureEndpoint = 'https://' . $azureEndpoint;
}

// Submit to Azure Document Intelligence
$analyzeUrl = $azureEndpoint . '/documentintelligence/documentModels/prebuilt-receipt:analyze?api-version=2024-11-30';

$ch = curl_init($analyzeUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $imageData,
    CURLOPT_HTTPHEADER => [
        'Content-Type: ' . $mimeType,
        'Ocp-Apim-Subscription-Key: ' . $azureKey,
    ],
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    error_log('Azure receipt scan cURL error: ' . $curlError);
    send_error_response(502, 'Failed to connect to receipt scanning service.', 'UPSTREAM_ERROR');
}

if ($httpCode !== 202) {
    $body = substr($response, $headerSize);
    error_log("Azure receipt scan error ({$httpCode}): " . substr($body, 0, 500));
    send_error_response(502, 'Receipt scanning service returned an error.', 'UPSTREAM_ERROR');
}

// Extract Operation-Location header for polling
$headers = substr($response, 0, $headerSize);
if (!preg_match('/Operation-Location:\s*(.+)/i', $headers, $matches)) {
    error_log('Azure receipt scan: No Operation-Location header');
    send_error_response(502, 'Invalid response from receipt scanning service.', 'UPSTREAM_ERROR');
}

$operationUrl = trim($matches[1]);

// Poll for result (max 60 seconds, check every 2 seconds)
$maxAttempts = 30;
$result = null;

for ($i = 0; $i < $maxAttempts; $i++) {
    usleep(2000000); // 2 seconds

    $ch = curl_init($operationUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Ocp-Apim-Subscription-Key: ' . $azureKey,
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $pollResponse = curl_exec($ch);
    $pollHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $pollError = curl_error($ch);
    curl_close($ch);

    if ($pollResponse === false || $pollHttpCode !== 200) {
        error_log("Azure poll attempt {$attempt}: HTTP {$pollHttpCode}, curl error: {$pollError}");
        continue;
    }

    $pollData = json_decode($pollResponse, true);
    $status = $pollData['status'] ?? '';

    if ($status === 'succeeded') {
        $result = $pollData;
        break;
    } elseif ($status === 'failed') {
        error_log('Azure receipt scan failed: ' . json_encode($pollData['error'] ?? []));
        send_error_response(502, 'Receipt scanning failed.', 'SCAN_FAILED');
    }
    // 'running' or 'notStarted' — keep polling
}

if ($result === null) {
    send_error_response(504, 'Receipt scanning timed out.', 'TIMEOUT');
}

// Parse Azure result into app-compatible format
$scanResult = parseAzureReceiptResult($result);

send_json_response(200, $scanResult);

/**
 * Parse Azure Document Intelligence receipt result into app-compatible format.
 */
function parseAzureReceiptResult(array $result): array
{
    $output = [
        'success' => true,
        'supplierName' => null,
        'transactionDate' => null,
        'subtotal' => null,
        'total' => null,
        'tax' => null,
        'currency' => null,
        'confidence' => null,
        'lineItems' => [],
        'rawText' => $result['analyzeResult']['content'] ?? '',
        'timestamp' => date('c'),
    ];

    $documents = $result['analyzeResult']['documents'] ?? [];
    if (empty($documents)) {
        return $output;
    }

    $doc = $documents[0];
    $fields = $doc['fields'] ?? [];
    $confidenceScores = [];

    // Document-level confidence
    if (isset($doc['confidence'])) {
        $confidenceScores[] = $doc['confidence'];
    }

    // Extract fields
    if (isset($fields['MerchantName'])) {
        $output['supplierName'] = $fields['MerchantName']['valueString']
            ?? $fields['MerchantName']['content'] ?? null;
        if (isset($fields['MerchantName']['confidence'])) {
            $confidenceScores[] = $fields['MerchantName']['confidence'];
        }
    }

    if (isset($fields['TransactionDate'])) {
        $output['transactionDate'] = $fields['TransactionDate']['valueDate'] ?? null;
        if (isset($fields['TransactionDate']['confidence'])) {
            $confidenceScores[] = $fields['TransactionDate']['confidence'];
        }
    }

    if (isset($fields['Subtotal'])) {
        $output['subtotal'] = extractCurrencyValue($fields['Subtotal'], $output);
        if (isset($fields['Subtotal']['confidence'])) {
            $confidenceScores[] = $fields['Subtotal']['confidence'];
        }
    }

    if (isset($fields['Total'])) {
        $output['total'] = extractCurrencyValue($fields['Total'], $output);
        if (isset($fields['Total']['confidence'])) {
            $confidenceScores[] = $fields['Total']['confidence'];
        }
    }

    // Tax can be 'Tax' or 'TotalTax'
    $taxField = $fields['TotalTax'] ?? $fields['Tax'] ?? null;
    if ($taxField !== null) {
        $output['tax'] = extractCurrencyValue($taxField, $output);
        if (isset($taxField['confidence'])) {
            $confidenceScores[] = $taxField['confidence'];
        }
    }

    // Line items
    if (isset($fields['Items']['valueArray'])) {
        foreach ($fields['Items']['valueArray'] as $item) {
            $itemFields = $item['valueObject'] ?? [];
            $lineItem = parseLineItem($itemFields);
            if ($lineItem !== null) {
                $output['lineItems'][] = $lineItem;
            }
        }
    }

    // Auto-calculate missing subtotal
    if ($output['subtotal'] === null && $output['total'] !== null && $output['tax'] !== null) {
        $output['subtotal'] = round($output['total'] - $output['tax'], 2);
    }

    // Average confidence
    if (!empty($confidenceScores)) {
        $output['confidence'] = round(array_sum($confidenceScores) / count($confidenceScores), 4);
    }

    return $output;
}

/**
 * Extract a currency value from an Azure field.
 */
function extractCurrencyValue(array $field, array &$output): ?float
{
    if (isset($field['valueCurrency'])) {
        $val = $field['valueCurrency']['amount'] ?? null;
        if ($output['currency'] === null && isset($field['valueCurrency']['currencyCode'])) {
            $output['currency'] = $field['valueCurrency']['currencyCode'];
        }
        return $val !== null ? (float)$val : null;
    }
    if (isset($field['valueNumber'])) {
        return (float)$field['valueNumber'];
    }
    // Try parsing from content string
    if (isset($field['content'])) {
        $cleaned = preg_replace('/[^0-9.]/', '', $field['content']);
        if (is_numeric($cleaned)) {
            return (float)$cleaned;
        }
    }
    return null;
}

/**
 * Parse a single line item from Azure receipt result.
 */
function parseLineItem(array $fields): ?array
{
    $item = [
        'description' => null,
        'quantity' => null,
        'unitPrice' => null,
        'totalPrice' => null,
        'confidence' => null,
    ];

    $confidenceScores = [];
    $hasData = false;

    if (isset($fields['Description'])) {
        $item['description'] = $fields['Description']['valueString']
            ?? $fields['Description']['content'] ?? null;
        if ($item['description'] !== null) $hasData = true;
        if (isset($fields['Description']['confidence'])) {
            $confidenceScores[] = $fields['Description']['confidence'];
        }
    }

    if (isset($fields['Quantity'])) {
        $item['quantity'] = isset($fields['Quantity']['valueNumber'])
            ? (float)$fields['Quantity']['valueNumber'] : null;
        if ($item['quantity'] !== null) $hasData = true;
        if (isset($fields['Quantity']['confidence'])) {
            $confidenceScores[] = $fields['Quantity']['confidence'];
        }
    }

    // Price can be 'Price' or 'UnitPrice'
    $priceField = $fields['Price'] ?? $fields['UnitPrice'] ?? null;
    if ($priceField !== null) {
        $dummyOutput = ['currency' => null];
        $item['unitPrice'] = extractCurrencyValue($priceField, $dummyOutput);
        if ($item['unitPrice'] !== null) $hasData = true;
        if (isset($priceField['confidence'])) {
            $confidenceScores[] = $priceField['confidence'];
        }
    }

    // TotalPrice can be 'TotalPrice' or 'Amount'
    $totalField = $fields['TotalPrice'] ?? $fields['Amount'] ?? null;
    if ($totalField !== null) {
        $dummyOutput = ['currency' => null];
        $item['totalPrice'] = extractCurrencyValue($totalField, $dummyOutput);
        if ($item['totalPrice'] !== null) $hasData = true;
        if (isset($totalField['confidence'])) {
            $confidenceScores[] = $totalField['confidence'];
        }
    }

    // Auto-calculate missing values
    if ($item['totalPrice'] === null && $item['quantity'] !== null && $item['unitPrice'] !== null) {
        $item['totalPrice'] = round($item['quantity'] * $item['unitPrice'], 2);
    }
    if ($item['unitPrice'] === null && $item['totalPrice'] !== null && $item['quantity'] !== null && $item['quantity'] > 0) {
        $item['unitPrice'] = round($item['totalPrice'] / $item['quantity'], 2);
    }

    if (!$hasData) {
        return null;
    }

    if (!empty($confidenceScores)) {
        $item['confidence'] = round(array_sum($confidenceScores) / count($confidenceScores), 4);
    }

    return $item;
}
