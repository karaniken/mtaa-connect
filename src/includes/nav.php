<?php
// Navigation – include this in all pages
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
?>
<header class="header">
    <h1>🏠 Mtaa‑Connect</h1>
    <button class="hamburger" id="hamburgerBtn" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
    </button>
    <nav class="nav-links" id="navLinks">
        <?php if ($is_logged_in): ?>
            <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            <?php if ($user_type === 'landlord'): ?>
                <a href="my_properties.php" class="<?= $current_page === 'my_properties.php' ? 'active' : '' ?>">My Properties</a>
                <a href="inquiries.php" class="<?= $current_page === 'inquiries.php' ? 'active' : '' ?>">Inquiries</a>
            <?php endif; ?>
            <?php if ($user_type === 'tenant'): ?>
                <a href="browse.php" class="<?= $current_page === 'browse.php' ? 'active' : '' ?>">Browse</a>
                <a href="my_inquiries.php" class="<?= $current_page === 'my_inquiries.php' ? 'active' : '' ?>">My Inquiries</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="browse.php" class="<?= $current_page === 'browse.php' ? 'active' : '' ?>">Browse</a>
            <a href="login.php" class="<?= $current_page === 'login.php' ? 'active' : '' ?>">Login</a>
            <a href="register.php" class="<?= $current_page === 'register.php' ? 'active' : '' ?>">Register</a>
        <?php endif; ?>
    </nav>
</header>

<script>
// Hamburger toggle
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('hamburgerBtn');
    const nav = document.getElementById('navLinks');
    if (btn && nav) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            nav.classList.toggle('open');
        });
        // Close on click outside
        document.addEventListener('click', function(event) {
            if (!nav.contains(event.target) && !btn.contains(event.target)) {
                nav.classList.remove('open');
            }
        });
    }
});
</script>
