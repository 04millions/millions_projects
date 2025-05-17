<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['email'])) {
        $_SESSION['message'] = "Session expired. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }

    $email = $_SESSION['email'];
    $entered_otp = trim($_POST['otp']);

    // Get OTP from DB
    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['message'] = "Email not found.";
        header("Location: forgot_password.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $stored_otp = $row['otp'];
    $otp_expiry = $row['otp_expiry'];

    // Validate OTP and expiry
    if ($entered_otp == $stored_otp) {
        if (strtotime($otp_expiry) >= time()) {
            $_SESSION['otp_verified'] = true;
            header("Location: reset_password.php");
            exit();
        } else {
            $_SESSION['message'] = "OTP has expired. Please request a new one.";
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Invalid OTP. Please try again.";
        header("Location: verify_otp.php");
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
