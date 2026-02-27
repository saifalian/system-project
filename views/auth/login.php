<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/navbar.php'; ?>

<?php
$showSignupPopup = false;
if (isset($_SESSION['signup_required'])) {
    $showSignupPopup = true;
    unset($_SESSION['signup_required']);
}
?>

<main class="auth-page fade-in">
    <div class="auth-card glassmorphism float-animation">
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Secure access to your account</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger fade-in">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success fade-in">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=auth/login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary btn-block glow-effect">Login</button>
        </form>

        <div class="auth-divider">
            <span>OR</span>
        </div>

        <a href="index.php?route=auth/google" class="btn btn-google btn-block hover-lift">
            <img src="https://www.google.com/favicon.ico" alt="Google" class="google-icon">
            Continue with Google
        </a>

        <div class="auth-footer">
            <p>Don't have an account? <a href="index.php?route=auth/register">Sign up</a></p>
        </div>
    </div>
</main>

<!-- Signup Required Popup -->
<?php if ($showSignupPopup): ?>
<div class="modal-overlay" id="signupModal">
    <div class="modal-card glassmorphism bounce-in">
        <div class="modal-icon">🔐</div>
        <h2>No Account Found</h2>
        <p>We couldn't find an account linked to those credentials.<br>Please sign up first!</p>
        <div class="modal-actions">
            <a href="index.php?route=auth/register" class="btn btn-primary glow-effect">Sign Up Now</a>
            <button onclick="document.getElementById('signupModal').style.display='none'" class="btn btn-outline">Stay on Login</button>
        </div>
    </div>
</div>
<style>
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    z-index: 999;
    animation: fadeIn 0.3s ease;
}
.modal-card {
    max-width: 380px; width: 90%;
    padding: 2.5rem;
    border-radius: 1.25rem;
    text-align: center;
}
.modal-icon { font-size: 3rem; margin-bottom: 1rem; }
.modal-card h2 { margin-bottom: 0.75rem; font-size: 1.5rem; }
.modal-card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.75rem; line-height: 1.6; }
.modal-actions { display: flex; flex-direction: column; gap: 0.75rem; }
.modal-actions .btn { width: 100%; padding: 0.75rem; border-radius: 0.5rem; font-weight: 500; cursor: pointer; }
</style>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>