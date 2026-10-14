<?php
/**
 * MIRACLE SPA - Inicio de Sesión de Administración
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/AdminUser.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';

$error = '';
$warning = '';

$flash = AuthHelper::getFlash();
if ($flash) {
    if ($flash['type'] === 'session_conflict' || $flash['type'] === 'warning') {
        $warning = $flash['message'];
    } elseif ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}

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
    <title>Acceso Administrativo — Miracle Spa Postmodern Atelier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        .font-syne { font-family: 'Syne', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F5EE] min-h-screen flex items-center justify-center p-4 antialiased selection:bg-[#D4FF00] selection:text-[#0D1117]">
    <!-- Formas Geométricas de Fondo Posmodernistas -->
    <div class="fixed top-10 left-10 w-28 h-28 rounded-2xl bg-[#D4FF00]/40 border-2 border-[#0D1117] rotate-12 pointer-events-none hidden sm:block"></div>
    <div class="fixed bottom-12 right-12 w-32 h-32 rounded-3xl -rotate-6 bg-[#2548E8]/20 border-2 border-[#0D1117] pointer-events-none hidden sm:block"></div>
    <div class="fixed bottom-16 left-16 w-20 h-20 bg-[#FF2E7E]/25 border-2 border-[#0D1117] rotate-45 pointer-events-none hidden sm:block"></div>

    <div class="max-w-md w-full bg-white rounded-3xl border-2 border-[#0D1117] pm-shadow-lg overflow-hidden relative z-10">
        <!-- Cabecera Posmodernista -->
        <div class="p-8 text-center bg-[#0D1117] text-white border-b-2 border-[#0D1117] relative">
            <span class="w-14 h-14 rounded-2xl bg-[#2548E8] text-white border-2 border-[#D4FF00] pm-shadow-xs flex items-center justify-center font-syne font-extrabold text-2xl mx-auto mb-3">
                M
            </span>
            <h1 class="text-2xl font-syne font-bold tracking-tight text-white">Miracle Spa</h1>
            <p class="font-mono-pm text-[10px] text-[#D4FF00] mt-1 uppercase tracking-widest font-bold">[ ACCESO ADMINISTRATIVO ATELIER ]</p>
        </div>

        <div class="p-8">
            <?php if ($warning): ?>
                <div class="bg-[#D4FF00]/25 border-2 border-[#0D1117] text-[#0D1117] text-xs rounded-xl p-3.5 mb-6 font-mono-pm font-bold pm-shadow-xs flex items-start gap-2.5">
                    <span class="text-lg shrink-0">⚡</span>
                    <div>
                        <div class="font-extrabold uppercase text-[10px] tracking-wider text-[#2548E8] mb-0.5">[ AVISO DE CONTROL DE SESIÓN ]</div>
                        <div><?= htmlspecialchars($warning) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-[#FF2E7E] border-2 border-[#0D1117] text-white text-xs rounded-xl p-3 mb-6 font-mono-pm font-bold pm-shadow-xs">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Nombre de Usuario</label>
                    <input type="text" name="username" value="admin" required class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm font-medium focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs">
                </div>

                <div>
                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Contraseña</label>
                    <input type="password" name="password" value="admin123" required class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm font-medium focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs">
                </div>

                <button type="submit" class="w-full btn-pm-primary py-3.5 rounded-xl text-sm font-space font-bold mt-3">
                    Ingresar al Panel de Gestión →
                </button>
            </form>

            <div class="mt-6 pt-6 border-t-2 border-[#0D1117] text-center">
                <div class="text-[11px] text-gray-500 font-mono-pm font-bold mb-1">[ CREDENCIALES POR DEFECTO ]</div>
                <div class="text-xs text-[#0D1117] font-mono-pm font-bold bg-[#EFECE3] px-3 py-1.5 rounded-lg border-2 border-[#0D1117] inline-block pm-shadow-xs">user: <strong>admin</strong> | pass: <strong>admin123</strong></div>
                <a href="../index.php" class="text-xs font-mono-pm text-gray-700 hover:text-[#2548E8] font-bold block mt-4 underline underline-offset-4">← Volver al Portal de Clientes</a>
            </div>
        </div>
    </div>
</body>
</html>
