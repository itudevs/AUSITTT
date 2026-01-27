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
    
    // MINIMAL SMTP settings
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    // ABSOLUTE MINIMAL settings
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    $mail->addReplyTo($email, $name);
    
    // PLAIN TEXT ONLY - NO HTML
    $mail->isHTML(false);
    
    // ULTRA SIMPLE subject and body
    $mail->Subject = $subject;
    
    // BARE MINIMUM body text
    $mail->Body = "From: $name <$email>\n\n$message";

    $mail->send();
    
    $_SESSION['message_sent'] = true;
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    error_log("Mail Error: " . $mail->ErrorInfo);
    echo "<h2>Error Sending Message</h2>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
}
?>