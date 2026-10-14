<?php
/**
 * MIRACLE SPA - Cabecera y Sidebar del Panel de Administración
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
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
    <title><?= $pageTitle ?? 'Panel de Administración' ?> — Miracle Spa Postmodern Atelier</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js para el Dashboard de demanda -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts: Syne, Space Grotesk, Space Mono, Plus Jakarta Sans -->
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
<body class="bg-[#F7F5EE] text-[#0D1117] min-h-screen flex antialiased selection:bg-[#D4FF00] selection:text-[#0D1117]">

    <!-- SIDEBAR DE ADMINISTRACIÓN POSMODERNISTA -->
    <aside class="w-64 bg-[#0D1117] text-white shrink-0 flex flex-col min-h-screen border-r-2 border-[#0D1117] hidden md:flex sticky top-0 h-screen z-30">
        <!-- Logo -->
        <div class="p-6 border-b-2 border-[#1E2430]">
            <a href="index.php" class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-[#2548E8] text-white border-2 border-[#D4FF00] flex items-center justify-center font-syne font-extrabold text-xl pm-shadow-xs">M</span>
                <div>
                    <span class="font-syne font-bold text-lg tracking-tight block text-white">Miracle Spa</span>
                    <span class="font-mono-pm text-[9px] uppercase tracking-wider text-[#D4FF00] font-bold block -mt-0.5">[ ATELIER ADMIN ]</span>
                </div>
            </a>
        </div>

        <!-- Menú de Navegación -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto text-xs font-mono-pm font-bold">
            <div class="text-[10px] uppercase font-bold text-[#D4FF00] px-3 py-1 tracking-wider">[ 01 // OPERACIONES ]</div>

            <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'index.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">📊</span>
                <span>Dashboard Demanda</span>
            </a>

            <a href="calendario.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'calendario.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">📅</span>
                <span>Calendario Global</span>
            </a>

            <a href="citas.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'citas.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">📋</span>
                <span>Gestión de Citas</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-[#2548E8] px-3 pt-4 pb-1 tracking-wider">[ 02 // CATÁLOGO ]</div>

            <a href="servicios.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'servicios.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">💆</span>
                <span>Servicios & Precios</span>
            </a>

            <a href="profesionales.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'profesionales.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">👥</span>
                <span>Especialistas</span>
            </a>

            <a href="clientes.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'clientes.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">📇</span>
                <span>Directorio Clientes</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-[#FF2E7E] px-3 pt-4 pb-1 tracking-wider">[ 03 // SISTEMA ]</div>

            <a href="correos.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'correos.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">✉️</span>
                <span>Bandeja Correos</span>
            </a>

            <a href="configuracion.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'configuracion.php' ? 'bg-[#D4FF00] text-[#0D1117] font-extrabold border-2 border-[#0D1117] pm-shadow-xs' : 'text-gray-300 hover:bg-[#1A202C] hover:text-[#D4FF00]' ?>">
                <span class="text-base">⚙️</span>
                <span>Ajustes & SMTP</span>
            </a>

            <a href="../tests.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-[#D4FF00] hover:bg-[#1A202C] hover:text-white">
                <span class="text-base">🧪</span>
                <span>Suite Pruebas</span>
            </a>
        </nav>

        <!-- Pie de Usuario Posmodernista -->
        <div class="p-4 border-t-2 border-[#1E2430] text-xs bg-[#05070A]">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-white font-space"><?= htmlspecialchars($loggedUser['name'] ?? 'Admin') ?></div>
                    <div class="text-[10px] text-[#D4FF00] font-mono-pm font-bold uppercase">[ <?= htmlspecialchars($loggedUser['role'] ?? 'ADMIN') ?> ]</div>
                    <div class="text-[9px] text-[#00E5A3] flex items-center gap-1 mt-1 font-mono-pm font-bold">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#00E5A3] animate-pulse"></span>
                        Sesión Única Activa
                    </div>
                </div>
                <a href="logout.php" title="Cerrar Sesión" class="text-gray-400 hover:text-[#FF2E7E] transition-colors p-1.5 rounded-lg hover:bg-[#1E2430]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ÁREA DE CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOPBAR SUPERIOR POSMODERNA -->
        <header class="bg-white/95 backdrop-blur-md border-b-2 border-[#0D1117] h-16 flex items-center justify-between px-6 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <h1 class="font-syne font-bold text-lg text-[#0D1117]"><?= $pageTitle ?? 'Panel de Control' ?></h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="btn-pm-acid text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5 font-space">
                    <span>Ver Portal Público</span>
                    <span>↗</span>
                </a>
                <div class="text-xs font-mono-pm font-bold text-[#0D1117] bg-[#EFECE3] px-3 py-1.5 rounded-xl border-2 border-[#0D1117]">
                    <?= date('d M, Y') ?>
                </div>
            </div>
        </header>

        <!-- ALERTAS FLASH POSMODERNAS -->
        <?php if ($flash): ?>
            <div class="px-6 pt-4">
                <div class="p-4 rounded-xl text-xs font-mono-pm font-bold border-2 border-[#0D1117] pm-shadow-sm <?= $flash['type'] === 'success' ? 'bg-[#00E5A3] text-[#0D1117]' : (($flash['type'] === 'warning' || $flash['type'] === 'session_conflict') ? 'bg-[#D4FF00] text-[#0D1117]' : 'bg-[#FF2E7E] text-white') ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        <main class="p-6 md:p-8 flex-1 overflow-y-auto">
