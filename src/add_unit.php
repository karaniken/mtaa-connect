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
<head><title>Add Unit – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .container { background: #2d1b33; padding: 40px; border-radius: 16px; max-width: 650px; width: 100%; border: 1px solid #7a2e8a; }
    h1 { text-align: center; color: #c084d8; }
    .subtitle { text-align: center; color: #b77dc2; margin-bottom: 24px; }
    label { display: block; color: #d4a0e0; font-weight: 600; margin: 12px 0 5px; }
    input, select, textarea { width: 100%; padding: 12px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; }
    input:focus, select:focus, textarea:focus { border-color: #b84fd4; outline: none; }
    textarea { min-height: 60px; resize: vertical; }
    .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; }
    .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
    .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #b77dc2; }
    .back-link a { color: #c084d8; text-decoration: none; }
    .form-row { display: flex; gap: 20px; }
    .form-row > div { flex: 1; }
    @media (max-width: 600px) { .form-row { flex-direction: column; } }
    .property-badge { background: #4a1a5a; color: #c084d8; padding: 5px 15px; border-radius: 20px; font-size: 14px; display: inline-block; margin-bottom: 10px; }
</style>
<script>
// Show/hide commercial category based on property type (if property selected)
function toggleCommercialField() {
    const propertySelect = document.getElementById('property_select');
    const commercialDiv = document.getElementById('commercial_div');
    if (!propertySelect) return;
    const selectedOption = propertySelect.options[propertySelect.selectedIndex];
    // If property type is 'commercial', show the commercial category field
    // We need to store property_type in data attribute of each option.
    // For simplicity, we'll check the value of a hidden field or just keep it visible.
    // I'll keep it visible always – landlord can select 'None' if not applicable.
}
</script>
</head>
<body>
<div class="container">
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
