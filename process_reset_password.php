<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['email']) || !isset($_SESSION['otp_verified'])) {
        $_SESSION['message'] = "Unauthorized access.";
        header("Location: forgot_password.php");
        exit();
    }

    $email = $_SESSION['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        header("Location: reset_password.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['message'] = "Password must be at least 6 characters.";
        header("Location: reset_password.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear OTP
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['message'] = "Password reset successful. Please login.";
        header("Location: login.php"); // make sure login.php exists
        exit();
    } else {
        $_SESSION['message'] = "Failed to reset password. Please try again.";
        header("Location: reset_password.php");
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}