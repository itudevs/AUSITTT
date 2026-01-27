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
    $mail->Subject = 'New Message: ' . $_POST['subject'];
    $mail->Body    = "Name: " . $_POST['name'] . "\n" .
                     "Email: " . $_POST['email'] . "\n\n" .
                     "Message:\n" . $_POST['message'];
    $mail->send();
    $_SESSION['message_sent'] = true;
    $_SESSION['message_time'] = time();
    echo "<script>
        sessionStorage.setItem('formSubmitted', 'true');
        sessionStorage.setItem('submissionTime', new Date().getTime());
        window.location.href='message-sent.html';
    </script>";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}