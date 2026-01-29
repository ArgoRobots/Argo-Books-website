<?php
/**
 * Invoice Email API Endpoint
 *
 * Handles sending invoice emails from the Argo Books application.
 * Configuration is loaded from environment variables.
 */

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.',
        'messageId' => null,
        'timestamp' => date('c')
    ]);
    exit;
}

// Get API key from environment
$configuredApiKey = $_ENV['INVOICE_EMAIL_API_KEY'] ?? getenv('INVOICE_EMAIL_API_KEY') ?? '';

if (empty($configuredApiKey)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error: API key not configured.',
        'messageId' => null,
        'errorCode' => 'CONFIG_ERROR',
        'timestamp' => date('c')
    ]);
    exit;
}

// Verify API key (support both X-Api-Key and Authorization: Bearer headers)
$providedApiKey = '';
if (!empty($_SERVER['HTTP_X_API_KEY'])) {
    $providedApiKey = $_SERVER['HTTP_X_API_KEY'];
} elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
        $providedApiKey = $matches[1];
    }
}

if (empty($providedApiKey) || !hash_equals($configuredApiKey, $providedApiKey)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing API key.',
        'messageId' => null,
        'errorCode' => 'UNAUTHORIZED',
        'timestamp' => date('c')
    ]);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON input: ' . json_last_error_msg(),
        'messageId' => null,
        'errorCode' => 'INVALID_JSON',
        'timestamp' => date('c')
    ]);
    exit;
}

// Validate required fields (matching Argo Books client field names)
$requiredFields = ['to', 'from', 'subject', 'html'];
$missingFields = [];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missingFields),
        'messageId' => null,
        'errorCode' => 'MISSING_FIELDS',
        'timestamp' => date('c')
    ]);
    exit;
}

// Validate email addresses
if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid recipient email address.',
        'messageId' => null,
        'errorCode' => 'INVALID_EMAIL',
        'timestamp' => date('c')
    ]);
    exit;
}

if (!filter_var($data['from'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid sender email address.',
        'messageId' => null,
        'errorCode' => 'INVALID_EMAIL',
        'timestamp' => date('c')
    ]);
    exit;
}

// Validate optional email fields
if (!empty($data['replyTo']) && !filter_var($data['replyTo'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid reply-to email address.',
        'messageId' => null,
        'errorCode' => 'INVALID_EMAIL',
        'timestamp' => date('c')
    ]);
    exit;
}

if (!empty($data['bcc']) && !filter_var($data['bcc'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid BCC email address.',
        'messageId' => null,
        'errorCode' => 'INVALID_EMAIL',
        'timestamp' => date('c')
    ]);
    exit;
}

// Include the email sender
require_once __DIR__ . '/InvoiceEmailSender.php';

try {
    $sender = new InvoiceEmailSender();
    $result = $sender->send($data);

    http_response_code($result['success'] ? 200 : 500);
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'messageId' => null,
        'errorCode' => 'SERVER_ERROR',
        'timestamp' => date('c')
    ]);
}
