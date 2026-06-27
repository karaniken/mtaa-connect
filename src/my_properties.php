<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$landlord_id = $_SESSION['user_id'];

// Fetch properties with unit count
$query = "SELECT p.*, COUNT(u.id) as unit_count
          FROM properties p
          LEFT JOIN units u ON p.id = u.property_id
          WHERE p.landlord_id = $landlord_id
          GROUP BY p.id";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Properties – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <div class="header">
        <h1>📋 My Properties</h1>
        <a href="add_property.php">+ Add New Property</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="property-card">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p>
                    📍 <?= htmlspecialchars($row['location']) ?>
                    <span class="badge"><?= ucfirst($row['property_type']) ?></span>
                    <span class="badge"><?= ucfirst(str_replace('_', ' ', $row['listing_type'])) ?></span>
                    <span class="badge"><?= $row['unit_count'] ?> units</span>
                </p>
                <p><?= htmlspecialchars($row['description']) ?></p>

                <div class="units-grid">
                    <?php
                    $units_query = mysqli_query($conn, "SELECT u.*,
                        (SELECT url FROM unit_media WHERE unit_id = u.id AND media_type = 'image' ORDER BY sort_order LIMIT 1) as thumb
                        FROM units u WHERE u.property_id = {$row['id']}");
                    while ($unit = mysqli_fetch_assoc($units_query)):
                    ?>
                        <div class="unit-item">
                            <?php if ($unit['thumb']): ?>
                                <img src="<?= htmlspecialchars($unit['thumb']) ?>" class="thumb" alt="Unit image">
                            <?php else: ?>
                                <div style="background:#1f0f24; height:120px; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#5a2a6a; font-size:12px; margin-bottom:8px;">No Image</div>
                            <?php endif; ?>
                            <span><strong><?= htmlspecialchars($unit['house_number']) ?></strong></span>
                            <span><?= $unit['size'] ?> • <span class="price">KES <?= number_format($unit['price']) ?></span></span>
                            <span>Floor <?= $unit['floor_number'] ?></span>
                            <?php if ($unit['commercial_category']): ?>
                                <span class="badge"><?= ucfirst($unit['commercial_category']) ?></span>
                            <?php endif; ?>
                            <span class="status-<?= $unit['status'] ?>"><?= ucfirst($unit['status']) ?></span>
                            <div style="margin-top:8px;">
                                <a href="edit_unit.php?id=<?= $unit['id'] ?>" class="btn-sm">Edit</a>
                                <a href="delete_unit.php?id=<?= $unit['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this unit?')">Delete</a>
                                <a href="upload_media.php?unit_id=<?= $unit['id'] ?>" class="btn-sm btn-upload">📸 Media</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <a href="add_unit.php?property_id=<?= $row['id'] ?>" class="btn-sm" style="display:inline-block;margin-top:10px; padding:8px 20px;">+ Add Unit</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color:#b77dc2;">You haven't added any properties yet. <a href="add_property.php" style="color:#c084d8;">Add your first property</a>.</p>
    <?php endif; ?>

    <div style="margin-top:20px;"><a href="dashboard.php">← Back to Dashboard</a></div>
</div>
</body>
</html>
