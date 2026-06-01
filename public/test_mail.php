<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load .env
$env = parse_ini_file('.env');

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $env['MAIL_USERNAME'] ?? 'smartestdevelopers@gmail.com';
    $mail->Password   = $env['MAIL_PASSWORD'] ?? 'wpeygokozmfufeyw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $env['MAIL_PORT'] ?? 465;

    //Recipients
    $mail->setFrom($env['MAIL_FROM_ADDRESS'] ?? 'smartestdevelopers@gmail.com', $env['MAIL_FROM_NAME'] ?? 'Test');
    $mail->addAddress($env['MAIL_USERNAME'] ?? 'smartestdevelopers@gmail.com');

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test';
    $mail->Body    = 'This is a test email to verify SMTP settings.';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
