<?php
/**
 * Build a follow-up email body for a lead. Static template with light
 * personalization — no per-lead AI call so cost stays at zero and
 * voice stays consistent across the cohort.
 *
 * Returns ['subject' => ..., 'body' => ..., 'preheader' => ...].
 *
 * The subject is intentionally a "Re: <original>" so it lands in the
 * recipient's existing inbox thread when paired with In-Reply-To /
 * References headers on the send-side.
 */

if (defined('OUTREACH_FOLLOWUP_TEMPLATE_LOADED')) return;
define('OUTREACH_FOLLOWUP_TEMPLATE_LOADED', true);

function build_followup_email(array $lead, string $unsubUrl): array
{
    $bizName = trim((string) ($lead['business_name'] ?? ''));
    $greeting = $bizName !== '' ? "Hi $bizName team," : 'Hi there,';

    $originalSubject = trim((string) ($lead['draft_subject'] ?? ''));
    // Don't double up the "Re:" prefix if the original somehow already has one.
    if ($originalSubject !== '' && stripos($originalSubject, 're:') !== 0) {
        $subject = 'Re: ' . $originalSubject;
    } elseif ($originalSubject !== '') {
        $subject = $originalSubject;
    } else {
        $subject = 'Following up';
    }

    $body =
        $greeting . "\n\n" .
        "Just bumping my note from last week in case it got buried. " .
        "Still happy to set you up with a free 1-year premium license for honest feedback on Argo Books — no rush, no pressure.\n\n" .
        "If you'd rather I stop reaching out, no worries: " . $unsubUrl . " and I'll take you off the list.\n\n" .
        "All the best,\n" .
        "Evan\n" .
        "Argo Books";

    return [
        'subject' => $subject,
        'body' => $body,
        'preheader' => 'Quick bump on my last note',
    ];
}
