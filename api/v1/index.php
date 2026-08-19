<?php
declare(strict_types=1);

/**
 * Front controller for the Argo Books public API.
 *
 * Everything under /v1 lands here via the rewrite in .htaccess. One entry point
 * rather than a file per endpoint, so authentication, rate limiting, version
 * pinning and the error envelope cannot be forgotten on a new route.
 *
 * Routes:
 *   GET    /v1/account
 *   GET    /v1/<resource>                      list        (read)
 *   POST   /v1/<resource>                      create      (write)
 *   GET    /v1/<resource>/<id>                 retrieve    (read)
 *   POST   /v1/<resource>/<id>                 update      (write)
 *   DELETE /v1/<resource>/<id>                 delete      (write)
 *   POST   /v1/<resource>/<id>/reject          reject      (write)
 *   GET    /v1/<resource>/<id>/line_items      list items  (read)
 *   POST   /v1/<resource>/<id>/line_items      add item    (write)
 *   GET    /v1/import_batches
 *   POST   /v1/import_batches
 *   GET    /v1/import_batches/<id>
 *   POST   /v1/import_batches/<id>/revert
 *
 * <resource> is one of: customers, suppliers, categories, products, expenses,
 * revenue, refunds.
 */

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/definitions.php';
require_once __DIR__ . '/lib/batches.php';
require_once __DIR__ . '/lib/account.php';
require_once __DIR__ . '/lib/webhook_endpoints.php';

// A thrown PDOException must not reach the client as a stack trace: it would
// leak table names, and the developer can do nothing with it anyway.
set_exception_handler(static function (Throwable $e): void {
    error_log('api/v1 unhandled: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        api_error(500, 'api_error', 'internal_error', 'Something went wrong on our end. The request id identifies this failure in our logs.');
    }
});

api_enforce_version();

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

// The API is server-to-server only. No CORS preflight is answered, because a
// secret key must never be in a browser where a preflight would be needed.
if ($method === 'OPTIONS') {
    api_error(405, 'invalid_request_error', 'method_not_allowed', 'This API does not accept cross-origin browser requests.');
}

$auth = api_authenticate();
api_enforce_rate_limit($auth['key_id']);
api_rate_limit_gc();
api_idempotency_gc();

$segments = array_values(array_filter(explode('/', api_request_path()), static fn ($s) => $s !== ''));

if ($segments === []) {
    api_error(
        404,
        'invalid_request_error',
        'unknown_route',
        'Nothing is served at /v1 itself. Start with GET /v1/account.'
    );
}

$head = array_shift($segments);

if ($head === 'account') {
    api_require_method($method, ['GET']);
    api_require_scope($auth, 'read');
    api_handle_account($auth);
}

if ($head === 'import_batches') {
    api_route_import_batches($method, $auth, $segments);
}

if ($head === 'webhook_endpoints') {
    api_route_webhook_endpoints($method, $auth, $segments);
}

if ($head === 'events') {
    if ($segments === []) {
        api_require_method($method, ['GET']);
        api_require_scope($auth, 'read');
        api_handle_list_events($auth);
    }
    api_require_method($method, ['GET']);
    api_require_scope($auth, 'read');
    api_handle_retrieve_event($auth, array_shift($segments));
}

$definitions = api_resource_definitions();
if (!isset($definitions[$head])) {
    api_error(
        404,
        'invalid_request_error',
        'unknown_route',
        "No such resource '$head'. Available: " . implode(', ', array_keys($definitions)) . '.'
    );
}

api_route_resource($method, $auth, $definitions[$head], $head, $segments);

// ---------------------------------------------------------------------------

/** Dispatch everything under /v1/import_batches. */
function api_route_import_batches(string $method, array $auth, array $segments): void
{
    if ($segments === []) {
        if ($method === 'GET') {
            api_require_scope($auth, 'read');
            api_handle_list_batches($auth);
        }
        api_require_method($method, ['GET', 'POST']);
        api_require_scope($auth, 'write');
        api_handle_create_batch($auth);
    }

    $publicId = array_shift($segments);

    if ($segments === []) {
        api_require_method($method, ['GET']);
        api_require_scope($auth, 'read');
        api_handle_retrieve_batch($auth, $publicId);
    }

    $action = array_shift($segments);
    if ($action === 'revert' && $segments === []) {
        api_require_method($method, ['POST']);
        api_require_scope($auth, 'write');
        api_handle_revert_batch($auth, $publicId);
    }

    api_error(404, 'invalid_request_error', 'unknown_route', "No such route under /v1/import_batches/$publicId.");
}

/** Dispatch everything under /v1/webhook_endpoints. */
function api_route_webhook_endpoints(string $method, array $auth, array $segments): void
{
    if ($segments === []) {
        if ($method === 'GET') {
            api_require_scope($auth, 'read');
            api_handle_list_endpoints($auth);
        }
        api_require_method($method, ['GET', 'POST']);
        api_require_scope($auth, 'write');
        api_handle_create_endpoint($auth);
    }

    $publicId = array_shift($segments);
    if ($segments !== []) {
        api_error(404, 'invalid_request_error', 'unknown_route', 'That path is nested deeper than any route this API serves.');
    }

    if ($method === 'GET') {
        api_require_scope($auth, 'read');
        api_handle_retrieve_endpoint($auth, $publicId);
    }
    if ($method === 'DELETE') {
        api_require_scope($auth, 'write');
        api_handle_delete_endpoint($auth, $publicId);
    }

    api_require_method($method, ['GET', 'POST', 'DELETE']);
    api_require_scope($auth, 'write');
    api_handle_update_endpoint($auth, $publicId);
}

/** Dispatch everything under /v1/<resource>. */
function api_route_resource(string $method, array $auth, array $spec, string $segment, array $segments): void
{
    if ($segments === []) {
        if ($method === 'GET') {
            api_require_scope($auth, 'read');
            api_handle_list($spec, $auth, $segment);
        }
        api_require_method($method, ['GET', 'POST']);
        api_require_scope($auth, 'write');
        api_handle_create($spec, $auth);
    }

    $publicId = array_shift($segments);

    if ($segments === []) {
        if ($method === 'GET') {
            api_require_scope($auth, 'read');
            api_handle_retrieve($spec, $auth, $publicId);
        }
        if ($method === 'DELETE') {
            api_require_scope($auth, 'write');
            api_handle_delete($spec, $auth, $publicId);
        }
        api_require_method($method, ['GET', 'POST', 'DELETE']);
        api_require_scope($auth, 'write');
        api_handle_update($spec, $auth, $publicId);
    }

    $action = array_shift($segments);
    if ($segments !== []) {
        api_error(404, 'invalid_request_error', 'unknown_route', 'That path is nested deeper than any route this API serves.');
    }

    if ($action === 'reject') {
        api_require_method($method, ['POST']);
        api_require_scope($auth, 'write');
        api_handle_reject($spec, $auth, $publicId);
    }

    if ($action === 'line_items') {
        if (empty($spec['line_items'])) {
            api_error(
                404,
                'invalid_request_error',
                'unknown_route',
                "A {$spec['object']} does not have line items. Only expenses and revenue do."
            );
        }
        if ($method === 'GET') {
            api_require_scope($auth, 'read');
            api_handle_list_line_items($spec, $auth, $publicId);
        }
        api_require_method($method, ['GET', 'POST']);
        api_require_scope($auth, 'write');
        api_handle_create_line_item($spec, $auth, $publicId);
    }

    api_error(404, 'invalid_request_error', 'unknown_route', "No such action '$action' on a {$spec['object']}.");
}

/** 405 with an Allow header when the verb is wrong for an otherwise valid route. */
function api_require_method(string $method, array $allowed): void
{
    if (in_array($method, $allowed, true)) {
        return;
    }
    header('Allow: ' . implode(', ', $allowed));
    api_error(
        405,
        'invalid_request_error',
        'method_not_allowed',
        "$method is not supported here. Allowed: " . implode(', ', $allowed) . '.'
    );
}
