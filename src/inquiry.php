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
<head><title>Inquiry – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .container { background: #2d1b33; padding: 40px; border-radius: 16px; max-width: 600px; width: 100%; border: 1px solid #7a2e8a; }
    h1 { text-align: center; color: #c084d8; }
    .subtitle { text-align: center; color: #b77dc2; margin-bottom: 20px; }
    .unit-info { background: #1f0f24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #5a2a6a; }
    .unit-info h3 { color: #c084d8; }
    .unit-info p { color: #b77dc2; }
    label { display: block; color: #d4a0e0; font-weight: 600; margin: 10px 0 5px; }
    textarea { width: 100%; padding: 12px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; min-height: 120px; resize: vertical; }
    textarea:focus { border-color: #b84fd4; outline: none; }
    .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; }
    .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
    .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #b77dc2; }
    .back-link a { color: #c084d8; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
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
