<?php

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

    // Send the email
    $email_subject = "Argo Books Contact: {$firstName} {$lastName}";
    $email_html = get_contact_email_template($firstName, $lastName, $email, $subject_label, $message);

    $to_email = 'contact@argorobots.com';

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Argo Books Website <noreply@argorobots.com>',
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion()
    ];
    $mail_result = mail($to_email, $email_subject, $email_html, implode("\r\n", $headers));

    if ($mail_result) {
        return ['success' => true];
    }
    return ['success' => false, 'message' => 'Failed to send message. Please try again or contact support directly.', 'form_data' => $form_data];
}

/**
 * Get HTML email template for contact form
 */
function get_contact_email_template($firstName, $lastName, $email, $subject, $message)
{
    $formatted_message = nl2br(htmlspecialchars($message));

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Contact Form Submission</title>
    <style>
        body {
            background-color: #f6f9fc;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #3b82f6;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            color: #fff;
            font-size: 22px;
            margin: 0;
        }
        .content {
            padding: 30px;
            color: #333;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #3b82f6;
        }
        .field-value {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px;
        }
        .message-content {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-top: 5px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
        </div>
        <div class="content">
            <div class="field">
                <span class="field-label">Name:</span>
                <div class="field-value">{$firstName} {$lastName}</div>
            </div>
            <div class="field">
                <span class="field-label">Email:</span>
                <div class="field-value">{$email}</div>
            </div>
            <div class="field">
                <span class="field-label">Category:</span>
                <div class="field-value">{$subject}</div>
            </div>
            <div class="field">
                <span class="field-label">Message:</span>
                <div class="message-content">{$formatted_message}</div>
            </div>
        </div>
        <div class="footer">
            <p>This message was sent from the Argo Books contact form.</p>
            <p>To reply, simply respond to this email which will go to: {$email}</p>
        </div>
    </div>
</body>
</html>
HTML;
}
