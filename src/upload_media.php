<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';
$landlord_id = $_SESSION['user_id'];
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;

// Verify this unit belongs to this landlord (via property)
if ($unit_id > 0) {
    $check = mysqli_query($conn, "SELECT u.*, p.title as property_title, p.id as property_id FROM units u JOIN properties p ON u.property_id = p.id WHERE u.id = $unit_id AND p.landlord_id = $landlord_id");
    $unit = mysqli_fetch_assoc($check);
    if (!$unit) {
        header('Location: dashboard.php');
        exit();
    }
} else {
    header('Location: dashboard.php');
    exit();
}

$error = $success = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $media_type = $_POST['media_type']; // image or video
    $file = $_FILES['media_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error.';
    } elseif (empty($label)) {
        $error = 'Please provide a label (e.g., Living Room).';
    } else {
        // Validate file type
        $allowed_images = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowed_videos = ['video/mp4', 'video/webm', 'video/ogg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $valid = false;
        if ($media_type === 'image' && in_array($mime_type, $allowed_images)) {
            $valid = true;
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'unit_' . $unit_id . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        } elseif ($media_type === 'video' && in_array($mime_type, $allowed_videos)) {
            $valid = true;
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'unit_' . $unit_id . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        } else {
            $error = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP (images) / MP4, WEBM, OGG (videos).';
        }

        if ($valid) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/mtaa/uploads/';
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $url = '/mtaa/uploads/' . $filename;
                $query = "INSERT INTO unit_media (unit_id, media_type, url, label) VALUES ('$unit_id', '$media_type', '$url', '$label')";
                if (mysqli_query($conn, $query)) {
                    $success = 'File uploaded successfully!';
                } else {
                    $error = 'Database error: ' . mysqli_error($conn);
                }
            } else {
                $error = 'Failed to move uploaded file. Check directory permissions.';
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $media_id = (int)$_GET['delete'];
    // Verify media belongs to this unit and unit belongs to landlord
    $del_check = mysqli_query($conn, "SELECT m.id, m.url FROM unit_media m JOIN units u ON m.unit_id = u.id JOIN properties p ON u.property_id = p.id WHERE m.id = $media_id AND p.landlord_id = $landlord_id");
    if ($media_row = mysqli_fetch_assoc($del_check)) {
        // Delete file from disk
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $media_row['url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        mysqli_query($conn, "DELETE FROM unit_media WHERE id = $media_id");
        $success = 'Media deleted successfully.';
    } else {
        $error = 'Media not found or you do not have permission.';
    }
}

// Fetch existing media for this unit
$media_query = mysqli_query($conn, "SELECT * FROM unit_media WHERE unit_id = $unit_id ORDER BY sort_order ASC, id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Media – Mtaa-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <?php include 'includes/nav.php'; ?>
    <h1>📸 Manage Media</h1>
    <p class="subtitle">
        Upload images and videos for <strong><?= htmlspecialchars($unit['property_title']) ?></strong> –
        Unit <?= htmlspecialchars($unit['house_number']) ?> (<?= $unit['size'] ?>)
    </p>

    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>

    <h2 style="color:#c084d8; margin-top:20px;">Upload New Media</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div>
                <label>Media Type</label>
                <select name="media_type" required>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </div>
            <div>
                <label>Label *</label>
                <input type="text" name="label" placeholder="e.g. Living Room, Bedroom, Kitchen, Exterior, Video Tour" required>
            </div>
        </div>
        <div>
            <label>Choose File</label>
            <input type="file" name="media_file" accept="image/*,video/*" required>
            <p style="color:#b77dc2; font-size:12px; margin-top:4px;">Allowed: JPG, PNG, GIF, WEBP (images) / MP4, WEBM, OGG (videos). Max size: 10MB (adjust in php.ini).</p>
        </div>
        <button type="submit" class="btn" style="margin-top:15px;">Upload</button>
    </form>

    <hr style="border-color:#5a2a6a; margin:30px 0;">

    <h2 style="color:#c084d8;">Existing Media (<?= mysqli_num_rows($media_query) ?>)</h2>
    <?php if (mysqli_num_rows($media_query) > 0): ?>
        <div class="media-grid">
            <?php while ($media = mysqli_fetch_assoc($media_query)): ?>
                <div class="media-item">
                    <?php if ($media['media_type'] === 'image'): ?>
                        <img src="<?= htmlspecialchars($media['url']) ?>" alt="<?= htmlspecialchars($media['label']) ?>">
                    <?php else: ?>
                        <video controls>
                            <source src="<?= htmlspecialchars($media['url']) ?>">
                            Your browser does not support video.
                        </video>
                    <?php endif; ?>
                    <div class="label"><?= htmlspecialchars($media['label']) ?></div>
                    <span class="type-badge"><?= ucfirst($media['media_type']) ?></span>
                    <a href="?unit_id=<?= $unit_id ?>&delete=<?= $media['id'] ?>" class="delete-btn" onclick="return confirm('Delete this media?')">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="color:#b77dc2;">No media uploaded yet for this unit.</p>
    <?php endif; ?>

    <div class="back-link"><a href="my_properties.php">← Back to My Properties</a></div>
</div>
</body>
</html>
