<?php
session_start();

// ========================================
// SERVER-SIDE RATE LIMITING
// Add this section RIGHT HERE
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
    // Anti-spam headers
    $mail->XMailer = ' ';
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->addCustomHeader('X-Mailer', 'Misaveni Holdings Contact Form');
    $mail->addCustomHeader('List-Unsubscribe', '<mailto:info@misaveniholdings.co.za>');

    // Recipients
     $mail->setFrom('info@ausitttfuneralservices.co.za', 'Website Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    
    // Sanitize input data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    $mail->addReplyTo($email, "$firstname $lastname");

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Contact Form: ' . $subject;
    
    // HTML version
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>New Contact Form Submission</h2>
            <div class='field'>
                <span class='label'>Name:</span> $name 
            </div>
            <div class='field'>
                <span class='label'>Email:</span> $email
            </div>
            <div class='field'>
                <span class='label'>Subject:</span> $subject
            </div>
            <div class='field'>
                <span class='label'>Message:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <hr>
            <p style='color: #666; font-size: 12px;'>
                Sent from Misaveni Holdings contact form<br>
                IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>
                Date: " . date('Y-m-d H:i:s') . "
            </p>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version
    $mail->AltBody = "New Contact Form Submission\n\n" .
                     "Name: $firstname $lastname\n" .
                     "Email: $email\n" .
                     "Subject: $subject\n\n" .
                     "Message:\n$message\n\n" .
                     "---\n" .
                     "IP: " . $_SERVER['REMOTE_ADDR'] . "\n" .
                     "Date: " . date('Y-m-d H:i:s');

    $mail->send();
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    echo "<script>
        sessionStorage.setItem('formSubmitted', 'true');
        sessionStorage.setItem('submissionTime', new Date().getTime());
        window.location.href='message-sent.html';
    </script>";
} catch (Exception $e) {
    error_log("Mail Error: {$mail->ErrorInfo}");
    echo "Message could not be sent. Please try again later.";
}