<?php
declare(strict_types=1);

/**
 * Verify a Square webhook HMAC-SHA256 signature.
 *
 * Square signs the concatenation of the notification URL and the raw request
 * body using the configured signature key. The signature header is the
 * base64-encoded HMAC-SHA256 of that string.
 *
 * Returns false (rather than throwing) for missing/empty key or signature so
 * the caller can decide on the appropriate HTTP response code.
 */
function verify_square_webhook_signature(
    string $notificationUrl,
    string $payload,
    string $signatureKey,
    string $providedSignature
): bool {
    if ($signatureKey === '' || $providedSignature === '') {
        return false;
    }
    $stringToSign = $notificationUrl . $payload;
    $expectedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $signatureKey, true));
    return hash_equals($expectedSignature, $providedSignature);
}
