<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

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
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $message = strip_tags(trim($_POST['message']));
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address provided');
    }
    
    if (strlen($message) < 10) {
        throw new Exception('Message is too short');
    }
    
    // FILTER SPAM TRIGGER WORDS from subject and message
    $spam_words = ['viagra', 'casino', 'lottery', 'winner', 'click here', 'free money', 'weight loss', 'mlm'];
    $subject_lower = strtolower($subject);
    $message_lower = strtolower($message);
    
    foreach ($spam_words as $spam_word) {
        if (strpos($subject_lower, $spam_word) !== false || strpos($message_lower, $spam_word) !== false) {
            throw new Exception('Message contains prohibited content');
        }
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
    
    // Anti-spam headers
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->XMailer = ' ';
    
    // CRITICAL: Match sender and envelope
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI Website');
    $mail->Sender = 'info@ausitttfuneralservices.co.za'; // Envelope sender
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    $mail->addReplyTo($email, $name);
    
    // Add custom headers to reduce spam score
    $mail->addCustomHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:info@ausitttfuneralservices.co.za>');
    
    $mail->isHTML(true);
    
    // CONSISTENT subject format - always the same pattern
    $mail->Subject = 'Website Enquiry: ' . $subject;
    
    // Clean HTML - minimal spam triggers
    $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333;">Website Contact Form</h2>
    </div>
    <div style="background-color: #ffffff; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse;" cellpadding="10">
            <tr style="background-color: #f9f9f9;">
                <td style="border: 1px solid #ddd; font-weight: bold; width: 120px;">Name</td>
                <td style="border: 1px solid #ddd;">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; font-weight: bold;">Email</td>
                <td style="border: 1px solid #ddd;">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td style="border: 1px solid #ddd; font-weight: bold;">Subject</td>
                <td style="border: 1px solid #ddd;">' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
        </table>
        <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #333;">
            <p style="margin: 0 0 10px 0; font-weight: bold;">Message:</p>
            <div>' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</div>
        </div>
    </div>
    <div style="margin-top: 20px; padding: 15px; background-color: #f4f4f4; font-size: 12px; color: #666; text-align: center;">
        <p style="margin: 5px 0;">Received on ' . date('l, F j, Y') . ' at ' . date('g:i A') . '</p>
    </div>
</body>
</html>';
    
    // CRITICAL: Always include plain text version
    $mail->AltBody = "Website Contact Form\n\n" .
                     "From: {$name}\n" .
                     "Email: {$email}\n" .
                     "Subject: {$subject}\n\n" .
                     "Message:\n" .
                     str_repeat("-", 50) . "\n" .
                     "{$message}\n" .
                     str_repeat("-", 50) . "\n\n" .
                     "Received: " . date('l, F j, Y \a\t g:i A');

    // WAIT briefly before sending (seems more "human")
    usleep(500000); // 0.5 second delay
    
    if (!$mail->send()) {
        throw new Exception('Email sending failed');
    }
    
    error_log("SUCCESS: Email sent from $email with subject: $subject");
    
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    error_log("FAILED: Mail Error - " . $mail->ErrorInfo . " | Exception: " . $e->getMessage());
    
    echo "<h2>Error Sending Message</h2>";
    echo "<h3>Details:</h3>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo ?: $e->getMessage()) . "</pre>";
    echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
}
?>