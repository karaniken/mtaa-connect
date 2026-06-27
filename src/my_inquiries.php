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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
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
