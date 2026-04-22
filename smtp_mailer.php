<?php

require_once __DIR__ . '/env_helper.php';

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Create a PHPMailer instance configured with SMTP settings from environment variables.
 * Returns null if SMTP is not configured (SMTP_HOST not set), allowing callers to
 * fall back to PHP's mail() function.
 *
 * Required .env variables for SMTP (Resend):
 *   SMTP_HOST     - Resend SMTP endpoint (smtp.resend.com)
 *   SMTP_PORT     - SMTP port (default: 587)
 *   SMTP_USERNAME - Always the literal string "resend"
 *   SMTP_PASSWORD - Resend API key (starts with re_)
 *
 * Optional:
 *   SMTP_FROM_EMAIL - Default sender email (default: noreply@argorobots.com)
 *   SMTP_FROM_NAME  - Default sender name (default: Argo Books)
 */
function create_smtp_mailer()
{
    $host = env('SMTP_HOST', '');

    if (empty($host)) {
        return null;
    }

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer not installed — falling back to mail(). Run composer install.');
        return null;
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->AuthType   = 'LOGIN';
    $mail->Username   = env('SMTP_USERNAME', '');
    $mail->Password   = env('SMTP_PASSWORD', '');
    $port = (int) env('SMTP_PORT', 587);
    $mail->Port       = $port;
    $mail->SMTPSecure = $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet    = 'UTF-8';
    $mail->isHTML(true);

    $fromEmail = env('SMTP_FROM_EMAIL', 'noreply@argorobots.com');
    $fromName  = env('SMTP_FROM_NAME', 'Argo Books');
    $mail->setFrom($fromEmail, $fromName);

    return $mail;
}
