<?php
namespace App\Services;

class TokenService {
    public static function createLoginSession($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Save the previous login time before updating it
        $previousLogin = $user['last_login_at'] ?? null;
        
        // Update last_login_at in DB
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['last_login_at'] = $previousLogin;
        $_SESSION['last_activity'] = time();
    }
    
    public static function destroySession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
    }
}
