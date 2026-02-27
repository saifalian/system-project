<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Services\OtpService;
use App\Services\EmailService;
use App\Utils\Validator;
use App\Utils\Response;

class AuthController {
    public function showLogin() {
        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function showRegister() {
        require_once dirname(__DIR__, 2) . '/views/auth/register.php';
    }

    public function login() {
        $email = Validator::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!Validator::isEmail($email) || empty($password)) {
            $_SESSION['error'] = "Valid email and password are required.";
            Response::redirect('index.php?route=auth/login');
        }

        $user = \App\Models\User::findByEmail($email);
        
        if (!$user) {
            $_SESSION['signup_required'] = true;
            Response::redirect('index.php?route=auth/login');
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Invalid password.";
            Response::redirect('index.php?route=auth/login');
        }

        // Generate OTP and send to Email
        $otpCode = OtpService::generateAndSave($user['id']);
        $sent = EmailService::sendOtp($user['email'], $otpCode);

        if ($sent) {
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['pending_email'] = $user['email'];
            Response::redirect('index.php?route=auth/otp');
        } else {
            $_SESSION['error'] = "Failed to send OTP to email. Please try again.";
            Response::redirect('index.php?route=auth/login');
        }
    }

    public function register() {
        $email = Validator::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!Validator::isEmail($email)) {
            $_SESSION['error'] = "Valid email is required.";
            Response::redirect('index.php?route=auth/register');
        }

        if (empty($password) || strlen($password) < 6) {
            $_SESSION['error'] = "Password must be at least 6 characters.";
            Response::redirect('index.php?route=auth/register');
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            Response::redirect('index.php?route=auth/register');
        }

        $user = UserService::registerEmailUser($email, $password);

        if (!$user) {
            $_SESSION['error'] = "Email already registered.";
            Response::redirect('index.php?route=auth/register');
        }

        // Generate OTP and send to Email
        $otpCode = OtpService::generateAndSave($user['id']);
        $sent = EmailService::sendOtp($user['email'], $otpCode);

        if ($sent) {
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['pending_email'] = $user['email'];
            $_SESSION['pending_is_new_user'] = true;
            Response::redirect('index.php?route=auth/otp');
        } else {
            \App\Models\User::deleteById($user['id']);
            $_SESSION['error'] = "Account registered, but failed to send OTP to email. Registration rolled back.";
            Response::redirect('index.php?route=auth/register');
        }
    }

    public function logout() {
        \App\Services\TokenService::destroySession();
        Response::redirect('index.php?route=auth/login');
    }
}
