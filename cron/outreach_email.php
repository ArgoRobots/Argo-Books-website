<?php
/**
 * Outreach Email Cron Job
 *
 * Automatically sends up to 10 approved outreach emails per day.
 * Picks leads that have drafts ready and an approval_status of 'approved'
 * but have not yet been sent (sent_at IS NULL).
 *
 * RECOMMENDED SCHEDULE: Daily at 9:00 AM
 *   0 9 * * * /usr/bin/php /path/to/outreach_email.php
 *
 * Manual execution:
 *   php outreach_email.php
 */

set_time_limit(120);

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../email_sender.php';

define('DAILY_SEND_LIMIT', 10);

function logOutreach($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";

    $logFile = __DIR__ . '/logs/outreach_email_' . date('Y-m-d') . '.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

function log_activity($pdo, $lead_id, $action_type, $details = null) {
    $stmt = $pdo->prepare("INSERT INTO outreach_activity_log (lead_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$lead_id, $action_type, $details]);
}

logOutreach('Starting outreach email send...');

try {
    global $pdo;

    // Check how many outreach emails were already sent today
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as sent_today
        FROM outreach_leads
        WHERE DATE(sent_at) = CURDATE()
    ");
    $stmt->execute();
    $sentToday = (int) $stmt->fetch(PDO::FETCH_ASSOC)['sent_today'];

    $remaining = DAILY_SEND_LIMIT - $sentToday;

    if ($remaining <= 0) {
        logOutreach("Daily limit of " . DAILY_SEND_LIMIT . " emails already reached ($sentToday sent today). Skipping.");
        exit(0);
    }

    logOutreach("Already sent $sentToday today. Will send up to $remaining more.");

    // Find approved leads with drafts that haven't been sent yet
    $stmt = $pdo->prepare("
        SELECT id, business_name, email, draft_subject, draft_body
        FROM outreach_leads
        WHERE approval_status = 'approved'
          AND draft_subject IS NOT NULL AND draft_subject != ''
          AND draft_body IS NOT NULL AND draft_body != ''
          AND email IS NOT NULL AND email != ''
          AND sent_at IS NULL
        ORDER BY date_added ASC
        LIMIT ?
    ");
    $stmt->execute([$remaining]);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        logOutreach('No approved leads ready to send. Done.');
        exit(0);
    }

    logOutreach("Found " . count($leads) . " approved leads to send.");

    $successCount = 0;
    $failCount = 0;

    foreach ($leads as $lead) {
        $id = $lead['id'];
        $businessName = $lead['business_name'];
        $email = $lead['email'];

        try {
            // Format body for HTML email
            $htmlBody = '<p>' . nl2br(htmlspecialchars($lead['draft_body'])) . '</p>';

            $result = send_styled_email(
                $email,
                $lead['draft_subject'],
                $htmlBody,
                '',
                'contact@argorobots.com',
                'Argo Books',
                'contact@argorobots.com'
            );

            if ($result) {
                $stmt = $pdo->prepare("UPDATE outreach_leads SET
                    sent_at = NOW(),
                    status = CASE WHEN status NOT IN ('replied','interested','not_interested','onboarded') THEN 'contacted' ELSE status END,
                    first_contact_date = COALESCE(first_contact_date, NOW()),
                    last_contact_date = NOW()
                    WHERE id = ?");
                $stmt->execute([$id]);

                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via cron to: ' . $email);

                logOutreach("Sent email to $businessName <$email> (lead #$id)");
                $successCount++;
            } else {
                log_activity($pdo, $id, 'email_failed', 'Automated email send failed for: ' . $email);
                logOutreach("Failed to send email to $businessName <$email> (lead #$id)", 'ERROR');
                $failCount++;
            }

            // Brief pause between sends to avoid rate limiting
            if ($successCount + $failCount < count($leads)) {
                sleep(2);
            }

        } catch (Exception $e) {
            log_activity($pdo, $id, 'email_failed', 'Automated email error: ' . $e->getMessage());
            logOutreach("Error sending to $businessName <$email> (lead #$id): " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logOutreach("Outreach email send complete. Sent: $successCount, Failed: $failCount");

} catch (PDOException $e) {
    logOutreach("Database error: " . $e->getMessage(), 'ERROR');
    exit(1);
}
