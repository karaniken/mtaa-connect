<?php
session_start();
// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once 'config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        $query = "SELECT id, fullname, email, password_hash, user_type FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password_hash'])) {
                // Login successful – set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_type'] = $row['user_type'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'No account found with that email.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login – Mtaa-Connect</title>
    <style>
        /* Same style as register.php – copy from register.php or include common CSS */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: #2d1b33; padding: 40px; border-radius: 16px; max-width: 400px; width: 100%; border: 1px solid #7a2e8a; }
        h1 { text-align: center; color: #c084d8; }
        .subtitle { text-align: center; color: #b77dc2; margin-bottom: 24px; }
        label { display: block; color: #d4a0e0; font-weight: 600; margin: 10px 0 5px; }
        input { width: 100%; padding: 12px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; }
        input:focus { border-color: #b84fd4; outline: none; }
        .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; }
        .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
        .register-link { text-align: center; margin-top: 18px; color: #b77dc2; }
        .register-link a { color: #c084d8; text-decoration: none; }
        .error { color: #ff6b6b; font-size: 13px; display: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>🏠 Mtaa‑Connect</h1>
    <p class="subtitle">Login to your account</p>

    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

    <form id="loginForm" method="POST">
        <label>Email</label>
        <input type="email" id="email" name="email" required>
        <div class="error" id="emailError">Email is required.</div>

        <label>Password</label>
        <input type="password" id="password" name="password" required>
        <div class="error" id="passwordError">Password is required.</div>

        <button type="submit" class="btn">Login</button>
    </form>

    <div class="register-link">Don't have an account? <a href="register.php">Register</a></div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const emailErr = document.getElementById('emailError');
    const pwdErr = document.getElementById('passwordError');

    if (email.value.trim() === '') {
        emailErr.style.display = 'block';
        valid = false;
    } else {
        emailErr.style.display = 'none';
    }
    if (password.value.trim() === '') {
        pwdErr.style.display = 'block';
        valid = false;
    } else {
        pwdErr.style.display = 'none';
    }
    if (!valid) e.preventDefault();
});
</script>
</body>
</html>
