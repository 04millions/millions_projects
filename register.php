<?php
$success = "";
$error = "";

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'millions_project';
$port = 3308;

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $fullname = trim($_POST['fullname']);
    $idnumber = trim($_POST['idnumber']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $city = trim($_POST['city']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $terms = isset($_POST['terms']) && $_POST['terms'] === "1" ? 1 : 0;

    // Validate phone number: starts with 1 or 7 and exactly 9 digits
    if (!preg_match('/^[a-zA-Z ]+$/', $fullname)) {
        $error = "Name must contain only letters and spaces.";
    } elseif (!preg_match('/^[17][0-9]{8}$/', $phone)) {
        $error = "Phone number must be exactly 9 digits, start with 1 or 7, and contain only numbers.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!isset($_POST['terms']) || $_POST['terms'] !== "1") {
        $error = "You must agree to the terms and conditions.";
    } else {
        //check if email exists
        $check_stmt = $conn->prepare("SElect id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already exists!";
        }
        else {
            // check if phone number exists
            $check_phone_stmt = $conn->prepare("SElect id FROM users WHERE phone = ?");
            $check_phone_stmt->bind_param("s", $phone);
            $check_phone_stmt->execute();
            $check_phone_result = $check_phone_stmt->get_result();

                if ($check_phone_result->num_rows > 0) {
                    $error = "Phone number already exists!";
                } else {
                    $hashed_password = $password; // Store plain password (NOT SECURE)
                    $sql = "INSERT INTO users (fullname, idnumber, phone, email, city, password, terms, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ssssssi", $fullname, $idnumber, $phone, $email, $city, $hashed_password, $terms);
                        if ($stmt->execute()) {
                            $success = "Registration successful! <a href='login.php'>Login here</a>";
                        } else {
                            $error = "Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $error = "Database error: " . $conn->error;
                    }
                }
            }
        }
        
    }
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Registration Form</title>
</head>
<body>
    
    <div class="wrapper">
        <div class="header">
            <h1>Registration</h1>
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
                <i class="fa fa-user"></i>
                <input type="text" id="fullname" name="fullname" placeholder="Fullname as per ID" required
                    value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>">
            </div>
            <div class="form-group">
                <i class="fa fa-id-card"></i>
                <input type="text" id="idnumber" name="idnumber" placeholder="ID Number" required
                    value="<?php echo isset($idnumber) ? htmlspecialchars($idnumber) : ''; ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: center;">
                <span style="padding: 10px; background-color: #ccc;color: black; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">+254</span>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    placeholder="7XXXXXXXX or 1XXXXXXXX" 
                    pattern="^[0-9]{9}$"
                    required 
                    style="flex: 1; border: none; border-bottom: 4px double #ccc; border-radius: 0 4px 4px 0; padding: 10px;"
                    value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
            </div>
            <div class="form-group">
                <i class="fa fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email" required
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
               
            </div>
            <div class="form-group">
                <i class="fa fa-map-marker"></i>
                <select id="city" name="city" required>
                    <option value="" disabled <?php if (!isset($city) || $city == '') echo 'selected'; ?>>Select your city</option>
                    <option value="Mombasa" <?php if (isset($city) && $city == 'Mombasa') echo 'selected'; ?>>Mombasa</option>
                    <option value="Nairobi" <?php if (isset($city) && $city == 'Nairobi') echo 'selected'; ?>>Nairobi</option>
                    <option value="Kisumu" <?php if (isset($city) && $city == 'Kisumu') echo 'selected'; ?>>Kisumu</option>
                    <option value="Nakuru" <?php if (isset($city) && $city == 'Nakuru') echo 'selected'; ?>>Nakuru</option>
                    <option value="Eldoret" <?php if (isset($city) && $city == 'Eldoret') echo 'selected'; ?>>Eldoret</option>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                <i class="fa fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required minlength="8" maxlength="12" style="flex:1; padding-right:40px;">
                <button type="button" onclick="togglePassword('password','toggleIcon1')" 
                    style="position: absolute; right: 40px; padding-bottom: 40px; background: none; border: none; cursor: pointer;">
                    <i id="toggleIcon1" class="fa fa-eye" style="color:#888;"></i>
                </button>
            </div>

            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                <i class="fa fa-lock"></i>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required minlength="8" maxlength="12" style="flex:1; padding-right:40px;">
                <button type="button" onclick="togglePassword('confirm-password','toggleIcon2')" 
                    style="position: absolute; right: 40px;padding-bottom: 40px; background: none; border: none; cursor: pointer;">
                    <i id="toggleIcon2" class="fa fa-eye" style="color:#888;"></i>
                </button>
            </div>

            <div>
                <input type="checkbox" id="terms" name="terms" value="1" required>
                <label for="terms">By signing up, you agree to our <a href="#">Terms and Conditions</a></label>
            </div>
            <div class="form-group">
                <button type="submit" value="Register" name="submit" class="btn">Sign Up</button>
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
            <p>Already Have an Account? <a href="login.php">Login Here</a></p>
            </div>
            <div class="footer">
                <p>&copy; 2025 Your Company. All rights reserved.</p>
            </div>
    </form>
</div>

<script>
function togglePassword(inputId, iconId) {
    var fields = [
        {input: 'password', icon: 'toggleIcon1'},
        {input: 'confirm-password', icon: 'toggleIcon2'}
    ];

    fields.forEach(function(field) {
        var pwd = document.getElementById(field.input);
        var icon = document.getElementById(field.icon);
        if (field.input === inputId) {
            // Show this eye icon
            icon.style.display = "inline";
            // Toggle the clicked one
            if (pwd.type === "password") {
                pwd.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
                icon.style.color = "#007bff";
            } else {
                pwd.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
                icon.style.color = "#888";
            }
        } else {
            // Hide the other eye icon and mask its password
            pwd.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
            icon.style.color = "#888";
            icon.style.display = "none";
        }
    });
}
</script>

</body>
</html>