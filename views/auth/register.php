<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>

<main class="auth-page fade-in">
    <div class="auth-card glassmorphism float-animation">
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join us today for a secure experience</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger fade-in">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=auth/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Create a password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn btn-primary btn-block glow-effect">Register Account</button>
        </form>

        <div class="auth-divider">
            <span>OR</span>
        </div>

        <a href="index.php?route=auth/google&action=register" class="btn btn-google btn-block hover-lift">
            <img src="https://www.google.com/favicon.ico" alt="Google" class="google-icon">
            Sign up with Google
        </a>

        <div class="auth-footer">
            <p>Already have an account? <a href="index.php?route=auth/login">Login here</a></p>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>