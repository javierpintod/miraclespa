<?php
/**
 * MIRACLE SPA - Ayudante de Autenticación y Sesiones (AuthHelper)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/app.php';

class AuthHelper {
    public static function isLoggedIn(): bool {
        return !empty($_SESSION['admin_user_id']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            $baseUrl = AppConfig::getBaseUrl();
            header("Location: {$baseUrl}/admin/login.php");
            exit;
        }
    }

    public static function login(array $user): void {
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];
    }

    public static function logout(): void {
        unset($_SESSION['admin_user_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        session_destroy();
    }

    public static function getUser(): ?array {
        if (!self::isLoggedIn()) return null;
        return [
            'id' => $_SESSION['admin_user_id'],
            'username' => $_SESSION['admin_username'],
            'name' => $_SESSION['admin_name'],
            'role' => $_SESSION['admin_role']
        ];
    }

    public static function setFlash(string $type, string $message): void {
        $_SESSION['flash_message'] = [
            'type' => $type, // 'success', 'error', 'warning', 'info'
            'message' => $message
        ];
    }

    public static function getFlash(): ?array {
        if (!empty($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }
}
