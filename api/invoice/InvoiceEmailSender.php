<?php
/**
 * Invoice Email Sender
 *
 * Handles sending invoice emails using PHP's native mail() function.
 * No external dependencies required.
 */

class InvoiceEmailSender
{
    private string $defaultFromEmail;
    private string $defaultFromName;
    private bool $logEnabled;
    private string $logFile;

    public function __construct()
    {
        $this->defaultFromEmail = $_ENV['INVOICE_DEFAULT_FROM_EMAIL'] ?? getenv('INVOICE_DEFAULT_FROM_EMAIL') ?: 'noreply@argorobots.com';
        $this->defaultFromName = $_ENV['INVOICE_DEFAULT_FROM_NAME'] ?? getenv('INVOICE_DEFAULT_FROM_NAME') ?: 'Argo Books';
        $this->logEnabled = filter_var($_ENV['INVOICE_LOG_ENABLED'] ?? getenv('INVOICE_LOG_ENABLED') ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->logFile = $_ENV['INVOICE_LOG_FILE'] ?? getenv('INVOICE_LOG_FILE') ?: __DIR__ . '/logs/invoice_emails.log';
    }

    /**
     * Send an invoice email
     *
     * @param array $data Email data from API request
     * @return array Result with success, message, messageId, and timestamp
     */
    public function send(array $data): array
    {
        $timestamp = date('c');

        try {
            // Get sender info
            $fromEmail = $data['from'] ?? $this->defaultFromEmail;
            $fromName = $data['fromName'] ?? $this->defaultFromName;
            $toEmail = $data['to'];
            $toName = $data['toName'] ?? '';
            $subject = $data['subject'];
            $htmlBody = $data['html'];
            $textBody = $data['text'] ?? strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));

            // Generate unique message ID
            $messageId = $this->generateMessageId($data['invoiceId'] ?? 'invoice');

            // Build headers
            $headers = [
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . $this->formatAddress($fromEmail, $fromName),
                'Message-ID: ' . $messageId,
                'X-Mailer: ArgoBooks/1.0'
            ];

            // Add reply-to if provided
            if (!empty($data['replyTo'])) {
                $headers[] = 'Reply-To: ' . $data['replyTo'];
            }

            // Add BCC if provided
            if (!empty($data['bcc'])) {
                $headers[] = 'Bcc: ' . $data['bcc'];
            }

            // Format recipient
            $to = $this->formatAddress($toEmail, $toName);

            // Handle PDF attachment if provided
            if (!empty($data['pdfAttachment'])) {
                $result = $this->sendWithAttachment($to, $subject, $htmlBody, $textBody, $headers, $data['pdfAttachment'], $data['pdfFilename'] ?? 'invoice.pdf');
            } else {
                $result = mail($to, $subject, $htmlBody, implode("\r\n", $headers));
            }

            if ($result) {
                $this->log('Email sent successfully', [
                    'to' => $toEmail,
                    'subject' => $subject,
                    'messageId' => $messageId,
                    'invoiceId' => $data['invoiceId'] ?? 'N/A'
                ]);

                return [
                    'success' => true,
                    'message' => 'Email sent successfully.',
                    'messageId' => $messageId,
                    'timestamp' => $timestamp
                ];
            } else {
                $this->log('Email sending failed', [
                    'to' => $toEmail,
                    'error' => 'mail() returned false'
                ], 'ERROR');

                return [
                    'success' => false,
                    'message' => 'Failed to send email.',
                    'messageId' => null,
                    'errorCode' => 'SEND_FAILED',
                    'timestamp' => $timestamp
                ];
            }

        } catch (\Exception $e) {
            $this->log('Email sending failed', [
                'to' => $data['to'] ?? 'unknown',
                'error' => $e->getMessage()
            ], 'ERROR');

            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'messageId' => null,
                'errorCode' => 'SEND_FAILED',
                'timestamp' => $timestamp
            ];
        }
    }

    /**
     * Send email with PDF attachment using multipart MIME
     */
    private function sendWithAttachment(string $to, string $subject, string $htmlBody, string $textBody, array $baseHeaders, string $pdfBase64, string $filename): bool
    {
        $boundary = md5(time());

        // Remove Content-Type from base headers (we'll set multipart)
        $headers = array_filter($baseHeaders, fn($h) => !str_starts_with($h, 'Content-Type:'));
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

        // Decode PDF
        $pdfContent = base64_decode($pdfBase64);
        if ($pdfContent === false) {
            throw new \Exception('Invalid base64 PDF content');
        }

        // Build multipart message
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: application/pdf; name=\"{$filename}\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
        $body .= chunk_split(base64_encode($pdfContent)) . "\r\n";

        $body .= "--{$boundary}--";

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * Format email address with optional name
     */
    private function formatAddress(string $email, string $name = ''): string
    {
        if (empty($name)) {
            return $email;
        }
        return "{$name} <{$email}>";
    }

    /**
     * Generate a unique message ID
     */
    private function generateMessageId(string $invoiceId): string
    {
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "<{$invoiceId}.{$timestamp}.{$random}@{$domain}>";
    }

    /**
     * Log email activity
     */
    private function log(string $message, array $context = [], string $level = 'INFO'): void
    {
        if (!$this->logEnabled) {
            return;
        }

        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message} {$contextStr}\n";

        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}
