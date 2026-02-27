<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Otp {
    public static function create($userId, $hash, $expiresAt) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO otps (user_id, otp_hash, expires_at) VALUES (:user_id, :otp_hash, :expires_at)");
        $stmt->execute([
            'user_id' => $userId,
            'otp_hash' => $hash,
            'expires_at' => $expiresAt
        ]);
        return $db->lastInsertId();
    }

    public static function findLatestByUserId($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM otps WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    public static function incrementAttempts($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE otps SET attempts = attempts + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function deleteByUserId($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM otps WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
}
