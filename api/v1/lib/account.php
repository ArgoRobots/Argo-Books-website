<?php
declare(strict_types=1);

/**
 * GET /v1/account
 *
 * The first call any integration should make: it confirms the key works, names
 * the company the key writes into, and reports how much is sitting unreviewed.
 *
 * The pending counts are the honest answer to "did my data land?". A developer
 * whose objects are piling up in `pending` is not being ignored by the API, they
 * are waiting on a human to open Argo Books, and saying so plainly here saves a
 * support email.
 */
function api_handle_account(array $auth): void
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM api_accounts WHERE id = ? LIMIT 1');
    $stmt->execute([$auth['account_id']]);
    $account = $stmt->fetch();

    if (!$account) {
        api_error(404, 'invalid_request_error', 'resource_missing', 'This key is not attached to an account.');
    }

    $pending = [];
    foreach (api_resource_definitions() as $spec) {
        $countStmt = $pdo->prepare(
            'SELECT COUNT(*) FROM ' . $spec['table'] . '
              WHERE account_id = ? AND deleted_at IS NULL AND import_status = ?'
        );
        $countStmt->execute([$auth['account_id'], 'pending']);
        $pending[$spec['object']] = (int) $countStmt->fetchColumn();
    }

    $lastImport = $pdo->prepare(
        'SELECT completed_at FROM api_import_batches
          WHERE account_id = ? AND status = ?
          ORDER BY id DESC LIMIT 1'
    );
    $lastImport->execute([$auth['account_id'], 'completed']);
    $lastCompleted = $lastImport->fetchColumn();

    api_json(200, [
        'id'                  => $account['public_id'],
        'object'              => 'account',
        'display_name'        => $account['display_name'],
        'company_uid'         => $account['company_uid'],
        'created'             => api_timestamp($account['created_at'] ?? null),
        'api_version'         => API_VERSION,
        'scopes'              => $auth['key_scopes'],
        'pending'             => (object) $pending,
        'last_import_at'      => api_timestamp($lastCompleted === false ? null : (string) $lastCompleted),
        'rate_limit_per_min'  => API_RATE_LIMIT_PER_MINUTE,
    ]);
}
