<?php
declare(strict_types=1);

/**
 * Events and webhook fan-out.
 *
 * Events describe what the merchant did, not what the developer did. A developer
 * already knows they created an expense; what they cannot otherwise see is the
 * moment a human accepted it, declined it, or undid the import. That is why
 * there is no <object>.created event: it would be noise on a channel whose whole
 * value is telling you something you could not have known.
 *
 * Recording an event never fails the request that caused it. A webhook that
 * cannot be queued is a missed notification; a refund that fails to record
 * because a notification could not be queued is a broken API.
 */

/** Event types this API emits. Also the allow-list for endpoint subscriptions. */
const API_EVENT_TYPES = [
    'customer.imported',
    'customer.rejected',
    'supplier.imported',
    'supplier.rejected',
    'category.imported',
    'category.rejected',
    'product.imported',
    'product.rejected',
    'expense.imported',
    'expense.rejected',
    'revenue.imported',
    'revenue.rejected',
    'refund.imported',
    'refund.rejected',
    'import_batch.completed',
    'import_batch.reverted',
];

/** Attempt schedule in seconds. Six tries spread over roughly a day. */
const API_WEBHOOK_BACKOFF = [0, 60, 300, 1800, 7200, 43200];

const API_WEBHOOK_MAX_ATTEMPTS = 6;

/**
 * Record an event and queue it to every endpoint subscribed to its type.
 *
 * @param array $payload The serialized object, as the API would return it.
 */
function api_record_event(int $accountId, string $type, ?string $objectId, array $payload): void
{
    global $pdo;

    try {
        $publicId = api_generate_id('evt');
        $pdo->prepare(
            'INSERT INTO api_events (account_id, public_id, type, object_id, data, environment)
             VALUES (?, ?, ?, ?, ?, ?)'
        )->execute([
            $accountId,
            $publicId,
            $type,
            $objectId,
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            api_env(),
        ]);

        api_queue_deliveries($accountId, (int) $pdo->lastInsertId(), $type);
    } catch (Throwable $e) {
        error_log('api/v1: failed to record event ' . $type . ': ' . $e->getMessage());
    }
}

/** Queue one delivery row per enabled endpoint subscribed to this type. */
function api_queue_deliveries(int $accountId, int $eventId, string $type): void
{
    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT id, enabled_events FROM api_webhook_endpoints
          WHERE account_id = ? AND environment = ? AND status = ? AND deleted_at IS NULL'
    );
    $stmt->execute([$accountId, api_env(), 'enabled']);

    foreach ($stmt->fetchAll() as $endpoint) {
        if (!api_endpoint_wants($endpoint['enabled_events'], $type)) {
            continue;
        }
        // First attempt is due immediately; the delivery cron picks it up on its
        // next pass rather than blocking this request on someone else's server.
        $pdo->prepare(
            'INSERT INTO api_webhook_deliveries (endpoint_id, event_id, next_attempt_at, environment)
             VALUES (?, ?, NOW(), ?)
             ON DUPLICATE KEY UPDATE id = id'
        )->execute([(int) $endpoint['id'], $eventId, api_env()]);
    }
}

/** NULL enabled_events means every type, matching Stripe's "receive all events". */
function api_endpoint_wants(?string $enabledEventsJson, string $type): bool
{
    if ($enabledEventsJson === null || $enabledEventsJson === '') {
        return true;
    }
    $list = json_decode($enabledEventsJson, true);
    return !is_array($list) || $list === [] || in_array($type, $list, true);
}

/**
 * The signature header for one delivery.
 *
 * Timestamped and signed together so a captured payload cannot be replayed
 * later: the receiver rejects anything whose timestamp is too old, and the
 * timestamp is inside the signed material so it cannot be edited.
 */
function api_webhook_signature(string $secret, int $timestamp, string $body): string
{
    $signature = hash_hmac('sha256', $timestamp . '.' . $body, $secret);
    return "t=$timestamp,v1=$signature";
}

/** Mint a signing secret. `whsec_` marks it recognisably for leak scanners. */
function api_generate_signing_secret(): string
{
    return 'whsec_' . bin2hex(random_bytes(24));
}

/** Serialize an event row for the API and for the webhook body. */
function api_serialize_event(array $row): array
{
    $data = json_decode((string) $row['data'], true);

    return [
        'id'      => $row['public_id'],
        'object'  => 'event',
        'type'    => $row['type'],
        'created' => api_timestamp($row['created_at'] ?? null),
        'data'    => ['object' => is_array($data) ? $data : new stdClass()],
    ];
}
