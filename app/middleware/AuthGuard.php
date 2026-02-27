<?php
namespace App\Middleware;

use App\Utils\Response;

class AuthGuard {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            // Check if it's an API request or Web request
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                Response::json(['error' => 'Unauthorized access'], 401);
            } else {
                Response::redirect('index.php?route=auth/login');
            }
        }
    }

    public static function checkGuest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
             Response::redirect('index.php?route=dashboard/home');
        }
    }
}
