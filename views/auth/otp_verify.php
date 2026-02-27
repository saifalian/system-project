<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>

<main class="auth-page fade-in">
    <div class="auth-card glassmorphism">
        <div class="icon-header">
            <span class="lock-icon">🔒</span>
        </div>
        <h1 class="auth-title">Verify OTP</h1>
        <p class="auth-subtitle">We've sent a 6-digit code to your email.<br>Enter it below to continue.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger bounce-in">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=auth/otp" method="POST" class="auth-form" id="otp-form">
            <div class="form-group otp-group">
                <input type="text" id="otp" name="otp" class="form-control otp-input" required placeholder="000000" maxlength="6" autocomplete="off">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block glow-effect">Verify Code</button>
        </form>

        <div class="auth-footer mt-4">
            <p>Didn't receive the code? 
                <button type="button" id="resend-otp" class="btn-link">Resend OTP</button>
            </p>
            <p id="resend-status" class="status-text"></p>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>