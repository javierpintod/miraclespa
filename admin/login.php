<?php
/**
 * MIRACLE SPA - Inicio de Sesión de Administración
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/AdminUser.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';

$error = '';

if (AuthHelper::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $user = AdminUser::authenticate($username, $password);

    if ($user) {
        AuthHelper::login($user);
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas. Verifica el usuario y la contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Miracle Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#12372a] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-950/20">
        <div class="p-8 text-center bg-[#0d2a20] text-white">
            <span class="w-12 h-12 rounded-full bg-[#d4af37]/20 border border-[#d4af37]/40 text-[#d4af37] flex items-center justify-center font-serif font-bold text-xl mx-auto mb-3">
                M
            </span>
            <h1 class="text-2xl font-serif font-bold tracking-tight">Miracle Spa</h1>
            <p class="text-xs text-emerald-200/80 mt-1 uppercase tracking-widest font-semibold">Panel de Control & Administración</p>
        </div>

        <div class="p-8">
            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-3 mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Usuario</label>
                    <input type="text" name="username" value="admin" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" value="admin123" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-[#12372a] text-[#d4af37] py-3.5 rounded-xl font-bold text-sm hover:bg-[#1a4a39] transition-all shadow-md mt-2">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <div class="text-[11px] text-gray-400">Credenciales por defecto:</div>
                <div class="text-xs text-gray-700 font-mono mt-0.5 font-semibold">Usuario: <strong>admin</strong> | Clave: <strong>admin123</strong></div>
                <a href="../index.php" class="text-xs text-gray-500 hover:text-[#12372a] underline block mt-4 font-medium">← Volver al Portal de Clientes</a>
            </div>
        </div>
    </div>
</body>
</html>
