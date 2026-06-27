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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
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
