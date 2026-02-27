<nav class="navbar glassmorphism">
    <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php?route=dashboard/home' : 'index.php?route=auth/login'; ?>" class="nav-brand">Social MFA</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?route=dashboard/home" class="nav-link">Dashboard</a>
            <a href="index.php?route=auth/logout" class="nav-btn btn-outline">Logout</a>
        <?php else: ?>
            <a href="index.php?route=auth/login" class="nav-link">Login</a>
            <a href="index.php?route=auth/register" class="nav-link">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>
