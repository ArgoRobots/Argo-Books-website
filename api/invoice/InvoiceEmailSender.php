<?php
/**
 * Invoice Email Sender
 *
 * Handles the actual sending of invoice emails using PHPMailer.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer (install via Composer: composer require phpmailer/phpmailer)
require_once __DIR__ . '/../../vendor/autoload.php';

class InvoiceEmailSender
{
    private $mailer;
    private $rateLimiter;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();

        if (defined('RATE_LIMIT_ENABLED') && RATE_LIMIT_ENABLED) {
            $this->rateLimiter = new RateLimiter();
        }
    }

    private function setupMailer()
    {
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->Port = SMTP_PORT;

        if (SMTP_AUTH) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
        }

        if (!empty(SMTP_SECURE)) {
            $this->mailer->SMTPSecure = SMTP_SECURE;
        }

        // Content settings
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    /**
     * Send an invoice email
     *
     * @param array $data Email data from API request
     * @return array Result with success, message, and messageId
     */
    public function send(array $data): array
    {
        // Check rate limit
        if ($this->rateLimiter && !$this->rateLimiter->check($data['from'])) {
            return [
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
                'messageId' => null
            ];
        }

        try {
            // Clear any previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();

            // Set sender
            $fromEmail = $data['from'] ?? DEFAULT_FROM_EMAIL;
            $fromName = $data['fromName'] ?? DEFAULT_FROM_NAME;
            $this->mailer->setFrom($fromEmail, $fromName);

            // Set recipient
            $toName = $data['toName'] ?? '';
            $this->mailer->addAddress($data['to'], $toName);

            // Set reply-to if provided
            if (!empty($data['replyTo'])) {
                $this->mailer->addReplyTo($data['replyTo']);
            }

            // Set subject
            $this->mailer->Subject = $data['subject'];

            // Set body
            $this->mailer->Body = $data['htmlBody'];

            // Set plain text alternative if provided
            if (!empty($data['plainTextBody'])) {
                $this->mailer->AltBody = $data['plainTextBody'];
            } else {
                // Generate plain text from HTML as fallback
                $this->mailer->AltBody = strip_tags(
                    str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $data['htmlBody'])
                );
            }

            // Handle attachments
            if (!empty($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $this->addAttachment($attachment);
                }
            }

            // Generate a unique message ID
            $messageId = $this->generateMessageId($data['invoiceId'] ?? 'invoice');
            $this->mailer->MessageID = $messageId;

            // Send the email
            $this->mailer->send();

            // Log success
            $this->log('Email sent successfully', [
                'to' => $data['to'],
                'subject' => $data['subject'],
                'messageId' => $messageId,
                'invoiceId' => $data['invoiceId'] ?? 'N/A'
            ]);

            return [
                'success' => true,
                'message' => 'Email sent successfully.',
                'messageId' => $messageId
            ];

        } catch (Exception $e) {
            // Log error
            $this->log('Email sending failed', [
                'to' => $data['to'] ?? 'unknown',
                'error' => $e->getMessage()
            ], 'ERROR');

            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'messageId' => null
            ];
        }
    }

    /**
     * Add an attachment to the email
     *
     * @param array $attachment Attachment data
     */
    private function addAttachment(array $attachment): void
    {
        if (empty($attachment['contentBase64']) || empty($attachment['filename'])) {
            return;
        }

        // Validate MIME type
        $mimeType = $attachment['mimeType'] ?? 'application/octet-stream';
        if (!in_array($mimeType, ALLOWED_ATTACHMENT_TYPES)) {
            throw new Exception("Attachment type not allowed: {$mimeType}");
        }

        // Decode base64 content
        $content = base64_decode($attachment['contentBase64']);
        if ($content === false) {
            throw new Exception("Invalid base64 content for attachment: {$attachment['filename']}");
        }

        // Check size
        if (strlen($content) > MAX_ATTACHMENT_SIZE) {
            throw new Exception("Attachment too large: {$attachment['filename']}");
        }

        // Add as string attachment
        $this->mailer->addStringAttachment(
            $content,
            $attachment['filename'],
            PHPMailer::ENCODING_BASE64,
            $mimeType
        );
    }

    /**
     * Generate a unique message ID
     *
     * @param string $invoiceId Invoice ID for reference
     * @return string Message ID
     */
    private function generateMessageId(string $invoiceId): string
    {
        $domain = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), PHP_URL_HOST);
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "<{$invoiceId}.{$timestamp}.{$random}@{$domain}>";
    }

    /**
     * Log email activity
     *
     * @param string $message Log message
     * @param array $context Additional context
     * @param string $level Log level
     */
    private function log(string $message, array $context = [], string $level = 'INFO'): void
    {
        if (!defined('LOG_ENABLED') || !LOG_ENABLED) {
            return;
        }

        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message} {$contextStr}\n";

        file_put_contents(LOG_FILE, $logLine, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Simple rate limiter using file-based storage
 */
class RateLimiter
{
    private $storageFile;

    public function __construct()
    {
        $this->storageFile = __DIR__ . '/logs/rate_limits.json';
    }

    /**
     * Check if request is within rate limits
     *
     * @param string $identifier Unique identifier (e.g., email or IP)
     * @return bool True if request is allowed
     */
    public function check(string $identifier): bool
    {
        $data = $this->loadData();
        $now = time();
        $window = RATE_LIMIT_TIME_WINDOW;
        $maxRequests = RATE_LIMIT_MAX_REQUESTS;

        // Clean up old entries
        $data = array_filter($data, function($entry) use ($now, $window) {
            return ($now - $entry['timestamp']) < $window;
        });

        // Count requests for this identifier
        $count = 0;
        foreach ($data as $entry) {
            if ($entry['identifier'] === $identifier) {
                $count++;
            }
        }

        if ($count >= $maxRequests) {
            return false;
        }

        // Add this request
        $data[] = [
            'identifier' => $identifier,
            'timestamp' => $now
        ];

        $this->saveData($data);
        return true;
    }

    private function loadData(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }

        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?? [];
    }

    private function saveData(array $data): void
    {
        $dir = dirname($this->storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->storageFile, json_encode($data), LOCK_EX);
    }
}
