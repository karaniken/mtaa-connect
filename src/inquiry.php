<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$tenant_id = $_SESSION['user_id'];
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$error = $success = '';

// Fetch unit details (ensure it exists and is vacant)
if ($unit_id > 0) {
    $unit_query = mysqli_query($conn, "SELECT u.*, p.title as property_title, p.location FROM units u JOIN properties p ON u.property_id = p.id WHERE u.id = $unit_id AND u.status = 'vacant'");
    $unit = mysqli_fetch_assoc($unit_query);
    if (!$unit) {
        header('Location: browse.php');
        exit();
    }
} else {
    header('Location: browse.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    if (empty($message)) {
        $error = 'Please enter a message.';
    } else {
        $query = "INSERT INTO inquiries (unit_id, tenant_id, message) VALUES ('$unit_id', '$tenant_id', '$message')";
        if (mysqli_query($conn, $query)) {
            $success = 'Your inquiry has been sent to the landlord. You will be notified when they reply.';
        } else {
            $error = 'Failed to send inquiry: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <h1>📩 Contact Landlord</h1>
    <div class="unit-info">
        <h3><?= htmlspecialchars($unit['property_title']) ?></h3>
        <p>📍 <?= htmlspecialchars($unit['location']) ?> • <?= $unit['size'] ?> • KES <?= number_format($unit['price']) ?></p>
        <p>Unit: <?= htmlspecialchars($unit['house_number']) ?></p>
    </div>
    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>
    <?php if (!$success): ?>
    <form method="POST">
        <label>Your Message</label>
        <textarea name="message" placeholder="Describe what you're looking for, ask about availability, etc." required></textarea>
        <button type="submit" class="btn">Send Inquiry</button>
    </form>
    <?php endif; ?>
    <div class="back-link"><a href="browse.php">← Back to Browse</a></div>
</div>
</body>
</html>
