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
    $mail->addReplyTo($_POST['email'], $_POST['firstname']);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Message: ' . $_POST['subject'];
    $mail->Body    = "Name: " . $_POST['firstname'] . " " . $_POST['lastname'] . "\n" .
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