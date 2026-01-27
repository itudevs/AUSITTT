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
// SERVER-SIDE RATE LIMITING - TEMPORARILY DISABLED FOR TESTING
// ========================================
/*
$max_emails_per_hour = 5;
$rate_limit_file = sys_get_temp_dir() . '/mail_rate_limit_' . md5($_SERVER['REMOTE_ADDR']);

if (file_exists($rate_limit_file)) {
    $attempts = json_decode(file_get_contents($rate_limit_file), true);
    $recent_attempts = array_filter($attempts, function($time) {
        return $time > (time() - 3600);
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
*/
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
    
    // Server settings - SIMPLIFIED
    $mail->isSMTP();
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->Debugoutput = function($str, $level) {
        echo "Debug level $level: " . htmlspecialchars($str) . "<br>\n";
    };
    $mail->Host = 'mail.ausitttfuneralservices.co.za';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ausitttfuneralservices.co.za';
    $mail->Password = 'MGH@infoAUSI2026';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    // CRITICAL SPAM FILTER SETTINGS
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'quoted-printable'; // CHANGED from base64 - less suspicious
    $mail->XMailer = ' '; // Remove PHPMailer signature
    
    // Minimal headers - the less, the better
    $mail->Priority = 3; // Normal priority
    
    // FROM address - MUST match authenticated domain
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'AUSI Website');
    
    // TO address
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    
    // REPLY-TO 
    $mail->addReplyTo($email, $name);

    // SIMPLIFIED CONTENT - Plain text only (most reliable)
    $mail->isHTML(false); // PLAIN TEXT ONLY
    
    // Simple, clear subject
    $mail->Subject = 'Website Contact from ' . $name;
    
    // Simple, clean body text
    $mail->Body = "You have received a new message from your website contact form.\n\n";
    $mail->Body .= "Sender Details:\n";
    $mail->Body .= "Name: " . $name . "\n";
    $mail->Body .= "Email: " . $email . "\n";
    $mail->Body .= "Subject: " . $subject . "\n\n";
    $mail->Body .= "Message:\n";
    $mail->Body .= str_repeat("-", 50) . "\n";
    $mail->Body .= $message . "\n";
    $mail->Body .= str_repeat("-", 50) . "\n\n";
    $mail->Body .= "Sent: " . date('d/m/Y H:i:s') . "\n";
    $mail->Body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

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