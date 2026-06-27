<?php
require_once 'config/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Server-side validation
    if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($user_type)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already registered. Please login.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (fullname, email, phone, password_hash, user_type)
                      VALUES ('$fullname', '$email', '$phone', '$hashed', '$user_type')";
            if (mysqli_query($conn, $query)) {
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
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
    <title>Register – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <h1>🏠 Mtaa‑Connect</h1>
    <p class="subtitle">Join your community marketplace</p>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= $success ?></div>
    <?php endif; ?>

    <form id="registerForm" method="POST" action="">
        <div class="form-group">
            <label>I am a:</label>
            <div class="role-group">
                <label><input type="radio" name="user_type" value="landlord" checked> Landlord</label>
                <label><input type="radio" name="user_type" value="tenant"> Tenant</label>
            </div>
        </div>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="e.g. John Doe" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
            <div class="error" id="fullnameError">Please enter your full name.</div>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            <div class="error" id="emailError">Please enter a valid email.</div>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="0712345678" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
            <div class="error" id="phoneError">Please enter a valid phone number.</div>
        </div>

        <div class="form-group">
            <label>Password (min 6 characters)</label>
            <input type="password" id="password" name="password" placeholder="••••••••">
            <div class="error" id="passwordError">Password must be at least 6 characters.</div>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" id="confirmPassword" placeholder="••••••••">
            <div class="error" id="confirmError">Passwords do not match.</div>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <div class="login-link">Already have an account? <a href="login.php">Login</a></div>
</div>

<script>
    (function() {
        const form = document.getElementById('registerForm');
        const fullname = document.getElementById('fullname');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const password = document.getElementById('password');
        const confirmPwd = document.getElementById('confirmPassword');

        const errFullname = document.getElementById('fullnameError');
        const errEmail = document.getElementById('emailError');
        const errPhone = document.getElementById('phoneError');
        const errPassword = document.getElementById('passwordError');
        const errConfirm = document.getElementById('confirmError');

        function validateField(field, errorEl, condition, message) {
            if (!condition) {
                errorEl.style.display = 'block';
                field.style.borderColor = '#ff4444';
                return false;
            } else {
                errorEl.style.display = 'none';
                field.style.borderColor = '#5a2a6a';
                return true;
            }
        }

        function validateForm() {
            let valid = true;

            const nameValid = fullname.value.trim().length >= 2;
            valid &= validateField(fullname, errFullname, nameValid, '');

            const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
            valid &= validateField(email, errEmail, emailValid, '');

            const phoneValid = /^[0-9]{10,15}$/.test(phone.value.trim());
            valid &= validateField(phone, errPhone, phoneValid, '');

            const pwdValid = password.value.length >= 6;
            valid &= validateField(password, errPassword, pwdValid, '');

            const confirmValid = password.value === confirmPwd.value && password.value !== '';
            valid &= validateField(confirmPwd, errConfirm, confirmValid, '');

            return !!valid;
        }

        // Real-time validation on blur
        [fullname, email, phone, password, confirmPwd].forEach(field => {
            field.addEventListener('blur', function() {
                if (this === fullname) {
                    validateField(fullname, errFullname, fullname.value.trim().length >= 2, '');
                } else if (this === email) {
                    validateField(email, errEmail, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()), '');
                } else if (this === phone) {
                    validateField(phone, errPhone, /^[0-9]{10,15}$/.test(phone.value.trim()), '');
                } else if (this === password) {
                    validateField(password, errPassword, password.value.length >= 6, '');
                } else if (this === confirmPwd) {
                    validateField(confirmPwd, errConfirm, password.value === confirmPwd.value && password.value !== '', '');
                }
            });
        });

        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    })();
</script>
</body>
</html>
