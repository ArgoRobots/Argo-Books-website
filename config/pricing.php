<?php
/**
 * Centralized Pricing Configuration
 *
 * All prices are in CAD. Override any price via environment variables.
 * Default values match current production prices so the system works
 * identically without any env vars set.
 *
 * Environment variables:
 *   PREMIUM_MONTHLY_PRICE       - Premium monthly subscription (default: 10.00)
 *   PREMIUM_YEARLY_PRICE        - Premium yearly subscription (default: 100.00)
 *   PROCESSING_FEE_PERCENT      - Payment processing fee percentage (default: 2.90)
 *   PROCESSING_FEE_FIXED        - Payment processing fixed fee in CAD (default: 0.30)
 *   RECEIPT_SCAN_MONTHLY_LIMIT  - Monthly receipt scan limit for premium tier (default: 500)
 *   AI_IMPORT_MONTHLY_LIMIT     - Monthly AI import limit for all users (default: 100)
 *   FREE_INVOICE_MONTHLY_LIMIT  - Monthly invoice send limit for free tier (default: 5)
 */

/**
 * Get the pricing configuration array.
 * Uses static caching so env vars are only parsed once per request.
 *
 * @return array Pricing configuration with keys:
 *   - premium_monthly_price (float)
 *   - premium_yearly_price (float)
 *   - processing_fee_percent (float)
 *   - processing_fee_fixed (float)
 *   - currency (string)
 */
function get_pricing_config() {
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [
        'premium_monthly_price' => _pricing_parse_env('PREMIUM_MONTHLY_PRICE', 10.00),
        'premium_yearly_price'  => _pricing_parse_env('PREMIUM_YEARLY_PRICE', 100.00),
        'processing_fee_percent' => _pricing_parse_env('PROCESSING_FEE_PERCENT', 2.90),
        'processing_fee_fixed'   => _pricing_parse_env('PROCESSING_FEE_FIXED', 0.30),
        'receipt_scan_monthly_limit' => _pricing_parse_int_env('RECEIPT_SCAN_MONTHLY_LIMIT', 500),
        'ai_import_monthly_limit'    => _pricing_parse_int_env('AI_IMPORT_MONTHLY_LIMIT', 100),
        'free_invoice_monthly_limit' => _pricing_parse_int_env('FREE_INVOICE_MONTHLY_LIMIT', 5),
        'currency'              => 'CAD',
    ];

    return $config;
}

/**
 * Parse a price value from an environment variable.
 * Returns the default if the env var is missing, empty, non-numeric, or negative.
 *
 * @param string $key     Environment variable name
 * @param float  $default Default value if env var is invalid
 * @return float Validated price value rounded to 2 decimal places
 */
function _pricing_parse_env($key, $default) {
    if (!isset($_ENV[$key]) || $_ENV[$key] === '' || !is_numeric($_ENV[$key])) {
        return $default;
    }

    $value = round(floatval($_ENV[$key]), 2);

    if ($value < 0) {
        return $default;
    }

    return $value;
}

/**
 * Parse an integer value from an environment variable.
 * Returns the default if the env var is missing, empty, non-numeric, or less than 1.
 *
 * @param string $key     Environment variable name
 * @param int    $default Default value if env var is invalid
 * @return int Validated integer value
 */
function _pricing_parse_int_env($key, $default) {
    if (!isset($_ENV[$key]) || $_ENV[$key] === '' || !is_numeric($_ENV[$key])) {
        return $default;
    }

    $value = intval($_ENV[$key]);

    if ($value < 1) {
        return $default;
    }

    return $value;
}

/**
 * Calculate the processing fee for a given subtotal.
 * Returns 0 if subtotal is zero or negative (e.g. credit-covered payments).
 *
 * @param float $subtotal The pre-fee charge amount
 * @return float Fee amount rounded to 2 decimal places
 */
function calculate_processing_fee($subtotal) {
    if ($subtotal <= 0) {
        return 0.00;
    }
    $config = get_pricing_config();
    return round(($subtotal * $config['processing_fee_percent'] / 100) + $config['processing_fee_fixed'], 2);
}

/**
 * Calculate the processing fee for an invoice payment, with currency conversion
 * for the fixed fee portion.
 *
 * The percentage portion (2.90%) is currency-agnostic. The fixed portion ($0.30 CAD)
 * is converted to the invoice's currency using exchange rates from the database.
 *
 * @param float  $amount   The payment amount
 * @param string $currency The invoice currency code (e.g., "USD", "CAD", "EUR")
 * @return float Fee amount rounded to 2 decimal places
 */
function calculate_invoice_processing_fee($amount, $currency = 'CAD') {
    if ($amount <= 0) {
        return 0.00;
    }

    $config = get_pricing_config();
    $percentFee = $amount * $config['processing_fee_percent'] / 100;
    $fixedFeeCAD = $config['processing_fee_fixed'];

    // If invoice is in CAD, no conversion needed
    $currency = strtoupper($currency);
    if ($currency === 'CAD') {
        return round($percentFee + $fixedFeeCAD, 2);
    }

    // Convert the fixed fee from CAD to the invoice currency using exchange rates
    $fixedFeeConverted = _convert_fixed_fee_to_currency($fixedFeeCAD, $currency);

    return round($percentFee + $fixedFeeConverted, 2);
}

/**
 * Convert a CAD amount to the target currency using exchange rates from the database.
 * Rates in the exchange_rates table are relative to USD.
 *
 * @param float  $amountCAD The amount in CAD
 * @param string $currency  Target currency code
 * @return float Converted amount (falls back to original amount if no rates available)
 */
function _convert_fixed_fee_to_currency($amountCAD, $currency) {
    try {
        $db = get_db_connection();
        $stmt = $db->prepare(
            'SELECT rates FROM exchange_rates WHERE rate_date <= CURDATE() ORDER BY rate_date DESC LIMIT 1'
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        if (!$row || empty($row['rates'])) {
            return $amountCAD; // Fallback: use same numeric value
        }

        $rates = json_decode($row['rates'], true);
        if (!$rates) {
            return $amountCAD;
        }

        $cadRate = $rates['CAD'] ?? null;
        $targetRate = $rates[$currency] ?? null;

        if (!$cadRate || $cadRate <= 0 || !$targetRate || $targetRate <= 0) {
            return $amountCAD;
        }

        // Convert: CAD -> USD -> target currency
        // $amountCAD / cadRate = amount in USD; * targetRate = amount in target currency
        return round($amountCAD / $cadRate * $targetRate, 4);
    } catch (\Throwable $e) {
        error_log('Fee currency conversion failed: ' . $e->getMessage());
        return $amountCAD; // Fallback: use same numeric value
    }
}

