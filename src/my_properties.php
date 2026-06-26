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
<head><title>My Properties – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; padding: 20px; }
    .container { max-width: 1200px; margin: 0 auto; background: #2d1b33; padding: 30px; border-radius: 16px; border: 1px solid #7a2e8a; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #5a2a6a; padding-bottom: 15px; }
    .header h1 { color: #c084d8; }
    .header a { color: #b84fd4; text-decoration: none; }
    .property-card { background: #1f0f24; padding: 20px; border-radius: 12px; border: 1px solid #5a2a6a; margin-top: 20px; }
    .property-card h3 { color: #c084d8; }
    .property-card p { color: #b77dc2; }
    .badge { background: #4a1a5a; color: #c084d8; padding: 2px 12px; border-radius: 12px; font-size: 12px; display: inline-block; margin: 2px; }
    .units-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; margin-top: 15px; }
    .unit-item { background: #2d1b33; padding: 15px; border-radius: 8px; border: 1px solid #3a1a4a; }
    .unit-item .thumb { max-width: 100%; height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 8px; }
    .unit-item span { display: block; color: #d4a0e0; font-size: 14px; }
    .unit-item .price { color: #c084d8; font-weight: bold; font-size: 16px; }
    .status-vacant { color: #8aff8a; }
    .status-occupied { color: #ff8a8a; }
    .status-booked { color: #ffa500; }
    .btn-sm { display: inline-block; padding: 4px 12px; background: #8b2f9b; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 12px; margin: 2px; }
    .btn-sm:hover { background: #a03fb0; }
    .btn-danger { background: #8b1a1a; }
    .btn-danger:hover { background: #a02020; }
    .btn-upload { background: #2a6a8b; }
    .btn-upload:hover { background: #3a8ab0; }
</style>
</head>
<body>
<div class="container">
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
