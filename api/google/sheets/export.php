<?php
/**
 * Google Sheets Export Proxy Endpoint
 *
 * POST /api/google/sheets/export - Create Google Spreadsheet with data and charts
 *
 * Receives spreadsheet data from the Argo Books app, creates a Google Sheet
 * using the company's stored OAuth tokens, and returns the spreadsheet URL.
 */

require_once __DIR__ . '/../../portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

// Rate limiting: 30 exports per 15 minutes
$ip = get_client_ip();
if (is_rate_limited($ip, 30, 900, 'sheets_' . $company['id'])) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($ip, 'sheets_' . $company['id']);

// Get Google tokens for this company
$db = get_db_connection();
$stmt = $db->prepare(
    'SELECT google_access_token, google_refresh_token, google_token_expires
     FROM portal_companies WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $company['id']);
$stmt->execute();
$tokenRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (empty($tokenRow['google_refresh_token'])) {
    $db->close();
    send_error_response(403, 'Google Sheets not connected. Please authorize via Settings.', 'NOT_AUTHENTICATED');
}

// Decrypt tokens
$accessToken = portal_decrypt($tokenRow['google_access_token']);
$refreshToken = portal_decrypt($tokenRow['google_refresh_token']);
$tokenExpires = $tokenRow['google_token_expires'];

// Refresh token if expired
if (empty($accessToken) || (!empty($tokenExpires) && strtotime($tokenExpires) <= time())) {
    $accessToken = refreshGoogleToken($refreshToken, $company['id'], $db);
    if (!$accessToken) {
        $db->close();
        send_error_response(403, 'Google authorization expired. Please re-authorize via Settings.', 'TOKEN_EXPIRED');
    }
}
$db->close();

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

if (empty($data['title'])) {
    send_error_response(400, 'Missing required field: title', 'MISSING_FIELDS');
}

$title = $data['title'];
$sheets = $data['sheets'] ?? [];
$chartConfig = $data['chartConfig'] ?? null;
$shareAsReader = $data['shareAsReader'] ?? false;

if (empty($sheets)) {
    send_error_response(400, 'At least one sheet is required.', 'MISSING_FIELDS');
}

// Step 1: Create spreadsheet
$spreadsheet = [
    'properties' => ['title' => $title],
    'sheets' => [],
];

foreach ($sheets as $i => $sheet) {
    $spreadsheet['sheets'][] = [
        'properties' => [
            'title' => $sheet['name'] ?? 'Sheet' . ($i + 1),
            'sheetId' => $i,
        ],
    ];
}

$createResponse = googleApiRequest($accessToken, 'POST', 'https://sheets.googleapis.com/v4/spreadsheets', $spreadsheet);
if (!$createResponse || !isset($createResponse['spreadsheetId'])) {
    send_error_response(502, 'Failed to create spreadsheet.', 'UPSTREAM_ERROR');
}

$spreadsheetId = $createResponse['spreadsheetId'];
$spreadsheetUrl = $createResponse['spreadsheetUrl'] ?? "https://docs.google.com/spreadsheets/d/{$spreadsheetId}";

// Step 2: Write data to each sheet
foreach ($sheets as $i => $sheet) {
    $sheetName = $sheet['name'] ?? 'Sheet' . ($i + 1);
    $headers = $sheet['headers'] ?? [];
    $rows = $sheet['rows'] ?? [];

    $values = [];
    if (!empty($headers)) {
        $values[] = $headers;
    }
    foreach ($rows as $row) {
        $values[] = $row;
    }

    if (!empty($values)) {
        $range = urlencode($sheetName) . '!A1';
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?valueInputOption=RAW";
        googleApiRequest($accessToken, 'PUT', $url, ['values' => $values]);
    }
}

// Step 3: Create chart if configured
if ($chartConfig && !empty($sheets)) {
    $chartType = mapChartType($chartConfig['type'] ?? 'column');
    $sheetId = 0;
    $firstSheet = $sheets[0];
    $numRows = count($firstSheet['rows'] ?? []) + 1; // +1 for header
    $numCols = count($firstSheet['headers'] ?? []);

    if ($numRows > 1 && $numCols >= 2) {
        $chartRequest = [
            'requests' => [[
                'addChart' => [
                    'chart' => [
                        'spec' => [
                            'title' => $chartConfig['title'] ?? $title,
                            'basicChart' => [
                                'chartType' => $chartType,
                                'legendPosition' => 'BOTTOM_LEGEND',
                                'domains' => [[
                                    'domain' => [
                                        'sourceRange' => [
                                            'sources' => [[
                                                'sheetId' => $sheetId,
                                                'startRowIndex' => 0,
                                                'endRowIndex' => $numRows,
                                                'startColumnIndex' => 0,
                                                'endColumnIndex' => 1,
                                            ]],
                                        ],
                                    ],
                                ]],
                                'series' => array_map(function ($colIdx) use ($sheetId, $numRows) {
                                    return [
                                        'series' => [
                                            'sourceRange' => [
                                                'sources' => [[
                                                    'sheetId' => $sheetId,
                                                    'startRowIndex' => 0,
                                                    'endRowIndex' => $numRows,
                                                    'startColumnIndex' => $colIdx,
                                                    'endColumnIndex' => $colIdx + 1,
                                                ]],
                                            ],
                                        ],
                                    ];
                                }, range(1, $numCols - 1)),
                                'headerCount' => 1,
                            ],
                        ],
                        'position' => [
                            'newSheet' => true,
                        ],
                    ],
                ],
            ]],
        ];

        googleApiRequest($accessToken, 'POST',
            "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}:batchUpdate",
            $chartRequest
        );
    }
}

// Step 4: Share if requested
if ($shareAsReader) {
    $permission = [
        'type' => 'anyone',
        'role' => 'reader',
    ];
    googleApiRequest($accessToken, 'POST',
        "https://www.googleapis.com/drive/v3/files/{$spreadsheetId}/permissions",
        $permission
    );
}

send_json_response(200, [
    'success' => true,
    'spreadsheetUrl' => $spreadsheetUrl,
    'spreadsheetId' => $spreadsheetId,
    'timestamp' => date('c'),
]);

// --- Helper Functions ---

/**
 * Make an authenticated Google API request.
 */
function googleApiRequest(string $accessToken, string $method, string $url, ?array $body = null): ?array
{
    $ch = curl_init($url);
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];

    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
    ];

    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
    } elseif ($method === 'PUT') {
        $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
    }

    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        error_log("Google API error ({$method} {$url}): HTTP {$httpCode} - " . substr($response ?: '', 0, 500));
        return null;
    }

    return json_decode($response, true);
}

/**
 * Refresh an expired Google access token using the refresh token.
 */
function refreshGoogleToken(string $refreshToken, int $companyId, $db): ?string
{
    $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
    $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';

    $payload = http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
    ]);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        error_log('Google token refresh failed: ' . ($response ?: 'curl error'));
        return null;
    }

    $tokenData = json_decode($response, true);
    $newAccessToken = $tokenData['access_token'] ?? '';
    $expiresIn = $tokenData['expires_in'] ?? 3600;

    if (empty($newAccessToken)) {
        return null;
    }

    // Update stored token
    $encrypted = portal_encrypt($newAccessToken);
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

    $stmt = $db->prepare(
        'UPDATE portal_companies SET google_access_token = ?, google_token_expires = ? WHERE id = ?'
    );
    $stmt->bind_param('ssi', $encrypted, $expiresAt, $companyId);
    $stmt->execute();
    $stmt->close();

    return $newAccessToken;
}

/**
 * Map app chart type names to Google Sheets chart types.
 */
function mapChartType(string $type): string
{
    return match (strtolower($type)) {
        'line', 'spline' => 'LINE',
        'column', 'bar' => 'COLUMN',
        'pie' => 'PIE',
        'area' => 'AREA',
        'scatter', 'stepline' => 'SCATTER',
        default => 'COLUMN',
    };
}
