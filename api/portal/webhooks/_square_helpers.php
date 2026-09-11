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

/**
 * Find the portal invoice that a completed Square payment event pays.
 *
 * Invoice numbers are only unique per company, so the match is limited to the
 * company whose connected Square account took the payment. Only reference_id
 * counts: the portal's checkout sets it, while the note is free text a seller
 * can type at the point of sale.
 */
function square_find_portal_invoice(PDO $pdo, array $event, string $environment): ?array
{
    $merchantId = (string) ($event['merchant_id'] ?? '');
    $invoiceRef = (string) ($event['data']['object']['payment']['reference_id'] ?? '');
    if ($merchantId === '' || $invoiceRef === '') {
        return null;
    }

    $stmt = $pdo->prepare(
        'SELECT pi.company_id, pi.invoice_id, pi.customer_name
         FROM portal_invoices pi
         JOIN portal_companies pc ON pc.id = pi.company_id
         WHERE pc.square_merchant_id = ? AND pc.environment = ?
           AND pi.invoice_id = ? AND pi.environment = ?
         LIMIT 2'
    );
    $stmt->execute([$merchantId, $environment, $invoiceRef, $environment]);
    $matches = $stmt->fetchAll();

    // One Square account connected to two portal companies that both use this
    // invoice number: crediting either one would be a guess.
    return count($matches) === 1 ? $matches[0] : null;
}
