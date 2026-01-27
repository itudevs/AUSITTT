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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Sanitize input data FIRST
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $message = strip_tags(trim($_POST['message']));
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address provided');
    }
    
    // Validate message length
    if (strlen($message) < 10) {
        throw new Exception('Message is too short');
    }
    
    // Server settings
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    // OPTIMIZED ANTI-SPAM SETTINGS
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64'; // More reliable encoding
    $mail->XMailer = ' '; // Remove PHPMailer signature
    
    // CRITICAL FIX: Use authenticated email ONLY
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI Website');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    
    // IMPORTANT: Set envelope sender (Return-Path) to match From
    $mail->Sender = 'info@ausitttfuneralservices.co.za';
    
    // REPLY-TO - visitor's email
    $mail->addReplyTo($email, $name);

    // Use simple HTML with better spam score
    $mail->isHTML(true);
    
    // Clean subject line without suspicious words
    $mail->Subject = 'Website Enquiry: ' . $subject;
    
    // SIMPLIFIED HTML - Less code = less spam triggers
    $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
    <div style="background-color: #f8f9fa; padding: 20px; border-bottom: 3px solid #007bff;">
        <h2 style="margin: 0; color: #007bff;">Website Contact Form</h2>
    </div>
    <div style="padding: 20px; background-color: #ffffff;">
        <p><strong>Contact Information:</strong></p>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Email:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Subject:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
        </table>
        <p style="margin-top: 20px;"><strong>Message:</strong></p>
        <div style="padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
            ' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '
        </div>
    </div>
    <div style="padding: 15px; background-color: #f8f9fa; font-size: 12px; color: #6c757d; text-align: center;">
        <p style="margin: 5px 0;">Received: ' . date('F j, Y \a\t g:i A') . '</p>
        <p style="margin: 5px 0;">From IP: ' . htmlspecialchars($_SERVER['REMOTE_ADDR'], ENT_QUOTES, 'UTF-8') . '</p>
    </div>
</body>
</html>';
    
    // Plain text alternative (CRITICAL for spam filters)
    $mail->AltBody = "Website Contact Form Submission\n\n" .
                     "CONTACT INFORMATION\n" .
                     "-------------------\n" .
                     "Name: {$name}\n" .
                     "Email: {$email}\n" .
                     "Subject: {$subject}\n\n" .
                     "MESSAGE\n" .
                     "-------\n" .
                     "{$message}\n\n" .
                     "-------------------\n" .
                     "Received: " . date('F j, Y \a\t g:i A') . "\n" .
                     "From IP: {$_SERVER['REMOTE_ADDR']}";

    // Send the email
    if (!$mail->send()) {
        throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
    }
    
    // Log success
    error_log("Email sent successfully from: $email to: info@ausitttfuneralservices.co.za");
    
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    
    // Redirect to success page
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    // Log the error
    error_log("Mail Error: " . $mail->ErrorInfo);
    error_log("Exception: " . $e->getMessage());
    
    echo "<h2>Error Sending Message</h2>";
    echo "<h3>PHPMailer Error:</h3>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
    echo "<h3>Exception Message:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>