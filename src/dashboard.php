<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$fullname = $_SESSION['fullname'];
$user_type = $_SESSION['user_type'];
$landlord_id = $_SESSION['user_id'];

// Count unread inquiries for landlord badge
$unread_count = 0;
if ($user_type === 'landlord') {
    $unread_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM inquiries i JOIN units u ON i.unit_id = u.id JOIN properties p ON u.property_id = p.id WHERE p.landlord_id = $landlord_id AND i.is_read = FALSE");
    $unread = mysqli_fetch_assoc($unread_query);
    $unread_count = $unread['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <div class="welcome">
        Welcome, <?= htmlspecialchars($fullname) ?>!
        <span class="badge"><?= ucfirst($user_type) ?></span>
    </div>

    <?php if ($user_type === 'landlord'): ?>
        <div class="cards">
            <div class="card">
                <h3>➕ Add Property</h3>
                <p>List a new property for tenants.</p>
                <a href="add_property.php" class="btn">Add Property</a>
            </div>
            <div class="card">
                <h3> My Properties</h3>
                <p>Manage your listings.</p>
                <a href="my_properties.php" class="btn">View Properties</a>
            </div>
            <div class="card">
                <h3>📩 Inquiries</h3>
                <p>Messages from tenants.</p>
                <?php
                $badge = $unread_count > 0 ? ' <span style="background:#ff4444; color:white; padding:2px 10px; border-radius:12px; font-size:12px;">' . $unread_count . ' new</span>' : '';
                ?>
                <a href="inquiries.php" class="btn">Check Inbox <?= $badge ?></a>
            </div>
        </div>
    <?php else: ?>
        <div class="cards">
            <div class="card">
                <h3> Browse Properties</h3>
                <p>Find your next home.</p>
                <a href="browse.php" class="btn">Browse Units</a>
            </div>
            <div class="card">
                <h3> My Inquiries</h3>
                <p>Track your property requests.</p>
                <a href="my_inquiries.php" class="btn">View Inquiries</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
