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
        return $time > (time() - 3600); // Last hour
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
    
    // Enable verbose debug output (comment out in production)
    $mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client and server
    $mail->Debugoutput = function($str, $level) {
        error_log("SMTP Debug level $level: $str");
        echo "Debug: $str<br>";
    };
    
    // Anti-spam headers
    $mail->XMailer = ' ';
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->addCustomHeader('X-Mailer', 'AUSI TTT Funeral Services Contact Form');
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:info@ausitttfuneralservices.co.za>');

    // Recipients
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI TTT Website Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    
    // Sanitize input data
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $message = strip_tags(trim($_POST['message']));
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address provided: ' . htmlspecialchars($email));
    }
    
    // Add reply-to (this was missing!)
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Contact Form: ' . $subject;
    
    // HTML version
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { padding: 20px; background-color: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>New Contact Form Submission</h2>
            <div class='field'>
                <span class='label'>Name:</span> " . htmlspecialchars($name) . "
            </div>
            <div class='field'>
                <span class='label'>Email:</span> " . htmlspecialchars($email) . "
            </div>
            <div class='field'>
                <span class='label'>Subject:</span> " . htmlspecialchars($subject) . "
            </div>
            <div class='field'>
                <span class='label'>Message:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <hr>
            <p style='color: #666; font-size: 12px;'>
                Sent from AUSI TTT Funeral Services contact form<br>
                IP Address: " . htmlspecialchars($_SERVER['REMOTE_ADDR']) . "<br>
                Date: " . date('Y-m-d H:i:s') . "
            </p>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version
    $mail->AltBody = "New Contact Form Submission\n\n" .
                     "Name: $name\n" .
                     "Email: $email\n" .
                     "Subject: $subject\n\n" .
                     "Message:\n$message\n\n" .
                     "---\n" .
                     "IP: " . $_SERVER['REMOTE_ADDR'] . "\n" .
                     "Date: " . date('Y-m-d H:i:s');

    $mail->send();
    
    // Log success
    error_log("Email sent successfully from: $email");
    
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    
    // Use header redirect instead of JavaScript
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    // Log the full error
    error_log("Mail Error: " . $mail->ErrorInfo);
    error_log("Exception: " . $e->getMessage());
    
    // Display error (remove in production)
    echo "<h2>Error Details:</h2>";
    echo "<p><strong>Mailer Error:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    
    // User-friendly message
    echo "<hr><p>Message could not be sent. Please try again later or contact us directly.</p>";
}