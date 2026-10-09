<?php
/**
 * MIRACLE SPA - API: Verificación e Inspección de Token JWT
 * GET /api/auth/verify.php
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/Services/JwtService.php';

$token = JwtService::getTokenFromRequest();

if (!$token) {
    http_response_code(401);
    echo json_encode([
        'valid' => false,
        'error' => 'No se proporcionó ningún token JWT en el encabezado Authorization: Bearer <token>'
    ]);
    exit;
}

$result = JwtService::validateToken($token);

if ($result['valid']) {
    echo json_encode([
        'valid' => true,
        'message' => 'Token JWT válido y vigente.',
        'payload' => $result['payload'],
        'header' => $result['header']
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'valid' => false,
        'error' => $result['error'] ?? 'Token JWT inválido o expirado.',
        'expired_at' => $result['expired_at'] ?? null
    ]);
}
