<?php
/**
 * MIRACLE SPA - Conexión a Base de Datos (PDO)
 * Compatible con XAMPP (Apache + MariaDB / MySQL) y variables de entorno (.env)
 */

class Database {
    private static ?PDO $instance = null;

    // Configuración por defecto de XAMPP
    private const DEFAULT_HOST = '127.0.0.1';
    private const DEFAULT_NAME = 'miraclespa_db';
    private const DEFAULT_USER = 'root';
    private const DEFAULT_PASS = '';
    private const DEFAULT_PORT = '3306';
    private const DEFAULT_CHARSET = 'utf8mb4';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::loadEnv();

            $host = getenv('DB_HOST') ?: self::DEFAULT_HOST;
            $dbname = getenv('DB_NAME') ?: self::DEFAULT_NAME;
            $user = getenv('DB_USER') ?: self::DEFAULT_USER;
            $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : self::DEFAULT_PASS;
            $port = getenv('DB_PORT') ?: self::DEFAULT_PORT;
            $charset = self::DEFAULT_CHARSET;

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                if (str_contains($msg, 'Unknown database') || str_contains($msg, 'Access denied')) {
                    header('Location: install.php');
                    exit;
                }
                die("Error de conexión a la base de datos de Miracle Spa: " . htmlspecialchars($msg));
            }
        }

        return self::$instance;
    }

    /**
     * Carga variables de entorno de un archivo .env si existe en la raíz
     */
    private static function loadEnv(): void {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$name, $value] = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    if (!getenv($name)) {
                        putenv("{$name}={$value}");
                        $_ENV[$name] = $value;
                    }
                }
            }
        }
    }
}
