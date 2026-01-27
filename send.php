<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: contact.html');
    exit();
}

// Get and sanitize form data
$name = isset($_POST['name']) ? stripslashes(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? stripslashes(trim($_POST['email'])) : '';
$subject = isset($_POST['subject']) ? stripslashes(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? stripslashes(trim($_POST['message'])) : '';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=invalid_email');
    exit();
}

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

    // Recipients
    $mail->setFrom('info@ausitttfuneralservices.co.za', 'Website Contact Form');
    $mail->addAddress('info@ausitttfuneralservices.co.za');
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'Website Contact: ' . $subject;
    $mail->Body    = "New message from website contact form\n\n" .
                     "Name: " . $name . "\n" .
                     "Email: " . $email . "\n" .
                     "Subject: " . $subject . "\n\n" .
                     "Message:\n" . $message;

    $mail->send();
    
    // Redirect to success page
    header('Location: message-sent.html');
    exit();
    
} catch (Exception $e) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - AUSI TTT Funeral Services</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="assets/css/main.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #0038B8 0%, #0052E0 50%, #8E791D 100%);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .error-section {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 80px 20px 40px;
            }
            
            .error-container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 700px;
                width: 100%;
                padding: 50px 40px;
                text-align: center;
                animation: slideUp 0.6s ease-out;
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
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
                animation: scaleIn 0.5s ease-out 0.2s both;
            }
            
            @keyframes scaleIn {
                from { transform: scale(0); }
                to { transform: scale(1); }
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
            
            .divider {
                width: 80px;
                height: 4px;
                background: linear-gradient(90deg, #0038B8, #8E791D);
                margin: 20px auto;
                border-radius: 2px;
            }
            
            .error-details {
                background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.05));
                border-left: 4px solid #dc3545;
                padding: 20px;
                border-radius: 8px;
                margin: 25px 0;
                text-align: left;
            }
            
            .error-details strong {
                color: #dc3545;
                display: block;
                margin-bottom: 10px;
                font-size: 1.1rem;
            }
            
            .error-details p {
                margin: 0;
                font-size: 0.95rem;
                color: #721c24;
            }
            
            .btn-group {
                display: flex;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 30px;
            }
            
            .btn-custom {
                display: inline-block;
                padding: 15px 35px;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.05rem;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }
            
            .btn-primary-custom {
                background: linear-gradient(135deg, #0038B8, #0052E0);
                color: white;
                box-shadow: 0 5px 20px rgba(0, 56, 184, 0.4);
            }
            
            .btn-primary-custom:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 56, 184, 0.6);
                color: white;
            }
            
            .btn-secondary-custom {
                background: #8E791D;
                color: white;
                box-shadow: 0 5px 20px rgba(142, 121, 29, 0.4);
            }
            
            .btn-secondary-custom:hover {
                transform: translateY(-3px);
                background: #6d5e16;
                color: white;
            }
            
            .contact-info {
                margin-top: 30px;
                padding: 25px;
                background: linear-gradient(135deg, rgba(0, 56, 184, 0.05), rgba(142, 121, 29, 0.05));
                border-radius: 10px;
                border-left: 4px solid #0038B8;
            }
            
            .contact-info p {
                margin: 12px 0;
                color: #333;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            
            .contact-info strong {
                color: #0038B8;
                display: block;
                margin-bottom: 15px;
                font-size: 1.1rem;
            }
            
            .contact-info i {
                color: #0038B8;
                font-size: 1.2rem;
            }
            
            @media (max-width: 768px) {
                .error-container {
                    padding: 35px 25px;
                }
                
                h1 {
                    font-size: 2rem;
                }
                
                .btn-group {
                    flex-direction: column;
                }
                
                .btn-custom {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <section class="error-section">
            <div class="error-container">
                <div class="error-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                
                <h1>Message Not Sent</h1>
                <div class="divider"></div>
                <p>We apologize, but there was an error sending your message. Please try again or contact us directly.</p>
                
                <div class="error-details">
                    <strong><i class="bi bi-info-circle"></i> Technical Details:</strong>
                    <p>' . htmlspecialchars($e->getMessage()) . '</p>
                </div>

                <div class="contact-info">
                    <strong>Alternative Contact Methods:</strong>
                    <p><i class="bi bi-telephone-fill"></i> <span>+27 60 724-7928 (Available 24/7)</span></p>
                    <p><i class="bi bi-envelope-fill"></i> <span>ausibotlokwa@ausitttfuneralservices.co.za</span></p>
                    <p><i class="bi bi-geo-alt-fill"></i> <span>Stand No 147 Sefene Village Botlokwa 0812</span></p>
                </div>
                
                <div class="btn-group">
                    <a href="contact.html" class="btn-custom btn-primary-custom">
                        <i class="bi bi-arrow-clockwise"></i> Try Again
                    </a>
                    <a href="index.html" class="btn-custom btn-secondary-custom">
                        <i class="bi bi-house-door"></i> Return Home
                    </a>
                </div>
            </div>
        </section>
    </body>
    </html>';
}
?>