<?php
/**
 * Shared helper functions for outreach automation.
 * Used by both the admin API (admin/outreach/api.php) and the cron pipeline.
 *
 * Contains: log_activity, send_outreach_lead, scrape_email_from_website,
 *           search_businesses_core, call_gemini, summarize_business,
 *           generate_draft_for_lead
 */

// Guard against double-inclusion
if (defined('OUTREACH_HELPERS_LOADED')) return;
define('OUTREACH_HELPERS_LOADED', true);

// ─── Discovery Category Pool ───
// Used by both the cron pipeline (deterministic cycling) and search_businesses_core (random fallback for admin searches)
const OUTREACH_CATEGORY_POOL = [
    'restaurants', 'plumbers', 'electricians', 'dentists', 'lawyers',
    'accountants', 'real estate agents', 'insurance agents', 'auto repair',
    'hair salons', 'fitness gyms', 'chiropractors', 'veterinarians',
    'cleaning services', 'landscaping', 'roofing contractors', 'HVAC',
    'photographers', 'florists', 'bakeries', 'coffee shops', 'pet stores',
    'daycare centers', 'tutoring services', 'martial arts studios',
    'yoga studios', 'massage therapists', 'optometrists', 'pharmacies',
    'printing services', 'moving companies', 'pest control', 'locksmiths',
    'car dealerships', 'tire shops', 'furniture stores', 'jewelry stores',
    'clothing boutiques', 'tattoo parlors', 'breweries', 'catering',
    'wedding planners', 'interior designers', 'architects', 'surveyors',
    'physiotherapists', 'psychologists', 'counsellors', 'notaries',
    'bookkeepers', 'IT support', 'web design', 'marketing agencies',
    'sign shops', 'trophy shops', 'music schools', 'dance studios',
    'dog groomers', 'boarding kennels', 'farm equipment dealers',
    'hardware stores', 'building supplies', 'appliance repair',
    'upholstery services', 'tailors', 'dry cleaners', 'spas',
    'tanning salons', 'nail salons', 'barber shops', 'optical stores',
    'hearing aid clinics', 'home inspectors', 'appraisers',
    'property management', 'storage facilities', 'courier services',
    'towing services', 'glass repair', 'fencing contractors',
    'concrete contractors', 'paving contractors', 'tree services',
    'snow removal', 'pool services', 'septic services',
    'garage door repair', 'security companies', 'staffing agencies',
    'travel agencies', 'event venues', 'food trucks',
];

// ─── Category Pain Points ───

/**
 * Look up 2-3 hand-curated pain points for a lead's category, falling back
 * to a generic '_default' list if the category is not in the map.
 * The map is loaded once per process.
 */
function get_category_pain_points($category)
{
    static $map = null;
    if ($map === null) {
        $map = require __DIR__ . '/category_pain_points.php';
    }
    $key = strtolower(trim((string) $category));
    return $map[$key] ?? $map['_default'];
}

// ─── A/B Test Infrastructure ───

require_once __DIR__ . '/ab_helpers.php';

/**
 * Find the single active A/B test of a given variant type, or null if none.
 * Returns ['test' => row, 'variants' => [rows]] on hit. Used by the cron's
 * promotion sweep, which iterates per type.
 */
function get_active_ab_test($pdo, $variantType)
{
    $stmt = $pdo->prepare("SELECT * FROM outreach_ab_tests
        WHERE status = 'active' AND variant_type = ?
        ORDER BY started_at DESC, id DESC LIMIT 1");
    $stmt->execute([$variantType]);
    $test = $stmt->fetch();
    if (!$test) return null;

    $vStmt = $pdo->prepare("SELECT * FROM outreach_ab_variants WHERE test_id = ? ORDER BY id ASC");
    $vStmt->execute([$test['id']]);
    $variants = $vStmt->fetchAll();
    if (empty($variants)) return null;

    return ['test' => $test, 'variants' => $variants];
}

/**
 * Find THE single active A/B test (any type) and its variants, or null. Use
 * this when the caller doesn't care which type — the framework's invariant is
 * one active test at a time, so iterating per-type is wasteful at draft time
 * (worst case 7 queries per drafted lead just to find the active one).
 */
function get_single_active_ab_test($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM outreach_ab_tests
        WHERE status = 'active'
        ORDER BY started_at DESC, id DESC LIMIT 1");
    $stmt->execute();
    $test = $stmt->fetch();
    if (!$test) return null;

    $vStmt = $pdo->prepare("SELECT * FROM outreach_ab_variants WHERE test_id = ? ORDER BY id ASC");
    $vStmt->execute([$test['id']]);
    $variants = $vStmt->fetchAll();
    if (empty($variants)) return null;

    return ['test' => $test, 'variants' => $variants];
}

/**
 * Pick a variant for the given lead, deterministically per-(lead, test).
 *
 * Previously this counted leads-already-assigned and used count % variants for
 * exact round-robin — but the count was read before the caller persisted the
 * assignment, so two concurrent draft-generation calls (the bulk-draft batch
 * runs three at a time) would both read the same count and assign the same
 * variant. Hashing (leadId, testId) instead is race-free and gives an even
 * distribution across leads with no shared counter.
 */
function pick_ab_variant($pdo, $test, $variants, $lead = null)
{
    $count = count($variants);
    $leadId = is_array($lead) && isset($lead['id']) ? (int) $lead['id'] : 0;
    if ($leadId <= 0) {
        // Defensive fallback for callers that don't pass a lead — pick a
        // uniformly random variant rather than collapsing to index 0.
        $idx = random_int(0, $count - 1);
    } else {
        // Mix the test ID in so the same lead doesn't always end up in the
        // same slot across separate tests. crc32 keeps the math cheap and
        // gives a uniform distribution at this scale.
        $idx = abs(crc32($leadId . ':' . (int) $test['id'])) % $count;
    }
    return $variants[$idx];
}

/**
 * Split a variant's `content` into ['mode' => 'directive'|'literal', 'text' => string].
 * Helper used by every per-type instruction builder.
 */
function ab_parse_variant_content($variant)
{
    $content = trim((string) $variant['content']);
    if (stripos($content, 'directive:') === 0) {
        return [
            'mode' => 'directive',
            'text' => trim(substr($content, strlen('directive:'))),
        ];
    }
    return ['mode' => 'literal', 'text' => $content];
}

/**
 * Subject-line instruction builder. Returns a prompt fragment to splice
 * into the system prompt's subject-line bullet.
 */
function ab_subject_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- SUBJECT LINE OVERRIDE: The text inside the quotes below is a STYLE INSTRUCTION, not the subject itself. Generate your own original short subject line (under 60 characters, no em dashes) in the style described — do NOT copy, echo, paraphrase, or include any of the instruction's wording in your output. Style instruction: \"" . $parsed['text'] . "\". This overrides any other guidance about subject lines above.";
    }
    return "\n- SUBJECT LINE OVERRIDE: use exactly this subject line, word for word, with no changes: \"" . $parsed['text'] . "\". This overrides any other guidance about subject lines above.";
}

/**
 * Body instruction builder. The override controls body shape — paragraph
 * count, opener style, tone, length — but explicitly preserves the
 * non-negotiable structural elements (website URL, {UNSUBSCRIBE_URL}
 * placeholder, three-line sign-off).
 */
function ab_body_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- BODY OVERRIDE: write the email body in the style described by this directive: \"" . $parsed['text'] . "\". This style guidance overrides the paragraph count, opener style, and tone rules above. You MUST still include the https://argorobots.com/ link, the {UNSUBSCRIBE_URL} placeholder line, and the \"All the best, / Evan / Argo Books\" sign-off exactly as specified.";
    }
    return "\n- BODY OVERRIDE: the email body must be exactly this text, word for word: \"" . $parsed['text'] . "\". The {UNSUBSCRIBE_URL} placeholder and the https://argorobots.com/ link inside this text will be processed before sending — keep them as written.";
}

/**
 * CTA / offer instruction builder. Replaces the standard "free 1-year
 * premium license in exchange for feedback" offer with whatever wording
 * (or directive) the variant specifies.
 */
function ab_cta_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- OFFER OVERRIDE: ignore the \"free 1-year premium license in exchange for feedback\" offer above. Instead, phrase the offer in line with this directive: \"" . $parsed['text'] . "\". Work it in naturally; do not list it like a bullet point.";
    }
    return "\n- OFFER OVERRIDE: ignore the \"free 1-year premium license in exchange for feedback\" offer above. The offer in this email must be exactly: \"" . $parsed['text'] . "\". Phrase it naturally inside the body — do not just paste it as a quoted line.";
}

/**
 * Per-type prompt-injection dispatch. Returns a string to append to the
 * system prompt for prompt-side variant types (subject / body / cta).
 * Send-side types (sender / preheader / format) apply at send time and
 * return an empty string here. Personalization is handled inline in
 * generate_draft_for_lead (it gates the AI summary call rather than
 * injecting into the prompt).
 */
function ab_instruction_for_variant($variant, $variantType = 'subject')
{
    switch ($variantType) {
        case 'subject':
            return ab_subject_instruction_for_variant($variant);
        case 'body':
            return ab_body_instruction_for_variant($variant);
        case 'cta':
            return ab_cta_instruction_for_variant($variant);
        default:
            return '';
    }
}

// ─── Activity Logging ───

function log_activity($pdo, $lead_id, $action_type, $details = null)
{
    $stmt = $pdo->prepare("INSERT INTO outreach_activity_log (lead_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$lead_id, $action_type, $details]);
}

// ─── Send a Single Outreach Email ───

/**
 * Send an outreach email for a lead and update its DB status.
 * Returns true on success, false on failure.
 *
 * The optional &$reason out-param disambiguates non-success outcomes for
 * callers that need to distinguish a real send failure from a benign skip
 * (race-condition guard, suppression list, invalid email). Possible values:
 *   'sent'         — email delivered, lead marked contacted
 *   'already_sent' — atomic claim lost (another process already sent it)
 *   'suppressed'   — email is on the outreach suppression list
 *   'invalid_email'— lead's email is missing or malformed
 *   'smtp_failed'  — SMTP/transport failure (the only "real failure" reason)
 * Existing callers that ignore the param still get the correct bool result.
 */
function send_outreach_lead($pdo, $lead, &$reason = null)
{
    $id = $lead['id'];
    $email = $lead['email'];

    // Atomic claim BEFORE sending. Without this, two simultaneous callers
    // (admin "Send" tab + cron stepSendEmails, or two tabs) can both pass
    // their pre-fetch sent_at check and both invoke SMTP.
    //
    // This sets sent_at = NOW() up front so only one process wins. If the
    // SMTP send subsequently fails, we restore sent_at = NULL below so the
    // lead remains sendable. The remaining failure mode is a process crash
    // between this claim and the actual send — sent_at would stay set with
    // no email having gone out. That's preferable to duplicate sends to a
    // prospect, and is recoverable by manually clearing sent_at.
    $claimStmt = $pdo->prepare(
        "UPDATE outreach_leads SET sent_at = NOW() WHERE id = ? AND sent_at IS NULL"
    );
    $claimStmt->execute([$id]);
    if ($claimStmt->rowCount() === 0) {
        log_activity($pdo, $id, 'email_skipped_already_sent', 'Skipped send: lead already sent (race-condition guard)');
        $reason = 'already_sent';
        return false;
    }

    // Skip if this email is on the suppression list. Release the claim so the
    // lead's state isn't misleadingly "sent" when nothing actually went out.
    if (!empty($email)) {
        $suppStmt = $pdo->prepare("SELECT 1 FROM email_suppressions WHERE email = ? AND context = 'outreach' LIMIT 1");
        $suppStmt->execute([strtolower(trim($email))]);
        if ($suppStmt->fetchColumn()) {
            $pdo->prepare("UPDATE outreach_leads SET sent_at = NULL WHERE id = ?")->execute([$id]);
            log_activity($pdo, $id, 'email_skipped_suppressed', 'Skipped send: email is on outreach suppression list (' . $email . ')');
            $reason = 'suppressed';
            return false;
        }
    }

    // Generate and persist an unsubscribe token if we don't have one yet
    $unsubscribeToken = $lead['unsubscribe_token'] ?? null;
    if (empty($unsubscribeToken)) {
        $unsubscribeToken = bin2hex(random_bytes(32));
        $tokStmt = $pdo->prepare("UPDATE outreach_leads SET unsubscribe_token = ? WHERE id = ?");
        $tokStmt->execute([$unsubscribeToken, $id]);
    }

    // Build the per-lead tracking URL. If the lead was assigned to an A/B
    // variant, append -v{variantId} so clicks can be attributed per variant.
    $sourceCode = 'outreach-' . $id;
    $variantId = isset($lead['ab_variant_id']) && $lead['ab_variant_id'] !== null && $lead['ab_variant_id'] !== ''
        ? (int) $lead['ab_variant_id']
        : null;
    if ($variantId) {
        $sourceCode .= '-v' . $variantId;
    }
    $trackedUrl = 'https://argorobots.com/?source=' . $sourceCode;
    $unsubUrl = 'https://argorobots.com/unsubscribe?t=' . $unsubscribeToken;

    // Send-side A/B variants (sender, preheader, format). Look up first since
    // format affects how the body is rendered below.
    $fromName = 'Argo Books';
    $preheader = null;
    $format = 'html';
    if ($variantId) {
        $vStmt = $pdo->prepare("SELECT v.content, t.variant_type
            FROM outreach_ab_variants v
            JOIN outreach_ab_tests t ON t.id = v.test_id
            WHERE v.id = ?");
        $vStmt->execute([$variantId]);
        $vRow = $vStmt->fetch();
        if ($vRow && trim((string) $vRow['content']) !== '') {
            $vContent = trim((string) $vRow['content']);
            if ($vRow['variant_type'] === 'sender') {
                // Strip CR/LF defensively. PHPMailer sanitizes headers, but
                // the mail() fallback path concatenates $fromName into the
                // From: header verbatim — a stray newline could enable
                // header injection if a variant's content was malformed.
                $fromName = preg_replace('/[\r\n]+/', ' ', $vContent);
            } elseif ($vRow['variant_type'] === 'preheader') {
                $preheader = $vContent;
            } elseif ($vRow['variant_type'] === 'format') {
                $format = ($vContent === 'plain') ? 'plain' : 'html';
            }
        }
    }

    if ($format === 'plain') {
        // Plain text: keep URLs bare so they remain clickable in plain-text
        // clients while still carrying the tracking source param. No HTML
        // escaping, no <a> wrapping.
        $body = (string) $lead['draft_body'];
        $body = preg_replace('#https?://argorobots\.com/?(?![\w?/])#', $trackedUrl, $body);
        $body = str_replace('{UNSUBSCRIBE_URL}', $unsubUrl, $body);
        if (strpos($body, 'unsubscribe?t=') === false) {
            $unsubLine = "\n\nNot interested? " . $unsubUrl . " and I'll stop emailing you.";
            $replaced = preg_replace('#(\nAll the best)#i', $unsubLine . "\n$1", $body, 1);
            if ($replaced !== null && strpos($replaced, 'unsubscribe?t=') !== false) {
                $body = $replaced;
            } else {
                $body .= $unsubLine;
            }
        }
        $finalBody = $body;
    } else {
        $anchorHtml = '<a href="' . htmlspecialchars($trackedUrl) . '" style="color:#3b82f6;text-decoration:underline">argorobots.com</a>';
        $escapedBody = htmlspecialchars($lead['draft_body']);
        $escapedBody = preg_replace('#https?://argorobots\.com/?(?![\w?/])#', $anchorHtml, $escapedBody);

        $unsubAnchor = '<a href="' . htmlspecialchars($unsubUrl) . '" style="color:#6b7280;text-decoration:underline">unsubscribe</a>';
        // Drafts since the unsubscribe-URL fix carry the real bare URL in
        // their body. Wrap any occurrences in the styled "unsubscribe" anchor.
        $escapedBody = preg_replace(
            '#https?://argorobots\.com/unsubscribe\?t=[a-f0-9]+#i',
            $unsubAnchor,
            $escapedBody
        );
        // Defensive: also handle the legacy {UNSUBSCRIBE_URL} placeholder for
        // any drafts created before the substitution moved to draft time.
        $escapedBody = str_replace('{UNSUBSCRIBE_URL}', $unsubAnchor, $escapedBody);

        if (strpos($escapedBody, 'unsubscribe?t=') === false) {
            $unsubLine = "\n\n<span style=\"color:#9ca3af;font-size:13px\">Not interested? " . $unsubAnchor . " and I'll stop emailing you.</span>";
            $replaced = preg_replace('#(\nAll the best)#i', $unsubLine . "\n$1", $escapedBody, 1);
            if ($replaced !== null && strpos($replaced, 'unsubscribe?t=') !== false) {
                $escapedBody = $replaced;
            } else {
                $escapedBody .= $unsubLine;
            }
        }

        $finalBody = '<p>' . nl2br($escapedBody) . '</p>';
    }

    $messageId = null;
    $result = send_styled_email(
        $email,
        $lead['draft_subject'],
        $finalBody,
        '',
        'contact@argorobots.com',
        $fromName,
        'contact@argorobots.com',
        [],
        $preheader,
        $format,
        $messageId
    );

    if ($result) {
        // sent_at was already set by the upfront claim. Update the rest of
        // the post-send fields here. The COALESCE on original_message_id and
        // next_followup_due_at means a re-send (manual resend or retry after
        // a transient failure) won't overwrite the original Message-ID or
        // push out the follow-up schedule.
        $stmt = $pdo->prepare("UPDATE outreach_leads SET
            status = CASE WHEN status NOT IN ('replied','interested','not_interested','onboarded','email_bounced') THEN 'contacted' ELSE status END,
            first_contact_date = COALESCE(first_contact_date, NOW()),
            last_contact_date = NOW(),
            original_message_id = COALESCE(original_message_id, ?),
            next_followup_due_at = COALESCE(next_followup_due_at, DATE_ADD(NOW(), INTERVAL 5 DAY))
            WHERE id = ?");
        $stmt->execute([$messageId, $id]);
        $reason = 'sent';
        return true;
    }

    // SMTP send failed — release the claim so retries / manual re-send work.
    $pdo->prepare("UPDATE outreach_leads SET sent_at = NULL WHERE id = ?")->execute([$id]);
    $reason = 'smtp_failed';
    return false;
}

/**
 * Send a follow-up email to a lead that was contacted but didn't reply.
 * Threaded as a Re: reply to the original via In-Reply-To / References,
 * so it lands in the recipient's existing inbox conversation rather than
 * arriving as a fresh cold email.
 *
 * Eligibility (status='contacted', followup_count=0, due-date passed) is
 * enforced via an atomic UPDATE-to-claim pattern so concurrent cron runs
 * can't double-send. Returns true on successful delivery, false otherwise.
 *
 * The optional &$reason out-param disambiguates non-success outcomes:
 *   'sent'         — follow-up delivered
 *   'not_eligible' — atomic claim lost (already followed up, replied,
 *                    not yet due, or claimed by a concurrent run)
 *   'invalid_email'— lead's email is missing or malformed
 *   'smtp_failed'  — SMTP/transport failure (the only "real failure" reason)
 */
function send_outreach_followup($pdo, array $lead, ?string &$reason = null): bool
{
    require_once __DIR__ . '/followup_template.php';

    $id = (int) $lead['id'];
    $email = trim((string) $lead['email']);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reason = 'invalid_email';
        return false;
    }

    // Atomically claim the slot. Re-checks every eligibility predicate
    // server-side, so two overlapping cron runs can't both send a follow-up
    // for the same lead. The pipeline lock-file already serializes runs in
    // practice, but be defensive.
    $claim = $pdo->prepare("UPDATE outreach_leads
        SET last_followup_at = NOW(), followup_count = followup_count + 1
        WHERE id = ? AND followup_count = 0
          AND status = 'contacted'
          AND next_followup_due_at IS NOT NULL
          AND next_followup_due_at <= NOW()");
    $claim->execute([$id]);
    if ($claim->rowCount() === 0) {
        $reason = 'not_eligible';
        return false; // already followed up, replied, not due, or race-lost
    }

    // Generate or reuse the unsubscribe token (same pattern as first send).
    $unsubscribeToken = $lead['unsubscribe_token'] ?? null;
    if (empty($unsubscribeToken)) {
        $unsubscribeToken = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE outreach_leads SET unsubscribe_token = ? WHERE id = ?")
            ->execute([$unsubscribeToken, $id]);
    }
    $unsubUrl = 'https://argorobots.com/unsubscribe?t=' . $unsubscribeToken;

    $followup = build_followup_email($lead, $unsubUrl);

    // Threading headers: makes the follow-up land in the recipient's existing
    // thread instead of arriving as a fresh cold email. Without these,
    // reply-rate gains from following up are roughly halved.
    $threadingHeaders = [];
    $origMsgId = trim((string) ($lead['original_message_id'] ?? ''));
    if ($origMsgId !== '') {
        $threadingHeaders['In-Reply-To'] = $origMsgId;
        $threadingHeaders['References']  = $origMsgId;
    }

    // Render body as HTML — same anchor-wrapping as the first-touch HTML
    // branch so the bare argorobots.com mention and the unsubscribe URL get
    // styled links instead of plain text.
    $trackedUrl = 'https://argorobots.com/?source=outreach-' . $id . '-fu1';
    $anchorHtml = '<a href="' . htmlspecialchars($trackedUrl) . '" style="color:#3b82f6;text-decoration:underline">argorobots.com</a>';
    $unsubAnchor = '<a href="' . htmlspecialchars($unsubUrl) . '" style="color:#6b7280;text-decoration:underline">unsubscribe</a>';
    $escaped = htmlspecialchars($followup['body']);
    $escaped = preg_replace('#https?://argorobots\.com/?(?![\w?/])#', $anchorHtml, $escaped);
    $escaped = preg_replace('#https?://argorobots\.com/unsubscribe\?t=[a-f0-9]+#i', $unsubAnchor, $escaped);
    $finalBody = '<p>' . nl2br($escaped) . '</p>';

    $messageId = null;
    $result = send_styled_email(
        $email,
        $followup['subject'],
        $finalBody,
        '',
        'contact@argorobots.com',
        'Evan',
        'contact@argorobots.com',
        $threadingHeaders,
        $followup['preheader'],
        'html',
        $messageId
    );

    if ($result) {
        // Update last_contact_date for timeline accuracy. followup_count and
        // last_followup_at were already updated by the claim above.
        $pdo->prepare("UPDATE outreach_leads SET last_contact_date = NOW() WHERE id = ?")->execute([$id]);
        log_activity($pdo, $id, 'followup_sent', 'Follow-up #1 delivered');
        $reason = 'sent';
        return true;
    }

    // Send failed: roll back the claim so retries work next cron run.
    $pdo->prepare("UPDATE outreach_leads
        SET last_followup_at = NULL, followup_count = followup_count - 1
        WHERE id = ?")->execute([$id]);
    $reason = 'smtp_failed';
    return false;
}

// ─── Email Scraping Helper ───

function scrape_email_from_website($url)
{
    if (empty($url)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $falsePositives = ['example.com', 'sentry.io', 'wixpress.com', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'gravatar.com'];

    // Clean an extracted email: decode URL encoding, strip non-ASCII and whitespace
    $cleanEmail = function($email) {
        $email = urldecode($email);
        // Strip any non-ASCII characters (emojis, special chars, zero-width spaces, etc.)
        $email = preg_replace('/[^\x20-\x7E]/', '', $email);
        $email = trim($email);
        // Validate it still looks like an email after cleaning
        if (preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email)) {
            return $email;
        }
        return null;
    };

    // Helper to extract email from HTML
    $extractEmail = function($html) use ($falsePositives, $cleanEmail) {
        // URL-decode the HTML so mailto:%20info@... becomes mailto: info@...
        $decodedHtml = urldecode($html);

        // Look for mailto: links first (most reliable)
        if (preg_match_all('/mailto:\s*([^\s"\'<>]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $decodedHtml, $matches)) {
            foreach ($matches[1] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $dominated = false;
                foreach ($falsePositives as $fp) { if (str_contains(strtolower($email), $fp)) { $dominated = true; break; } }
                if (!$dominated) return $email;
            }
        }
        // Fallback: email patterns in text (strip HTML tags first to avoid matching attributes)
        $text = strip_tags($decodedHtml);
        // Remove common non-ASCII clutter (emojis, zero-width chars) before matching
        $text = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $text);
        if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            foreach ($matches[0] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $dominated = false;
                foreach ($falsePositives as $fp) { if (str_contains(strtolower($email), $fp)) { $dominated = true; break; } }
                if (!$dominated) return $email;
            }
        }
        return null;
    };

    // Try homepage first
    $html = @file_get_contents($url, false, $context);
    if ($html) {
        $email = $extractEmail($html);
        if ($email) return $email;

        // Find contact page links in the HTML
        // Parse base URL properly for resolving relative links
        $parsed = parse_url($url);
        $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
        $basePath = rtrim($url, '/');
        $contactPaths = [];

        // Match all <a> tags - check both href path AND link text for contact-related keywords
        $contactKeywords = 'contact|about|about-us|contact-us|connect|get-in-touch|reach-us|reach out';
        if (preg_match_all('/<a\s[^>]*href=["\']([^"\'#][^"\']*)["\'][^>]*>(.*?)<\/a>/is', $html, $linkMatches, PREG_SET_ORDER)) {
            foreach ($linkMatches as $m) {
                $href = $m[1];
                $text = strip_tags($m[2]);
                // Match if href OR link text contains contact keywords
                if (!preg_match('/' . $contactKeywords . '/i', $href) && !preg_match('/' . $contactKeywords . '/i', $text)) continue;
                // Skip mailto/tel/javascript
                if (preg_match('/^(mailto:|tel:|javascript:)/i', $href)) continue;

                // Resolve relative URLs
                if (str_starts_with($href, 'http')) {
                    $contactPaths[] = $href;
                } elseif (str_starts_with($href, '/')) {
                    $contactPaths[] = $origin . $href;
                } else {
                    $contactPaths[] = $basePath . '/' . $href;
                }
            }
        }

        // Fallback: try common paths if none found in links
        if (empty($contactPaths)) {
            $contactPaths = [
                $basePath . '/contact',
                $basePath . '/contact-us',
                $basePath . '/about',
            ];
        }

        // Try each contact page
        foreach (array_unique(array_slice($contactPaths, 0, 3)) as $contactUrl) {
            $contactHtml = @file_get_contents($contactUrl, false, $context);
            if ($contactHtml) {
                $email = $extractEmail($contactHtml);
                if ($email) return $email;
            }
        }
    }

    return null;
}

// ─── Business Discovery (Google Places API) ───

/**
 * Core business search logic. Returns array with 'businesses', 'count', 'rounds'.
 * Used by both the admin API endpoint and the cron pipeline.
 */
function search_businesses_core($city, $province, $category, $limit, $apiKey, $excludePlaceIds = [], $maxRounds = 5)
{
    $location = $province ? "$city, $province" : $city;
    $businesses = [];
    $seenPlaceIds = [];
    // Pre-seed seen IDs so we skip businesses already known
    foreach ($excludePlaceIds as $id) {
        $seenPlaceIds[trim($id)] = true;
    }
    $roundsUsed = 0;

    // Stream context with timeouts for all Google API calls
    $httpContext = stream_context_create(['http' => [
        'timeout' => 10,
        'ignore_errors' => true,
    ]]);

    // Build query variations to search across multiple rounds
    $queries = [];
    if ($category) {
        $queries[] = "$category in $location";
        $queries[] = "$category near $location";
        $queries[] = "$category services in $location";
        $queries[] = "$category companies in $location";
        $queries[] = "best $category in $location";
    } else {
        // When no category provided (admin dashboard searches), pick random
        // categories from the shared pool so each round searches a different industry
        $categoryPool = OUTREACH_CATEGORY_POOL;
        shuffle($categoryPool);
        for ($i = 0; $i < $maxRounds; $i++) {
            $queries[] = $categoryPool[$i] . " in $location";
        }
    }

    // Track which pool category was searched per round (for labeling when no category provided)
    $queryCategories = [];
    if (!$category) {
        foreach ($queries as $q) {
            $queryCategories[] = ucwords(str_replace(" in $location", '', $q));
        }
    }

    // Map category keywords to Google Places types for more targeted results
    $placeTypeMap = [
        'restaurant' => 'restaurant', 'plumber' => 'plumber',
        'electrician' => 'electrician', 'dentist' => 'dentist',
        'lawyer' => 'lawyer', 'accountant' => 'accounting',
        'gym' => 'gym', 'salon' => 'hair_care', 'veterinarian' => 'veterinary_care',
        'pharmacy' => 'pharmacy', 'car dealership' => 'car_dealer',
        'bakery' => 'bakery', 'cafe' => 'cafe', 'coffee' => 'cafe',
        'spa' => 'spa', 'florist' => 'florist', 'pet store' => 'pet_store',
        'furniture' => 'furniture_store', 'jewelry' => 'jewelry_store',
        'hardware' => 'hardware_store', 'barber' => 'hair_care',
        'locksmith' => 'locksmith', 'storage' => 'storage',
        'travel agenc' => 'travel_agency', 'insurance' => 'insurance_agency',
        'real estate' => 'real_estate_agency',
    ];

    for ($round = 0; $round < $maxRounds && count($businesses) < $limit; $round++) {
        $query = $queries[$round] ?? null;
        if (!$query) break;
        $countBefore = count($businesses);
        $roundsUsed++;

        // Initial search for this round
        $params = ['query' => $query, 'key' => $apiKey];
        // Try to match a Google Places type from the query for better results
        foreach ($placeTypeMap as $keyword => $type) {
            if (stripos($query, $keyword) !== false) {
                $params['type'] = $type;
                break;
            }
        }
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query($params);

        $resp = @file_get_contents($url, false, $httpContext);
        if ($resp === false) {
            if ($roundsUsed === 1) {
                return ['error' => 'Failed to connect to Google Places API', 'businesses' => [], 'count' => 0, 'rounds' => 0];
            }
            break;
        }

        $data = json_decode($resp, true);
        $status = $data['status'] ?? '';
        if ($status !== 'OK' && $status !== 'ZERO_RESULTS') {
            if ($roundsUsed === 1) {
                $errorMsg = $data['error_message'] ?? $status ?? 'Unknown error';
                return ['error' => 'Google Places API error: ' . $errorMsg, 'businesses' => [], 'count' => 0, 'rounds' => 0];
            }
            break;
        }

        $candidates = $data['results'] ?? [];
        $nextPageToken = $data['next_page_token'] ?? null;
        $maxPages = 3;
        $pagesUsed = 1;

        // Process candidates from this round, paging through Google results
        while (count($businesses) < $limit) {
            foreach ($candidates as $place) {
                if (count($businesses) >= $limit) break;

                $placeId = $place['place_id'] ?? '';
                // Skip duplicates across rounds
                if ($placeId && isset($seenPlaceIds[$placeId])) continue;
                if ($placeId) $seenPlaceIds[$placeId] = true;

                $business = [
                    'places_id' => $placeId,
                    'business_name' => $place['name'] ?? '',
                    'address' => $place['formatted_address'] ?? '',
                    'category' => $category ?: ($queryCategories[$round] ?? (isset($place['types'][0]) ? ucfirst(str_replace('_', ' ', $place['types'][0])) : '')),
                    'city' => $city,
                    'phone' => null,
                    'website' => null,
                    'email' => null,
                ];

                // Fetch place details for phone and website
                if (!empty($placeId)) {
                    $detailUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                        'place_id' => $placeId,
                        'fields' => 'formatted_phone_number,website,url',
                        'key' => $apiKey,
                    ]);
                    $detailResp = @file_get_contents($detailUrl, false, $httpContext);
                    if ($detailResp) {
                        $detail = json_decode($detailResp, true);
                        $r = $detail['result'] ?? [];
                        $business['phone'] = $r['formatted_phone_number'] ?? null;
                        $business['website'] = $r['website'] ?? null;
                        $business['contact_page_url'] = $r['url'] ?? null;
                    }
                }

                // Skip businesses without a website
                if (empty($business['website'])) continue;

                // Scrape email from business website and validate
                $business['email'] = scrape_email_from_website($business['website']);

                // Skip businesses where we couldn't find a valid email
                if (empty($business['email']) || !filter_var($business['email'], FILTER_VALIDATE_EMAIL)) {
                    $business['email'] = null;
                    continue;
                }

                $businesses[] = $business;
            }

            // If we have enough or no more pages, stop paging
            if (count($businesses) >= $limit || empty($nextPageToken) || $pagesUsed >= $maxPages) break;

            // Google requires a short delay before next_page_token is valid
            sleep(2);

            $nextUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
                'pagetoken' => $nextPageToken,
                'key' => $apiKey,
            ]);
            $nextResp = @file_get_contents($nextUrl, false, $httpContext);
            if (!$nextResp) break;

            $nextData = json_decode($nextResp, true);
            if (($nextData['status'] ?? '') !== 'OK') break;

            $candidates = $nextData['results'] ?? [];
            $nextPageToken = $nextData['next_page_token'] ?? null;
            $pagesUsed++;
        }

        // Bail early if this round produced too few new results (diminishing returns)
        $newThisRound = count($businesses) - $countBefore;
        if ($newThisRound < 2 && $round > 0) {
            break;
        }
    }

    return ['businesses' => $businesses, 'count' => count($businesses), 'rounds' => $roundsUsed];
}

// ─── Gemini Call ───

function call_gemini($systemPrompt, $userPrompt)
{
    $apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    if (empty($apiKey)) {
        return ['error' => 'Gemini API key not configured'];
    }

    $model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';

    $payload = [
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 2000,
        ],
    ];
    if (!empty($systemPrompt)) {
        $payload['system_instruction'] = ['parts' => [['text' => $systemPrompt]]];
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'Gemini request failed';
        return ['error' => $errorMsg];
    }

    $result = json_decode($response, true);
    return ['content' => $result['candidates'][0]['content']['parts'][0]['text'] ?? ''];
}

// ─── Business Summarization ───

function summarize_business($website)
{
    if (empty($website)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $html = @file_get_contents($website, false, $context);
    if (!$html) return null;

    // Strip scripts, styles, and tags to get readable text
    $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
    $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $text);
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim(mb_substr($text, 0, 3000)); // Cap at 3000 chars

    if (strlen($text) < 50) return null;

    $result = call_gemini(
        "You summarize businesses based on their website content. Respond with ONLY a concise summary (3-5 sentences) covering:
1. What specific services or products they offer
2. Who their typical customers are
3. How they likely handle billing (e.g. do they invoice clients, do project quotes, charge hourly, sell products, etc.)
4. Any pain points a simple bookkeeping/invoicing tool could solve for them (e.g. tracking job expenses, sending invoices, managing payments)
Be specific and factual based on the website content. Do not include any other text or preamble.",
        "Website content from $website:\n\n$text"
    );

    return $result['content'] ?? null;
}

// ─── Draft Generation ───

/**
 * Generate an AI email draft for a lead. Saves draft to DB.
 * Returns ['success' => true, 'subject' => ..., 'body' => ...] or ['error' => ...].
 */
function generate_draft_for_lead($pdo, $lead)
{
    $id = $lead['id'];

    // A/B variant lookup must happen before the summary block so a
    // personalization test can gate the AI summary call entirely.
    // Only one test can be active at a time across the whole framework
    // (enforced by the activation handler), so a single SELECT finds it.
    //
    // If the lead is already assigned to the currently-active test (e.g.
    // admin clicked "Regenerate Draft" after the email went out), keep the
    // existing variant. Re-running pick_ab_variant on a regenerate would
    // skew round-robin balance and orphan any clicks already tracked under
    // the lead's previous "?source=outreach-{id}-v{old}" URL.
    $abTestId = null;
    $abVariantId = null;
    $abSubjectOverride = '';
    $abBodyOverride = '';
    $abCtaOverride = '';
    $personalizationOff = false;
    $abSubjectDirectiveText = null; // captured for the post-generation echo check
    $existingAbTestId = isset($lead['ab_test_id']) ? (int) $lead['ab_test_id'] : 0;
    $existingAbVariantId = isset($lead['ab_variant_id']) ? (int) $lead['ab_variant_id'] : 0;
    $active = get_single_active_ab_test($pdo);
    if ($active) {
        $activeTestId = (int) $active['test']['id'];
        $eligibleType = (string) $active['test']['variant_type'];

        $variant = null;
        if ($existingAbTestId === $activeTestId && $existingAbVariantId > 0) {
            foreach ($active['variants'] as $candidate) {
                if ((int) $candidate['id'] === $existingAbVariantId) {
                    $variant = $candidate;
                    break;
                }
            }
        }
        if ($variant === null) {
            $variant = pick_ab_variant($pdo, $active['test'], $active['variants'], $lead);
        }

        $abTestId = $activeTestId;
        $abVariantId = (int) $variant['id'];
        $instruction = ab_instruction_for_variant($variant, $eligibleType);
        if ($eligibleType === 'subject') {
            $abSubjectOverride = $instruction;
            $parsedVariant = ab_parse_variant_content($variant);
            if ($parsedVariant['mode'] === 'directive') {
                $abSubjectDirectiveText = $parsedVariant['text'];
            }
        }
        elseif ($eligibleType === 'body') $abBodyOverride = $instruction;
        elseif ($eligibleType === 'cta') $abCtaOverride = $instruction;
        elseif ($eligibleType === 'personalization') {
            $personalizationOff = (trim((string) $variant['content']) === 'off');
        }
        // sender / preheader / format: assignment alone is what matters;
        // their dispatched instruction is empty and they apply at send time.
    }

    // Generate a business summary if we don't have one yet — unless the lead
    // is in the 'off' arm of an active personalization test, in which case we
    // skip the AI call entirely. If a stored summary exists from before
    // the test started, mask it for this draft so the prompt truly operates
    // without personalization.
    $summary = $lead['business_summary'] ?? null;
    if ($personalizationOff) {
        $summary = null;
    } elseif (empty($summary) && !empty($lead['website'])) {
        $summary = summarize_business($lead['website']);
        if ($summary) {
            $stmt = $pdo->prepare("UPDATE outreach_leads SET business_summary = ? WHERE id = ?");
            $stmt->execute([$summary, $id]);
        }
    }

    $isLocal = false;
    $city = strtolower(trim($lead['city'] ?? ''));
    $province = strtolower(trim($lead['province'] ?? ''));
    if ($province === 'saskatchewan' || $province === 'sk' || in_array($city, ['saskatoon','regina','prince albert','moose jaw','swift current','yorkton','north battleford','estevan','weyburn','martensville','warman','humboldt','melfort','meadow lake','lloydminster'])) {
        $isLocal = true;
    }

    $isSaskatoon = in_array($city, ['saskatoon','martensville','warman']);

    $localInstruction = $isLocal
        ? "- The business is in Saskatchewan. Evan is a local Saskatchewan software developer based in Saskatoon. ALWAYS mention being local, e.g. \"I'm a local Saskatoon software developer\" or \"As a fellow Saskatchewan business\". This local connection is important, make it feel personal."
        : "- The business is outside Saskatchewan. Evan is a Canadian software developer. Say \"Canadian software developer\", do NOT say \"local\" and do NOT mention Saskatoon or Saskatchewan.";

    $inPersonInstruction = $isSaskatoon
        ? "\n- IMPORTANT: Since this business is in the Saskatoon area, you MUST include an offer for an in-person visit to help them get set up. Work it in naturally, e.g. \"Since I'm right here in Saskatoon, I'd be happy to stop by and help you get set up in person\" or \"I could even swing by to walk you through it\". This is a key selling point for local businesses."
        : "\n- Do NOT mention any in-person visits or stopping by. The business is not in Saskatoon.";

    // Pull industry-level pain points for this category. These are typical
    // day-to-day headaches for the trade, NOT claims about this business.
    $painPoints = get_category_pain_points($lead['category'] ?? '');
    $painPointsList = '';
    foreach ($painPoints as $pp) {
        $painPointsList .= "  * " . $pp . "\n";
    }
    $categoryLabel = !empty($lead['category']) ? $lead['category'] : 'this industry';
    $painPointsInstruction = "\n- Small businesses in the '" . $categoryLabel . "' category commonly deal with things like:\n"
        . $painPointsList
        . "You MAY gently allude to ONE of these as something Argo Books can help with, phrased as a general industry pattern (e.g. \"businesses like yours often deal with X\"), NEVER as an assertion about this specific business. Pick at most one. If none fit naturally, skip them entirely.";

    $systemPrompt = "You are helping write a brief, personal outreach email from Evan, the developer behind Argo Books, to a small business. The goal is to get honest product feedback on Argo Books, a bookkeeping and invoicing app for small businesses.

About Argo Books:
- It is a simple bookkeeping and invoicing app designed so you do not need any accounting knowledge at all
- Built specifically for small businesses, not a bloated enterprise tool
- Features include invoicing, expense tracking, and simple bookkeeping
- Evan is " . ($isLocal ? "a local independent software developer based in Saskatoon" : "a Canadian independent software developer") . " building this specifically for small businesses

Rules:
- Keep it very short (2-3 short paragraphs max, under 100 words ideally)
- Sound human, friendly, and genuine, not like marketing spam
$localInstruction
$inPersonInstruction
- Do NOT refer to a \"team\", Evan is a solo developer
- Get to the point quickly in the first sentence - say why you are emailing. Do NOT open with generic filler like \"I hope this message finds you well\" or vague flattery like \"I admire your work\"
- Use the business name in the greeting (e.g. \"Hi LVM Landscaping\" or \"Hi [business name]\" if available)

PERSONALIZATION (this is critical):
- You may reference the business's industry/category to explain why Argo Books could be useful (e.g. \"running a landscaping business usually means a lot of invoicing\")
- NEVER claim to know specific details about how the business operates, what tools they use, what payment methods they accept, or how they handle their finances. You do NOT know these things. Do NOT say things like \"I know you handle donations\" or \"I see you use e-transfers\" or \"I noticed you do quotes\". This comes across as creepy and dishonest
- Instead of asserting facts about their business, use general industry knowledge. Say things like \"businesses like yours often deal with...\" or \"in the [industry] space, invoicing can be a hassle\" rather than \"I know you do X\"
- Only reference Argo Books features that are relevant to their general industry. Do not list every feature
- If a business summary is provided, use it ONLY to understand their industry and tailor which Argo features to mention. Do NOT parrot back details from the summary as if you personally know about their business
- If no summary is available, keep it more general but still mention their industry/category if known
$painPointsInstruction

- Briefly describe Argo Books as a simple bookkeeping and invoicing app that requires no accounting knowledge. Do NOT just say \"check it out\" without explaining what it is
- Mention you are looking for honest feedback from small business owners
- Mention offering a free 1-year premium license in exchange for feedback$abCtaOverride
- Use a casual but professional tone
- NEVER use placeholders like [Your Name], [Your Title], [Your Company], etc.
- ALWAYS include the website link https://argorobots.com/ in the email body. This is required in every single email, no exceptions
- NEVER use em dashes in the email. Use commas, periods, or regular hyphens instead
- The subject line should sound like a short personal email one human would send to one specific business owner — NOT marketing copy. Keep it under 7 words, reference the recipient's business if natural, and avoid clickbait hooks like \"A surprising way to...\", \"The secret to...\", \"How [business] can...\", or anything that pattern-matches as a sales template. Lowercase is fine. Good examples: \"Quick question about [business name]\", \"Thought of you guys\", \"feedback on Argo Books?\", \"[business name] — bookkeeping question\". Bad examples: \"A surprising way to save time for [business]\", \"Unlock growth for [business]\", \"Transform your bookkeeping today\"$abSubjectOverride
- You MUST include the line \"You can check it out here: https://argorobots.com/\" (or similar natural phrasing with that exact URL) somewhere in the email body, ideally after mentioning what Argo Books is
- End the email body with a line like \"Feel free to reply to this email if you have any questions!\" or similar, before the sign-off
- After that line, add ONE short, respectful unsubscribe line on its own paragraph, such as: \"Not interested? {UNSUBSCRIBE_URL} and I'll stop emailing you.\" The literal token {UNSUBSCRIBE_URL} will be replaced with a tracked unsubscribe link before sending — include it verbatim, do NOT invent or replace the placeholder yourself. Keep the tone soft, brief, and non-pushy.
- Always sign off with three separate lines: \"All the best,\" then \"Evan\" then \"Argo Books\" (each on its own line, separated by \\n)$abBodyOverride

Return your response as JSON with two fields:
{\"subject\": \"the email subject line\", \"body\": \"the email body text (plain text, use \\n for line breaks)\"}

Return ONLY the JSON, no other text.";

    $details = "Business: {$lead['business_name']}";
    if ($lead['category']) $details .= "\nCategory/Industry: {$lead['category']}";
    if ($lead['city']) $details .= "\nCity: {$lead['city']}";
    if ($isLocal) $details .= "\nLocal: Yes, this business is in Saskatchewan (same province as Evan)";
    if ($lead['website']) $details .= "\nWebsite: {$lead['website']}";
    if ($lead['contact_name']) $details .= "\nContact person: {$lead['contact_name']}";
    if ($summary) $details .= "\nBusiness summary: $summary";

    $result = call_gemini($systemPrompt, $details);

    if (isset($result['error'])) {
        return ['error' => $result['error']];
    }

    // Parse JSON response from AI
    $content = trim($result['content']);
    // Strip markdown code fences if present
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $parsed = json_decode($content, true);
    if (!$parsed || !isset($parsed['subject']) || !isset($parsed['body'])) {
        // AI returned invalid JSON — save with needs_review so it won't be auto-approved
        $fallbackSubject = "Quick question for {$lead['business_name']}";
        $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, ab_test_id = ?, ab_variant_id = ?, drafted_at = NOW(), approval_status = 'needs_review' WHERE id = ?");
        $stmt->execute([$fallbackSubject, $content, $abTestId, $abVariantId, $id]);

        return ['success' => true, 'needs_review' => true, 'subject' => $fallbackSubject, 'body' => $content];
    }

    // Ensure the website URL is in the body — inject before sign-off if AI omitted it
    if (stripos($parsed['body'], 'argorobots.com') === false) {
        $parsed['body'] = preg_replace(
            '/(Feel free to|Don\'t hesitate|Let me know|Reply to this)/i',
            "You can check it out here: https://argorobots.com/\n\n$1",
            $parsed['body'],
            1
        );
        // If regex didn't match, append before sign-off
        if (stripos($parsed['body'], 'argorobots.com') === false) {
            $parsed['body'] = preg_replace(
                '/(\nAll the best)/i',
                "\n\nYou can check it out here: https://argorobots.com/\n$1",
                $parsed['body'],
                1
            );
        }

        // Final fallback: if URL is still missing, append it at the end
        if (stripos($parsed['body'], 'argorobots.com') === false) {
            $parsed['body'] .= "\n\nYou can check it out here: https://argorobots.com/";
        }
    }

    // Defensive check: the AI is told a subject directive describes a STYLE
    // and not to copy it, but it occasionally echoes the directive verbatim
    // anyway. Catch the obvious failures and hold the draft for review
    // instead of auto-sending an email whose subject is literally the prompt.
    $needsReviewReason = null;
    if ($abSubjectDirectiveText !== null) {
        $genNorm = strtolower(rtrim(trim($parsed['subject']), '.!?'));
        $dirNorm = strtolower(rtrim(trim($abSubjectDirectiveText), '.!?'));
        if ($genNorm !== '' && $dirNorm !== '' && ($genNorm === $dirNorm || strpos($genNorm, $dirNorm) !== false)) {
            $needsReviewReason = 'AI returned the subject directive verbatim instead of generating a subject in that style.';
        }
    }

    // Substitute the {UNSUBSCRIBE_URL} placeholder with the real per-lead URL
    // now (was previously deferred to send time). Saving the real URL means
    // the admin sees the actual link when reviewing the draft instead of the
    // raw placeholder. The send-side still handles the placeholder defensively
    // for any old drafts that pre-date this change.
    $unsubscribeToken = $lead['unsubscribe_token'] ?? null;
    if (empty($unsubscribeToken)) {
        $unsubscribeToken = bin2hex(random_bytes(32));
        $tokStmt = $pdo->prepare("UPDATE outreach_leads SET unsubscribe_token = ? WHERE id = ?");
        $tokStmt->execute([$unsubscribeToken, $id]);
    }
    $unsubUrl = 'https://argorobots.com/unsubscribe?t=' . $unsubscribeToken;
    $parsed['body'] = str_replace('{UNSUBSCRIBE_URL}', $unsubUrl, $parsed['body']);

    // Save draft to lead. If the directive-echo check tripped, mark the
    // draft as needs_review so the auto-approve step skips it and the admin
    // sees the issue before anything goes out.
    if ($needsReviewReason !== null) {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, ab_test_id = ?, ab_variant_id = ?, drafted_at = NOW(), approval_status = 'needs_review', status = CASE WHEN status IN ('new','awaiting_approval','approved') THEN 'draft_generated' ELSE status END WHERE id = ?");
        $stmt->execute([$parsed['subject'], $parsed['body'], $abTestId, $abVariantId, $id]);
        log_activity($pdo, $id, 'draft_needs_review', $needsReviewReason);
    } else {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, ab_test_id = ?, ab_variant_id = ?, drafted_at = NOW(), status = CASE WHEN status IN ('new','awaiting_approval','approved') THEN 'draft_generated' ELSE status END WHERE id = ?");
        $stmt->execute([$parsed['subject'], $parsed['body'], $abTestId, $abVariantId, $id]);
    }

    return ['success' => true, 'subject' => $parsed['subject'], 'body' => $parsed['body'], 'ab_test_id' => $abTestId, 'ab_variant_id' => $abVariantId];
}

// ─── A/B Test Automation (called from stepManageAbTests in outreach_pipeline.php) ───

/**
 * Variant types the A/B framework knows about. The cron iterates over this
 * list when looking for active tests to promote.
 */
function ab_known_variant_types()
{
    return ['subject', 'body', 'sender', 'cta', 'preheader', 'format', 'personalization'];
}

/**
 * Evaluate the active test of a given variant type and promote a winner if
 * any exit criterion is met. Side effect: on trigger, UPDATE the test row to
 * completed with winner_variant_id set; optionally self-pauses automation if
 * the winning CTR is below the configured safety floor.
 *
 * Returns one of:
 *   ['action' => 'none', 'reason' => '...', 'variant_type' => $variantType, ...]
 *   ['action' => 'promoted', 'test_id' => N, 'variant_type' => $variantType, ...]
 *   ['action' => 'paused_safety', 'test_id' => N, 'variant_type' => $variantType, ...]
 *
 * Exit criteria (any one triggers promotion):
 *   a) leader significant at p<0.05 vs EVERY other variant AND every variant sent >= 30
 *   b) test age >= 14 days AND every variant sent >= 20
 *   c) test age >= 28 days (force-close; leader by CTR, ties by assigned then lowest id)
 */
function ab_check_and_promote_active_test($pdo, $variantType = 'subject')
{
    $active = get_active_ab_test($pdo, $variantType);
    if (!$active) {
        return ['action' => 'none', 'reason' => 'no_active_test', 'variant_type' => $variantType];
    }
    $test = $active['test'];

    $variants = load_variants_with_stats($pdo, (int) $test['id']);
    if (count($variants) < 2) {
        return ['action' => 'none', 'reason' => 'too_few_variants'];
    }

    // Pick scoring metric: reply rate is the real conversion signal we want
    // to optimize, but if no variant has any replies yet we fall back to
    // CTR so low-volume tests can still promote on something.
    $totalReplies = array_sum(array_column($variants, 'replied_count'));
    $metric = $totalReplies > 0 ? 'reply_rate' : 'ctr';

    $leaderIdx = find_leader_idx($variants, $metric);
    $startedAt = strtotime($test['started_at'] ?: $test['created_at']);
    $ageDays = (int) floor((time() - $startedAt) / 86400);
    $minSent = min(array_column($variants, 'sent_count'));

    $trigger = null;

    // Criterion (a) — leader is statistically significant on the chosen
    // metric vs every other variant
    if ($leaderIdx !== null && $minSent >= 30) {
        $allSig = true;
        foreach ($variants as $i => $v) {
            if ($i === $leaderIdx) continue;
            $c = confidence_vs_leader_on($metric, $variants[$leaderIdx], $v);
            if ($c['tag'] !== 'significant') { $allSig = false; break; }
        }
        if ($allSig) $trigger = 'significance';
    }

    // Criterion (b)
    if (!$trigger && $leaderIdx !== null && $ageDays >= 14 && $minSent >= 20) {
        $trigger = 'timebox';
    }

    // Criterion (c) — force-close even if nothing has been sent yet
    if (!$trigger && $ageDays >= 28) {
        $trigger = 'hard_timeout';
        if ($leaderIdx === null) {
            $leaderIdx = 0;
            foreach ($variants as $i => $v) {
                if ($v['assigned_count'] > $variants[$leaderIdx]['assigned_count']) {
                    $leaderIdx = $i;
                }
            }
        }
    }

    if (!$trigger) {
        return [
            'action' => 'none',
            'reason' => 'criteria_not_met',
            'variant_type' => $variantType,
            'age_days' => $ageDays,
            'min_sent' => $minSent,
            'metric' => $metric,
            'test_id' => (int) $test['id'],
            'test_name' => $test['name'],
        ];
    }

    $winner = $variants[$leaderIdx];
    $pdo->prepare("UPDATE outreach_ab_tests SET winner_variant_id = ?, status = 'completed', completed_at = NOW() WHERE id = ?")
        ->execute([$winner['id'], $test['id']]);

    // Safety-floor checks. CTR floor is always evaluated — it's a
    // deliverability signal (CTR below 1% suggests the email isn't even
    // reaching inboxes) regardless of which metric drove promotion. Reply
    // floor is only evaluated when we promoted on reply rate.
    $floorStmt = $pdo->prepare("SELECT state_key, state_value FROM outreach_pipeline_state WHERE state_key IN ('ab_ctr_floor','ab_reply_floor')");
    $floorStmt->execute();
    $floorRows = $floorStmt->fetchAll();
    $ctrFloor = 0.01;
    $replyFloor = 0.005;
    foreach ($floorRows as $row) {
        if ($row['state_key'] === 'ab_ctr_floor')   $ctrFloor   = (float) $row['state_value'];
        if ($row['state_key'] === 'ab_reply_floor') $replyFloor = (float) $row['state_value'];
    }

    $pausedForSafety = false;
    $pauseReason = null;
    if ($winner['sent_count'] >= 20) {
        if ($metric === 'reply_rate' && $winner['reply_rate'] < $replyFloor) {
            $pauseReason = 'Winner reply rate ' . number_format($winner['reply_rate'] * 100, 2)
                . '% below floor ' . number_format($replyFloor * 100, 2)
                . '% on test #' . (int) $test['id'];
        } elseif ($winner['ctr'] < $ctrFloor) {
            $pauseReason = 'Winner CTR ' . number_format($winner['ctr'] * 100, 2)
                . '% below floor ' . number_format($ctrFloor * 100, 2)
                . '% on test #' . (int) $test['id'];
        }
    }
    if ($pauseReason !== null) {
        $pauseStmt = $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)");
        $pauseStmt->execute(['ab_auto_enabled', '0']);
        $pauseStmt->execute(['ab_auto_last_pause_reason', $pauseReason]);
        $pausedForSafety = true;
    }

    return [
        'action' => $pausedForSafety ? 'paused_safety' : 'promoted',
        'variant_type' => $variantType,
        'test_id' => (int) $test['id'],
        'test_name' => $test['name'],
        'winner_id' => (int) $winner['id'],
        'winner_label' => $winner['label'],
        'winner_ctr' => $winner['ctr'],
        'winner_reply_rate' => $winner['reply_rate'],
        'metric' => $metric,
        'trigger' => $trigger,
        'age_days' => $ageDays,
    ];
}

/**
 * Ask Gemini for N fresh subject-line directives for the next A/B cycle.
 * Seeds the prompt with the content of the most-recent winners so proven
 * styles get reinforced. Falls back to a curated seed list if Gemini errors.
 *
 * Returns ['directives' => [...strings...], 'source' => 'ai'|'fallback'].
 */
function generate_ab_subject_variants($pdo, $count = 3)
{
    // Prefer subjects that actually got a reply over subjects that just got
    // clicks — replies are the conversion signal we now optimize for. Falls
    // back to past CTR winners during cold-start (when the replied-subject
    // pool is too small to seed three distinct examples).
    $repliedStmt = $pdo->prepare("
        SELECT DISTINCT draft_subject AS content
        FROM outreach_leads
        WHERE status IN ('replied','interested','onboarded')
          AND draft_subject IS NOT NULL
          AND draft_subject <> ''
        ORDER BY last_contact_date DESC
        LIMIT 5
    ");
    $repliedStmt->execute();
    $seeds = array_column($repliedStmt->fetchAll(), 'content');
    $seedSource = 'replies';

    if (count($seeds) < 3) {
        $winStmt = $pdo->prepare("
            SELECT v.content
            FROM outreach_ab_tests t
            JOIN outreach_ab_variants v ON v.id = t.winner_variant_id
            WHERE t.status = 'completed' AND t.variant_type = 'subject'
            ORDER BY t.completed_at DESC
            LIMIT 3
        ");
        $winStmt->execute();
        foreach ($winStmt->fetchAll() as $row) {
            if (!in_array($row['content'], $seeds, true)) {
                $seeds[] = $row['content'];
            }
        }
        $seedSource = empty($seeds) ? 'none' : (count($seeds) > 0 && count(array_filter($seeds)) > 0 ? 'replies+winners' : 'winners');
    }
    $priorWinners = $seeds; // variable name kept for downstream code compatibility

    $winnersText = '';
    if (!empty($priorWinners)) {
        $label = $seedSource === 'replies'
            ? "Subjects that have actually gotten replies recently — generate variations in this register:"
            : ($seedSource === 'replies+winners'
                ? "Mix of subjects that got replies and past CTR winners (cold-start blend) — generate variations in this register:"
                : "Recent winning subject strategies (most recent first):");
        $winnersText = "\n\n" . $label . "\n";
        foreach ($priorWinners as $w) {
            $winnersText .= "- " . trim((string) $w) . "\n";
        }
    }

    $systemPrompt = "You generate subject-line directives for an A/B test on a small-business outreach email from Evan, a solo developer, about a simple bookkeeping app called Argo Books.\n\n"
        . "Return STRICT JSON: { \"directives\": [\"directive 1\", \"directive 2\", ...] } with exactly $count entries.\n\n"
        . "CRITICAL: Each directive describes a STYLE for the writer to follow when crafting one specific lead's subject — it is NOT the subject itself. A second AI will read your directive and generate a fresh subject in that style for each lead. Your directive must NOT look like a subject line.\n\n"
        . "Bad examples (these read like subject lines, not styles): \"Lead with a personal touch about Argo Books\" / \"Quick question for {business}\" / \"Let's simplify your bookkeeping\"\n"
        . "Good examples (these describe HOW to write, not WHAT to write): \"Open with a one-line curiosity question that mentions the recipient's industry, no product names\" / \"Frame as a peer-to-peer note from one local Saskatoon business owner to another, max 5 words\" / \"Reference a specific category-typical pain point as a question, avoid sounding salesy\"\n\n"
        . "Rules for each directive:\n"
        . "- Talk ABOUT the writing technique (Open with…, Frame as…, Reference…, Pose…), don't write the subject text.\n"
        . "- 10 to 25 words. Concrete and specific about the technique.\n"
        . "- Mention what to AVOID as well as what to do (e.g. 'avoid product names', 'no greeting', 'no question mark').\n"
        . "- Each directive must be meaningfully different from the others in technique (question vs statement, local angle vs industry angle, ultra-short vs detail-rich, etc.).\n"
        . "- Refer to placeholders generically: the business name, their industry, their city. Do not invent product names or facts.\n"
        . "- Target cold B2B open rate: personal, curious, short. Avoid marketing-speak.";

    $userPrompt = "Propose $count distinct subject-line directives for the next A/B cycle." . $winnersText;

    $result = call_gemini($systemPrompt, $userPrompt);
    $directives = null;
    if (!isset($result['error'])) {
        $content = trim($result['content'] ?? '');
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $parsed = json_decode($content, true);
        if (is_array($parsed) && isset($parsed['directives']) && is_array($parsed['directives'])) {
            $clean = [];
            foreach ($parsed['directives'] as $d) {
                $d = trim((string) $d);
                if ($d !== '') $clean[] = mb_substr($d, 0, 500);
            }
            if (count($clean) >= 2) {
                $directives = array_slice($clean, 0, $count);
            }
        }
    }

    if (!$directives) {
        $fallback = [
            'Ask a short curiosity question that references the business name without making claims about how they operate',
            'Lead with a single concrete pain point the industry commonly has, phrased as a question',
            'Reference the city casually to sound local, under 10 words, no exclamation marks',
            'Keep it ultra-short (under 6 words) and intriguing without mentioning the product',
            'Open with the industry name as a single-word hook then a brief follow-up question',
        ];
        shuffle($fallback);
        return ['directives' => array_slice($fallback, 0, $count), 'source' => 'fallback'];
    }

    return ['directives' => $directives, 'source' => 'ai'];
}

/**
 * Per-type variant generator dispatch. Returns
 *   ['directives' => [...], 'source' => 'ai'|'fallback'|'fixed', 'literal' => bool?]
 * 'literal' is true when the contents are stored verbatim (no 'directive: '
 * prefix); ab_start_new_cycle uses that flag to decide whether to carry the
 * prior winner forward. Returns ['directives' => [], 'source' => 'unsupported']
 * for types with no generator — caller treats as failure.
 */
function generate_ab_variants_for_type($pdo, $variantType, $count = 3)
{
    switch ($variantType) {
        case 'subject':
            return generate_ab_subject_variants($pdo, $count);
        case 'sender':
            // Small fixed pool — content is the literal from-name.
            return [
                'directives' => ['Evan', 'Evan from Argo Books', 'Argo Books'],
                'source' => 'fixed',
                'literal' => true,
            ];
        case 'format':
            return [
                'directives' => ['html', 'plain'],
                'source' => 'fixed',
                'literal' => true,
            ];
        case 'personalization':
            return [
                'directives' => ['on', 'off'],
                'source' => 'fixed',
                'literal' => true,
            ];
        // body / cta / preheader stay admin-initiated — they need carefully
        // crafted copy and have no AI generator. ab_auto_rotation_order()
        // omits them so the cron's rotation never lands here.
        default:
            return ['directives' => [], 'source' => 'unsupported'];
    }
}

/**
 * Rotation order used by stepManageAbTests when ab_auto_rotation is on.
 * Every type listed here must have a generator in generate_ab_variants_for_type.
 */
function ab_auto_rotation_order()
{
    return ['subject', 'sender', 'format', 'personalization'];
}

/**
 * Start a new auto-cycle test for a given variant type. The previous winner
 * for that type (if any) is carried forward as variant A so the established
 * baseline keeps being measured; newly-generated directives fill the other slots.
 *
 * Returns ['action' => 'created', 'test_id' => N, 'variant_type' => ..., ...]
 *      or ['action' => 'failed', 'variant_type' => ..., 'error' => '...'].
 */
function ab_start_new_cycle($pdo, $variantType = 'subject')
{
    $count = 3;

    $gen = generate_ab_variants_for_type($pdo, $variantType, $count);
    $items = $gen['directives'] ?? [];
    $isLiteral = !empty($gen['literal']);

    if (count($items) < 2) {
        return [
            'action' => 'failed',
            'variant_type' => $variantType,
            'error' => 'Variant generation returned fewer than 2 entries (source: ' . ($gen['source'] ?? 'unknown') . ')',
        ];
    }

    // Carry-forward only makes sense for directive-style types where each
    // cycle generates *new* candidate copy and we want the prior winner kept
    // as a baseline. Literal types (sender / format / personalization) cycle
    // over a fixed pool, so carry-forward would just duplicate one variant.
    $prior = null;
    if (!$isLiteral) {
        $priorStmt = $pdo->prepare("
            SELECT v.content
            FROM outreach_ab_tests t
            JOIN outreach_ab_variants v ON v.id = t.winner_variant_id
            WHERE t.status = 'completed' AND t.variant_type = ?
            ORDER BY t.completed_at DESC
            LIMIT 1
        ");
        $priorStmt->execute([$variantType]);
        $prior = $priorStmt->fetchColumn() ?: null;
    }

    $name = 'Auto-cycle ' . $variantType . ' ' . date('Y-m-d H:i');
    $notes = 'Auto-generated by stepManageAbTests. Source: ' . ($gen['source'] ?? 'ai')
        . ($prior ? '. Prior winner carried forward as variant A.' : '.');

    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO outreach_ab_tests (name, variant_type, status, started_at, notes) VALUES (?, ?, 'active', NOW(), ?)")
            ->execute([$name, $variantType, $notes]);
        $testId = (int) $pdo->lastInsertId();

        $vStmt = $pdo->prepare("INSERT INTO outreach_ab_variants (test_id, label, content, is_default) VALUES (?, ?, ?, ?)");

        $label = 'A';
        $isDefault = 1;

        if ($prior) {
            $vStmt->execute([$testId, $label, $prior, $isDefault]);
            $label = 'B';
            $isDefault = 0;
        }

        foreach ($items as $d) {
            $content = $isLiteral ? $d : ('directive: ' . $d);
            $vStmt->execute([$testId, $label, $content, $isDefault]);
            $isDefault = 0;
            if ($label === 'D') break;
            $label = chr(ord($label) + 1);
        }

        $pdo->commit();

        $varStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_ab_variants WHERE test_id = ?");
        $varStmt->execute([$testId]);
        $variantCount = (int) $varStmt->fetchColumn();

        return [
            'action' => 'created',
            'variant_type' => $variantType,
            'test_id' => $testId,
            'test_name' => $name,
            'variant_count' => $variantCount,
            'carried_winner' => (bool) $prior,
            'source' => $gen['source'] ?? 'ai',
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['action' => 'failed', 'variant_type' => $variantType, 'error' => $e->getMessage()];
    }
}
