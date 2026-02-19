<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../config/pricing.php';

$pricing = get_pricing_config();
$monthlyPrice = $pricing['premium_monthly_price'];
$currency = $pricing['currency'];

echo json_encode([
    'premium' => [
        'price_display' => '$' . number_format($monthlyPrice, 0) . ' ' . $currency,
        'billing_period' => '/month',
    ],
]);
