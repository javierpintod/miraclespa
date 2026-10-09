<?php
/**
 * MIRACLE SPA - Cierre de Sesión Administrativa
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';

AuthHelper::logout();
header('Location: login.php');
exit;
