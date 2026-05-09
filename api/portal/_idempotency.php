<?php
declare(strict_types=1);

/**
 * Idempotency-Key support for POST endpoints. The desktop generates a fresh
 * GUID per logical action and replays it on retry. We cache (status, body) by
 * (company_id, key, body_hash); same key + same body returns the cached
 * response within 24h; same key + different body returns 409.
 *
 * Backed by refund_idempotency_cache table (created by the refunds migration).
 */

/**
 * @return array|null ['status' => int, 'body' => string] when a matching prior
 *                    response exists; null when there's no prior response.
 *                    On key+body mismatch, terminates with 409 + error JSON.
 */
function idempotency_lookup(PDO $pdo, int $company_id, string $key, string $body_hash): ?array {
    $stmt = $pdo->prepare("
        SELECT body_hash, response_status, response_body
        FROM refund_idempotency_cache
        WHERE company_id = ? AND idempotency_key = ?
          AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$company_id, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;
    if (!hash_equals($row['body_hash'], $body_hash)) {
        http_response_code(409);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'IDEMPOTENCY_KEY_MISMATCH',
            'message' => 'Same Idempotency-Key reused with different request body.',
        ]);
        exit;
    }
    return ['status' => (int)$row['response_status'], 'body' => $row['response_body']];
}

function idempotency_store(PDO $pdo, int $company_id, string $key, string $body_hash, int $status, string $body): void {
    $stmt = $pdo->prepare("
        INSERT INTO refund_idempotency_cache (company_id, idempotency_key, body_hash, response_status, response_body)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            response_status = VALUES(response_status),
            response_body = VALUES(response_body),
            created_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$company_id, $key, $body_hash, $status, $body]);
}

/**
 * Wrap a handler so its response is cached by Idempotency-Key. If the header
 * is missing, the handler runs uncached. On replay, the cached response is
 * served with the original status code. The handler MUST echo its body and
 * call http_response_code() before returning.
 */
function with_idempotency(PDO $pdo, int $company_id, string $raw_body, callable $handler): void {
    $key = $_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? null;
    if (!$key) {
        $handler();
        return;
    }
    if (strlen($key) > 128) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'IDEMPOTENCY_KEY_TOO_LONG']);
        return;
    }
    $body_hash = hash('sha256', $raw_body);
    $cached = idempotency_lookup($pdo, $company_id, $key, $body_hash);
    if ($cached !== null) {
        http_response_code($cached['status']);
        header('Content-Type: application/json');
        echo $cached['body'];
        return;
    }
    ob_start();
    $handler();
    $body = ob_get_clean();
    $status = http_response_code() ?: 200;
    idempotency_store($pdo, $company_id, $key, $body_hash, $status, $body);
    http_response_code($status);
    header('Content-Type: application/json');
    echo $body;
}
