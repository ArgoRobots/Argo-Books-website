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
 *   AI_IMPORT_MONTHLY_LIMIT     - Monthly AI import limit for premium tier (default: 10)
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
        'ai_import_monthly_limit'    => _pricing_parse_int_env('AI_IMPORT_MONTHLY_LIMIT', 10),
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
 * Convert a dollar amount to cents safely (avoids floating-point precision issues).
 *
 * @param float $amount Dollar amount
 * @return int Amount in cents
 */
function price_to_cents($amount) {
    return (int) round($amount * 100);
}
