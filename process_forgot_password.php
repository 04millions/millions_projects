<?php
session_start();
require 'db.php';

// PHPMailer manually included
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['message'] = "This email is not registered.";
        header("Location: forgot_password.php");
        exit();
    } else {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Save OTP to database
        $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $otp, $expiry, $email);
        $stmt->execute();

        // Send OTP via PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';        
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dannyreagan37@gmail.com';  
            $mail->Password   = 'bniv elgm qvch ekpl';           
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('no-reply@yourdomain.com', 'Your App');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your One-Time Password (OTP) is: <b>$otp</b><br><br>This code will expire in 10 minutes.<br><br> If you did not request this, please ignore this email.Please do not reply to this email because the address is not monitored.For further assistance, <a href='support.php'>Contact Support</a> if may need help. Thanks for your support and understanding as it helps us to serve you better.";

            $mail->send();

            $_SESSION['email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to send OTP: {$mail->ErrorInfo}";
            header("Location: forgot_password.php");
            exit();
        }
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
