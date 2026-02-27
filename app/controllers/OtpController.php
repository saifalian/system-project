<?php
namespace App\Controllers;

use App\Services\OtpService;
use App\Services\TokenService;
use App\Models\User;
use App\Utils\Validator;
use App\Utils\Response;

class OtpController {
    public function showVerify() {
        if (!isset($_SESSION['pending_user_id'])) {
            Response::redirect('index.php?route=auth/login');
        }
        require_once dirname(__DIR__, 2) . '/views/auth/otp_verify.php';
    }

    public function verify() {
        if (!isset($_SESSION['pending_user_id'])) {
            Response::redirect('index.php?route=auth/login');
        }

        $userId = $_SESSION['pending_user_id'];
        $otpCode = Validator::sanitize($_POST['otp'] ?? '');

        if (empty($otpCode)) {
            $_SESSION['error'] = "Please enter OTP.";
            Response::redirect('index.php?route=auth/otp');
        }

        $result = OtpService::verify($userId, $otpCode);

        if ($result['success']) {
            $isNewUser = isset($_SESSION['pending_is_new_user']);

            unset($_SESSION['pending_user_id']);
            unset($_SESSION['pending_email']);
            unset($_SESSION['pending_is_new_user']);

            if ($isNewUser) {
                // Signup complete — show success popup on login page
                $_SESSION['success'] = '✅ Account registered successfully! Please log in.';
                Response::redirect('index.php?route=auth/login');
            } else {
                // Login OTP verified — start the session and go to dashboard
                $user = User::findById($userId);
                TokenService::createLoginSession($user);
                Response::redirect('index.php?route=dashboard/home');
            }
        } elseif (!empty($result['max_attempts_exceeded'])) {
            // Determine if this was a login or signup flow
            $wasRegister = isset($_SESSION['pending_is_new_user']);

            // If this was a new registration, delete the unverified user so the
            // email is not permanently blocked and can be used again on retry.
            if ($wasRegister) {
                User::deleteById($userId);
            }

            // Clear all pending session data
            unset($_SESSION['pending_user_id']);
            unset($_SESSION['pending_email']);
            unset($_SESSION['pending_is_new_user']);
            $_SESSION['error'] = '❌ OTP verification failed — too many wrong attempts. Please try again.';
            $redirect = $wasRegister ? 'index.php?route=auth/register' : 'index.php?route=auth/login';
            Response::redirect($redirect);
        } else {
            $_SESSION['error'] = $result['message'];
            Response::redirect('index.php?route=auth/otp');
        }
    }
}
