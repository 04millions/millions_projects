<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
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
        <h2>Forgot Password</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="process_forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send OTP</button>
        </form>
    </div>
</body>
</html>
