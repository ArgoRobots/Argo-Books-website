<?php
/**
 * Invoice Email API Endpoint
 *
 * This endpoint handles sending invoice emails from the Argo Books application.
 *
 * @author Argo Books
 * @version 1.0.0
 */

// Enable error reporting for development (disable in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');

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
        'messageId' => null
    ]);
    exit;
}

// Load configuration
require_once __DIR__ . '/config.php';

// Verify API key
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if (empty($apiKey) || $apiKey !== INVOICE_API_KEY) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing API key.',
        'messageId' => null
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
        'messageId' => null
    ]);
    exit;
}

// Validate required fields
$requiredFields = ['to', 'from', 'subject', 'htmlBody'];
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
        'messageId' => null
    ]);
    exit;
}

// Validate email addresses
if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid recipient email address.',
        'messageId' => null
    ]);
    exit;
}

if (!filter_var($data['from'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid sender email address.',
        'messageId' => null
    ]);
    exit;
}

// Include the email sender
require_once __DIR__ . '/InvoiceEmailSender.php';

try {
    $sender = new InvoiceEmailSender();
    $result = $sender->send($data);

    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }

    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'messageId' => null
    ]);
}
