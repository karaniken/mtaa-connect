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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .container { background: #2d1b33; padding: 40px; border-radius: 16px; box-shadow: 0 8px 30px rgba(128, 0, 128, 0.4); max-width: 500px; width: 100%; border: 1px solid #7a2e8a; }
        h1 { text-align: center; color: #c084d8; margin-bottom: 8px; font-size: 28px; }
        .subtitle { text-align: center; color: #b77dc2; margin-bottom: 24px; font-size: 14px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; color: #d4a0e0; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input, select { width: 100%; padding: 12px 14px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; font-size: 16px; transition: 0.3s; }
        input:focus, select:focus { border-color: #b84fd4; outline: none; box-shadow: 0 0 10px rgba(184, 79, 212, 0.3); }
        .error { color: #ff6b6b; font-size: 13px; margin-top: 4px; display: none; }
        .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; border-left: 4px solid #ff4444; margin-bottom: 16px; }
        .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; border-left: 4px solid #44ff44; margin-bottom: 16px; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); transform: scale(1.02); }
        .login-link { text-align: center; margin-top: 18px; color: #b77dc2; }
        .login-link a { color: #c084d8; text-decoration: none; font-weight: bold; }
        .login-link a:hover { text-decoration: underline; }
        .role-group { display: flex; gap: 20px; margin-top: 5px; }
        .role-group label { display: flex; align-items: center; gap: 8px; color: #d4a0e0; font-weight: normal; font-size: 16px; cursor: pointer; }
        input[type="radio"] { width: auto; accent-color: #b84fd4; transform: scale(1.2); }
    </style>
</head>
<body>
<div class="container">
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
