<?php
/**
 * MIRACLE SPA - Cabecera y Sidebar del Panel de Administración
 * Estilo Gráfico: Glassmorfismo / Glassmorphism & Aurora Luxury Spa
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
    <title><?= $pageTitle ?? 'Panel de Administración' ?> — Miracle Spa Sanctuary</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js para el Dashboard de demanda -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts: Outfit, Plus Jakarta Sans, Space Mono -->
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
<body class="text-slate-800 min-h-screen flex antialiased selection:bg-teal-500 selection:text-white relative">

    <!-- Orbes de Luz de Fondo Glassmorfista para la Suite de Administración -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-1/3 -right-24"></div>

    <!-- SIDEBAR DE ADMINISTRACIÓN GLASSMORFISTA OSCURO -->
    <aside class="w-64 bg-slate-950/90 backdrop-blur-2xl text-slate-200 shrink-0 flex flex-col min-h-screen border-r border-white/10 hidden md:flex sticky top-0 h-screen z-30 shadow-2xl">
        <!-- Logo -->
        <div class="p-6 border-b border-white/10">
            <a href="index.php" class="flex items-center gap-3 group">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-outfit font-extrabold text-xl shadow-lg shadow-teal-500/25 group-hover:scale-105 transition-transform border border-white/20">M</span>
                <div>
                    <span class="font-outfit font-bold text-lg tracking-tight block text-white leading-none">Miracle Spa</span>
                    <span class="font-mono-pm text-[9px] uppercase tracking-wider text-teal-400 font-bold block mt-1">[ ADMIN CONCIERGE ]</span>
                </div>
            </a>
        </div>

        <!-- Menú de Navegación -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto text-xs font-mono-pm">
            <div class="text-[10px] uppercase font-bold text-teal-400/80 px-3 py-1.5 tracking-wider">[ 01 // OPERACIONES ]</div>

            <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'index.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">📊</span>
                <span>Dashboard Demanda</span>
            </a>

            <a href="calendario.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'calendario.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">📅</span>
                <span>Calendario Global</span>
            </a>

            <a href="citas.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'citas.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">📋</span>
                <span>Gestión de Citas</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-teal-400/80 px-3 pt-4 pb-1.5 tracking-wider">[ 02 // CATÁLOGO ]</div>

            <a href="servicios.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'servicios.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">💆</span>
                <span>Servicios & Precios</span>
            </a>

            <a href="profesionales.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'profesionales.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">👥</span>
                <span>Especialistas</span>
            </a>

            <a href="clientes.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'clientes.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">📇</span>
                <span>Directorio Clientes</span>
            </a>

            <div class="text-[10px] uppercase font-bold text-teal-400/80 px-3 pt-4 pb-1.5 tracking-wider">[ 03 // SISTEMA ]</div>

            <a href="correos.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'correos.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">✉️</span>
                <span>Bandeja Correos</span>
            </a>

            <a href="configuracion.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentScript === 'configuracion.php' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-sm backdrop-blur-md' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="text-base">⚙️</span>
                <span>Ajustes & SMTP</span>
            </a>

            <a href="../tests.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-teal-300/80 hover:bg-white/5 hover:text-teal-200">
                <span class="text-base">🧪</span>
                <span>Suite Pruebas</span>
            </a>
        </nav>

        <!-- Pie de Usuario Glassmorfista -->
        <div class="p-4 border-t border-white/10 text-xs bg-slate-950/80">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-white font-outfit"><?= htmlspecialchars($loggedUser['name'] ?? 'Admin') ?></div>
                    <div class="text-[10px] text-teal-400 font-mono-pm font-bold uppercase">[ <?= htmlspecialchars($loggedUser['role'] ?? 'ADMIN') ?> ]</div>
                    <div class="text-[9px] text-emerald-400 flex items-center gap-1.5 mt-1 font-mono-pm">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sesión Única Activa
                    </div>
                </div>
                <a href="logout.php" title="Cerrar Sesión" class="text-slate-400 hover:text-rose-400 transition-colors p-2 rounded-xl hover:bg-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ÁREA DE CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col min-w-0 relative z-10">
        <!-- TOPBAR SUPERIOR GLASSMORFISTA -->
        <header class="glass-card rounded-none border-x-0 border-t-0 border-b border-white/60 h-16 flex items-center justify-between px-6 sticky top-0 z-20 shadow-xs">
            <div class="flex items-center gap-3">
                <h1 class="font-outfit font-extrabold text-lg text-slate-900"><?= $pageTitle ?? 'Panel de Control' ?></h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="btn-glass-secondary text-xs px-3.5 py-1.5 rounded-xl flex items-center gap-1.5 font-outfit">
                    <span>Ver Portal Público</span>
                    <span>↗</span>
                </a>
                <div class="text-xs font-mono-pm font-bold text-slate-700 bg-white/70 px-3 py-1.5 rounded-xl border border-white/80 shadow-2xs">
                    <?= date('d M, Y') ?>
                </div>
            </div>
        </header>

        <!-- ALERTAS FLASH GLASSMORFISTAS -->
        <?php if ($flash): ?>
            <div class="px-6 pt-4">
                <div class="glass-card-subtle p-4 rounded-2xl text-xs font-mono-pm font-bold border <?= $flash['type'] === 'success' ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-900' : (($flash['type'] === 'warning' || $flash['type'] === 'session_conflict') ? 'bg-amber-500/15 border-amber-500/30 text-amber-900' : 'bg-rose-500/15 border-rose-500/30 text-rose-900') ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        <main class="p-6 md:p-8 flex-1 overflow-y-auto">
