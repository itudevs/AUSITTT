<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Email details
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'Website Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    $mail->addReplyTo($email, $name);
    
    $mail->isHTML(false);
    $mail->Subject = 'Contact Form: ' . $subject;
    $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    // Send email
    $mail->send();
    
    // Redirect to success page
    echo "<script>
        sessionStorage.setItem('formSubmitted', 'true');
        window.location.href='message-sent.html';
    </script>";
    
} catch (Exception $e) {
    echo "Error: " . $mail->ErrorInfo;
}
?>