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
        error_log('PHPMailer not installed, falling back to mail(). Run composer install.');
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

/**
 * Send one HTML email through Resend's SMTP relay, falling back to mail() only
 * when SMTP is not configured.
 *
 * Every caller outside email_sender.php was hand-repeating this: call
 * create_smtp_mailer(), configure and send if it returns a mailer, otherwise
 * assemble MIME headers and call mail(). The fallback half in particular was
 * copy-pasted, and getting it subtly wrong means mail that silently never
 * arrives. See CLAUDE.md "Email sending".
 *
 * @param string $to      Recipient address
 * @param string $subject
 * @param string $html    HTML body
 * @param array  $opts    Optional:
 *   - toName        (string) recipient display name
 *   - fromEmail     (string) overrides the SMTP_FROM_EMAIL default
 *   - fromName      (string)
 *   - replyTo       (string) address; defaults to no explicit reply-to
 *   - replyToName   (string)
 *   - textBody      (string) plain-text alternative, SMTP path only
 *   - attachments   (array)  [['data' => string, 'name' => string, 'mime' => string], ...]
 *                            SMTP path only; mail() fallback sends the body alone
 *   - headers       (array)  extra raw headers, e.g. ['Message-ID: <...>']
 *
 * @return array{success: bool, method: string, error: ?string}
 *   method is 'smtp' or 'mail'; on total failure it reports which path was tried.
 */
function argo_send_html_email(string $to, string $subject, string $html, array $opts = []): array
{
    $fromEmail = $opts['fromEmail'] ?? env('SMTP_FROM_EMAIL', 'noreply@argorobots.com');
    $fromName  = $opts['fromName']  ?? env('SMTP_FROM_NAME', 'Argo Books');
    $toName    = $opts['toName']    ?? '';

    try {
        $mailer = create_smtp_mailer();

        if ($mailer) {
            // create_smtp_mailer() already applied the default From; only
            // override when the caller asked for a different one.
            if (isset($opts['fromEmail']) || isset($opts['fromName'])) {
                $mailer->setFrom($fromEmail, $fromName);
            }
            $mailer->addAddress($to, $toName);
            if (!empty($opts['replyTo'])) {
                $mailer->addReplyTo($opts['replyTo'], $opts['replyToName'] ?? '');
            }
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            if (!empty($opts['textBody'])) {
                $mailer->AltBody = $opts['textBody'];
            }
            foreach ($opts['attachments'] ?? [] as $a) {
                $mailer->addStringAttachment($a['data'], $a['name'], 'base64', $a['mime'] ?? 'application/octet-stream');
            }
            foreach ($opts['headers'] ?? [] as $header) {
                if (strpos($header, ':') !== false) {
                    [$name, $value] = explode(':', $header, 2);
                    $mailer->addCustomHeader(trim($name), trim($value));
                }
            }
            $mailer->send();
            return ['success' => true, 'method' => 'smtp', 'error' => null];
        }

        // No SMTP relay configured. mail() cannot carry the attachments, which
        // is why the SMTP path is the one that matters in production.
        $headers = array_merge([
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'X-Mailer: ArgoBooks/1.0',
        ], $opts['headers'] ?? []);
        if (!empty($opts['replyTo'])) {
            $headers[] = 'Reply-To: ' . $opts['replyTo'];
        }

        $recipient = $toName !== ''
            ? '"' . str_replace('"', '', $toName) . '" <' . $to . '>'
            : $to;

        if (@mail($recipient, $subject, $html, implode("\r\n", $headers))) {
            return ['success' => true, 'method' => 'mail', 'error' => null];
        }
        error_log('argo_send_html_email: mail() returned false for ' . $to);
        return ['success' => false, 'method' => 'mail', 'error' => 'mail() returned false'];
    } catch (\Throwable $e) {
        error_log('argo_send_html_email failed for ' . $to . ': ' . $e->getMessage());
        return ['success' => false, 'method' => 'smtp', 'error' => $e->getMessage()];
    }
}
