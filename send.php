<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if form was actually submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// Validate required fields exist
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
    die('All fields are required');
}

// ========================================
// SERVER-SIDE RATE LIMITING
// ========================================
$max_emails_per_hour = 5;
$rate_limit_file = sys_get_temp_dir() . '/mail_rate_limit_' . md5($_SERVER['REMOTE_ADDR']);

if (file_exists($rate_limit_file)) {
    $attempts = json_decode(file_get_contents($rate_limit_file), true);
    $recent_attempts = array_filter($attempts, function($time) {
        return $time > (time() - 3600);
    });
    
    if (count($recent_attempts) >= $max_emails_per_hour) {
        die('Too many submission attempts. Please try again later.');
    }
    $attempts = $recent_attempts;
} else {
    $attempts = [];
}

$attempts[] = time();
file_put_contents($rate_limit_file, json_encode($attempts));
// ========================================
// END RATE LIMITING
// ========================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    // Sanitize input data FIRST
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $message = strip_tags(trim($_POST['message']));
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address provided');
    }
    
    // Validate message length (spam filters flag very short messages)
    if (strlen($message) < 10) {
        throw new Exception('Message is too short');
    }
    
    // Anti-spam headers
    $mail->XMailer = ' '; // Hide PHPMailer signature
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    
    // CRITICAL: Add proper headers
    $mail->addCustomHeader('X-Priority', '3'); // Normal priority
    $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
    $mail->addCustomHeader('Importance', 'Normal');
    $mail->addCustomHeader('X-Mailer', 'AUSI Contact Form v1.0');
    
    // FROM address - MUST match domain
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI Funeral Services Website');
    
    // TO address
    $mail->addAddress('info@ausitttfuneralservices.co.za', 'AUSI Info');
    
    // REPLY-TO with proper format
    $mail->addReplyTo($email, $name);

    // Content settings
    $mail->isHTML(true);
    $mail->Subject = 'Website Enquiry: ' . $subject; // CHANGED: More descriptive subject
    
    // Create more substantial HTML content
    $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 5px;
        }
        .email-header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .email-body {
            padding: 30px;
        }
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            width: 100px;
        }
        .info-value {
            color: #555555;
        }
        .message-content {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #2c3e50;
            margin-top: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .email-footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-radius: 0 0 5px 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2 style="margin: 0;">New Website Contact Form Submission</h2>
        </div>
        <div class="email-body">
            <div class="info-row">
                <span class="info-label">From:</span>
                <span class="info-value">' . htmlspecialchars($name) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></span>
            </div>
            <div class="info-row">
                <span class="info-label">Subject:</span>
                <span class="info-value">' . htmlspecialchars($subject) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Message:</span>
                <div class="message-content">' . nl2br(htmlspecialchars($message)) . '</div>
            </div>
            <div class="info-row" style="border-bottom: none;">
                <span class="info-label">Date:</span>
                <span class="info-value">' . date('l, F j, Y \a\t g:i A') . '</span>
            </div>
        </div>
        <div class="email-footer">
            <p style="margin: 5px 0;">This message was sent from the AUSI TTT Funeral Services website contact form</p>
            <p style="margin: 5px 0;">Sender IP: ' . htmlspecialchars($_SERVER['REMOTE_ADDR']) . '</p>
            <p style="margin: 5px 0;">&copy; ' . date('Y') . ' AUSI TTT Funeral Services. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
    
    // Plain text version (IMPORTANT for spam filters)
    $mail->AltBody = "NEW WEBSITE CONTACT FORM SUBMISSION\n";
    $mail->AltBody .= "=====================================\n\n";
    $mail->AltBody .= "CONTACT INFORMATION:\n";
    $mail->AltBody .= "Name: " . $name . "\n";
    $mail->AltBody .= "Email: " . $email . "\n";
    $mail->AltBody .= "Subject: " . $subject . "\n\n";
    $mail->AltBody .= "MESSAGE:\n";
    $mail->AltBody .= "-------\n";
    $mail->AltBody .= $message . "\n\n";
    $mail->AltBody .= "=====================================\n";
    $mail->AltBody .= "Submission Details:\n";
    $mail->AltBody .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $mail->AltBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $mail->AltBody .= "User Agent: " . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown') . "\n\n";
    $mail->AltBody .= "This is an automated message from ausitttfuneralservices.co.za";

    // Send the email
    $mail->send();
    
    // Log success
    error_log("Email sent successfully from: $email");
    
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    
    // Redirect to success page
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    // Log the error
    error_log("Mail Error: " . $mail->ErrorInfo);
    error_log("Exception: " . $e->getMessage());
    
    // Display error (comment out in production)
    echo "<h2>Error Sending Message</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Production message
    // echo "We're sorry, but your message could not be sent at this time. Please try again later or contact us directly at info@ausitttfuneralservices.co.za";
}
?>