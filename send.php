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
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Message Sent - AUSI TTT Funeral Services</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: "Roboto", sans-serif;
                background: linear-gradient(135deg, #0038B8 0%, #0052E0 50%, #8E791D 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .message-container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 600px;
                width: 100%;
                padding: 50px 40px;
                text-align: center;
                animation: slideIn 0.6s ease-out;
            }
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .success-icon {
                width: 100px;
                height: 100px;
                margin: 0 auto 30px;
                background: linear-gradient(135deg, #0038B8, #0052E0);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 30px rgba(0, 56, 184, 0.3);
                animation: scaleIn 0.5s ease-out 0.3s both;
            }
            @keyframes scaleIn {
                from {
                    transform: scale(0);
                }
                to {
                    transform: scale(1);
                }
            }
            .success-icon svg {
                width: 50px;
                height: 50px;
                stroke: white;
                stroke-width: 3;
                fill: none;
                stroke-linecap: round;
                stroke-linejoin: round;
            }
            .checkmark {
                stroke-dasharray: 100;
                stroke-dashoffset: 100;
                animation: draw 0.8s ease-out 0.5s forwards;
            }
            @keyframes draw {
                to {
                    stroke-dashoffset: 0;
                }
            }
            h1 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 15px;
                background: linear-gradient(135deg, #0038B8, #8E791D);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            p {
                font-size: 1.1rem;
                color: #666;
                margin-bottom: 30px;
                line-height: 1.6;
            }
            .divider {
                width: 60px;
                height: 4px;
                background: linear-gradient(90deg, #0038B8, #8E791D);
                margin: 30px auto;
                border-radius: 2px;
            }
            .info-box {
                background: #f8f9fa;
                border-left: 4px solid #0038B8;
                padding: 20px;
                margin: 30px 0;
                border-radius: 8px;
                text-align: left;
            }
            .info-box p {
                margin: 5px 0;
                font-size: 0.95rem;
            }
            .info-box strong {
                color: #0038B8;
            }
            .btn-home {
                display: inline-block;
                background: linear-gradient(135deg, #0038B8, #0052E0);
                color: white;
                padding: 15px 40px;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.05rem;
                transition: all 0.3s ease;
                box-shadow: 0 5px 20px rgba(0, 56, 184, 0.4);
                margin-top: 10px;
            }
            .btn-home:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 56, 184, 0.6);
            }
            .footer-text {
                margin-top: 30px;
                font-size: 0.9rem;
                color: #999;
            }
            .contact-icons {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin-top: 25px;
            }
            .contact-icon {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #555;
                font-size: 0.9rem;
            }
            .icon-circle {
                width: 35px;
                height: 35px;
                background: #8E791D;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.9rem;
            }
        </style>
    </head>
    <body>
        <div class="message-container">
            <div class="success-icon">
                <svg viewBox="0 0 52 52">
                    <polyline class="checkmark" points="14 27 22 35 38 19"/>
                </svg>
            </div>
            
            <h1>Message Sent Successfully!</h1>
            <div class="divider"></div>
            <p>Thank you for reaching out to AUSI TTT Funeral Services. We have received your message and will respond as soon as possible.</p>
            
            <div class="info-box">
                <p><strong>What happens next?</strong></p>
                <p>✓ Our compassionate team will review your message</p>
                <p>✓ You will receive a response within 24 hours</p>
                <p>✓ For urgent matters, please call us directly</p>
            </div>

            <div class="contact-icons">
                <div class="contact-icon">
                    <div class="icon-circle">📞</div>
                    <span>+27 (60) 724-7928</span>
                </div>
                <div class="contact-icon">
                    <div class="icon-circle">⏰</div>
                    <span>24/7 Available</span>
                </div>
            </div>
            
            <a href="index.html" class="btn-home">Return to Home</a>
            
            <p class="footer-text">AUSI TTT Funeral Services - Compassionate Care When You Need It Most</p>
        </div>
    </body>
    </html>
    ';
} catch (Exception $e) {
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