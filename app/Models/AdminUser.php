<?php
/**
 * MIRACLE SPA - Modelo AdminUser
 */

require_once __DIR__ . '/../../config/database.php';

class AdminUser {
    public static function authenticate(string $username, string $password): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([trim($username)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }

        return null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, name, email, role, created_at FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
