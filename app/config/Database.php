<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $db      = $_ENV['DB_NAME'] ?? 'system_social_media_mfa';
            $user    = $_ENV['DB_USER'] ?? 'root';
            $pass    = $_ENV['DB_PASS'] ?? '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
                // Auto-create tables from schema if they don't exist yet
                self::initSchema(self::$connection);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed.");
            }
        }
        return self::$connection;
    }

    /**
     * Runs schema.sql automatically so tables are always created if missing.
     * Safe to run every time — all statements use CREATE TABLE IF NOT EXISTS.
     */
    private static function initSchema(PDO $pdo) {
        $schemaFile = dirname(__DIR__, 2) . '/database/schema.sql';
        if (!file_exists($schemaFile)) {
            return;
        }
        $sql = file_get_contents($schemaFile);
        // Split on semicolons and run each statement individually
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => $s !== ''
        );
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
    }
}
