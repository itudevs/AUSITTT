<?php
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
    $mail->Username   = 'info@ausitttfuneralservices.co.za'; 
    $mail->Password   = 'MGH@AUSITTT2026';        
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'Website Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za'); 
    $mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Message: ' . $_POST['subject'];
    $mail->Body    = "Name: " .   $_POST['name'] . "\n" .
                     "Email: " . $_POST['email'] . "\n\n" .
                     "Message:\n" . $_POST['message'];

    $mail->send();
    // Redirect to the message-sent.html page
    header('Location: message-sent.html');
    exit();
} catch (Exception $e) {
    // Clear the session storage flag on error
    echo '<script>sessionStorage.removeItem("messageSubmitted");</script>';
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - AUSI TTT Funeral Services</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: "Roboto", sans-serif;
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 600px;
                width: 100%;
                padding: 50px 40px;
                text-align: center;
            }
            .error-icon {
                width: 100px;
                height: 100px;
                margin: 0 auto 30px;
                background: linear-gradient(135deg, #dc3545, #c82333);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 3rem;
                box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
            }
            h1 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 15px;
                color: #dc3545;
            }
            p {
                font-size: 1.1rem;
                color: #666;
                margin-bottom: 20px;
                line-height: 1.6;
            }
            .error-details {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: left;
                font-size: 0.9rem;
            }
            .btn-group {
                display: flex;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 30px;
            }
            .btn {
                display: inline-block;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.05rem;
                transition: all 0.3s ease;
            }
            .btn-primary {
                background: linear-gradient(135deg, #0038B8, #0052E0);
                color: white;
                box-shadow: 0 5px 20px rgba(0, 56, 184, 0.4);
            }
            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 56, 184, 0.6);
            }
            .btn-secondary {
                background: #6c757d;
                color: white;
                box-shadow: 0 5px 20px rgba(108, 117, 125, 0.4);
            }
            .btn-secondary:hover {
                transform: translateY(-3px);
                background: #5a6268;
            }
            .contact-info {
                margin-top: 30px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 10px;
                border-left: 4px solid #0038B8;
            }
            .contact-info p {
                margin: 10px 0;
                color: #333;
            }
            .contact-info strong {
                color: #0038B8;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">✖</div>
            
            <h1>Message Not Sent</h1>
            <p>We apologize, but there was an error sending your message.</p>
            
            <div class="error-details">
                <strong>Error Details:</strong><br>
                ' . htmlspecialchars($mail->ErrorInfo) . '
            </div>

            <div class="contact-info">
                <p><strong>Please contact us directly:</strong></p>
                <p>📞 Phone: +27 (60) 724-7928</p>
                <p>✉️ Email: ausibotlokwa@ausitttfuneralservices.co.za</p>
                <p>⏰ Available 24/7 for urgent matters</p>
            </div>
            
            <div class="btn-group">
                <a href="contact.html" class="btn btn-primary">Try Again</a>
                <a href="index.html" class="btn btn-secondary">Return Home</a>
            </div>
        </div>
    </body>
    </html>
    ';
}