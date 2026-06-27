<?php
session_start();
require_once 'config/db.php';
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';

// Build filter conditions
$where_conditions = ["u.status = 'vacant'"];

$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$property_type = isset($_GET['property_type']) ? $_GET['property_type'] : '';
$listing_type = isset($_GET['listing_type']) ? $_GET['listing_type'] : '';
$size = isset($_GET['size']) ? $_GET['size'] : '';
$price_min = isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float)$_GET['price_max'] : 0;
$amenities = isset($_GET['amenities']) ? mysqli_real_escape_string($conn, $_GET['amenities']) : '';

if ($location) {
    $where_conditions[] = "(p.location LIKE '%$location%' OR p.title LIKE '%$location%')";
}
if ($property_type) {
    $where_conditions[] = "p.property_type = '$property_type'";
}
if ($listing_type) {
    $where_conditions[] = "p.listing_type = '$listing_type'";
}
if ($size) {
    $where_conditions[] = "u.size = '$size'";
}
if ($price_min > 0) {
    $where_conditions[] = "u.price >= $price_min";
}
if ($price_max > 0) {
    $where_conditions[] = "u.price <= $price_max";
}
if ($amenities) {
    $where_conditions[] = "u.amenities LIKE '%$amenities%'";
}

$where_sql = implode(" AND ", $where_conditions);

// Fetch units with their properties and first image
$sql = "SELECT u.*, p.title as property_title, p.location, p.property_type, p.listing_type,
        (SELECT url FROM unit_media WHERE unit_id = u.id AND media_type = 'image' ORDER BY sort_order LIMIT 1) as thumb
        FROM units u
        JOIN properties p ON u.property_id = p.id
        WHERE $where_sql
        ORDER BY u.created_at DESC";

$result = mysqli_query($conn, $sql);
$total_units = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Units – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <div class="header">
        <h1>🔍 Browse Units</h1>
        <div>
            <?php if ($user_type === 'guest'): ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
            <?php else: ?>
                <a href="dashboard.php">Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Form -->
    <form class="filter-section" method="GET" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="e.g. Kilimani" value="<?= htmlspecialchars($location) ?>">
            </div>
            <div class="filter-group">
                <label>Property Type</label>
                <select name="property_type">
                    <option value="">All</option>
                    <option value="residential" <?= $property_type === 'residential' ? 'selected' : '' ?>>Residential</option>
                    <option value="commercial" <?= $property_type === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                    <option value="land" <?= $property_type === 'land' ? 'selected' : '' ?>>Land</option>
                    <option value="short_term" <?= $property_type === 'short_term' ? 'selected' : '' ?>>Short‑term / Airbnb</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Listing Type</label>
                <select name="listing_type">
                    <option value="">All</option>
                    <option value="sale" <?= $listing_type === 'sale' ? 'selected' : '' ?>>For Sale</option>
                    <option value="rent" <?= $listing_type === 'rent' ? 'selected' : '' ?>>For Rent</option>
                    <option value="lease" <?= $listing_type === 'lease' ? 'selected' : '' ?>>Lease</option>
                    <option value="short_let" <?= $listing_type === 'short_let' ? 'selected' : '' ?>>Short‑let</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Size</label>
                <select name="size">
                    <option value="">All</option>
                    <option value="Studio" <?= $size === 'Studio' ? 'selected' : '' ?>>Studio</option>
                    <option value="1BR" <?= $size === '1BR' ? 'selected' : '' ?>>1 Bedroom</option>
                    <option value="2BR" <?= $size === '2BR' ? 'selected' : '' ?>>2 Bedrooms</option>
                    <option value="3BR" <?= $size === '3BR' ? 'selected' : '' ?>>3 Bedrooms</option>
                    <option value="4BR+" <?= $size === '4BR+' ? 'selected' : '' ?>>4+ Bedrooms</option>
                    <option value="Shop" <?= $size === 'Shop' ? 'selected' : '' ?>>Shop</option>
                    <option value="Office" <?= $size === 'Office' ? 'selected' : '' ?>>Office</option>
                    <option value="Warehouse" <?= $size === 'Warehouse' ? 'selected' : '' ?>>Warehouse</option>
                    <option value="Stall" <?= $size === 'Stall' ? 'selected' : '' ?>>Stall</option>
                </select>
            </div>
        </div>
        <div class="filter-row" style="margin-top:15px;">
            <div class="filter-group">
                <label>Min Price (KES)</label>
                <input type="number" name="price_min" placeholder="0" value="<?= $price_min > 0 ? $price_min : '' ?>">
            </div>
            <div class="filter-group">
                <label>Max Price (KES)</label>
                <input type="number" name="price_max" placeholder="No max" value="<?= $price_max > 0 ? $price_max : '' ?>">
            </div>
            <div class="filter-group">
                <label>Amenities</label>
                <input type="text" name="amenities" placeholder="e.g. WiFi, Parking" value="<?= htmlspecialchars($amenities) ?>">
            </div>
            <div class="filter-group" style="display: flex; gap: 10px; align-items: end; min-width: 120px;">
                <button type="submit" class="filter-btn">Search</button>
                <a href="browse.php" class="filter-btn filter-btn" style="background:#5a2a6a; text-decoration:none; text-align:center;">Reset</a>
            </div>
        </div>
    </form>

    <div class="results-info">Found <?= $total_units ?> vacant units matching your criteria.</div>

    <div class="units-grid">
        <?php if ($total_units > 0): ?>
            <?php while ($unit = mysqli_fetch_assoc($result)): ?>
                <div class="unit-card">
                    <?php if ($unit['thumb']): ?>
                        <img src="<?= htmlspecialchars($unit['thumb']) ?>" class="thumb" alt="Unit image">
                    <?php else: ?>
                        <div style="background:#2d1b33; height:180px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#5a2a6a; font-size:14px; margin-bottom:10px;">No Image</div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($unit['property_title']) ?></h3>
                    <div class="location">📍 <?= htmlspecialchars($unit['location']) ?></div>
                    <div class="price">KES <?= number_format($unit['price']) ?></div>
                    <div class="details">
                        <span><?= $unit['size'] ?></span>
                        <span>Floor <?= $unit['floor_number'] ?></span>
                        <span><?= htmlspecialchars($unit['house_number']) ?></span>
                    </div>
                    <div>
                        <span class="badge"><?= ucfirst($unit['property_type']) ?></span>
                        <span class="badge"><?= ucfirst(str_replace('_', ' ', $unit['listing_type'])) ?></span>
                        <?php if ($unit['commercial_category']): ?>
                            <span class="badge"><?= ucfirst($unit['commercial_category']) ?></span>
                        <?php endif; ?>
                        <span class="status-vacant">Vacant</span>
                    </div>
                    <div class="amenities"><?= htmlspecialchars($unit['amenities']) ?></div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'tenant'): ?>
                        <a href="inquiry.php?unit_id=<?= $unit['id'] ?>" class="btn">Contact Landlord</a>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn">Login to Contact</a>
                    <?php else: ?>
                        <span style="color:#b77dc2; font-size:13px;">Only tenants can contact.</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results" style="grid-column: 1/-1;">
                <p>No units found matching your filters.</p>
                <p style="font-size:14px; margin-top:10px;">Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
