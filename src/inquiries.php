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
<head><title>Inquiries – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; padding: 20px; }
    .container { max-width: 1100px; margin: 0 auto; background: #2d1b33; padding: 30px; border-radius: 16px; border: 1px solid #7a2e8a; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #5a2a6a; padding-bottom: 15px; }
    .header h1 { color: #c084d8; }
    .inquiry-item { background: #1f0f24; padding: 20px; border-radius: 12px; border: 1px solid #5a2a6a; margin-top: 20px; }
    .inquiry-item .unread { background: #4a1a5a; color: #c084d8; padding: 2px 12px; border-radius: 12px; font-size: 12px; }
    .inquiry-item .tenant { color: #d4a0e0; font-weight: bold; }
    .inquiry-item .property { color: #b77dc2; }
    .inquiry-item .message { margin: 10px 0; color: #f0e0f5; }
    .inquiry-item .reply-form { margin-top: 15px; border-top: 1px solid #5a2a6a; padding-top: 15px; }
    .inquiry-item .reply-form textarea { width: 100%; padding: 10px; border: 2px solid #5a2a6a; border-radius: 8px; background: #2d1b33; color: #f0e0f5; min-height: 80px; resize: vertical; }
    .inquiry-item .reply-form .btn { padding: 8px 20px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 6px; cursor: pointer; }
    .inquiry-item .reply-form .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
    .inquiry-item .reply { background: #2d1b33; padding: 10px; border-radius: 8px; margin: 10px 0; border-left: 3px solid #b84fd4; color: #d4a0e0; }
    .back-link { display: block; margin-top: 20px; }
    .back-link a { color: #c084d8; text-decoration: none; }
    .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
</style>
</head>
<body>
<div class="container">
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
