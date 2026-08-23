<?php
/**
 * Portal Invoice Balance API Endpoint
 *
 * POST /api/portal/invoices/balance - Reconcile invoice balances from Argo Books
 *
 * Requires API key authentication (Argo Books -> Server).
 *
 * Argo Books owns payments taken OUTSIDE the portal (cash, cheque, bank
 * transfer). Without this endpoint the server never learns about them, so
 * portal_invoices.balance_due stays stale and the reminder cron would chase a
 * customer who has already paid.
 *
 * Why this exists instead of re-publishing through POST /api/portal/invoices:
 * that endpoint rewrites status 'paid' back to 'pending' (see invoices.php),
 * clobbers the server-set 'viewed' status, replaces invoice_data (so a later
 * template change would silently alter invoices the customer already has a
 * link to), and writes an ABSOLUTE balance_due that races the atomic decrement
 * in record_portal_payment(). This endpoint touches two columns, sends no
 * email, and never assigns an absolute balance.
 *
 * Expects JSON body:
 *   { "invoices": [ { "invoiceId": "...", "totalAmount": 1200.00,
 *                     "externalPaid": 300.00, "currency": "CAD",
 *                     "dueDate": "2026-08-01", "cancelled": false } ] }
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['POST']);

// Batched so company-open reconciliation is a single round trip. Bounded so a
// buggy or hostile client cannot hand us an unbounded write loop.
const PORTAL_BALANCE_MAX_ITEMS = 200;

// Ceiling on one re-rendered invoice. Generous next to a real invoice (tens of
// KB) while stopping a single request from writing megabytes into a JSON column.
const PORTAL_BALANCE_MAX_HTML_BYTES = 524288;

$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

if (!is_array($data) || !isset($data['invoices']) || !is_array($data['invoices'])) {
    send_error_response(400, 'Missing or invalid required field: invoices', 'MISSING_FIELDS');
}

$items = $data['invoices'];
if (count($items) > PORTAL_BALANCE_MAX_ITEMS) {
    send_error_response(
        400,
        'Too many invoices in one request (max ' . PORTAL_BALANCE_MAX_ITEMS . ').',
        'TOO_MANY_ITEMS'
    );
}

$companyId = $company['id'];
$results = [];
$updated = 0;
$notFound = 0;
$rejected = 0;

// Prepared once, reused per item.
//
// SET-clause order is load-bearing here, the same trap documented in
// record_portal_payment() (portal-helper.php, "Single atomic UPDATE"). MySQL
// evaluates SET assignments left to right and later clauses see the NEW value:
//
//   1. `status = CASE ...` MUST come BEFORE `balance_due = ...`, so the CASE
//      compares against the OLD balance. Reverse them and a partial payment
//      evaluates against the already-reduced balance and marks the invoice
//      'paid' when it is only 'partial'.
//   2. `external_paid` and `total_amount` MUST be assigned AFTER every
//      expression that reads them, so the delta is computed from the OLD
//      stored values. Reverse those and the delta collapses to zero and the
//      balance never moves.
//
// The delta is relative, never absolute:
//     new_balance = old_balance - ((externalPaid_new - externalPaid_old)
//                                - (total_new - total_old))
// More paid outside the portal lowers the balance; a raised total raises it.
// Because both this and record_portal_payment() only ever ADJUST the stored
// value rather than overwrite it, the two are commutative and each is
// serialized by the InnoDB row lock, so they can interleave in either order
// and still land on the same number. It also means a repeat push with
// unchanged numbers is a zero delta, which is what makes retries safe.
$updateStmt = $pdo->prepare(
    'UPDATE portal_invoices
     SET status = CASE
             WHEN status = "cancelled" THEN status
             WHEN balance_due - ((? - external_paid) - (? - total_amount)) <= 0 THEN "paid"
             WHEN balance_due - ((? - external_paid) - (? - total_amount)) < ? THEN "partial"
             ELSE status
         END,
         balance_due = GREATEST(0, balance_due - ((? - external_paid) - (? - total_amount))),
         external_paid = ?,
         total_amount = ?,
         due_date = COALESCE(?, due_date),
         updated_at = NOW()
     WHERE company_id = ? AND invoice_id = ?'
);

// Cancellation runs separately so it does not fight the "keep cancelled"
// guard in the CASE above. A paid invoice is never retro-cancelled.
$cancelStmt = $pdo->prepare(
    'UPDATE portal_invoices SET status = "cancelled", updated_at = NOW()
     WHERE company_id = ? AND invoice_id = ? AND status NOT IN ("cancelled", "paid")'
);

// Un-cancelling falls back to a balance-derived state rather than guessing.
$uncancelStmt = $pdo->prepare(
    'UPDATE portal_invoices
     SET status = CASE
             WHEN balance_due <= 0 THEN "paid"
             WHEN balance_due < total_amount THEN "partial"
             ELSE "sent"
         END,
         updated_at = NOW()
     WHERE company_id = ? AND invoice_id = ? AND status = "cancelled"'
);

$lookupStmt = $pdo->prepare(
    'SELECT id, currency FROM portal_invoices WHERE company_id = ? AND invoice_id = ? LIMIT 1'
);

// The stored invoice HTML is a snapshot taken when Argo Books published the
// invoice, so once anything is paid it keeps showing the original totals with
// no Amount Paid row. Argo Books re-renders and sends it whenever that would
// be wrong.
//
// JSON_SET patches only this one key: invoice_data also holds lineItems,
// addresses and notes, which the non-custom portal template renders from, and
// replacing the whole document would drop them.
$htmlStmt = $pdo->prepare(
    'UPDATE portal_invoices
     SET invoice_data = JSON_SET(COALESCE(invoice_data, JSON_OBJECT()), "$.customInvoiceHtml", ?),
         updated_at = NOW()
     WHERE company_id = ? AND invoice_id = ?'
);

foreach ($items as $item) {
    if (!is_array($item) || empty($item['invoiceId']) || !is_string($item['invoiceId'])) {
        $rejected++;
        $results[] = ['invoiceId' => null, 'result' => 'invalid'];
        continue;
    }

    $invoiceId = $item['invoiceId'];

    if (!isset($item['totalAmount']) || !is_numeric($item['totalAmount'])
        || !isset($item['externalPaid']) || !is_numeric($item['externalPaid'])) {
        $rejected++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'invalid'];
        continue;
    }

    $totalAmount = round(floatval($item['totalAmount']), 2);
    $externalPaid = round(floatval($item['externalPaid']), 2);
    if ($totalAmount < 0 || $externalPaid < 0) {
        $rejected++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'invalid'];
        continue;
    }

    // Existence and currency are validated with a read, but the balance maths
    // below is still a relative delta, so a payment landing between this
    // SELECT and the UPDATE cannot corrupt the result.
    $lookupStmt->execute([$companyId, $invoiceId]);
    $existing = $lookupStmt->fetch();

    if (!$existing) {
        $notFound++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'not_found'];
        continue;
    }

    // A currency change after publish would apply a delta denominated in one
    // currency against a balance denominated in another. Refuse rather than
    // silently corrupt the balance.
    $claimedCurrency = isset($item['currency']) && is_string($item['currency'])
        ? strtoupper(preg_replace('/[^A-Za-z]/', '', $item['currency']))
        : '';
    if ($claimedCurrency !== '' && $claimedCurrency !== strtoupper((string)$existing['currency'])) {
        $rejected++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'currency_mismatch'];
        continue;
    }

    $dueDate = null;
    if (!empty($item['dueDate']) && is_string($item['dueDate'])) {
        $ts = strtotime($item['dueDate']);
        if ($ts !== false) {
            $dueDate = date('Y-m-d', $ts);
        }
    }

    try {
        $updateStmt->execute([
            $externalPaid, $totalAmount,   // 'paid' branch
            $externalPaid, $totalAmount,   // 'partial' branch
            $totalAmount,                  // compared against the NEW total, not the column
            $externalPaid, $totalAmount,   // balance_due delta
            $externalPaid,                 // external_paid, assigned last
            $totalAmount,                  // total_amount, assigned last
            $dueDate,
            $companyId, $invoiceId,
        ]);

        if (array_key_exists('cancelled', $item)) {
            if (filter_var($item['cancelled'], FILTER_VALIDATE_BOOLEAN)) {
                $cancelStmt->execute([$companyId, $invoiceId]);
            } else {
                $uncancelStmt->execute([$companyId, $invoiceId]);
            }
        }

        // Optional: only present when the stored snapshot would be out of date.
        if (!empty($item['customInvoiceHtml']) && is_string($item['customInvoiceHtml'])
            && strlen($item['customInvoiceHtml']) <= PORTAL_BALANCE_MAX_HTML_BYTES) {
            $htmlStmt->execute([$item['customInvoiceHtml'], $companyId, $invoiceId]);
        }

        $updated++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'updated'];
    } catch (\PDOException $e) {
        error_log('Portal invoice balance DB error: ' . $e->getMessage());
        $rejected++;
        $results[] = ['invoiceId' => $invoiceId, 'result' => 'error'];
    }
}

send_json_response(200, [
    'success' => true,
    'results' => $results,
    'updated' => $updated,
    'notFound' => $notFound,
    'rejected' => $rejected,
    'timestamp' => date('c')
]);
