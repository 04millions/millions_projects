<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'millions_project';
$port = 3308;

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $fullname, $hashed_password);
            $stmt->fetch();

            if ($password === $hashed_password) {
                // Login successful
                $_SESSION["user_id"] = $user_id;
                $_SESSION["fullname"] = $fullname;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h2>Login</h2>
    </div>
    <?php if (!empty($error)): ?>
        <div style="color: #fff; background: #e74c3c; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align:center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div style="color: #fff; background: #27ae60; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align:center;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" required
            value="<?php echo isset($email) ? $email : ''; ?>">
        </div>
        <div class="form-group" style="display: flex; align-items: center; position: relative;">
            <i class="fa fa-lock"></i>
            <input type="password" id="password" name="password" maxlength="12" minlength="8" required
                value="<?php echo isset($password) ? $password : ''; ?>">
            <button type="button" onclick="togglePassword()" 
                style="position: absolute; right: 70px; padding-bottom: 40px; background: none; border: none; cursor: pointer;">
                <i id="toggleIcon" class="fa fa-eye"></i>
            </button>
        </div>
        <div class="recover">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        <div class="form-group">
            <button type="submit" name="submit" value="Login" class="btn">Login</button>
        </div>
        <div class="or">
            <p>---------------or---------------</p>
        </div>
        <div class="icons">
            <i class="fab fa-facebook"></i>
            <i class="fab fa-twitter"></i>
            <i class="fab fa-google"></i>
        </div>
        <div class="links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Your Company. All rights reserved.</p>
        </div>
    </form>
</div>
<script>
function togglePassword() {
    var pwd = document.getElementById("password");
    var icon = document.getElementById("toggleIcon");
    if (pwd.type === "password") {
        pwd.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
        icon.style.color = "#007bff"; // Blue when visible
    } else {
        pwd.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
        icon.style.color = "#888"; // Gray when hidden
    }
}
</script>
</body>
</html>
