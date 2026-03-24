<?php

require_once __DIR__ . '/../email_sender.php';

/**
 * Processes the contact form submission and sends an email
 *
 * @return array Result with 'success', 'message', and 'form_data' keys
 */
function check_rate_limit()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $max_submissions = 3;
    $window_seconds = 600; // 10 minutes
    $now = time();

    if (!isset($_SESSION['contact_submissions'])) {
        $_SESSION['contact_submissions'] = [];
    }

    // Remove entries older than the window
    $_SESSION['contact_submissions'] = array_filter(
        $_SESSION['contact_submissions'],
        function ($timestamp) use ($now, $window_seconds) {
            return ($now - $timestamp) < $window_seconds;
        }
    );

    if (count($_SESSION['contact_submissions']) >= $max_submissions) {
        return false;
    }

    $_SESSION['contact_submissions'][] = $now;
    return true;
}

function process_contact_form()
{
    // Rate limit check
    if (!check_rate_limit()) {
        return ['success' => false, 'message' => 'Too many submissions. Please wait a few minutes before trying again.', 'form_data' => []];
    }

    // Get form data
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'general';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $form_data = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ];

    // Map subject values to display labels
    $subject_labels = [
        'general' => 'General Inquiry',
        'support' => 'Technical Support',
        'billing' => 'Billing Question',
        'feature' => 'Feature Request',
        'bug' => 'Bug Report',
        'other' => 'Other'
    ];
    $subject_label = isset($subject_labels[$subject]) ? $subject_labels[$subject] : 'General Inquiry';

    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
        return ['success' => false, 'message' => 'All fields are required.', 'form_data' => $form_data];
    }

    // Validate email format and reject header injection attempts
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) {
        return ['success' => false, 'message' => 'Please enter a valid email address.', 'form_data' => $form_data];
    }

    // Send the email via SMTP relay when configured, with mail() fallback
    $safe_first = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    $safe_last = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
    $safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    $clean_first = str_replace(["\r", "\n", "\t"], '', $firstName);
    $clean_last = str_replace(["\r", "\n", "\t"], '', $lastName);
    $email_subject = "Argo Books Contact: {$clean_first} {$clean_last}";
    $formatted_message = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

    $body = <<<HTML
        <h1>New Contact Form Submission</h1>
        <p><strong>Name:</strong> {$safe_first} {$safe_last}</p>
        <p><strong>Email:</strong> {$safe_email}</p>
        <p><strong>Category:</strong> {$subject_label}</p>
        <p><strong>Message:</strong></p>
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 15px; margin-top: 5px;">
            {$formatted_message}
        </div>
        <p style="font-size: 12px; color: #64748b; margin-top: 20px;">To reply, respond to: {$safe_email}</p>
        HTML;

    $to_email = 'contact@argorobots.com';
    $mail_result = send_styled_email($to_email, $email_subject, $body, '', null, null, $email);

    if ($mail_result) {
        return ['success' => true];
    }
    return ['success' => false, 'message' => 'Failed to send message. Please try again or contact support directly.', 'form_data' => $form_data];
}

