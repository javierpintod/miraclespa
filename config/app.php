<?php
/**
 * MIRACLE SPA - Configuración General de la Aplicación
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/database.php';

class AppConfig {
    private static array $settingsCache = [];

    public static function getBaseUrl(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptPath));
        
        // Remove trailing subdirectories if inside admin, api, tests
        $dir = preg_replace('#/(admin|api|tests)(/.*)?$#', '', $dir);
        $dir = rtrim($dir, '/');
        
        return $protocol . $host . ($dir ? $dir : '');
    }

    public static function getSetting(string $key, string $default = ''): string {
        if (empty(self::$settingsCache)) {
            self::loadSettings();
        }
        return self::$settingsCache[$key] ?? $default;
    }

    public static function setSetting(string $key, string $value): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)");
        $res = $stmt->execute([$key, $value]);
        self::$settingsCache[$key] = $value;
        return $res;
    }

    public static function getAllSettings(): array {
        if (empty(self::$settingsCache)) {
            self::loadSettings();
        }
        return self::$settingsCache;
    }

    private static function loadSettings(): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            self::$settingsCache = $rows ?: [];
        } catch (Exception $e) {
            // Valores de reserva en caso de fallo antes de inicializar la BD
            self::$settingsCache = [
                'spa_name' => 'Miracle Spa Sanctuary',
                'spa_address' => 'Av. Las Palmas 450, Centro de Bienestar, Piso 2',
                'spa_phone' => '+1 (555) 789-2345',
                'spa_email' => 'citas@miraclespa.com',
                'currency_symbol' => '$',
                'buffer_minutes' => '15',
                'min_cancel_hours' => '4',
                'opening_time' => '09:00:00',
                'closing_time' => '20:00:00',
                'slot_interval_minutes' => '30',
                'mail_driver' => 'simulated'
            ];
        }
    }
}
