<?php
/**
 * MIRACLE SPA - Ayudante de Autenticación Híbrida (Sesiones & JWT)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Services/JwtService.php';
require_once __DIR__ . '/../Services/UserSessionManager.php';

class AuthHelper {
    /**
     * Retorna la instancia única del administrador de sesiones (Singleton)
     */
    public static function getSessionManager(): UserSessionManager {
        return UserSessionManager::getInstance();
    }

    /**
     * Comprueba si el usuario actual está autenticado por Sesión PHP o Token JWT,
     * validando estrictamente que sea la ÚNICA sesión activa registrada en el sistema.
     */
    public static function isLoggedIn(): bool {
        $sessionManager = self::getSessionManager();

        // 1. Verificar Sesión PHP nativa y validar contra Singleton / BD
        if (!empty($_SESSION['admin_user_id'])) {
            if ($sessionManager->validateCurrentSession()) {
                return true;
            }
            // Si la sesión fue revocada por otro inicio de sesión, validateCurrentSession() ya limpió las credenciales
            return false;
        }

        // 2. Verificar Token JWT en encabezado 'Authorization: Bearer' o cookie
        $token = JwtService::getTokenFromRequest();
        if ($token) {
            $validation = JwtService::validateToken($token);
            if ($validation['valid']) {
                $payload = $validation['payload'];
                $userId = (int)($payload['sub'] ?? 1);
                $jwtSid = $payload['sid'] ?? null;

                // Si el token incluye identificador de sesión única, verificar que continúe siendo el activo
                if ($jwtSid !== null && !$sessionManager->isSessionActive($userId, $jwtSid)) {
                    return false;
                }

                // Hidratar sesión a partir del payload del JWT
                $_SESSION['admin_user_id'] = $userId;
                $_SESSION['admin_username'] = $payload['username'] ?? 'admin';
                $_SESSION['admin_name'] = $payload['name'] ?? 'Administrador';
                $_SESSION['admin_role'] = $payload['role'] ?? 'admin';
                $_SESSION['jwt_token'] = $token;
                if ($jwtSid) {
                    $_SESSION['active_session_token'] = $jwtSid;
                }
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
                    'error' => 'No autorizado. La sesión no está activa o fue revocada por un inicio de sesión en otro dispositivo.',
                    'code' => 'UNAUTHORIZED_SESSION_CONFLICT'
                ]);
                exit;
            }

            $baseUrl = AppConfig::getBaseUrl();
            header("Location: {$baseUrl}/admin/login.php");
            exit;
        }
    }

    /**
     * Inicia sesión garantizando una ÚNICA sesión activa en el sistema (Patrón Singleton).
     * Cualquier sesión abierta anteriormente para este usuario queda invalidada.
     */
    public static function login(array $user): string {
        $userId = (int)$user['id'];
        $sessionManager = self::getSessionManager();

        // Registrar nueva sesión única en el Singleton
        $activeToken = $sessionManager->registerSession($userId);

        $_SESSION['admin_user_id'] = $userId;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['active_session_token'] = $activeToken;

        // Generar JWT incluyendo el claim 'sid' enlazado a la sesión única activa
        $jwtToken = JwtService::generateToken([
            'sub' => $userId,
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role'],
            'sid' => $activeToken
        ]);

        $_SESSION['jwt_token'] = $jwtToken;

        // Establecer cookie HttpOnly para el token si los encabezados no se han enviado aún
        if (!headers_sent()) {
            setcookie('jwt_token', $jwtToken, [
                'expires' => time() + 86400,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        return $jwtToken;
    }

    /**
     * Cierra la sesión activa única tanto localmente como en la base de datos
     */
    public static function logout(): void {
        self::getSessionManager()->terminateSession();
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
