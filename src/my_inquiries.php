<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$tenant_id = $_SESSION['user_id'];
$query = "SELECT i.*, u.house_number, u.size, p.title as property_title, p.location
          FROM inquiries i
          JOIN units u ON i.unit_id = u.id
          JOIN properties p ON u.property_id = p.id
          WHERE i.tenant_id = $tenant_id
          ORDER BY i.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head><title>My Inquiries – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; padding: 20px; }
    .container { max-width: 900px; margin: 0 auto; background: #2d1b33; padding: 30px; border-radius: 16px; border: 1px solid #7a2e8a; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #5a2a6a; padding-bottom: 15px; }
    .header h1 { color: #c084d8; }
    .inquiry-item { background: #1f0f24; padding: 15px; border-radius: 8px; border: 1px solid #5a2a6a; margin-top: 15px; }
    .inquiry-item .property { color: #c084d8; }
    .inquiry-item .message { margin: 5px 0; color: #f0e0f5; }
    .inquiry-item .reply { background: #2d1b33; padding: 10px; border-radius: 6px; border-left: 3px solid #b84fd4; margin-top: 8px; }
    .inquiry-item .status { font-size: 12px; }
    .status-replied { color: #8aff8a; }
    .status-pending { color: #ffa500; }
    .back-link { margin-top: 20px; }
    .back-link a { color: #c084d8; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📌 My Inquiries</h1>
        <a href="dashboard.php">← Dashboard</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="inquiry-item">
                <div class="property"><?= htmlspecialchars($row['property_title']) ?> • <?= htmlspecialchars($row['location']) ?></div>
                <div>Unit: <?= $row['house_number'] ?> (<?= $row['size'] ?>)</div>
                <div class="message"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                <?php if ($row['reply']): ?>
                    <div class="reply"><strong style="color:#c084d8;">Landlord replied:</strong><br><?= nl2br(htmlspecialchars($row['reply'])) ?></div>
                    <div class="status status-replied">✅ Replied</div>
                <?php else: ?>
                    <div class="status status-pending">⏳ Awaiting reply</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color:#b77dc2;">You haven't sent any inquiries yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
