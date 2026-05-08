<?php
declare(strict_types=1);

/**
 * Strip CR/LF and the rest of the ASCII control range from any value that
 * ends up in an email header. PHPMailer sanitizes its own header inputs,
 * but the mail() fallback path in send_styled_email() concatenates these
 * into the headers string verbatim — a stray newline would let an attacker
 * inject Bcc/Cc/etc. via user-controlled fields (subject lines from
 * community posts, contact-form reply-to, etc.).
 *
 * Returns null for null input so callers can pass through optional fields.
 */
function sanitize_header_value(?string $value): ?string
{
    if ($value === null) {
        return null;
    }
    return preg_replace('/[\r\n\x00-\x1f]+/', ' ', $value);
}
