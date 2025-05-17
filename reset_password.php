<?php session_start(); ?>
<?php
if (!isset($_SESSION['email']) || !isset($_SESSION['otp_verified'])) {
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #ffc107;
            color: black;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e0a800;
        }
        .message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="process_reset_password.php" method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
