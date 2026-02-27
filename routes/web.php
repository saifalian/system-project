<?php

use App\Controllers\AuthController;
use App\Controllers\OtpController;
use App\Controllers\GoogleController;
use App\Middleware\AuthGuard;

$route = $_GET['route'] ?? 'auth/login';

$authController = new AuthController();
$otpController = new OtpController();
$googleController = new GoogleController();

switch ($route) {
    // Guest Routes
    case 'auth/login':
        AuthGuard::checkGuest();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLogin();
        }
        break;

    case 'auth/register':
        AuthGuard::checkGuest();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->register();
        } else {
            $authController->showRegister();
        }
        break;

    case 'auth/link_telegram':
        AuthGuard::checkGuest();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->verifyLinkTelegram();
        } else {
            $authController->showLinkTelegram();
        }
        break;

    case 'auth/otp':
        AuthGuard::checkGuest();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otpController->verify();
        } else {
            $otpController->showVerify();
        }
        break;

    case 'auth/google':
        AuthGuard::checkGuest();
        $googleController->redirect();
        break;

    case 'auth/google/callback':
        AuthGuard::checkGuest();
        $googleController->callback();
        break;

    // Protected Routes
    case 'dashboard/home':
        AuthGuard::check();
        require_once dirname(__DIR__) . '/views/dashboard/home.php';
        break;

    case 'auth/logout':
        $authController->logout();
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
        exit;
}
