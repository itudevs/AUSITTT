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
    $mail->Host       = 'mail.ausitttfuneralservices.co.za'; // Your Truehost Mail Server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@ausitttfuneralservices.co.za'; // Your cPanel email
    $mail->Password   = 'MGH@infoAUSI2026';        
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'Website Query');
    $mail->addAddress('info@ausitttfuneralservices.co.za'); 
    $mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'Contact Form - ' . $_POST['subject'];
    
    // Build message body with user's contact info prominently displayed
    $mail->Body  = "NEW CONTACT FORM SUBMISSION\n";
    $mail->Body .= "======================================\n\n";
    $mail->Body .= "FROM:\n";
    $mail->Body .= "Name: " . $_POST['name'] . "\n";
    $mail->Body .= "Email: " . $_POST['email'] . "\n";
    $mail->Body .= "Reply to this email: " . $_POST['email'] . "\n\n";
    $mail->Body .= "SUBJECT: " . $_POST['subject'] . "\n\n";
    $mail->Body .= "MESSAGE:\n";
    $mail->Body .= "--------------------------------------\n";
    $mail->Body .= $_POST['message'] . "\n";
    $mail->Body .= "--------------------------------------\n\n";
    $mail->Body .= "This message was sent via the contact form at ausitttfuneralservices.co.za\n";

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