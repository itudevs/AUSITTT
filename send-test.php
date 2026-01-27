<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Script started...<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "POST request received...<br>";
    
    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Subject: " . htmlspecialchars($subject) . "<br>";
    echo "Message received: " . (!empty($message) ? "Yes" : "No") . "<br>";
    
    // Simple email configuration
    $to = 'info@ausitttfuneralservices.co.za';
    $email_subject = 'Website Message: ' . $subject;
    $email_body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: $email\r\nReply-To: $email\r\n";
    
    echo "<br>Attempting to send email...<br>";
    
    if (mail($to, $email_subject, $email_body, $headers)) {
        echo "Email sent successfully!<br>";
        echo '<a href="message-sent.html">Go to success page</a>';
    } else {
        echo "Email failed to send.<br>";
        echo "Error: " . error_get_last()['message'] ?? 'Unknown error';
    }
} else {
    echo "No POST request. Please submit the form.";
}
?>
