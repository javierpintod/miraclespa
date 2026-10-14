<?php
/**
 * MIRACLE SPA - Gestor de Sesiones de Usuario con Patrón Singleton
 * 
 * Implementación del Patrón Creacional Singleton (GoF) para garantizar:
 * 1. Una ÚNICA instancia del administrador de sesiones en todo el ciclo de vida de la aplicación.
 * 2. Que cada usuario autenticado SOLO pueda tener UNA ÚNICA SESIÓN ACTIVA simultánea.
 * 
 * Si un usuario inicia sesión en otro navegador, pestaña o dispositivo:
 * - La sesión anterior es inmediatamente revocada y sustituida en la base de datos.
 * - Al siguiente intento de acceso de la sesión antigua, se detecta el conflicto,
 *   se destruye la sesión caducada y se redirige con una notificación de seguridad.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

class UserSessionManager {
    /**
     * Referencia a la única instancia global (Patrón Singleton)
     */
    private static ?UserSessionManager $instance = null;

    /**
     * Conexión a Base de Datos
     */
    private ?PDO $db = null;

    /**
     * Almacenamiento en memoria para tests aislados o fallback
     */
    private array $memorySessions = [];

    /**
     * Constructor privado: evita la creación de instancias mediante 'new UserSessionManager()'
     */
    private function __construct(?PDO $db = null) {
        $this->ensureSessionStarted();
        try {
            $this->db = $db ?? Database::getConnection();
            $this->ensureTableExists();
        } catch (\Throwable $e) {
            // En entornos sin base de datos activa opera en modo memoria
            $this->db = null;
        }
    }

    /**
     * Previene la clonación del objeto (Regla Singleton)
     */
    private function __clone() {}

    /**
     * Previene la deserialización del objeto (Regla Singleton)
     */
    public function __wakeup(): void {
        throw new \Exception("No se permite deserializar una instancia del patrón Singleton.");
    }

    /**
     * Punto de acceso global para obtener la única instancia del gestor de sesiones.
     * 
     * @param PDO|null $db Conexión opcional (útil en testing o inyección de dependencias)
     * @return UserSessionManager La instancia única
     */
    public static function getInstance(?PDO $db = null): self {
        if (self::$instance === null) {
            self::$instance = new self($db);
        } elseif ($db !== null) {
            self::$instance->db = $db;
            self::$instance->ensureTableExists();
        }
        return self::$instance;
    }

    /**
     * Restablece la instancia Singleton (exclusivo para reiniciar estado en suites de prueba)
     */
    public static function resetInstance(): void {
        self::$instance = null;
    }

    /**
     * Inicia de forma segura la sesión nativa de PHP si aún no fue iniciada
     */
    public function ensureSessionStarted(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Asegura la creación de la tabla de sesiones activas en la BD
     */
    public function ensureTableExists(): void {
        if ($this->db === null) {
            return;
        }

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `active_sessions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL UNIQUE,
                `session_token` VARCHAR(128) NOT NULL,
                `ip_address` VARCHAR(45) NULL,
                `user_agent` VARCHAR(255) NULL,
                `last_activity` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user_token` (`user_id`, `session_token`),
                CONSTRAINT `fk_active_session_user` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            try {
                // Fallback sin foreign key para SQLite o MySQL restringido
                $sqlFallback = "CREATE TABLE IF NOT EXISTS `active_sessions` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL UNIQUE,
                    `session_token` VARCHAR(128) NOT NULL,
                    `ip_address` VARCHAR(45) NULL,
                    `user_agent` VARCHAR(255) NULL,
                    `last_activity` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );";
                $this->db->exec($sqlFallback);
            } catch (\Throwable $ex) {
                // Modo fallback en memoria
            }
        }
    }

    /**
     * Registra una nueva sesión única para un usuario.
     * Si el usuario ya tenía una sesión previa activa, esta es SOBRESCRITA e INVALIDADA
     * inmediatamente en la base de datos gracias a la restricción UNIQUE en user_id.
     *
     * @param int $userId ID del usuario autenticado
     * @param string|null $customToken Token personalizado o nulo para autogenerar
     * @param array $metadata Metadatos de la sesión (ip, user_agent)
     * @return string Token de la nueva sesión activa autorizada
     */
    public function registerSession(int $userId, ?string $customToken = null, array $metadata = []): string {
        $this->ensureSessionStarted();

        $token = $customToken ?? bin2hex(random_bytes(32));
        $ip = $metadata['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $ua = substr($metadata['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'), 0, 255);

        // 1. Guardar en registro en memoria
        $this->memorySessions[$userId] = [
            'token' => $token,
            'ip' => $ip,
            'user_agent' => $ua,
            'updated_at' => time()
        ];

        // 2. Persistir en Base de Datos de manera atómica (ON DUPLICATE KEY UPDATE)
        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO active_sessions (user_id, session_token, ip_address, user_agent, last_activity, created_at)
                    VALUES (:user_id, :token, :ip, :ua, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        session_token = VALUES(session_token),
                        ip_address = VALUES(ip_address),
                        user_agent = VALUES(user_agent),
                        last_activity = NOW()
                ");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':token' => $token,
                    ':ip' => $ip,
                    ':ua' => $ua
                ]);
            } catch (\Throwable $e) {
                error_log("UserSessionManager: Error al registrar sesión en BD: " . $e->getMessage());
            }
        }

        // 3. Vincular con las variables de sesión PHP nativas
        $_SESSION['admin_user_id'] = $userId;
        $_SESSION['active_session_token'] = $token;

        return $token;
    }

    /**
     * Verifica si el token suministrado corresponde a la sesión actualmente activa en la BD.
     *
     * @param int $userId ID del usuario
     * @param string|null $token Token a comprobar
     * @return bool True si es la sesión activa autorizada
     */
    public function isSessionActive(int $userId, ?string $token): bool {
        if (empty($token)) {
            return false;
        }

        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("SELECT session_token FROM active_sessions WHERE user_id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $row = $stmt->fetch();
                if ($row) {
                    return hash_equals((string)$row['session_token'], (string)$token);
                }
            } catch (\Throwable $e) {
                // Fallback a memoria
            }
        }

        if (isset($this->memorySessions[$userId])) {
            return hash_equals((string)$this->memorySessions[$userId]['token'], (string)$token);
        }

        return false;
    }

    /**
     * Valida la sesión actual en ejecución contra la autoridad centralizada (Singleton + BD).
     * Si otra sesión tomó el control del usuario, destruye la sesión actual y devuelve false.
     *
     * @return bool True si la sesión actual es válida; False si fue revocada por conflicto
     */
    public function validateCurrentSession(): bool {
        $this->ensureSessionStarted();

        if (empty($_SESSION['admin_user_id'])) {
            return false;
        }

        $userId = (int)$_SESSION['admin_user_id'];
        $currentToken = $_SESSION['active_session_token'] ?? null;

        // Si no hay token o el token no coincide con el único autorizado en la BD:
        if (!$currentToken || !$this->isSessionActive($userId, $currentToken)) {
            // CONFLICTO DETECTADO: El usuario abrió sesión en otro equipo/navegador
            $this->destroyLocalSession();

            $_SESSION['flash_message'] = [
                'type' => 'session_conflict',
                'message' => 'Tu sesión ha sido finalizada automáticamente porque se inició una nueva sesión con tu cuenta desde otro dispositivo o pestaña. Por seguridad, solo se permite una única sesión activa por usuario.'
            ];

            return false;
        }

        // Actualizar último timestamp de actividad
        $this->touchActivity($userId);
        return true;
    }

    /**
     * Actualiza la fecha de última actividad para la sesión activa
     */
    public function touchActivity(int $userId): void {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("UPDATE active_sessions SET last_activity = NOW() WHERE user_id = ?");
                $stmt->execute([$userId]);
            } catch (\Throwable $e) {}
        }

        if (isset($this->memorySessions[$userId])) {
            $this->memorySessions[$userId]['updated_at'] = time();
        }
    }

    /**
     * Termina la sesión activa del usuario (Cierre de Sesión / Logout),
     * eliminándola de la base de datos y limpiando la sesión PHP local.
     */
    public function terminateSession(?int $userId = null): void {
        $this->ensureSessionStarted();

        $targetUserId = $userId ?? ($_SESSION['admin_user_id'] ?? null);

        if ($targetUserId !== null) {
            $targetUserId = (int)$targetUserId;
            unset($this->memorySessions[$targetUserId]);

            if ($this->db !== null) {
                try {
                    $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE user_id = ?");
                    $stmt->execute([$targetUserId]);
                } catch (\Throwable $e) {}
            }
        }

        $this->destroyLocalSession();
    }

    /**
     * Limpia las variables de sesión y cookies de autenticación locales
     */
    public function destroyLocalSession(): void {
        $this->ensureSessionStarted();

        unset($_SESSION['admin_user_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['jwt_token']);
        unset($_SESSION['active_session_token']);

        if (!headers_sent()) {
            setcookie('jwt_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        unset($_COOKIE['jwt_token']);
    }

    /**
     * Obtiene los metadatos de la sesión activa del usuario
     */
    public function getActiveSessionDetails(int $userId): ?array {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM active_sessions WHERE user_id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $row = $stmt->fetch();
                if ($row) return $row;
            } catch (\Throwable $e) {}
        }

        if (isset($this->memorySessions[$userId])) {
            return [
                'user_id' => $userId,
                'session_token' => $this->memorySessions[$userId]['token'],
                'ip_address' => $this->memorySessions[$userId]['ip'],
                'user_agent' => $this->memorySessions[$userId]['user_agent'],
                'last_activity' => date('Y-m-d H:i:s', $this->memorySessions[$userId]['updated_at']),
                'created_at' => date('Y-m-d H:i:s', $this->memorySessions[$userId]['updated_at']),
            ];
        }

        return null;
    }
}
