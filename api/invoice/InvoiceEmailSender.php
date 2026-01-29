<?php
/**
 * Invoice Email Sender
 *
 * Handles sending invoice emails using PHPMailer.
 * Configuration is loaded from environment variables.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class InvoiceEmailSender
{
    private PHPMailer $mailer;
    private ?RateLimiter $rateLimiter = null;

    // Configuration from environment
    private string $smtpHost;
    private int $smtpPort;
    private bool $smtpAuth;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $smtpSecure;
    private string $defaultFromEmail;
    private string $defaultFromName;
    private bool $rateLimitEnabled;
    private int $rateLimitMaxRequests;
    private int $rateLimitTimeWindow;
    private bool $logEnabled;
    private string $logFile;
    private int $maxAttachmentSize;

    public function __construct()
    {
        $this->loadConfig();
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();

        if ($this->rateLimitEnabled) {
            $this->rateLimiter = new RateLimiter(
                $this->rateLimitMaxRequests,
                $this->rateLimitTimeWindow
            );
        }
    }

    private function loadConfig(): void
    {
        // SMTP settings
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: 'localhost';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 587);
        $this->smtpAuth = filter_var($_ENV['SMTP_AUTH'] ?? getenv('SMTP_AUTH') ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME') ?: '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?: '';
        $this->smtpSecure = $_ENV['SMTP_SECURE'] ?? getenv('SMTP_SECURE') ?: 'tls';

        // Default sender
        $this->defaultFromEmail = $_ENV['INVOICE_DEFAULT_FROM_EMAIL'] ?? getenv('INVOICE_DEFAULT_FROM_EMAIL') ?: 'noreply@argorobots.com';
        $this->defaultFromName = $_ENV['INVOICE_DEFAULT_FROM_NAME'] ?? getenv('INVOICE_DEFAULT_FROM_NAME') ?: 'Argo Books';

        // Rate limiting
        $this->rateLimitEnabled = filter_var($_ENV['INVOICE_RATE_LIMIT_ENABLED'] ?? getenv('INVOICE_RATE_LIMIT_ENABLED') ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->rateLimitMaxRequests = (int)($_ENV['INVOICE_RATE_LIMIT_MAX'] ?? getenv('INVOICE_RATE_LIMIT_MAX') ?: 100);
        $this->rateLimitTimeWindow = (int)($_ENV['INVOICE_RATE_LIMIT_WINDOW'] ?? getenv('INVOICE_RATE_LIMIT_WINDOW') ?: 3600);

        // Logging
        $this->logEnabled = filter_var($_ENV['INVOICE_LOG_ENABLED'] ?? getenv('INVOICE_LOG_ENABLED') ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->logFile = $_ENV['INVOICE_LOG_FILE'] ?? getenv('INVOICE_LOG_FILE') ?: __DIR__ . '/logs/invoice_emails.log';

        // Attachments
        $this->maxAttachmentSize = (int)($_ENV['INVOICE_MAX_ATTACHMENT_SIZE'] ?? getenv('INVOICE_MAX_ATTACHMENT_SIZE') ?: 10485760); // 10MB
    }

    private function setupMailer(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->smtpHost;
        $this->mailer->Port = $this->smtpPort;

        if ($this->smtpAuth) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->smtpUsername;
            $this->mailer->Password = $this->smtpPassword;
        }

        if (!empty($this->smtpSecure)) {
            $this->mailer->SMTPSecure = $this->smtpSecure === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        }

        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
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

        // Check rate limit
        if ($this->rateLimiter && !$this->rateLimiter->check($data['from'])) {
            return [
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
                'messageId' => null,
                'errorCode' => 'RATE_LIMITED',
                'timestamp' => $timestamp
            ];
        }

        try {
            // Clear previous state
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();
            $this->mailer->clearBCCs();

            // Set sender
            $fromEmail = $data['from'] ?? $this->defaultFromEmail;
            $fromName = $data['fromName'] ?? $this->defaultFromName;
            $this->mailer->setFrom($fromEmail, $fromName);

            // Set recipient
            $toName = $data['toName'] ?? '';
            $this->mailer->addAddress($data['to'], $toName);

            // Set reply-to if provided
            if (!empty($data['replyTo'])) {
                $this->mailer->addReplyTo($data['replyTo']);
            }

            // Set BCC if provided
            if (!empty($data['bcc'])) {
                $this->mailer->addBCC($data['bcc']);
            }

            // Set subject
            $this->mailer->Subject = $data['subject'];

            // Set body (field is 'html' from Argo Books client)
            $this->mailer->Body = $data['html'];

            // Set plain text alternative (field is 'text' from Argo Books client)
            if (!empty($data['text'])) {
                $this->mailer->AltBody = $data['text'];
            } else {
                $this->mailer->AltBody = strip_tags(
                    str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $data['html'])
                );
            }

            // Handle PDF attachment (from Argo Books client)
            if (!empty($data['pdfAttachment'])) {
                $this->addPdfAttachment($data['pdfAttachment'], $data['pdfFilename'] ?? 'invoice.pdf');
            }

            // Generate unique message ID
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
                'messageId' => $messageId,
                'timestamp' => $timestamp
            ];

        } catch (Exception $e) {
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
     * Add PDF attachment from base64 content
     */
    private function addPdfAttachment(string $base64Content, string $filename): void
    {
        $content = base64_decode($base64Content);
        if ($content === false) {
            throw new Exception("Invalid base64 content for PDF attachment");
        }

        if (strlen($content) > $this->maxAttachmentSize) {
            throw new Exception("PDF attachment too large (max " . round($this->maxAttachmentSize / 1048576, 1) . "MB)");
        }

        $this->mailer->addStringAttachment(
            $content,
            $filename,
            PHPMailer::ENCODING_BASE64,
            'application/pdf'
        );
    }

    /**
     * Generate a unique message ID
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

/**
 * Rate limiter using file-based storage
 */
class RateLimiter
{
    private string $storageFile;
    private int $maxRequests;
    private int $timeWindow;

    public function __construct(int $maxRequests, int $timeWindow)
    {
        $this->storageFile = __DIR__ . '/logs/rate_limits.json';
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
    }

    public function check(string $identifier): bool
    {
        $data = $this->loadData();
        $now = time();

        // Clean up old entries
        $data = array_filter($data, fn($entry) => ($now - $entry['timestamp']) < $this->timeWindow);

        // Count requests for this identifier
        $count = count(array_filter($data, fn($entry) => $entry['identifier'] === $identifier));

        if ($count >= $this->maxRequests) {
            return false;
        }

        $data[] = ['identifier' => $identifier, 'timestamp' => $now];
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
