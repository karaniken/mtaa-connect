<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$error = $success = '';
$landlord_id = $_SESSION['user_id'];

// Get property_id from GET or POST
$property_id = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 0;
if (isset($_POST['property_id'])) {
    $property_id = (int)$_POST['property_id'];
}

// Fetch all properties for this landlord (for dropdown)
$properties_query = mysqli_query($conn, "SELECT id, title, property_type FROM properties WHERE landlord_id = $landlord_id ORDER BY title");

// Fetch selected property details (if any)
$selected_property = null;
if ($property_id > 0) {
    $sel_query = mysqli_query($conn, "SELECT title, property_type FROM properties WHERE id = $property_id AND landlord_id = $landlord_id");
    $selected_property = mysqli_fetch_assoc($sel_query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_unit'])) {
    $property_id = (int)$_POST['property_id'];
    $floor = (int)$_POST['floor'];
    $house = mysqli_real_escape_string($conn, $_POST['house']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $price = (float)$_POST['price'];
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);
    $status = $_POST['status'];
    $commercial_category = isset($_POST['commercial_category']) ? mysqli_real_escape_string($conn, $_POST['commercial_category']) : '';

    if (empty($house) || empty($size) || $price <= 0 || $property_id <= 0) {
        $error = 'House number, size, price, and property are required.';
    } else {
        $query = "INSERT INTO units (property_id, floor_number, house_number, size, price, amenities, status, commercial_category)
                  VALUES ('$property_id', '$floor', '$house', '$size', '$price', '$amenities', '$status', '$commercial_category')";
        if (mysqli_query($conn, $query)) {
            $unit_id = mysqli_insert_id($conn);
            $success = "Unit added successfully! <a href='upload_media.php?unit_id=$unit_id'>Upload images/videos</a> or <a href='add_unit.php?property_id=$property_id'>Add another unit</a>.";
        } else {
            $error = 'Failed to add unit: ' . mysqli_error($conn);
        }
    }
}

// If property_id is passed but doesn't exist or doesn't belong to this landlord, unset it
if ($property_id > 0 && !$selected_property) {
    $property_id = 0;
    $selected_property = null;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Unit – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <h1>🏠 Add Unit</h1>
    <p class="subtitle">Add an apartment/room under a property</p>

    <?php if ($selected_property): ?>
        <div style="text-align:center; margin-bottom:15px;">
            <span class="property-badge">Property: <?= htmlspecialchars($selected_property['title']) ?></span>
            <span class="property-badge">Type: <?= ucfirst($selected_property['property_type']) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>

    <form method="POST">
        <!-- Property selection -->
        <?php if ($property_id > 0 && $selected_property): ?>
            <input type="hidden" name="property_id" value="<?= $property_id ?>">
        <?php else: ?>
            <label>Select Property *</label>
            <select name="property_id" id="property_select" required>
                <option value="">-- Choose a property --</option>
                <?php while ($prop = mysqli_fetch_assoc($properties_query)): ?>
                    <option value="<?= $prop['id'] ?>" data-type="<?= $prop['property_type'] ?>">
                        <?= htmlspecialchars($prop['title']) ?> (<?= ucfirst($prop['property_type']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>

        <div class="form-row">
            <div>
                <label>Floor Number</label>
                <input type="number" name="floor" placeholder="e.g. 3" min="0">
            </div>
            <div>
                <label>House Number *</label>
                <input type="text" name="house" placeholder="e.g. Apt 301" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Size *</label>
                <select name="size" required>
                    <option value="">-- Select --</option>
                    <option value="Studio">Studio</option>
                    <option value="1BR">1 Bedroom</option>
                    <option value="2BR">2 Bedrooms</option>
                    <option value="3BR">3 Bedrooms</option>
                    <option value="4BR+">4+ Bedrooms</option>
                    <option value="Shop">Shop</option>
                    <option value="Office">Office</option>
                    <option value="Warehouse">Warehouse</option>
                    <option value="Stall">Stall</option>
                    <option value="Land">Land (sqm)</option>
                </select>
            </div>
            <div>
                <label>Price (KES) *</label>
                <input type="number" name="price" step="0.01" placeholder="e.g. 25000" required>
            </div>
        </div>

        <label>Commercial Category</label>
        <select name="commercial_category" id="commercial_category">
            <option value="">-- Not applicable / None --</option>
            <option value="carwash">Carwash</option>
            <option value="garage">Garage / Workshop</option>
            <option value="office">Office</option>
            <option value="shop">Shop</option>
            <option value="stall">Stall / Kiosk</option>
            <option value="warehouse">Warehouse</option>
            <option value="restaurant">Restaurant / Café</option>
            <option value="other_commercial">Other Commercial</option>
        </select>
        <p style="color:#b77dc2; font-size:12px; margin-top:4px;">Select a category if this is a commercial unit.</p>

        <label>Amenities</label>
        <input type="text" name="amenities" placeholder="e.g. WiFi, Parking, Gym, Security">

        <label>Status</label>
        <select name="status">
            <option value="vacant">Vacant</option>
            <option value="occupied">Occupied</option>
            <option value="booked">Booked</option>
        </select>

        <button type="submit" name="submit_unit" class="btn">Add Unit</button>
    </form>
    <div class="back-link"><a href="dashboard.php">← Back to Dashboard</a></div>
</div>
</body>
</html>
