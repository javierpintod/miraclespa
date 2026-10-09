<?php
/**
 * MIRACLE SPA - Ayudante de Autenticación Híbrida (Sesiones & JWT)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Services/JwtService.php';

class AuthHelper {
    /**
     * Comprueba si el usuario actual está autenticado por Sesión PHP o Token JWT
     */
    public static function isLoggedIn(): bool {
        // 1. Verificar Sesión PHP
        if (!empty($_SESSION['admin_user_id'])) {
            return true;
        }

        // 2. Verificar Token JWT en encabezado 'Authorization: Bearer' o cookie
        $token = JwtService::getTokenFromRequest();
        if ($token) {
            $validation = JwtService::validateToken($token);
            if ($validation['valid']) {
                // Hidratar sesión temporalmente a partir del payload del JWT
                $payload = $validation['payload'];
                $_SESSION['admin_user_id'] = $payload['sub'] ?? 1;
                $_SESSION['admin_username'] = $payload['username'] ?? 'admin';
                $_SESSION['admin_name'] = $payload['name'] ?? 'Administrador';
                $_SESSION['admin_role'] = $payload['role'] ?? 'admin';
                $_SESSION['jwt_token'] = $token;
                return true;
            }
        }

        return false;
    }

    /**
     * Exige autenticación; si es petición API devuelve 401 JSON, si es web redirige a login
     */
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            $isApi = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/') || 
                     (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'error' => 'No autorizado. Se requiere un token JWT válido en el encabezado Authorization: Bearer <token>',
                    'code' => 'UNAUTHORIZED_JWT'
                ]);
                exit;
            }

            $baseUrl = AppConfig::getBaseUrl();
            header("Location: {$baseUrl}/admin/login.php");
            exit;
        }
    }

    /**
     * Inicia sesión guardando en sesión y generando el token JWT
     */
    public static function login(array $user): string {
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];

        // Generar JWT
        $jwtToken = JwtService::generateToken([
            'sub' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role']
        ]);

        $_SESSION['jwt_token'] = $jwtToken;

        // Establecer cookie HttpOnly para el token
        setcookie('jwt_token', $jwtToken, [
            'expires' => time() + 86400,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return $jwtToken;
    }

    /**
     * Cierra la sesión y destruye el token JWT
     */
    public static function logout(): void {
        unset($_SESSION['admin_user_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['jwt_token']);
        session_destroy();

        // Borrar cookie
        setcookie('jwt_token', '', [
            'expires' => time() - 3600,
            'path' => '/'
        ]);
    }

    /**
     * Retorna los datos del usuario autenticado
     */
    public static function getUser(): ?array {
        if (!self::isLoggedIn()) return null;
        return [
            'id' => $_SESSION['admin_user_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? '',
            'name' => $_SESSION['admin_name'] ?? '',
            'role' => $_SESSION['admin_role'] ?? 'admin',
            'jwt_token' => $_SESSION['jwt_token'] ?? self::getJwtToken()
        ];
    }

    /**
     * Obtiene el token JWT actual
     */
    public static function getJwtToken(): ?string {
        if (!empty($_SESSION['jwt_token'])) {
            return $_SESSION['jwt_token'];
        }
        return JwtService::getTokenFromRequest();
    }

    public static function setFlash(string $type, string $message): void {
        $_SESSION['flash_message'] = [
            'type' => $type,
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
