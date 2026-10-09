<?php
/**
 * MIRACLE SPA - Cabecera y Sidebar del Panel de Administración
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';
require_once __DIR__ . '/../app/Helpers/ViewHelper.php';

AuthHelper::requireLogin();
$loggedUser = AuthHelper::getUser();
$currentScript = basename($_SERVER['PHP_SELF']);
$flash = AuthHelper::getFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Panel de Administración' ?> - Miracle Spa</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js para el Dashboard de demanda -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#f4f7f5] text-[#1c2420] min-h-screen flex">

    <!-- SIDEBAR DE ADMINISTRACIÓN -->
    <aside class="w-64 bg-[#12372a] text-white shrink-0 flex flex-col min-h-screen border-r border-[#1a4a39] hidden md:flex sticky top-0 h-screen z-30">
        <!-- Logo -->
        <div class="p-6 border-b border-[#1a4a39]">
            <a href="index.php" class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-[#d4af37]/20 border border-[#d4af37]/40 text-[#d4af37] flex items-center justify-center font-serif font-bold text-lg">M</span>
                <div>
                    <span class="font-serif text-lg font-bold tracking-tight block">Miracle Spa</span>
                    <span class="text-[10px] uppercase tracking-widest text-[#d4af37] font-semibold -mt-1 block">Panel de Negocio</span>
                </div>
            </a>
        </div>

        <!-- Menú de Navegación -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto text-xs font-semibold">
            <div class="text-[10px] uppercase font-bold text-emerald-400/60 px-3 py-1 tracking-wider">Operaciones</div>

            <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'index.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">📊</span>
                <span>Dashboard de Ocupación</span>
            </a>

            <a href="calendario.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'calendario.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">📅</span>
                <span>Calendario Global</span>
            </a>

            <a href="citas.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'citas.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">📋</span>
                <span>Gestión de Citas</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-emerald-400/60 px-3 pt-4 pb-1 tracking-wider">Catálogo & Personal</div>

            <a href="servicios.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'servicios.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">💆</span>
                <span>Servicios & Precios</span>
            </a>

            <a href="profesionales.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'profesionales.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">👥</span>
                <span>Profesionales & Horarios</span>
            </a>

            <a href="clientes.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'clientes.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">📇</span>
                <span>Clientes & Historial</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-emerald-400/60 px-3 pt-4 pb-1 tracking-wider">Comunicaciones & Sistema</div>

            <a href="correos.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'correos.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">✉️</span>
                <span>Bandeja de Correos</span>
            </a>

            <a href="configuracion.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors <?= $currentScript === 'configuracion.php' ? 'bg-[#1a4a39] text-[#d4af37] font-bold shadow-xs' : 'text-emerald-100/80 hover:bg-[#164333] hover:text-white' ?>">
                <span class="text-base">⚙️</span>
                <span>Configuración & SMTP</span>
            </a>

            <a href="../tests.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-emerald-200/90 hover:bg-[#164333] hover:text-white">
                <span class="text-base">🧪</span>
                <span>Ejecutor de Pruebas</span>
            </a>
        </nav>

        <!-- Pie de Usuario -->
        <div class="p-4 border-t border-[#1a4a39] text-xs">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-white"><?= htmlspecialchars($loggedUser['name'] ?? 'Admin') ?></div>
                    <div class="text-[10px] text-emerald-300 capitalize"><?= htmlspecialchars($loggedUser['role'] ?? 'Admin') ?></div>
                </div>
                <a href="logout.php" title="Cerrar Sesión" class="text-gray-300 hover:text-rose-400 transition-colors p-1.5 rounded-lg hover:bg-[#164333]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ÁREA DE CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOPBAR SUPERIOR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-20 shadow-2xs">
            <div class="flex items-center gap-3">
                <h1 class="font-serif font-bold text-lg text-gray-900"><?= $pageTitle ?? 'Panel de Control' ?></h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="text-xs font-semibold text-gray-600 hover:text-[#12372a] flex items-center gap-1.5 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                    <span>Ver Sitio Público</span>
                    <span>↗</span>
                </a>
                <div class="text-xs text-gray-400">
                    <?= date('d M, Y') ?>
                </div>
            </div>
        </header>

        <!-- ALERTAS FLASH -->
        <?php if ($flash): ?>
            <div class="px-6 pt-4">
                <div class="p-4 rounded-xl text-xs font-medium border <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        <main class="p-6 md:p-8 flex-1 overflow-y-auto">
