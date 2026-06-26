<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $property_type = $_POST['property_type'];
    $listing_type = $_POST['listing_type'];
    $landlord_id = $_SESSION['user_id'];

    if (empty($title) || empty($location) || empty($property_type) || empty($listing_type)) {
        $error = 'Title, location, property type, and listing type are required.';
    } else {
        $query = "INSERT INTO properties (landlord_id, title, location, description, property_type, listing_type)
                  VALUES ('$landlord_id', '$title', '$location', '$description', '$property_type', '$listing_type')";
        if (mysqli_query($conn, $query)) {
            $property_id = mysqli_insert_id($conn);
            $success = "Property added! Now <a href='add_unit.php?property_id=$property_id'>add units</a> to this property.";
        } else {
            $error = 'Failed to add property. Error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Property – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .container { background: #2d1b33; padding: 40px; border-radius: 16px; max-width: 650px; width: 100%; border: 1px solid #7a2e8a; }
    h1 { text-align: center; color: #c084d8; }
    .subtitle { text-align: center; color: #b77dc2; margin-bottom: 24px; }
    label { display: block; color: #d4a0e0; font-weight: 600; margin: 12px 0 5px; }
    input, textarea, select { width: 100%; padding: 12px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; }
    input:focus, textarea:focus, select:focus { border-color: #b84fd4; outline: none; }
    textarea { min-height: 100px; resize: vertical; }
    .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; }
    .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
    .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #b77dc2; }
    .back-link a { color: #c084d8; text-decoration: none; }
    .form-row { display: flex; gap: 20px; }
    .form-row > div { flex: 1; }
    @media (max-width: 600px) { .form-row { flex-direction: column; } }
</style>
</head>
<body>
<div class="container">
    <h1>🏠 Add Property</h1>
    <p class="subtitle">List a new building or complex</p>
    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>
    <form method="POST">
        <label>Property Title *</label>
        <input type="text" name="title" placeholder="e.g. Kilimani Heights" required>

        <label>Location *</label>
        <input type="text" name="location" placeholder="e.g. Nairobi, Kilimani" required>

        <div class="form-row">
            <div>
                <label>Property Type *</label>
                <select name="property_type" required>
                    <option value="">-- Select --</option>
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                    <option value="land">Land</option>
                    <option value="short_term">Short‑term / Airbnb</option>
                </select>
            </div>
            <div>
                <label>Listing Type *</label>
                <select name="listing_type" required>
                    <option value="">-- Select --</option>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                    <option value="lease">Lease</option>
                    <option value="short_let">Short‑let</option>
                </select>
            </div>
        </div>

        <label>Description</label>
        <textarea name="description" placeholder="Describe the property (amenities, features, etc.)"></textarea>

        <button type="submit" class="btn">Add Property</button>
    </form>
    <div class="back-link"><a href="dashboard.php">← Back to Dashboard</a></div>
</div>
</body>
</html>
