<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO users (email, password, google_id) VALUES (:email, :password, :google_id)");
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'] ?? null,
            'google_id' => $data['google_id'] ?? null
        ]);
        return $db->lastInsertId();
    }

    public static function findByEmail($email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public static function findById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function deleteById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
