<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>AUSI TTT Email Configuration Test</h1>";
echo "<hr>";

// Test 1: Check PHP version
echo "<h2>1. PHP Version Check</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo (version_compare(phpversion(), '7.0.0', '>=')) ? "✓ PHP version is compatible<br>" : "✗ PHP version too old<br>";
echo "<hr>";

// Test 2: Check if PHPMailer is available
echo "<h2>2. PHPMailer Availability</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✓ Composer autoload found<br>";
    require 'vendor/autoload.php';
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✓ PHPMailer class is available<br>";
        
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;
        
        $mail = new PHPMailer(true);
        echo "✓ PHPMailer object created successfully<br>";
    } else {
        echo "✗ PHPMailer class not found<br>";
    }
} else {
    echo "✗ Composer autoload not found<br>";
    echo "Checking for manual PHPMailer installation...<br>";
    
    // Check common manual installation paths
    $possible_paths = [
        'PHPMailer/PHPMailer.php',
        'phpmailer/PHPMailer.php',
        'lib/PHPMailer.php',
        'includes/PHPMailer.php'
    ];
    
    $found = false;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            echo "✓ Found PHPMailer at: $path<br>";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ PHPMailer not found in common locations<br>";
        echo "<strong style='color: red;'>PHPMailer is NOT installed!</strong><br>";
    }
}
echo "<hr>";

// Test 3: Check SMTP connection
echo "<h2>3. SMTP Connection Test</h2>";
$smtp_host = 'mail.ausitttfuneralservices.co.za';
$smtp_port = 465;

echo "Testing connection to $smtp_host:$smtp_port...<br>";

$connection = @fsockopen('ssl://' . $smtp_host, $smtp_port, $errno, $errstr, 10);
if ($connection) {
    echo "✓ Successfully connected to SMTP server<br>";
    $response = fgets($connection, 515);
    echo "Server response: " . htmlspecialchars($response) . "<br>";
    fclose($connection);
} else {
    echo "✗ Failed to connect to SMTP server<br>";
    echo "Error: $errstr ($errno)<br>";
}
echo "<hr>";

// Test 4: Check required PHP extensions
echo "<h2>4. Required PHP Extensions</h2>";
$required_extensions = ['openssl', 'sockets', 'filter'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext extension is loaded<br>";
    } else {
        echo "✗ $ext extension is NOT loaded<br>";
    }
}
echo "<hr>";

// Test 5: Try sending a test email if PHPMailer is available
echo "<h2>5. Test Email Send</h2>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = 'mail.ausitttfuneralservices.co.za';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@ausitttfuneralservices.co.za';
        $mail->Password   = 'MGH@infoAUSI2026';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        
        // Recipients
        $mail->setFrom('info@ausitttfuneralservices.co.za', 'Test Sender');
        $mail->addAddress('info@ausitttfuneralservices.co.za', 'Test Recipient');
        
        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Test Email from Configuration Test';
        $mail->Body    = 'This is a test email sent from the configuration test script.';
        
        echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
        $mail->send();
        echo "</pre>";
        echo "<strong style='color: green;'>✓ Test email sent successfully!</strong><br>";
        
    } catch (Exception $e) {
        echo "<pre style='background: #ffe0e0; padding: 10px; border-radius: 5px;'>";
        echo "Error: {$mail->ErrorInfo}\n";
        echo "Exception: " . $e->getMessage();
        echo "</pre>";
        echo "<strong style='color: red;'>✗ Failed to send test email</strong><br>";
    }
} else {
    echo "<strong style='color: orange;'>Skipped - PHPMailer not available</strong><br>";
}
echo "<hr>";

// Test 6: Check file permissions
echo "<h2>6. File Permissions</h2>";
echo "Current directory: " . getcwd() . "<br>";
echo "send.php exists: " . (file_exists('send.php') ? 'Yes' : 'No') . "<br>";
echo "send.php readable: " . (is_readable('send.php') ? 'Yes' : 'No') . "<br>";
echo "<hr>";

// Test 7: Display server information
echo "<h2>7. Server Information</h2>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "<hr>";

echo "<h2>Summary</h2>";
echo "<p>If you see errors above, please share this page output for troubleshooting.</p>";
echo "<p><a href='contact.html'>Back to Contact Form</a></p>";
?>
