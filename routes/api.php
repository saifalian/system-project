<?php
// API routes not fully fleshed out in instructions, but scaffolding exists
// e.g., for async OTP resend

use App\Utils\Response;
use App\Middleware\RateLimiter;
use App\Services\OtpService;
use App\Services\EmailService;
use App\Models\User;

$route = $_GET['route'] ?? '';

header('Content-Type: application/json');

if ($route === 'api/otp/resend') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         Response::json(['error' => 'Method not allowed'], 405);
    }

    if (!RateLimiter::checkOtpRequest($_SERVER['REMOTE_ADDR'])) {
        exit; // Response already sent by middleware
    }

    if (!isset($_SESSION['pending_user_id'])) {
        Response::json(['error' => 'Unauthorized'], 401);
    }

    $userId = $_SESSION['pending_user_id'];
    $user = User::findById($userId);

    if (!$user) {
        Response::json(['error' => 'User not found'], 404);
    }

    $otpCode = OtpService::generateAndSave($userId);
    $sent = EmailService::sendOtp($user['email'], $otpCode);

    if ($sent) {
        Response::json(['success' => true, 'message' => 'OTP resent successfully.']);
    } else {
        Response::json(['success' => false, 'error' => 'Failed to send OTP.']);
    }
} else {
    Response::json(['error' => 'Not found'], 404);
}
