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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
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
