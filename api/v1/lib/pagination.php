<?php
declare(strict_types=1);

/**
 * Cursor pagination.
 *
 * Cursors, not offsets. An offset silently skips or repeats rows when something
 * is inserted mid-walk, which for an ingest queue the desktop is draining is not
 * a theoretical problem. A cursor anchored on a row id cannot drift.
 *
 * Lists are newest-first, matching Stripe:
 *   starting_after=<id>  the page after that object (older rows)
 *   ending_before=<id>   the page before it (newer rows)
 */

const API_LIMIT_DEFAULT = 10;
const API_LIMIT_MAX = 100;

/** Parse and bounds-check limit / starting_after / ending_before. */
function api_pagination_params(): array
{
    $limit = API_LIMIT_DEFAULT;
    if (isset($_GET['limit']) && $_GET['limit'] !== '') {
        if (!preg_match('/^\d+$/', (string) $_GET['limit'])) {
            api_error(400, 'invalid_request_error', 'parameter_invalid_type', "Parameter 'limit' must be an integer.", 'limit');
        }
        $limit = (int) $_GET['limit'];
        if ($limit < 1 || $limit > API_LIMIT_MAX) {
            api_error(
                400,
                'invalid_request_error',
                'parameter_out_of_range',
                'Parameter \'limit\' must be between 1 and ' . API_LIMIT_MAX . '.',
                'limit'
            );
        }
    }

    $startingAfter = isset($_GET['starting_after']) ? trim((string) $_GET['starting_after']) : '';
    $endingBefore = isset($_GET['ending_before']) ? trim((string) $_GET['ending_before']) : '';

    if ($startingAfter !== '' && $endingBefore !== '') {
        api_error(
            400,
            'invalid_request_error',
            'parameter_conflict',
            "Pass either 'starting_after' or 'ending_before', not both.",
            'starting_after'
        );
    }

    return [
        'limit'          => $limit,
        'starting_after' => $startingAfter !== '' ? $startingAfter : null,
        'ending_before'  => $endingBefore !== '' ? $endingBefore : null,
    ];
}

/**
 * Turn a cursor's public id into the internal row id the WHERE clause needs.
 * An unknown cursor is a client bug, so it gets a clear 400 rather than an
 * empty page that looks like "no more results".
 */
function api_resolve_cursor(string $table, string $publicId, int $accountId, string $param): int
{
    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT id FROM ' . $table . ' WHERE public_id = ? AND account_id = ? AND environment = ? LIMIT 1'
    );
    $stmt->execute([$publicId, $accountId, api_env()]);
    $id = $stmt->fetchColumn();

    if ($id === false) {
        api_error(
            400,
            'invalid_request_error',
            'cursor_not_found',
            "No such object '$publicId' to paginate from.",
            $param
        );
    }

    return (int) $id;
}

/**
 * Build the cursor clause.
 *
 * Returns [sqlFragment, bindings, needsReverse]. needsReverse is true for
 * ending_before, where rows come back ascending and have to be flipped so the
 * caller still sees newest-first.
 */
function api_cursor_clause(array $page, string $table, int $accountId): array
{
    if ($page['starting_after'] !== null) {
        $id = api_resolve_cursor($table, $page['starting_after'], $accountId, 'starting_after');
        return [' AND id < ?', [$id], false];
    }
    if ($page['ending_before'] !== null) {
        $id = api_resolve_cursor($table, $page['ending_before'], $accountId, 'ending_before');
        return [' AND id > ?', [$id], true];
    }
    return ['', [], false];
}
