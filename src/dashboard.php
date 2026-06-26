<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$fullname = $_SESSION['fullname'];
$user_type = $_SESSION['user_type'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard – Mtaa-Connect</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #1a0e1e; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #2d1b33; padding: 30px; border-radius: 16px; border: 1px solid #7a2e8a; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #5a2a6a; padding-bottom: 15px; }
        .header h1 { color: #c084d8; }
        .header .logout a { color: #ff6b6b; text-decoration: none; font-weight: bold; }
        .welcome { color: #d4a0e0; margin: 20px 0; font-size: 18px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: #1f0f24; padding: 20px; border-radius: 12px; border: 1px solid #5a2a6a; }
        .card h3 { color: #c084d8; }
        .card p { color: #b77dc2; }
        .btn { display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #8b2f9b, #c84fd4); color: white; border: none; border-radius: 8px; text-decoration: none; margin-top: 10px; }
        .btn:hover { background: linear-gradient(135deg, #a03fb0, #d86ae6); }
        .role-badge { background: #4a1a5a; color: #c084d8; padding: 5px 15px; border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🏠 Mtaa‑Connect</h1>
        <div class="logout"><a href="logout.php">Logout</a></div>
    </div>
    <div class="welcome">
        Welcome, <?= htmlspecialchars($fullname) ?>! 
        <span class="role-badge"><?= ucfirst($user_type) ?></span>
    </div>

    <?php if ($user_type === 'landlord'): ?>
        <div class="cards">
            <div class="card">
                <h3>➕ Add Property</h3>
                <p>List a new property for tenants.</p>
                <a href="#" class="btn">Add Property (Coming Soon)</a>
            </div>
            <div class="card">
                <h3>📋 My Properties</h3>
                <p>Manage your listings.</p>
                <a href="#" class="btn">View Properties (Coming Soon)</a>
            </div>
            <div class="card">
                <h3>📩 Inquiries</h3>
                <p>Messages from tenants.</p>
                <a href="#" class="btn">Check Inbox (Coming Soon)</a>
            </div>
        </div>
    <?php else: ?>
        <div class="cards">
            <div class="card">
                <h3>🔍 Browse Properties</h3>
                <p>Find your next home.</p>
                <a href="#" class="btn">Search (Coming Soon)</a>
            </div>
            <div class="card">
                <h3>📌 My Inquiries</h3>
                <p>Track your property requests.</p>
                <a href="#" class="btn">View Inquiries (Coming Soon)</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
