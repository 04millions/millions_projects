<?php
// filepath: c:\wamp64\www\millions_project\profile.php
require_once 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, phone, address, bio, country_code FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Handle profile update (credentials)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_credentials'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);
    $country_code = trim($_POST['country_code']);

    $errors = [];

    // Validate country code
    if (!preg_match('/^\+\d+$/', $country_code)) {
        $errors[] = "Invalid country code. It must start with '+' and contain only numbers.";
    }

    // Country-specific phone number lengths
    $phoneLengths = [
        "+1" => 10,  // USA/Canada
        "+44" => 10, // UK
        "+91" => 10, // India
        "+61" => 9,  // Australia
        "+81" => 10, // Japan
        "+234" => 10, // Nigeria
        "+971" => 9,  // UAE
        // Add more country codes and lengths as needed
    ];

    // Validate phone number length
    if (isset($phoneLengths[$country_code]) && strlen($phone) !== $phoneLengths[$country_code]) {
        $errors[] = "Phone number must be exactly " . $phoneLengths[$country_code] . " digits for country code $country_code.";
    }

    // Validate email
    if (empty($username) || empty($email)) {
        $errors[] = "Username and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If no errors, update the user data
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ?, bio = ?, country_code = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $username, $email, $phone, $address, $bio, $country_code, $user_id);

        if ($stmt->execute()) {
            $success_credentials = "Profile updated successfully.";
            $_SESSION['username'] = $username; // Update session username
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}

// Handle password update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors_password = [];

    if (empty($password) || empty($confirm_password)) {
        $errors_password[] = "Both password fields are required.";
    } elseif ($password !== $confirm_password) {
        $errors_password[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors_password[] = "Password must be at least 8 characters.";
    }

    // If no errors, update the password
    if (empty($errors_password)) {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $user_id);

        if ($stmt->execute()) {
            $success_password = "Password updated successfully.";
        } else {
            $errors_password[] = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Your Profile</h1>

        <!-- Toggle Buttons -->
        <div class="flex space-x-4 mb-6">
            <button id="show-credentials" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update Profile</button>
            <button id="show-password" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Change Password</button>
        </div>

        <!-- Update Credentials Section -->
        <div id="credentials-section">
            <h2 class="text-xl font-semibold mb-4">Update Profile Information</h2>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($success_credentials)): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($success_credentials); ?>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block font-medium">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="email" class="block font-medium">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="phone" class="block font-medium">Phone Number</label>
                    <div class="flex space-x-2">
                        <!-- Country Code Input -->
                        <input type="text" id="country_code" name="country_code" value="<?php echo htmlspecialchars($user['country_code'] ?? ''); ?>" class="p-2 border rounded w-1/3" placeholder="+1" required>

                        <!-- Phone Number Input -->
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="flex-1 p-2 border rounded" placeholder="Enter phone number" required>
                    </div>
                    <small id="phone-error" class="text-red-500 hidden">Invalid phone number or country code.</small>
                </div>
                <div>
                    <label for="address" class="block font-medium">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="bio" class="block font-medium">Bio</label>
                    <textarea id="bio" name="bio" class="w-full p-2 border rounded"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="update_credentials" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update Profile</button>
            </form>
        </div>

        <!-- Update Password Section -->
        <div id="password-section" class="hidden">
            <h2 class="text-xl font-semibold mt-8 mb-4">Change Password</h2>
            <?php if (!empty($errors_password)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <ul>
                        <?php foreach ($errors_password as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($success_password)): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($success_password); ?>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST" class="space-y-4">
                <div>
                    <label for="password" class="block font-medium">New Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="confirm_password" class="block font-medium">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full p-2 border rounded" required>
                </div>
                <button type="submit" name="update_password" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update Password</button>
            </form>
        </div>
    </div>

    <script>
        const credentialsSection = document.getElementById('credentials-section');
        const passwordSection = document.getElementById('password-section');
        const showCredentials = document.getElementById('show-credentials');
        const showPassword = document.getElementById('show-password');

        showCredentials.addEventListener('click', () => {
            credentialsSection.classList.remove('hidden');
            passwordSection.classList.add('hidden');
        });

        showPassword.addEventListener('click', () => {
            passwordSection.classList.remove('hidden');
            credentialsSection.classList.add('hidden');
        });

        const countryCodeInput = document.getElementById('country_code');
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');

        // Country-specific phone number lengths
        const phoneLengths = {
            "+1": 10,  // USA/Canada
            "+44": 10, // UK
            "+91": 10, // India
            "+61": 9,  // Australia
            "+81": 10, // Japan
            "+234": 10, // Nigeria
            "+971": 9, // UAE
            // Add more country codes and lengths as needed
        };

        // Validate phone number on form submission
        document.querySelector('form').addEventListener('submit', (e) => {
            const countryCode = countryCodeInput.value.trim();
            const phone = phoneInput.value.trim();

            // Check if country code starts with "+" and contains only numbers
            if (!/^\+\d+$/.test(countryCode)) {
                phoneError.textContent = "Invalid country code. It must start with '+' and contain only numbers.";
                phoneError.classList.remove('hidden');
                e.preventDefault();
                return;
            }

            // Check if phone number length matches the expected length for the country
            const expectedLength = phoneLengths[countryCode];
            if (expectedLength && phone.length !== expectedLength) {
                phoneError.textContent = `Phone number must be exactly ${expectedLength} digits for country code ${countryCode}.`;
                phoneError.classList.remove('hidden');
                e.preventDefault();
                return;
            }

            // Hide error if validation passes
            phoneError.classList.add('hidden');
        });
    </script>
</body>
</html>