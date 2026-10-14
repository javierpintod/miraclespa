<?php
/**
 * MIRACLE SPA - Inicio de Sesión de Administración
 * Estilo Gráfico: Glassmorfismo / Glassmorphism & Aurora Luxury Spa
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
    <title>Acceso Administrativo — Miracle Spa Sanctuary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased selection:bg-teal-500 selection:text-white relative overflow-hidden">

    <!-- Orbes de Luz de Fondo Glassmorfista -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-1/4 -right-24"></div>
    <div class="aurora-orb aurora-orb-rose w-80 h-80 -bottom-10 left-1/4"></div>

    <div class="max-w-md w-full glass-modal rounded-3xl overflow-hidden relative z-10 border border-white/80 shadow-2xl">
        <!-- Cabecera Glassmorfista -->
        <div class="p-8 text-center bg-slate-900/85 backdrop-blur-md text-white border-b border-white/10 relative">
            <span class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-outfit font-extrabold text-2xl mx-auto mb-3 shadow-lg shadow-teal-500/30 border border-white/30">
                M
            </span>
            <h1 class="text-2xl font-outfit font-extrabold tracking-tight text-white">Miracle Spa</h1>
            <p class="font-mono-pm text-[10px] text-teal-300 mt-1 uppercase tracking-widest font-bold">[ ACCESO ADMINISTRATIVO ]</p>
        </div>

        <div class="p-8">
            <?php if ($warning): ?>
                <div class="glass-card-subtle bg-amber-500/15 border border-amber-500/30 text-amber-900 text-xs rounded-2xl p-4 mb-6 font-mono-pm font-bold flex items-start gap-2.5">
                    <span class="text-lg shrink-0">⚡</span>
                    <div>
                        <div class="font-extrabold uppercase text-[10px] tracking-wider text-amber-800 mb-0.5">[ CONTROL DE SESIÓN ÚNICA ]</div>
                        <div><?= htmlspecialchars($warning) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="glass-card-subtle bg-rose-500/15 border border-rose-500/30 text-rose-900 text-xs rounded-2xl p-4 mb-6 font-mono-pm font-bold flex items-center gap-2">
                    <span class="text-rose-600 font-bold text-base">⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nombre de Usuario</label>
                    <input type="text" name="username" value="admin" required class="w-full px-4 py-3 rounded-2xl border border-white/80 bg-white/80 text-sm font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contraseña</label>
                    <input type="password" name="password" value="admin123" required class="w-full px-4 py-3 rounded-2xl border border-white/80 bg-white/80 text-sm font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none shadow-inner">
                </div>

                <button type="submit" class="w-full btn-glass-primary py-3.5 rounded-2xl text-sm font-outfit font-bold mt-2 shadow-lg">
                    Ingresar al Panel de Gestión →
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-200/60 text-center">
                <div class="text-[11px] text-slate-500 font-mono-pm font-bold mb-1.5">[ CREDENCIALES POR DEFECTO ]</div>
                <div class="text-xs text-slate-700 font-mono-pm font-bold bg-white/60 px-3 py-1.5 rounded-xl border border-white/80 inline-block shadow-2xs">user: <strong>admin</strong> | pass: <strong>admin123</strong></div>
                <a href="../index.php" class="text-xs font-mono-pm text-slate-600 hover:text-teal-700 font-bold block mt-4 transition-colors">← Volver al Portal de Clientes</a>
            </div>
        </div>
    </div>
</body>
</html>
