<?php
/**
 * MIRACLE SPA - API: Autenticación y Emisión de Token JWT
 * POST /api/auth/login.php
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/Models/AdminUser.php';
require_once __DIR__ . '/../../app/Services/JwtService.php';
require_once __DIR__ . '/../../app/Helpers/AuthHelper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Debes proporcionar usuario y contraseña.']);
    exit;
}

$user = AdminUser::authenticate($username, $password);

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Credenciales inválidas. Usuario o contraseña incorrectos.'
    ]);
    exit;
}

// Iniciar sesión y generar JWT
$token = AuthHelper::login($user);

echo json_encode([
    'success' => true,
    'message' => 'Autenticación exitosa con JWT.',
    'token' => $token,
    'token_type' => 'Bearer',
    'expires_in' => 86400,
    'user' => [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ]
]);
