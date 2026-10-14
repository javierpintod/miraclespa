<?php
/**
 * MIRACLE SPA - Suite de Pruebas Unitarias & Integración
 * Patrón Creacional Singleton & Sesión Única Activa por Usuario
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/UserSessionManager.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';

class SessionSingletonTest {
    private array $results = [];
    private int $testUserAId = 0;
    private int $testUserBId = 0;

    public function runAll(): array {
        try {
            $this->setUp();

            $this->testSingletonIdentity();
            $this->testConstructorIsPrivate();
            $this->testCloneIsPrivate();
            $this->testUnserializeIsRejected();
            $this->testSingleActiveSessionPerUser();
            $this->testSupersededSessionIsInvalidated();
            $this->testValidateCurrentSessionConflictDetection();
            $this->testMultiUserSessionIsolation();
            $this->testTerminateSessionLogout();
            $this->testAuthHelperIntegrationWithSingleton();
        } finally {
            $this->cleanup();
        }

        return $this->results;
    }

    private function setUp(): void {
        try {
            $db = Database::getConnection();
            $this->cleanup();

            $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash, name, email, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['test_single_a', password_hash('pass123', PASSWORD_DEFAULT), 'Admin Alpha Test', 'alpha@spa.test', 'admin']);
            $this->testUserAId = (int)$db->lastInsertId();

            $stmt->execute(['test_single_b', password_hash('pass123', PASSWORD_DEFAULT), 'Admin Beta Test', 'beta@spa.test', 'recepcionista']);
            $this->testUserBId = (int)$db->lastInsertId();
        } catch (\Throwable $e) {
            // Si la BD no está disponible, utilizar IDs ficticios en modo memoria
            $this->testUserAId = 9991;
            $this->testUserBId = 9992;
        }
    }

    private function cleanup(): void {
        try {
            $db = Database::getConnection();
            $db->exec("DELETE FROM active_sessions WHERE user_id IN (SELECT id FROM admin_users WHERE username LIKE 'test_single_%')");
            $db->exec("DELETE FROM admin_users WHERE username LIKE 'test_single_%'");
        } catch (\Throwable $e) {}
    }

    private function assert(string $testName, bool $condition, string $message = ''): void {
        $this->results[] = [
            'suite' => 'Patrón Singleton & Sesión Única por Usuario',
            'test' => $testName,
            'passed' => $condition,
            'message' => $message
        ];
    }

    /**
     * Prueba 1: Verifica que múltiples llamadas a getInstance() devuelvan exactamente la misma instancia
     */
    private function testSingletonIdentity(): void {
        $instance1 = UserSessionManager::getInstance();
        $instance2 = UserSessionManager::getInstance();
        $helperManager = AuthHelper::getSessionManager();

        $this->assert(
            'UserSessionManager::getInstance() debe devolver idéntica referencia en memoria (Singleton)',
            $instance1 === $instance2 && $instance1 === $helperManager
        );
    }

    /**
     * Prueba 2: Verifica que el constructor sea privado para evitar 'new UserSessionManager()'
     */
    private function testConstructorIsPrivate(): void {
        $reflector = new ReflectionClass(UserSessionManager::class);
        $constructor = $reflector->getConstructor();

        $this->assert(
            'El constructor de UserSessionManager debe ser privado (Principio GoF)',
            $constructor !== null && $constructor->isPrivate()
        );
    }

    /**
     * Prueba 3: Verifica que __clone() sea privado para impedir duplicación de instancias
     */
    private function testCloneIsPrivate(): void {
        $reflector = new ReflectionClass(UserSessionManager::class);
        $hasPrivateClone = false;

        if ($reflector->hasMethod('__clone')) {
            $cloneMethod = $reflector->getMethod('__clone');
            $hasPrivateClone = $cloneMethod->isPrivate();
        }

        $this->assert(
            'El método __clone() debe ser privado para prevenir duplicación del Singleton',
            $hasPrivateClone
        );
    }

    /**
     * Prueba 4: Verifica que __wakeup() lance excepción impidiendo deserialización
     */
    private function testUnserializeIsRejected(): void {
        $reflector = new ReflectionClass(UserSessionManager::class);
        $rejected = false;

        try {
            $instance = UserSessionManager::getInstance();
            $instance->__wakeup();
        } catch (\Exception $e) {
            $rejected = str_contains(strtolower($e->getMessage()), 'singleton');
        }

        $this->assert(
            'La deserialización (__wakeup) debe arrojar excepción de protección Singleton',
            $rejected
        );
    }

    /**
     * Prueba 5: Registro de sesión única activa
     */
    private function testSingleActiveSessionPerUser(): void {
        $manager = UserSessionManager::getInstance();
        $userId = $this->testUserAId;

        $token1 = $manager->registerSession($userId, null, ['ip' => '192.168.1.10', 'user_agent' => 'Browser Alpha']);

        $this->assert(
            'El token de sesión única generado debe ser un hash criptográfico de al menos 32 caracteres',
            strlen($token1) >= 32
        );

        $this->assert(
            'La sesión registrada debe ser verificada como activa para el usuario',
            $manager->isSessionActive($userId, $token1) === true
        );
    }

    /**
     * Prueba 6: Al iniciar una nueva sesión para el mismo usuario, la anterior queda invalidada
     */
    private function testSupersededSessionIsInvalidated(): void {
        $manager = UserSessionManager::getInstance();
        $userId = $this->testUserAId;

        // Sesión 1: Navegador en casa
        $token1 = $manager->registerSession($userId, null, ['ip' => '190.25.1.1', 'user_agent' => 'Chrome Casa']);
        $this->assert('Sesión inicial del usuario debe estar activa', $manager->isSessionActive($userId, $token1));

        // Sesión 2: Mismo usuario inicia sesión en la oficina (nuevo dispositivo)
        $token2 = $manager->registerSession($userId, null, ['ip' => '200.40.2.2', 'user_agent' => 'Edge Oficina']);

        $this->assert(
            'La nueva sesión (dispositivo 2) debe quedar inmediatamente como la activa',
            $manager->isSessionActive($userId, $token2) === true
        );

        $this->assert(
            'La sesión anterior (dispositivo 1) DEBE SER INVALIDADA (Garantía de sesión única)',
            $manager->isSessionActive($userId, $token1) === false
        );
    }

    /**
     * Prueba 7: Detección automática de conflicto en validateCurrentSession()
     */
    private function testValidateCurrentSessionConflictDetection(): void {
        $manager = UserSessionManager::getInstance();
        $userId = $this->testUserAId;

        // Dispositivo A inicia sesión
        $tokenA = $manager->registerSession($userId);

        // Simulamos que Dispositivo B inicia sesión con el mismo usuario
        $tokenB = $manager->registerSession($userId);

        // Dispositivo A intenta realizar una petición llevando en $_SESSION su token anterior
        $_SESSION['admin_user_id'] = $userId;
        $_SESSION['active_session_token'] = $tokenA;

        $isValid = $manager->validateCurrentSession();

        $this->assert(
            'validateCurrentSession() debe retornar FALSE ante un token superado por otro inicio de sesión',
            $isValid === false
        );

        $this->assert(
            'Las variables de autenticación local deben ser purgadas tras detectar el conflicto',
            empty($_SESSION['admin_user_id']) && empty($_SESSION['active_session_token'])
        );

        $this->assert(
            'Debe fijarse un mensaje flash de advertencia sobre la finalización por sesión simultánea',
            !empty($_SESSION['flash_message']) && $_SESSION['flash_message']['type'] === 'session_conflict'
        );

        // Limpiar flash
        unset($_SESSION['flash_message']);
    }

    /**
     * Prueba 8: Aislamiento entre diferentes usuarios (User A y User B no interfieren)
     */
    private function testMultiUserSessionIsolation(): void {
        $manager = UserSessionManager::getInstance();
        $user1 = $this->testUserAId;
        $user2 = $this->testUserBId;

        $tokenUser1 = $manager->registerSession($user1);
        $tokenUser2 = $manager->registerSession($user2);

        $this->assert('Usuario 1 tiene su sesión activa independiente', $manager->isSessionActive($user1, $tokenUser1));
        $this->assert('Usuario 2 tiene su sesión activa independiente', $manager->isSessionActive($user2, $tokenUser2));

        // Renovar sesión de Usuario 1
        $newTokenUser1 = $manager->registerSession($user1);

        $this->assert('La renovación de sesión de Usuario 1 no debe afectar la sesión activa de Usuario 2', 
            $manager->isSessionActive($user2, $tokenUser2) === true &&
            $manager->isSessionActive($user1, $newTokenUser1) === true &&
            $manager->isSessionActive($user1, $tokenUser1) === false
        );
    }

    /**
     * Prueba 9: Cierre explícito de sesión (Logout)
     */
    private function testTerminateSessionLogout(): void {
        $manager = UserSessionManager::getInstance();
        $userId = $this->testUserAId;

        $token = $manager->registerSession($userId);
        $this->assert('Sesión creada previo al cierre', $manager->isSessionActive($userId, $token));

        $manager->terminateSession($userId);

        $this->assert(
            'Tras terminateSession(), la sesión no debe figurar activa en la base de datos ni memoria',
            $manager->isSessionActive($userId, $token) === false
        );
    }

    /**
     * Prueba 10: Integración completa de AuthHelper con UserSessionManager
     */
    private function testAuthHelperIntegrationWithSingleton(): void {
        $user = [
            'id' => $this->testUserAId,
            'username' => 'test_single_a',
            'name' => 'Admin Alpha Test',
            'role' => 'admin'
        ];

        // Login vía AuthHelper
        $jwt = AuthHelper::login($user);
        $this->assert('AuthHelper::login() debe emitir JWT y registrar sesión en el Singleton', !empty($jwt) && AuthHelper::isLoggedIn());

        $firstToken = $_SESSION['active_session_token'] ?? null;
        $this->assert('AuthHelper debe almacenar el active_session_token en la sesión', !empty($firstToken));

        // Simular inicio de sesión concurrente del mismo usuario desde otra ventana/dispositivo
        $manager = AuthHelper::getSessionManager();
        $secondToken = $manager->registerSession($user['id']);

        // Ahora restauramos el token de la primera ventana en la sesión
        $_SESSION['active_session_token'] = $firstToken;
        $_SESSION['admin_user_id'] = $user['id'];

        $isFirstStillLogged = AuthHelper::isLoggedIn();
        $this->assert(
            'AuthHelper::isLoggedIn() debe denegar acceso a la primera sesión cuando se abre otra para el mismo usuario',
            $isFirstStillLogged === false
        );

        // Limpiar
        $manager->terminateSession($user['id']);
    }
}
