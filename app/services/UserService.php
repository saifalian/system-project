<?php
namespace App\Services;

use App\Models\User;

class UserService {
    public static function findOrCreateGoogleUser($googleUser) {
        $user = User::findByEmail($googleUser['email']);
        
        if ($user) {
            // If existing email account has no google_id yet, link it now
            if (empty($user['google_id'])) {
                $db = \App\Config\Database::getConnection();
                $stmt = $db->prepare("UPDATE users SET google_id = :gid WHERE id = :id");
                $stmt->execute(['gid' => $googleUser['id'], 'id' => $user['id']]);
                $user = User::findById($user['id']);
            }
            return $user;
        }

        // New user — create account with Google ID (no password needed)
        $userId = User::create([
            'email' => $googleUser['email'],
            'google_id' => $googleUser['id']
        ]);

        return User::findById($userId);
    }

    // Find existing Google user (login only — does NOT create accounts)
    public static function findGoogleUser($googleUser) {
        $user = User::findByEmail($googleUser['email']);
        if ($user) {
            // Link Google ID if not linked yet
            if (empty($user['google_id'])) {
                $db = \App\Config\Database::getConnection();
                $stmt = $db->prepare("UPDATE users SET google_id = :gid WHERE id = :id");
                $stmt->execute(['gid' => $googleUser['id'], 'id' => $user['id']]);
                $user = User::findById($user['id']);
            }
            return $user;
        }
        return null; // Not registered — caller must handle redirect
    }

    public static function registerEmailUser($email, $password) {
        $user = User::findByEmail($email);
        
        if ($user) {
            return false; // User already exists
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userId = User::create([
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return User::findById($userId);
    }
}
