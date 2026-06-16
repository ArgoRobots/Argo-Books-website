<?php
/**
 * Transactional emails for the affiliate program. All go through
 * send_styled_email() (Resend via SMTP, with the mail() fallback handled
 * inside email_sender.php).
 */

require_once __DIR__ . '/../../email_sender.php';

if (!function_exists('send_affiliate_application_received_email')) {
    /** Confirmation that an application was received and is under review. */
    function send_affiliate_application_received_email(string $to_email, string $username): bool
    {
        $name = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $body = <<<HTML
            <h1>We received your affiliate application</h1>
            <p>Hi {$name},</p>
            <p>Thanks for applying to the Argo Books affiliate program. Your application is now under review.</p>
            <p>We review applications manually, so this usually takes a day or two. We'll email you the moment a decision is made. If approved, you'll get your unique referral link and can start earning right away.</p>
            <p>Talk soon,<br>The Argo Books team</p>
HTML;

        return send_styled_email($to_email, 'Your Argo Books affiliate application', $body, 'purple');
    }
}

if (!function_exists('send_affiliate_approved_email')) {
    /** Approval email containing the affiliate's live referral link + dashboard. */
    function send_affiliate_approved_email(string $to_email, string $username, string $referral_url, string $dashboard_url): bool
    {
        $name = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars($referral_url, ENT_QUOTES, 'UTF-8');
        $dash = htmlspecialchars($dashboard_url, ENT_QUOTES, 'UTF-8');
        $body = <<<HTML
            <h1>You're in! Welcome to the Argo Books affiliate program</h1>
            <p>Hi {$name},</p>
            <p>Your application is approved. Here's your unique referral link, share it anywhere you like:</p>
            <p style="font-size:16px;"><strong><a href="{$link}">{$link}</a></strong></p>
            <p>You earn <strong>50% of every payment</strong> from customers you refer, for the first 12 months of their subscription. Track your clicks, signups, and commission any time from your dashboard:</p>
            <div class="button-container">
                <a href="{$dash}" class="button">Open my affiliate dashboard</a>
            </div>
            <p>Happy promoting,<br>The Argo Books team</p>
HTML;

        return send_styled_email($to_email, 'Your Argo Books affiliate link is ready', $body, 'purple');
    }
}

if (!function_exists('send_affiliate_rejected_email')) {
    /** Polite decline. $reason is optional admin-supplied context. */
    function send_affiliate_rejected_email(string $to_email, string $username, string $reason = ''): bool
    {
        $name = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $reasonHtml = '';
        if (trim($reason) !== '') {
            $safe = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
            $reasonHtml = "<p>{$safe}</p>";
        }
        $body = <<<HTML
            <h1>Update on your affiliate application</h1>
            <p>Hi {$name},</p>
            <p>Thanks for your interest in the Argo Books affiliate program. We're not able to approve your application at this time.</p>
            {$reasonHtml}
            <p>You're welcome to apply again in the future. We appreciate your support of Argo Books.</p>
            <p>The Argo Books team</p>
HTML;

        return send_styled_email($to_email, 'Update on your Argo Books affiliate application', $body, 'purple');
    }
}
