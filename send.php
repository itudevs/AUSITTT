<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@ausitttfuneralservices.co.za';
    $mail->Password   = 'MGH@infoAUSI2026';        
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    
    // Additional headers to look more legitimate
    $mail->XMailer = ' '; // Hide X-Mailer header
    $mail->CharSet = 'UTF-8';
    
    // Add Message-ID to look more legitimate
    $mail->MessageID = '<' . md5(uniqid(time())) . '@ausitttfuneralservices.co.za>';

    // Recipients - Both from and to are same domain (no external emails)
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI Contact Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za', 'AUSI Reception');
    
    // REMOVED: addReplyTo - this triggers spam filters when using external email

    // Content - Simple subject without special characters
    $mail->isHTML(false);
    $mail->Subject = 'Website Contact Form Submission';
    
    // Simple, clean message body
    $name = strip_tags($_POST['name']);
    $email = strip_tags($_POST['email']);
    $subject = strip_tags($_POST['subject']);
    $message = strip_tags($_POST['message']);
    
   $mail->isHTML(false);
  // Validate and sanitize inputs
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email address');
}

// Check for required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    die('All fields are required');
}

// Prevent header injection
$name = str_replace(["\r", "\n", "%0a", "%0d"], '', $name);
$subject = str_replace(["\r", "\n", "%0a", "%0d"], '', $subject);
$email = str_replace(["\r", "\n", "%0a", "%0d"], '', $email);

// Set email properties
$mail->Subject = 'New Message: ' . $subject;
$mail->Body    = "Name: " . $name . "\n" .
                 "Email: " . $email . "\n\n" .
                 "Message:\n" . $message;

// If using HTML email, escape the content
// $mail->Body = "Name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "<br>...";
    echo "<script>
        sessionStorage.setItem('formSubmitted', 'true');
        sessionStorage.setItem('submissionTime', new Date().getTime());
        window.location.href='message-sent.html';
    </script>";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}