<?php
/**
 * MIRACLE SPA SANCTUARY - Portal Principal de Clientes & Agendamiento
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Models/Category.php';
require_once __DIR__ . '/app/Models/Service.php';
require_once __DIR__ . '/app/Models/Professional.php';
require_once __DIR__ . '/app/Helpers/ViewHelper.php';

$categories = Category::getAll();
$services = Service::getAll(true);
$professionals = Professional::getAll(true);
$spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
$spaAddress = AppConfig::getSetting('spa_address', 'Av. Las Palmas 450, Centro de Bienestar');
$spaPhone = AppConfig::getSetting('spa_phone', '+1 (555) 789-2345');
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($spaName) ?> — Atelier Posmoderno de Bienestar & Agendamiento</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Syne, Space Grotesk, Space Mono, Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <!-- Estilos del Sistema Posmodernista -->
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .font-syne { font-family: 'Syne', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
    </style>
</head>
<body class="bg-[#F7F5EE] text-[#0D1117] min-h-screen flex flex-col antialiased selection:bg-[#D4FF00] selection:text-[#0D1117]">

    <!-- CINTILLO TICKER SUPERIOR POSMODERNISTA (MARQUEE) -->
    <div class="bg-[#0D1117] border-b-2 border-[#0D1117] text-[#D4FF00] text-xs py-2 px-4 font-mono-pm font-bold tracking-wider overflow-hidden">
        <div class="pm-marquee-wrap flex items-center justify-center">
            <div class="pm-marquee-track gap-8 items-center">
                <span class="inline-flex items-center gap-2">✦ ATELIER POSMODERNO DE BIENESTAR</span>
                <span class="text-white">///</span>
                <span class="inline-flex items-center gap-2">AGENDAMIENTO EN TIEMPO REAL</span>
                <span class="text-white">///</span>
                <span class="bg-[#2548E8] text-white px-2 py-0.5 rounded text-[10px] font-bold">[ 100% LIBRE DE DOBLE RESERVA ]</span>
                <span class="text-white">///</span>
                <span class="inline-flex items-center gap-2">UÑAS • CABELLO • COLOR • MASAJES • FACIALES</span>
                <span class="text-white">///</span>
                <span class="bg-[#D4FF00] text-[#0D1117] px-2 py-0.5 rounded text-[10px] font-extrabold">[ PROTOCOLO DE AUTOGESTIÓN TOKENIZADA ]</span>
                <span class="text-white">///</span>
                <span class="inline-flex items-center gap-2">✦ ATELIER POSMODERNO DE BIENESTAR</span>
                <span class="text-white">///</span>
                <span class="inline-flex items-center gap-2">AGENDAMIENTO EN TIEMPO REAL</span>
                <span class="text-white">///</span>
                <span class="bg-[#2548E8] text-white px-2 py-0.5 rounded text-[10px] font-bold">[ 100% LIBRE DE DOBLE RESERVA ]</span>
                <span class="text-white">///</span>
            </div>
        </div>
    </div>

    <!-- NAVBAR POSMODERNISTA -->
    <header class="bg-white/95 backdrop-blur-md border-b-2 border-[#0D1117] sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3.5 group">
                    <span class="w-11 h-11 rounded-xl bg-[#2548E8] text-white flex items-center justify-center font-syne font-extrabold text-2xl border-2 border-[#0D1117] pm-shadow-sm group-hover:rotate-6 group-hover:bg-[#D4FF00] group-hover:text-[#0D1117] transition-all">
                        M
                    </span>
                    <div>
                        <span class="font-syne text-2xl font-extrabold tracking-tight text-[#0D1117] block leading-none">Miracle Spa</span>
                        <span class="font-mono-pm text-[10px] uppercase tracking-widest text-[#2548E8] font-bold block mt-0.5">[ POSTMODERN WELLNESS ATELIER ]</span>
                    </div>
                </a>
            </div>

            <!-- Navegación Central -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117]">
                <a href="#servicios-destacados" class="hover:text-[#2548E8] hover:underline underline-offset-4 transition-all py-1">01 // Servicios</a>
                <a href="#booking-wizard-section" class="hover:text-[#2548E8] hover:underline underline-offset-4 transition-all py-1">02 // Reservar</a>
                <a href="#consultar-cita" class="hover:text-[#2548E8] hover:underline underline-offset-4 transition-all py-1">03 // Mi Cita</a>
                <a href="tests.php" class="bg-[#D4FF00] text-[#0D1117] px-3 py-1 rounded-md border-2 border-[#0D1117] pm-shadow-xs hover:translate-x-0.5 hover:translate-y-0.5 transition-transform flex items-center gap-1 font-bold">
                    <span>🧪</span> Suite Pruebas (28)
                </a>
            </nav>

            <!-- Acceso Admin / Reservar -->
            <div class="flex items-center gap-3">
                <a href="admin/index.php" class="text-xs font-mono-pm font-bold text-[#0D1117] bg-white border-2 border-[#0D1117] px-3.5 py-2 rounded-xl pm-shadow-sm hover:bg-[#F2EFE8] transition-all flex items-center gap-1.5">
                    <span class="text-sm">⚙️</span>
                    <span>Admin Panel</span>
                </a>
                <a href="#booking-wizard-section" class="btn-pm-primary px-4 py-2 rounded-xl text-xs flex items-center gap-2">
                    <span>Agendar Cita</span>
                    <span class="text-[#D4FF00]">→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION POSMODERNISTA -->
    <section class="relative py-16 lg:py-24 overflow-hidden border-b-2 border-[#0D1117] bg-[#EFECE3]">
        <!-- Formas y Marcos Geométricos Posmodernistas -->
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-3xl bg-[#D4FF00]/40 border-2 border-[#0D1117] rotate-6 pointer-events-none hidden lg:block"></div>
        <div class="absolute top-1/3 -left-10 w-32 h-32 rounded-2xl bg-[#2548E8]/20 border-2 border-[#0D1117] -rotate-12 pointer-events-none hidden md:block"></div>
        <div class="absolute bottom-6 right-1/4 w-20 h-20 rounded-xl bg-[#FF2E7E]/20 border-2 border-[#0D1117] rotate-45 pointer-events-none hidden md:block"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <!-- Stickers / Sellos Posmodernistas -->
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="pm-stamp sticker-yellow">✦ ATELIER BOUTIQUE</span>
                    <span class="pm-stamp sticker-blue">💅 NAIL STUDIO</span>
                    <span class="pm-stamp sticker-dark">✂ HAIR & VISAGISMO</span>
                    <span class="pm-stamp sticker-pink">🌿 RITUALES HOLÍSTICOS</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-syne font-extrabold text-[#0D1117] leading-[1.05] mb-6">
                    La Estética que <span class="bg-[#D4FF00] px-3 py-1 border-2 border-[#0D1117] inline-block -rotate-1 pm-shadow-sm">Desafía</span> lo Convencional.
                </h1>

                <p class="text-[#262C36] text-base sm:text-xl leading-relaxed mb-8 max-w-2xl font-normal">
                    Bienestar posmoderno donde la arquitectura, el diseño de autor y la alta cosmética convergen. Agendamiento inteligente en tiempo real con bloqueo atómico contra dobles reservas.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="#booking-wizard-section" class="btn-pm-primary px-8 py-4 rounded-xl text-sm flex items-center gap-3">
                        <span class="font-space font-bold tracking-wide">Configurar Mi Cita</span>
                        <span class="bg-[#D4FF00] text-[#0D1117] w-6 h-6 rounded-md flex items-center justify-center font-bold">→</span>
                    </a>
                    <a href="#consultar-cita" class="btn-pm-acid px-6 py-4 rounded-xl text-sm font-space">
                        Consultar Reserva Activa
                    </a>
                </div>

                <!-- Sellos Métricos Posmodernos -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-10 mt-10 border-t-2 border-[#0D1117]">
                    <div class="bg-white p-4 rounded-xl border-2 border-[#0D1117] pm-shadow-sm">
                        <div class="text-2xl font-mono-pm font-bold text-[#2548E8]">[ 100% ]</div>
                        <div class="text-xs font-bold text-[#0D1117] mt-0.5">Protocolos Médicos de Esterilización</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border-2 border-[#0D1117] pm-shadow-sm">
                        <div class="text-2xl font-mono-pm font-bold text-[#FF2E7E]">[ 15 MIN ]</div>
                        <div class="text-xs font-bold text-[#0D1117] mt-0.5">Buffer Preventivo de Desinfección</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border-2 border-[#0D1117] pm-shadow-sm">
                        <div class="text-2xl font-mono-pm font-bold text-[#0D1117]">[ TOKEN ]</div>
                        <div class="text-xs font-bold text-[#0D1117] mt-0.5">Confirmación Criptográfica & Email</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN WIZARD DE RESERVAS (FLUJO POSMODERNISTA) -->
    <section id="booking-wizard-section" class="py-16 bg-[#F7F5EE] border-b-2 border-[#0D1117]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="pm-stamp sticker-purple mb-2">✦ WORKFLOW EN 5 ETAPAS ✦</span>
                <h2 class="text-3xl sm:text-4xl font-syne font-extrabold text-[#0D1117] mt-2">Personaliza Tu Experiencia</h2>
                <p class="text-gray-600 text-sm mt-2 font-medium">
                    Sin intermediarios ni esperas. Selecciona tratamiento, fecha de visita, bloque horario y especialista.
                </p>
            </div>

            <!-- Indicadores de Pasos Posmodernistas -->
            <div class="max-w-4xl mx-auto mb-10 overflow-x-auto pb-4">
                <div class="flex items-center justify-between min-w-[560px] px-4">
                    <!-- Paso 1 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet active">01</div>
                        <div>
                            <div class="text-xs font-mono-pm font-extrabold text-[#0D1117]">SERVICIO</div>
                            <div class="text-[10px] text-gray-500 font-bold">Tratamiento</div>
                        </div>
                    </div>
                    <div class="w-10 h-0.5 bg-[#0D1117]"></div>

                    <!-- Paso 2 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">02</div>
                        <div>
                            <div class="text-xs font-mono-pm font-extrabold text-[#0D1117]">FECHA</div>
                            <div class="text-[10px] text-gray-500 font-bold">Día de visita</div>
                        </div>
                    </div>
                    <div class="w-10 h-0.5 bg-[#0D1117]"></div>

                    <!-- Paso 3 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">03</div>
                        <div>
                            <div class="text-xs font-mono-pm font-extrabold text-[#0D1117]">HORARIO</div>
                            <div class="text-[10px] text-gray-500 font-bold">Turno libre</div>
                        </div>
                    </div>
                    <div class="w-10 h-0.5 bg-[#0D1117]"></div>

                    <!-- Paso 4 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">04</div>
                        <div>
                            <div class="text-xs font-mono-pm font-extrabold text-[#0D1117]">TERAPEUTA</div>
                            <div class="text-[10px] text-gray-500 font-bold">Especialista</div>
                        </div>
                    </div>
                    <div class="w-10 h-0.5 bg-[#0D1117]"></div>

                    <!-- Paso 5 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">05</div>
                        <div>
                            <div class="text-xs font-mono-pm font-extrabold text-[#0D1117]">CONFIRMAR</div>
                            <div class="text-[10px] text-gray-500 font-bold">Tus datos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid: Contenido del Wizard (Izquierda) + Resumen de Reserva (Derecha) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl mx-auto">
                <!-- Columna Izquierda: Pasos Interactivos (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-8 border-2 border-[#0D1117] pm-shadow-lg">

                    <!-- PASO 1: SELECCIÓN DE SERVICIO -->
                    <div class="wizard-step-content" data-step="1">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#0D1117]">
                            <div>
                                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block">[ ETAPA 01 ]</span>
                                <h3 class="text-xl font-syne font-bold text-[#0D1117]">Selecciona Tu Tratamiento</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Uñas de autor, peluquería vanguardista, balayage, masajes o faciales de alta gama.</p>
                            </div>
                        </div>

                        <!-- Filtro de Categorías Posmodernistas -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button type="button" data-category-filter="todas" class="px-3.5 py-1.5 rounded-lg text-xs font-mono-pm font-bold bg-[#0D1117] text-white border-2 border-[#0D1117] pm-shadow-xs transition-all">
                                Todas las Categorías
                            </button>
                            <?php foreach ($categories as $cat): ?>
                                <button type="button" data-category-filter="<?= htmlspecialchars($cat['slug']) ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-mono-pm font-bold bg-white text-[#0D1117] border-2 border-[#0D1117] pm-shadow-xs hover:bg-[#D4FF00] transition-all">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lista de Servicios Disponibles Cards -->
                        <div class="space-y-3.5 max-h-[500px] overflow-y-auto pr-1">
                            <?php foreach ($services as $srv): ?>
                                <div class="service-card p-4 rounded-xl border-2 border-[#0D1117] bg-white hover:bg-[#F7F5EE] cursor-pointer transition-all pm-shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                                     data-id="<?= $srv['id'] ?>"
                                     data-name="<?= htmlspecialchars($srv['name']) ?>"
                                     data-duration="<?= $srv['duration_minutes'] ?>"
                                     data-price="<?= $srv['price'] ?>"
                                     data-category="<?= htmlspecialchars($srv['category_slug']) ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-mono-pm font-bold uppercase tracking-wider bg-[#D4FF00] text-[#0D1117] border border-[#0D1117] px-2 py-0.5 rounded">
                                                <?= htmlspecialchars($srv['category_name']) ?>
                                            </span>
                                            <span class="text-xs font-mono-pm text-gray-700 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-300">⏱ <?= $srv['duration_minutes'] ?> min</span>
                                        </div>
                                        <h4 class="font-space font-bold text-gray-900 text-sm sm:text-base"><?= htmlspecialchars($srv['name']) ?></h4>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2"><?= htmlspecialchars($srv['description']) ?></p>
                                    </div>
                                    <div class="sm:text-right shrink-0">
                                        <div class="text-lg font-mono-pm font-extrabold text-[#2548E8] bg-white px-3 py-1 rounded-lg border-2 border-[#0D1117] inline-block pm-shadow-xs">
                                            $<?= number_format((float)$srv['price'], 2) ?> USD
                                        </div>
                                        <span class="text-[11px] font-mono-pm text-[#0D1117] font-bold block mt-1">Elegir →</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#0D1117] flex justify-end">
                            <button type="button" id="btn-next-to-date" disabled class="btn-pm-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed font-space">
                                <span>Continuar a Fecha</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 2: SELECCIÓN DE FECHA -->
                    <div class="wizard-step-content hidden" data-step="2">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#0D1117]">
                            <div>
                                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block">[ ETAPA 02 ]</span>
                                <h3 class="text-xl font-syne font-bold text-[#0D1117]">Selecciona la Fecha</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Elige el día en que deseas programar tu tratamiento de spa.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline">Cambiar Servicio</button>
                        </div>

                        <div class="bg-[#EFECE3] p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm max-w-md mx-auto text-center">
                            <label for="booking-date" class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-2">Fecha en el Calendario</label>
                            <input type="date" id="booking-date" class="w-full text-center text-lg font-mono-pm font-bold py-3 px-4 rounded-xl border-2 border-[#0D1117] bg-white focus:ring-2 focus:ring-[#2548E8] focus:outline-none mb-4 pm-shadow-sm">
                            <div class="bg-white p-3 rounded-xl border border-[#0D1117] text-xs text-gray-700 font-medium">
                                🗓 Atención: Lunes a Sábado de 09:00 AM a 08:00 PM.
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#0D1117] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:text-[#2548E8]">
                                ← Volver a Servicio
                            </button>
                            <button type="button" id="btn-next-to-time" class="btn-pm-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 font-space">
                                <span>Ver Horarios Disponibles</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 3: SELECCIÓN DE HORARIO -->
                    <div class="wizard-step-content hidden" data-step="3">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#0D1117]">
                            <div>
                                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block">[ ETAPA 03 ]</span>
                                <h3 class="text-xl font-syne font-bold text-[#0D1117]">Turnos Disponibles</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Disponibilidad en vivo con 15 minutos de desinfección preventiva entre turnos.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline">Cambiar Fecha</button>
                        </div>

                        <!-- Contenedor dinámico de turnos -->
                        <div id="slots-loading" class="text-center py-12 text-gray-400">
                            <div class="w-8 h-8 rounded-full border-4 border-[#0D1117] border-t-[#2548E8] animate-spin mx-auto mb-2"></div>
                            <span class="font-bold text-xs text-gray-600 font-mono-pm">Calculando turnos disponibles en cabinas...</span>
                        </div>

                        <div id="slots-empty" class="hidden text-center py-10 bg-[#D4FF00]/30 border-2 border-[#0D1117] rounded-2xl text-[#0D1117] text-sm pm-shadow-sm">
                            <p class="font-syne font-extrabold text-base">No hay turnos libres para esta fecha.</p>
                            <p class="text-xs mt-1 text-gray-600 font-medium">Por favor selecciona otro día en el calendario.</p>
                            <button type="button" onclick="goToWizardStep(2)" class="mt-3 text-xs font-mono-pm font-extrabold bg-[#0D1117] text-[#D4FF00] px-4 py-2 rounded-xl pm-shadow-xs">Cambiar fecha</button>
                        </div>

                        <div id="slots-container"></div>

                        <div class="mt-8 pt-4 border-t-2 border-[#0D1117] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:text-[#2548E8]">
                                ← Volver a Fecha
                            </button>
                            <button type="button" id="btn-next-to-prof" disabled class="btn-pm-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed font-space">
                                <span>Continuar a Especialista</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 4: SELECCIÓN DE PROFESIONAL -->
                    <div class="wizard-step-content hidden" data-step="4">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#0D1117]">
                            <div>
                                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block">[ ETAPA 04 ]</span>
                                <h3 class="text-xl font-syne font-bold text-[#0D1117]">Selección de Terapeuta</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Elige a tu especialista de preferencia o utiliza la asignación inteligente con mayor disponibilidad.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline">Cambiar Horario</button>
                        </div>

                        <div id="professionals-container" class="space-y-3.5">
                            <!-- Inyectado vía JavaScript -->
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#0D1117] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:text-[#2548E8]">
                                ← Volver a Horario
                            </button>
                            <button type="button" id="btn-next-to-form" class="btn-pm-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 font-space">
                                <span>Completar Mis Datos</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 5: DATOS DEL CLIENTE Y CONFIRMACIÓN -->
                    <div class="wizard-step-content hidden" data-step="5">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#0D1117]">
                            <div>
                                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block">[ ETAPA 05 ]</span>
                                <h3 class="text-xl font-syne font-bold text-[#0D1117]">Tus Datos de Contacto</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Emitiremos tu comprobante oficial con código único y token de autogestión por correo.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(4)" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline">Cambiar Especialista</button>
                        </div>

                        <form id="booking-form" class="space-y-4">
                            <div>
                                <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Nombre Completo *</label>
                                <input type="text" id="client-name" required placeholder="Ej. Lucía Fernández" class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs font-medium">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Correo Electrónico *</label>
                                    <input type="email" id="client-email" required placeholder="tu-correo@ejemplo.com" class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs font-medium">
                                    <span class="text-[11px] text-gray-500 font-medium block mt-1">Recibirás aquí tu comprobante para reprogramar o cancelar.</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Teléfono Móvil (WhatsApp) *</label>
                                    <input type="tel" id="client-phone" required placeholder="+1 555 123 4567" class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117] mb-1">Notas o Preferencias Especiales</label>
                                <textarea id="client-notes" rows="2" placeholder="Sensibilidad a productos, alergias, diseño especial..." class="w-full px-4 py-3 rounded-xl border-2 border-[#0D1117] text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs font-medium"></textarea>
                            </div>

                            <div class="bg-[#EFECE3] border-2 border-[#0D1117] rounded-xl p-4 text-xs text-[#0D1117] font-semibold pm-shadow-xs">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" required checked class="mt-0.5 rounded text-[#2548E8] focus:ring-[#2548E8]">
                                    <span>Acepto las políticas de Miracle Spa (reprogramación y cancelación sin costo con al menos 4 horas de anticipación).</span>
                                </label>
                            </div>

                            <div class="mt-8 pt-4 border-t-2 border-[#0D1117] flex items-center justify-between">
                                <button type="button" onclick="goToWizardStep(4)" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:text-[#2548E8]">
                                    ← Volver a Terapeuta
                                </button>
                                <button type="submit" id="btn-confirm-booking" class="btn-pm-primary px-8 py-3.5 rounded-xl text-sm flex items-center gap-2 font-space">
                                    <span>Confirmar Mi Reserva Ahora</span>
                                    <span>✓</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PASO 6: PANTALLA DE ÉXITO -->
                    <div class="wizard-step-content hidden" data-step="6">
                        <div class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-[#D4FF00] text-[#0D1117] text-3xl font-extrabold flex items-center justify-center mx-auto mb-4 border-2 border-[#0D1117] pm-shadow">
                                ✓
                            </div>
                            <span class="pm-stamp sticker-teal">¡CITA CONFIRMADA CON ÉXITO!</span>
                            <h3 class="text-2xl sm:text-3xl font-syne font-extrabold text-[#0D1117] mt-3 mb-2">Tu Espacio de Bienestar está Asegurado</h3>
                            <p class="text-gray-600 text-sm max-w-md mx-auto font-medium">
                                Hemos bloqueado tu horario en la agenda del spa y despachado el comprobante oficial por correo electrónico.
                            </p>
                        </div>

                        <!-- Tarjeta de comprobante Posmodernista -->
                        <div class="bg-[#EFECE3] border-2 border-[#0D1117] rounded-2xl p-6 pm-shadow max-w-lg mx-auto mb-8">
                            <div class="flex items-center justify-between border-b-2 border-[#0D1117] pb-4 mb-4">
                                <span class="text-xs font-mono-pm uppercase font-bold text-gray-700">Código de Reserva</span>
                                <span id="confirmed-code" class="font-mono-pm font-extrabold text-lg text-[#0D1117] bg-white px-3 py-1 rounded-xl border-2 border-[#0D1117] pm-shadow-xs">-</span>
                            </div>

                            <div class="space-y-3 text-xs font-medium">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Servicio:</span>
                                    <span id="confirmed-service" class="font-bold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Especialista:</span>
                                    <span id="confirmed-professional" class="font-bold text-[#2548E8]">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fecha:</span>
                                    <span id="confirmed-date" class="font-bold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Horario:</span>
                                    <span id="confirmed-time" class="font-bold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Inversión:</span>
                                    <span id="confirmed-price" class="font-mono-pm font-extrabold text-[#2548E8] text-base">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Correo Notificado:</span>
                                    <span id="confirmed-email" class="font-mono-pm font-bold text-gray-800 text-xs">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones posteriores -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a id="confirmed-manage-link" href="#" class="btn-pm-primary px-6 py-3 rounded-xl text-xs text-center font-space">
                                Ver y Gestionar Mi Cita (Cancelar / Reprogramar)
                            </a>
                            <button type="button" onclick="location.reload()" class="btn-pm-acid px-5 py-3 rounded-xl text-xs font-space">
                                Agendar Otra Cita
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Tarjeta Resumen Sticky Posmoderna (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl p-6 border-2 border-[#0D1117] pm-shadow-lg sticky top-28">
                        <div class="flex items-center justify-between pb-4 border-b-2 border-[#0D1117] mb-4">
                            <h4 class="font-syne font-bold text-base text-[#0D1117]">Resumen de Cita</h4>
                            <span class="pm-stamp sticker-yellow text-[10px]">[ EN VIVO ]</span>
                        </div>

                        <div class="space-y-4 text-xs font-medium">
                            <div>
                                <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Servicio Seleccionado:</span>
                                <div id="summary-service-name" class="font-space font-bold text-sm text-gray-900">Ninguno seleccionado</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span id="summary-service-duration" class="text-gray-600 font-mono-pm font-bold">-</span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span id="summary-service-price" class="font-mono-pm font-bold text-[#2548E8] text-sm">-</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#0D1117]/10">
                                <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Fecha:</span>
                                <div id="summary-date" class="font-bold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#0D1117]/10">
                                <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Horario del Turno:</span>
                                <div id="summary-time" class="font-bold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#0D1117]/10">
                                <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Especialista Asignado:</span>
                                <div id="summary-professional" class="font-bold text-[#2548E8]">Cualquier especialista disponible</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#0D1117]/10">
                                <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Sede:</span>
                                <div class="text-gray-700"><?= htmlspecialchars($spaAddress) ?></div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t-2 border-[#0D1117] bg-[#EFECE3] -mx-6 -mb-6 p-6 rounded-b-2xl">
                            <div class="text-[11px] text-[#0D1117] font-bold flex items-start gap-2">
                                <span class="text-base">🛡️</span>
                                <span>Bloqueo atómico anti-doble reserva con token criptográfico de autogestión.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN: CONSULTAR O GESTIONAR CITA EXISTENTE -->
    <section id="consultar-cita" class="py-16 bg-[#EFECE3] border-b-2 border-[#0D1117]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="pm-stamp sticker-blue mb-2">✦ AUTOGESTIÓN DE CLIENTES ✦</span>
            <h2 class="text-3xl font-syne font-extrabold text-[#0D1117] mt-2 mb-3">¿Ya tienes una cita agendada?</h2>
            <p class="text-gray-700 text-sm max-w-xl mx-auto mb-8 font-medium">
                Revisa los detalles de tu turno, reprograma hacia una nueva fecha disponible o cancela utilizando tu código oficial de reserva.
            </p>

            <form action="gestionar-cita.php" method="GET" class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <input type="text" name="code" required placeholder="Ej. MRC-2026-1007" class="w-full sm:w-2/3 px-4 py-3 rounded-xl border-2 border-[#0D1117] bg-white text-sm font-mono-pm uppercase font-bold tracking-wider text-center sm:text-left pm-shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2548E8]">
                <button type="submit" class="w-full sm:w-auto btn-pm-primary px-6 py-3 rounded-xl text-xs font-space">
                    Consultar Cita
                </button>
            </form>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS (VITRINA POSMODERNISTA) -->
    <section id="servicios-destacados" class="py-20 bg-white border-b-2 border-[#0D1117]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="pm-stamp sticker-yellow mb-2">✦ CARTA DE AUTOR ✦</span>
                <h2 class="text-3xl sm:text-4xl font-syne font-extrabold text-[#0D1117] mt-2">Tratamientos Destacados</h2>
                <p class="text-gray-600 text-sm mt-2 font-medium">
                    Servicios botánicos con productos orgánicos de origen europeo y cabinas privadas climatizadas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Uñas -->
                <div class="bg-[#F7F5EE] p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#2548E8] text-white flex items-center justify-center text-xl mb-4 border-2 border-[#0D1117] pm-shadow-xs font-bold">
                            💅
                        </div>
                        <span class="font-mono-pm text-[10px] text-[#2548E8] font-bold block mb-1">[ LAB DE UÑAS ]</span>
                        <h3 class="font-syne font-bold text-lg text-[#0D1117] mb-2">Uñas & Manicura de Lujo</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Esmaltado semipermanente de alta duración, esculpidas y nutrición botánica con parafina.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline flex items-center gap-1">
                        Reservar Manicura →
                    </a>
                </div>

                <!-- Cabello -->
                <div class="bg-[#F7F5EE] p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#0D1117] text-[#D4FF00] flex items-center justify-center text-xl mb-4 border-2 border-[#0D1117] pm-shadow-xs font-bold">
                            ✂️
                        </div>
                        <span class="font-mono-pm text-[10px] text-[#0D1117] font-bold block mb-1">[ VISAGISMO ]</span>
                        <h3 class="font-syne font-bold text-lg text-[#0D1117] mb-2">Corte & Peluquería</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Cortes arquitectónicos de diseño para damas y caballeros con lavado revitalizante.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:underline flex items-center gap-1">
                        Reservar Corte →
                    </a>
                </div>

                <!-- Pintura & Color -->
                <div class="bg-[#F7F5EE] p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#D4FF00] text-[#0D1117] flex items-center justify-center text-xl mb-4 border-2 border-[#0D1117] pm-shadow-xs font-bold">
                            🎨
                        </div>
                        <span class="font-mono-pm text-[10px] text-[#2548E8] font-bold block mb-1">[ COLORIMETRÍA ]</span>
                        <h3 class="font-syne font-bold text-lg text-[#0D1117] mb-2">Pintura & Balayage</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Coloración sin amoníaco, mechas babylights y balayage con degradados luminosos de autor.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-mono-pm font-bold text-[#2548E8] hover:underline flex items-center gap-1">
                        Reservar Color →
                    </a>
                </div>

                <!-- Masajes -->
                <div class="bg-[#F7F5EE] p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#00E5A3] text-[#0D1117] flex items-center justify-center text-xl mb-4 border-2 border-[#0D1117] pm-shadow-xs font-bold">
                            🌿
                        </div>
                        <span class="font-mono-pm text-[10px] text-[#0D1117] font-bold block mb-1">[ HOLÍSTICA ]</span>
                        <h3 class="font-syne font-bold text-lg text-[#0D1117] mb-2">Masajes & Bienestar</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Piedras volcánicas tibias, liberación descontracturante profunda y aromaterapia de bosque.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-mono-pm font-bold text-[#0D1117] hover:underline flex items-center gap-1">
                        Reservar Masaje →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER POSMODERNISTA -->
    <footer class="bg-[#0D1117] text-white mt-auto border-t-4 border-[#D4FF00]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-10 rounded-xl bg-[#2548E8] text-white flex items-center justify-center font-syne font-bold text-lg border-2 border-white pm-shadow-xs">
                            M
                        </span>
                        <span class="font-syne text-2xl font-bold tracking-tight text-white"><?= htmlspecialchars($spaName) ?></span>
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed max-w-sm mb-6 font-medium">
                        Atelier posmoderno de bienestar y belleza holística. Plataforma web modular con arquitectura limpia y soporte para despliegue ágil en entornos XAMPP y servidores Cloud.
                    </p>
                    <div class="text-xs text-gray-400 space-y-1 font-mono-pm">
                        <div>📍 <?= htmlspecialchars($spaAddress) ?></div>
                        <div>📞 <?= htmlspecialchars($spaPhone) ?></div>
                        <div>✉️ citas@miraclespa.com</div>
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-mono-pm font-bold uppercase tracking-widest text-[#D4FF00] mb-4">[ PLATAFORMA ]</h5>
                    <ul class="text-xs space-y-2.5 text-gray-300 font-mono-pm">
                        <li><a href="#booking-wizard-section" class="hover:text-[#D4FF00] transition-colors">Agendamiento de Citas</a></li>
                        <li><a href="#consultar-cita" class="hover:text-[#D4FF00] transition-colors">Reprogramar o Cancelar</a></li>
                        <li><a href="tests.php" class="hover:text-[#D4FF00] transition-colors text-[#D4FF00]">🧪 Suite Automatizada (28)</a></li>
                        <li><a href="install.php" class="hover:text-[#D4FF00] transition-colors">Instalador Base de Datos</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-xs font-mono-pm font-bold uppercase tracking-widest text-[#2548E8] mb-4">[ ADMINISTRACIÓN ]</h5>
                    <ul class="text-xs space-y-2.5 text-gray-300 font-mono-pm">
                        <li><a href="admin/login.php" class="hover:text-[#D4FF00] transition-colors">Iniciar Sesión Admin</a></li>
                        <li><a href="admin/index.php" class="hover:text-[#D4FF00] transition-colors">Dashboard de Demanda</a></li>
                        <li><a href="admin/calendario.php" class="hover:text-[#D4FF00] transition-colors">Calendario Global</a></li>
                        <li><a href="admin/correos.php" class="hover:text-[#D4FF00] transition-colors">Bandeja de Correos (Simulador)</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-10 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-400 font-mono-pm">
                <div>© <?= date('Y') ?> <?= htmlspecialchars($spaName) ?> &bull; Edición Posmodernista</div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-[#2548E8] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-md bg-[#D4FF00] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-md bg-[#FF2E7E] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-md bg-[#00E5A3] inline-block border border-white"></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script del Wizard -->
    <script src="assets/js/booking.js"></script>
</body>
</html>
