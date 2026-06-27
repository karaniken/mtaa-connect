<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$landlord_id = $_SESSION['user_id'];
$reply_error = $reply_success = '';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $inquiry_id = (int)$_POST['inquiry_id'];
    $reply = mysqli_real_escape_string($conn, $_POST['reply_text']);
    if (!empty($reply)) {
        $update = "UPDATE inquiries SET reply = '$reply', replied = TRUE WHERE id = $inquiry_id";
        if (mysqli_query($conn, $update)) {
            $reply_success = 'Reply sent successfully.';
        } else {
            $reply_error = 'Failed to send reply.';
        }
    } else {
        $reply_error = 'Please enter a reply.';
    }
}

// Mark inquiry as read when viewed (landlord sees it)
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $read_id = (int)$_GET['mark_read'];
    mysqli_query($conn, "UPDATE inquiries SET is_read = TRUE WHERE id = $read_id");
}

// Fetch all inquiries for this landlord's properties
$query = "SELECT i.*, u.house_number, u.size, u.price, p.title as property_title, p.location,
          t.fullname as tenant_name, t.email as tenant_email, t.phone as tenant_phone
          FROM inquiries i
          JOIN units u ON i.unit_id = u.id
          JOIN properties p ON u.property_id = p.id
          JOIN users t ON i.tenant_id = t.id
          WHERE p.landlord_id = $landlord_id
          ORDER BY i.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <div class="header">
        <h1>📩 Inquiries</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <?php if ($reply_error): ?><div class="error-msg"><?= $reply_error ?></div><?php endif; ?>
    <?php if ($reply_success): ?><div class="success-msg"><?= $reply_success ?></div><?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
            // Mark as read automatically when displayed
            if (!$row['is_read']) {
                mysqli_query($conn, "UPDATE inquiries SET is_read = TRUE WHERE id = {$row['id']}");
            }
            ?>
            <div class="inquiry-item">
                <div>
                    <span class="tenant"><?= htmlspecialchars($row['tenant_name']) ?></span>
                    <span class="unread"><?= $row['is_read'] ? 'Read' : 'New' ?></span>
                </div>
                <div class="property">
                    <?= htmlspecialchars($row['property_title']) ?> • <?= htmlspecialchars($row['location']) ?>
                    (<?= $row['size'] ?> • KES <?= number_format($row['price']) ?>)
                </div>
                <div class="message"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                <div style="font-size:13px; color:#b77dc2;">
                    <?= $row['tenant_email'] ?> • <?= $row['tenant_phone'] ?> • <?= date('M j, Y g:i A', strtotime($row['created_at'])) ?>
                </div>

                <?php if ($row['reply']): ?>
                    <div class="reply">
                        <strong style="color:#c084d8;">Your Reply:</strong><br>
                        <?= nl2br(htmlspecialchars($row['reply'])) ?>
                    </div>
                <?php endif; ?>

                <div class="reply-form">
                    <form method="POST">
                        <input type="hidden" name="inquiry_id" value="<?= $row['id'] ?>">
                        <textarea name="reply_text" placeholder="Write your reply..."><?= $row['reply'] ? htmlspecialchars($row['reply']) : '' ?></textarea>
                        <button type="submit" name="reply" class="btn"><?= $row['reply'] ? 'Update Reply' : 'Send Reply' ?></button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color:#b77dc2;">No inquiries yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
