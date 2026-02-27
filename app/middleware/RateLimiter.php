<?php
namespace App\Middleware;

use App\Utils\Response;

class RateLimiter {
    public static function checkOtpRequest($ip, $maxRequests = 3, $timeWindowSeconds = 300) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_otp_' . $ip;
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'first_request' => time()
            ];
            return true;
        }

        $bucket = $_SESSION[$key];
        
        if (time() - $bucket['first_request'] > $timeWindowSeconds) {
            // Reset bucket
            $_SESSION[$key] = [
                'count' => 1,
                'first_request' => time()
            ];
            return true;
        }

        if ($bucket['count'] >= $maxRequests) {
            Response::json(['error' => 'Too many OTP requests. Please try again later.'], 429);
            return false;
        }

        $_SESSION[$key]['count']++;
        return true;
    }
}
