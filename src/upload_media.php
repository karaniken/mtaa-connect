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
<head><title>Upload Media – Mtaa-Connect</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; padding: 20px; }
    .container { max-width: 1000px; margin: 0 auto; background: #2d1b33; padding: 30px; border-radius: 16px; border: 1px solid #7a2e8a; }
    h1 { color: #c084d8; }
    .subtitle { color: #b77dc2; margin-bottom: 20px; }
    label { display: block; color: #d4a0e0; font-weight: 600; margin: 12px 0 5px; }
    input, select { width: 100%; padding: 12px; border: 2px solid #5a2a6a; border-radius: 8px; background: #1f0f24; color: #f0e0f5; }
    input:focus, select:focus { border-color: #b84fd4; outline: none; }
    .btn { padding: 12px 25px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
    .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
    .btn-danger { background: #8b1a1a; }
    .btn-danger:hover { background: #a02020; }
    .error-msg { background: #3b1a1a; color: #ff8a8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .success-msg { background: #1a3b1a; color: #8aff8a; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
    .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
    .media-item { background: #1f0f24; padding: 10px; border-radius: 10px; border: 1px solid #5a2a6a; text-align: center; }
    .media-item img, .media-item video { max-width: 100%; max-height: 150px; border-radius: 6px; }
    .media-item .label { color: #d4a0e0; margin: 8px 0; font-weight: bold; }
    .media-item .type-badge { font-size: 11px; background: #4a1a5a; color: #c084d8; padding: 2px 10px; border-radius: 12px; }
    .media-item .delete-btn { display: inline-block; margin-top: 8px; padding: 4px 12px; background: #8b1a1a; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 12px; }
    .media-item .delete-btn:hover { background: #a02020; }
    .back-link { display: block; margin-top: 20px; }
    .back-link a { color: #c084d8; text-decoration: none; }
    .form-row { display: flex; gap: 15px; align-items: end; }
    .form-row > div { flex: 1; }
    @media (max-width: 600px) { .form-row { flex-direction: column; } }
</style>
</head>
<body>
<div class="container">
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
