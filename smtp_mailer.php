<?php

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Create a PHPMailer instance configured with SMTP settings from environment variables.
 * Returns null if SMTP is not configured (SMTP_HOST not set), allowing callers to
 * fall back to PHP's mail() function.
 *
 * Required .env variables for SMTP (Amazon SES):
 *   SMTP_HOST     - SES SMTP endpoint (e.g. email-smtp.us-east-2.amazonaws.com)
 *   SMTP_PORT     - SMTP port (default: 587)
 *   SMTP_USERNAME - SES SMTP credentials username (starts with AKIA)
 *   SMTP_PASSWORD - SES SMTP credentials password
 *
 * Optional:
 *   SMTP_FROM_EMAIL - Default sender email (default: noreply@argorobots.com)
 *   SMTP_FROM_NAME  - Default sender name (default: Argo Books)
 */
function create_smtp_mailer()
{
    $host = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: '';

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
    $mail->Username   = $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME') ?: '';
    $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?: '';
    $port = (int) ($_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 587);
    $mail->Port       = $port;
    $mail->SMTPSecure = $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet    = 'UTF-8';
    $mail->isHTML(true);

    $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?: 'noreply@argorobots.com';
    $fromName  = $_ENV['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME') ?: 'Argo Books';
    $mail->setFrom($fromEmail, $fromName);

    return $mail;
}
