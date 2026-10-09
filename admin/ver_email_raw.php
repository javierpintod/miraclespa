<?php
/**
 * MIRACLE SPA - Visualizador Raw de Correo HTML para el Iframe del Simulador
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';
require_once __DIR__ . '/../app/Models/EmailLog.php';

AuthHelper::requireLogin();

$id = (int)($_GET['id'] ?? 0);
$email = EmailLog::findById($id);

if (!$email) {
    die("Correo no encontrado.");
}

// Renderizar el HTML guardado
echo $email['body_html'];
