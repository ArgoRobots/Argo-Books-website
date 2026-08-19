<?php
declare(strict_types=1);

/**
 * Shared bootstrap for the Argo Books public API (/v1).
 *
 * Loads the global $pdo, establishes the request id, pins the API version, and
 * pulls in the rest of the lib. Every endpoint goes through index.php, which
 * requires this file first.
 *
 * Design notes that apply everywhere under /v1:
 *   - Money crosses the wire as an integer in the currency's minor unit, the
 *     way Stripe does it. No floats, no rounding surprises.
 *   - Every row this API touches carries an `environment` column and EVERY
 *     query filters on api_env(). Production and dev share one database.
 *   - Responses are Stripe-shaped: `object` on every resource, `object: "list"`
 *     with `data`/`has_more` on every collection, one error envelope.
 */

require_once __DIR__ . '/../../../db_connect.php';

/** The only API version this build speaks. Sent back on every response. */
const API_VERSION = '2026-08-18';

/** Where the docs live, used to build `doc_url` on errors. */
const API_DOC_BASE = 'https://argorobots.com/documentation/api';

/** Requests per minute, per key. */
const API_RATE_LIMIT_PER_MINUTE = 120;

require_once __DIR__ . '/ids.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/ratelimit.php';
require_once __DIR__ . '/idempotency.php';
require_once __DIR__ . '/validate.php';
require_once __DIR__ . '/pagination.php';
require_once __DIR__ . '/resource.php';

/**
 * Deploy environment for row filtering. NOT a user-facing test mode: `sandbox`
 * means dev.argorobots.com. There is deliberately no test/live key split, since
 * a developer who wants a scratch space just makes a second Argo Books company.
 */
function api_env(): string
{
    return current_environment();
}

/**
 * Per-request identifier. Echoed in the Request-Id header and embedded in every
 * error body so a developer can quote it in a support email.
 */
function api_request_id(): string
{
    static $id = null;
    if ($id === null) {
        $id = 'req_' . bin2hex(random_bytes(12));
    }
    return $id;
}

/**
 * The path after /v1/, e.g. "expenses/exp_ab12" for GET /v1/expenses/exp_ab12.
 *
 * Read from REQUEST_URI rather than a rewrite-supplied query parameter on
 * purpose: with QSA a caller could append their own ?__path= and PHP would take
 * the later occurrence, letting them address a different route than the one the
 * rewrite matched. Parsing the real URI removes that whole class of problem, and
 * it also copes with the local Laragon subfolder mount.
 */
function api_request_path(): string
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $uri = explode('?', $uri, 2)[0];

    $pos = strpos($uri, '/v1/');
    if ($pos === false) {
        // Bare /v1 with no trailing slash.
        $pos = strpos($uri, '/v1');
        if ($pos === false) {
            return '';
        }
        return '';
    }

    $path = substr($uri, $pos + 4);
    return trim(rawurldecode($path), '/');
}

/**
 * Parsed JSON body for write requests. Also accepts form-encoded bodies, which
 * is what curl sends by default and what most Stripe examples people copy use.
 * Returns [decodedArray, rawString].
 */
function api_request_body(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $raw = (string) file_get_contents('php://input');
    if ($raw === '') {
        return $cached = [[], ''];
    }

    $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
    if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
        $parsed = [];
        parse_str($raw, $parsed);
        return $cached = [$parsed, $raw];
    }

    $parsed = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
        api_error(400, 'invalid_request_error', 'invalid_json', 'Request body could not be parsed as JSON.');
    }

    return $cached = [$parsed, $raw];
}

/**
 * Reject an unrecognised Argo-Version. Absent means "use the current version",
 * which is the only one that exists today; the header is accepted now so that
 * pinning becomes a server-side change later rather than a client rewrite.
 */
function api_enforce_version(): void
{
    $requested = trim((string) ($_SERVER['HTTP_ARGO_VERSION'] ?? ''));
    if ($requested === '' || $requested === API_VERSION) {
        return;
    }
    api_error(
        400,
        'invalid_request_error',
        'unknown_api_version',
        "Unknown Argo-Version '$requested'. This server speaks " . API_VERSION . '.',
        'Argo-Version'
    );
}
