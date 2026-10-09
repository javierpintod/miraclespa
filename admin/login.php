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
<body class="bg-[#FFF9E6] min-h-screen flex items-center justify-center p-4">
    <!-- Formas Geométricas de Fondo Memphis -->
    <div class="fixed top-10 left-10 w-24 h-24 rounded-full bg-[#3A86FF]/30 border-2 border-[#18181B] pointer-events-none hidden sm:block"></div>
    <div class="fixed bottom-12 right-12 w-28 h-28 rotate-12 bg-[#FF006E]/20 border-2 border-[#18181B] pointer-events-none hidden sm:block"></div>
    <div class="fixed bottom-16 left-16 w-16 h-16 bg-[#FFBE0B]/40 border-2 border-[#18181B] rotate-45 pointer-events-none hidden sm:block"></div>

    <div class="max-w-md w-full bg-white rounded-3xl border-2 border-[#18181B] memphis-shadow-lg overflow-hidden relative z-10">
        <!-- Cabecera Memphis -->
        <div class="p-8 text-center bg-[#FF006E] text-white border-b-2 border-[#18181B] relative">
            <span class="w-14 h-14 rounded-2xl bg-[#FFBE0B] text-[#18181B] border-2 border-[#18181B] memphis-shadow-sm flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                M
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-white">Miracle Spa</h1>
            <p class="text-xs text-[#FFBE0B] mt-1 uppercase tracking-widest font-extrabold">⚡ Acceso Administrativo</p>
        </div>

        <div class="p-8">
            <?php if ($error): ?>
                <div class="bg-[#FF006E] border-2 border-[#18181B] text-white text-xs rounded-xl p-3 mb-6 font-bold memphis-shadow-sm">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Usuario</label>
                    <input type="text" name="username" value="admin" required class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm font-medium focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Contraseña</label>
                    <input type="password" name="password" value="admin123" required class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm font-medium focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm">
                </div>

                <button type="submit" class="w-full btn-memphis-primary py-3.5 rounded-xl text-sm font-extrabold mt-3">
                    Ingresar al Panel
                </button>
            </form>

            <div class="mt-6 pt-6 border-t-2 border-[#18181B] text-center">
                <div class="text-[11px] text-gray-500 font-bold">Credenciales por defecto:</div>
                <div class="text-xs text-[#18181B] font-mono mt-0.5 font-bold bg-[#FFBE0B] px-3 py-1.5 rounded-lg border border-[#18181B] inline-block memphis-shadow-sm">Usuario: <strong>admin</strong> | Clave: <strong>admin123</strong></div>
                <a href="../index.php" class="text-xs text-gray-700 hover:text-[#FF006E] font-bold block mt-4 underline">← Volver al Portal de Clientes</a>
            </div>
        </div>
    </div>
</body>
</html>
