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
    $mail->SMTPDebug = 0; // DISABLE DEBUG FOR PRODUCTION (was 2)
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    // ANTI-SPAM SETTINGS
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = '8bit'; // CHANGED - most natural encoding
    $mail->XMailer = ' '; // Remove PHPMailer signature
    $mail->Priority = 3; // Normal priority
    
    // CRITICAL: Different FROM and TO addresses
    $mail->setFrom('noreply@ausitttfuneralservices.co.za', 'AUSI Contact Form');
    
    // TO address - DIFFERENT from FROM address
    $mail->addAddress('ausibotlokwa@ausitttfuneralservices.co.za', 'AUSI Team');
    
    // REPLY-TO - visitor's email
    $mail->addReplyTo($email, $name);

    // Use HTML email with plain text fallback (more legitimate looking)
    $mail->isHTML(true);
    
    // Professional subject line
    $mail->Subject = 'New Contact Form Submission - ' . $subject;
    
    // HTML Body (looks more legitimate)
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #f4f4f4; padding: 10px; border-left: 4px solid #0066cc; }
            .content { padding: 20px 0; }
            .field { margin: 10px 0; }
            .label { font-weight: bold; color: #555; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Website Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>From:</span> {$name}
                </div>
                <div class='field'>
                    <span class='label'>Email:</span> {$email}
                </div>
                <div class='field'>
                    <span class='label'>Subject:</span> {$subject}
                </div>
                <div class='field'>
                    <span class='label'>Message:</span>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
            </div>
            <div class='footer'>
                <p>Submitted on: " . date('l, F j, Y \a\t g:i A') . "</p>
                <p>IP Address: {$_SERVER['REMOTE_ADDR']}</p>
            </div>
        </div>
    </body>
    </html>";
    
    // Plain text alternative (important for spam filters)
    $mail->AltBody = "New Contact Form Submission\n\n" .
                     "From: {$name}\n" .
                     "Email: {$email}\n" .
                     "Subject: {$subject}\n\n" .
                     "Message:\n{$message}\n\n" .
                     "---\n" .
                     "Submitted: " . date('d/m/Y H:i:s') . "\n" .
                     "IP: {$_SERVER['REMOTE_ADDR']}";

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
    
    echo "<h2>Error Sending Message</h2>";
    echo "<h3>PHPMailer Error:</h3>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
    echo "<h3>Exception Message:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>