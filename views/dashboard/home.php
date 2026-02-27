<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>

<main class="dashboard-page fade-in">
    <div class="dashboard-container">
        <div class="dashboard-header glassmorphism slide-up">
            <?php if (empty($_SESSION['last_login_at'])): ?>
                <h1>👋 Welcome!</h1>
                <p>This is your first time logging in. Great to have you here!</p>
            <?php else: ?>
                <h1>👋 Welcome back!</h1>
                <p>You are securely logged in.</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-content">
            <div class="card glassmorphism delay-1">
                <div class="card-icon">📧</div>
                <h3>Current Account</h3>
                <p style="font-size: 1.1rem; color: var(--text-main); margin-top: 0.5rem;">
                    <?php echo htmlspecialchars($_SESSION['email'] ?? 'Unknown'); ?>
                </p>
            </div>

            <div class="card glassmorphism delay-2">
                <div class="card-icon">🕐</div>
                <h3>Last Login</h3>
                <p style="font-size: 1rem; color: var(--text-main); margin-top: 0.5rem;">
                    <?php
                    $lastLogin = $_SESSION['last_login_at'] ?? null;
                    if ($lastLogin) {
                        $now  = new DateTime();
                        $prev = new DateTime($lastLogin);
                        $diff = $now->getTimestamp() - $prev->getTimestamp();

                        if ($diff < 60) {
                            $ago = "Just now";
                        } elseif ($diff < 3600) {
                            $m = floor($diff / 60);
                            $ago = "{$m} minute" . ($m > 1 ? 's' : '') . " ago";
                        } elseif ($diff < 86400) {
                            $h = floor($diff / 3600);
                            $ago = "{$h} hour" . ($h > 1 ? 's' : '') . " ago";
                        } elseif ($diff < 2592000) {
                            $d = floor($diff / 86400);
                            $ago = "{$d} day" . ($d > 1 ? 's' : '') . " ago";
                        } else {
                            $ago = $prev->format('d M Y');
                        }
                        echo $ago;
                    } else {
                        echo '<span style="color: var(--text-muted);">This is your first login</span>';
                    }
                ?>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>