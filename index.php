<?php
/**
 * MIRACLE SPA SANCTUARY - Portal Principal de Clientes & Agendamiento
 * Estilo Gráfico: Glassmorphism / Glassmorfismo & Aurora Luxury Sanctuary
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
    <title><?= htmlspecialchars($spaName) ?> — Santuario de Bienestar & Agendamiento Glassmorphism</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Outfit, Plus Jakarta Sans, Space Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- Estilos del Sistema Glassmorphism -->
    <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime(__DIR__ . '/assets/css/app.css') ?>">
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-teal-500/20 selection:text-teal-900 relative overflow-x-hidden">

    <!-- Orbes de Luz Ambiental para el Efecto Glassmorfismo -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-40 -right-20"></div>
    <div class="aurora-orb aurora-orb-rose w-96 h-96 top-[55rem] left-1/4"></div>
    <div class="aurora-orb aurora-orb-teal w-[30rem] h-[30rem] top-[90rem] -right-10"></div>

    <!-- CINTILLO TICKER TRASLÚCIDO (GLASS MARQUEE) -->
    <div class="bg-white/40 backdrop-blur-md border-b border-white/60 text-slate-800 text-xs py-2 px-4 font-medium tracking-wide overflow-hidden relative z-30">
        <div class="pm-marquee-wrap flex items-center justify-center">
            <div class="pm-marquee-track gap-8 items-center text-[11px]">
                <span class="inline-flex items-center gap-2 font-bold text-teal-800">✨ SANTUARIO DE BIENESTAR INTEGRAL</span>
                <span class="text-slate-400">•</span>
                <span class="inline-flex items-center gap-1.5 font-medium">AGENDAMIENTO INTELIGENTE EN TIEMPO REAL</span>
                <span class="text-slate-400">•</span>
                <span class="bg-teal-600/15 text-teal-800 px-2.5 py-0.5 rounded-full border border-teal-500/30 font-bold">100% LIBRE DE DOBLE RESERVA</span>
                <span class="text-slate-400">•</span>
                <span>UÑAS • CABELLO • COLOR • MASAJES • FACIALES</span>
                <span class="text-slate-400">•</span>
                <span class="bg-purple-600/15 text-purple-800 px-2.5 py-0.5 rounded-full border border-purple-500/30 font-bold">AUTOGESTIÓN SEGURA POR TOKEN</span>
                <span class="text-slate-400">•</span>
                <span class="inline-flex items-center gap-2 font-bold text-teal-800">✨ SANTUARIO DE BIENESTAR INTEGRAL</span>
                <span class="text-slate-400">•</span>
                <span class="inline-flex items-center gap-1.5 font-medium">AGENDAMIENTO INTELIGENTE EN TIEMPO REAL</span>
                <span class="text-slate-400">•</span>
                <span class="bg-teal-600/15 text-teal-800 px-2.5 py-0.5 rounded-full border border-teal-500/30 font-bold">100% LIBRE DE DOBLE RESERVA</span>
                <span class="text-slate-400">•</span>
            </div>
        </div>
    </div>

    <!-- NAVBAR GLASSMORFISTA FLOTANTE -->
    <header class="bg-white/55 backdrop-blur-xl border-b border-white/70 sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3.5 group">
                    <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center font-outfit font-extrabold text-2xl border border-white/50 shadow-[0_8px_20px_rgba(13,148,136,0.3)] group-hover:scale-105 transition-all">
                        M
                    </span>
                    <div>
                        <span class="font-outfit text-2xl font-extrabold tracking-tight text-slate-900 block leading-none">Miracle Spa</span>
                        <span class="text-[10px] uppercase tracking-widest text-teal-700 font-bold block mt-0.5">Sanctuary &bull; Beauty Lab</span>
                    </div>
                </a>
            </div>

            <!-- Navegación Central Cristalina -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-outfit font-semibold uppercase tracking-wider text-slate-700">
                <a href="#servicios-destacados" class="hover:text-teal-700 hover:scale-105 transition-all py-1">Servicios</a>
                <a href="#booking-wizard-section" class="hover:text-teal-700 hover:scale-105 transition-all py-1">Reservar Cita</a>
                <a href="#consultar-cita" class="hover:text-teal-700 hover:scale-105 transition-all py-1">Mi Cita</a>
                <a href="tests.php" class="bg-white/70 hover:bg-white text-slate-800 px-3 py-1.5 rounded-full border border-white/90 shadow-sm transition-all flex items-center gap-1.5 font-bold">
                    <span>🧪</span> Pruebas (48)
                </a>
            </nav>

            <!-- Acceso Admin / Reservar -->
            <div class="flex items-center gap-3">
                <a href="admin/index.php" class="text-xs font-outfit font-bold text-slate-700 bg-white/60 hover:bg-white/90 border border-white/80 px-3.5 py-2 rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                    <span class="text-sm">⚙️</span>
                    <span>Admin Panel</span>
                </a>
                <a href="#booking-wizard-section" class="btn-glass-primary px-4 py-2.5 rounded-xl text-xs flex items-center gap-2">
                    <span>Agendar Ahora</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION GLASSMORFISMO -->
    <section class="relative py-16 lg:py-24 overflow-hidden z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <!-- Badges Glassmorfistas Flotantes -->
                <div class="flex flex-wrap items-center gap-2.5 mb-6">
                    <span class="glass-badge glass-badge-teal">🌸 SPA SANCTUARY</span>
                    <span class="glass-badge glass-badge-blue">💅 NAIL STUDIO</span>
                    <span class="glass-badge glass-badge-purple">✂ HAIR & VISAGISMO</span>
                    <span class="glass-badge glass-badge-rose">🌿 RITUALES BOTÁNICOS</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-outfit font-extrabold text-slate-900 leading-[1.1] mb-6">
                    El Ritual de Belleza que <span class="bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">Armoniza</span> Tu Energía.
                </h1>

                <p class="text-slate-600 text-base sm:text-lg leading-relaxed mb-8 max-w-2xl font-normal">
                    Vive una experiencia multisensorial en cabinas privadas y climatizadas. Reserva en tiempo real tu servicio, fecha y especialista sin esperas ni fricciones.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="#booking-wizard-section" class="btn-glass-primary px-8 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-[0_12px_28px_rgba(13,148,136,0.35)]">
                        <span class="font-outfit font-bold">Empezar Mi Reserva</span>
                        <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold">→</span>
                    </a>
                    <a href="#consultar-cita" class="btn-glass-secondary px-6 py-4 rounded-2xl text-sm font-outfit">
                        Consultar Mi Cita Existente
                    </a>
                </div>

                <!-- Tarjetas Métricas en Cristal Esmerilado -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-10 mt-10 border-t border-white/60">
                    <div class="glass-card p-5 border border-white/80 shadow-[0_8px_30px_rgba(31,38,135,0.06)]">
                        <div class="text-2xl font-outfit font-bold text-teal-700">100%</div>
                        <div class="text-xs font-semibold text-slate-700 mt-1">Esterilización Quirúrgica de Instrumental</div>
                    </div>
                    <div class="glass-card p-5 border border-white/80 shadow-[0_8px_30px_rgba(31,38,135,0.06)]">
                        <div class="text-2xl font-outfit font-bold text-sky-700">15 min</div>
                        <div class="text-xs font-semibold text-slate-700 mt-1">Buffer de Higiene y Amortiguamiento</div>
                    </div>
                    <div class="glass-card p-5 border border-white/80 shadow-[0_8px_30px_rgba(31,38,135,0.06)]">
                        <div class="text-2xl font-outfit font-bold text-purple-700">Directo</div>
                        <div class="text-xs font-semibold text-slate-700 mt-1">Confirmación Tokenizada por Correo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN WIZARD DE RESERVAS (FLUJO GLASSMORFISTA) -->
    <section id="booking-wizard-section" class="py-16 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="glass-badge glass-badge-purple mb-2">✦ WORKFLOW FLUIDO EN 5 PASOS ✦</span>
                <h2 class="text-3xl sm:text-4xl font-outfit font-extrabold text-slate-900 mt-2">Personaliza Tu Experiencia Spa</h2>
                <p class="text-slate-600 text-sm mt-2 font-medium">
                    Sin intermediarios ni demoras. Selecciona tu tratamiento, día de visita, horario libre y especialista.
                </p>
            </div>

            <!-- Indicadores de Pasos Glassmorfistas -->
            <div class="max-w-4xl mx-auto mb-10 overflow-x-auto pb-4">
                <div class="flex items-center justify-between min-w-[560px] px-4">
                    <!-- Paso 1 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet active">1</div>
                        <div>
                            <div class="text-xs font-outfit font-bold text-slate-900">Servicio</div>
                            <div class="text-[10px] text-slate-500 font-medium">Tratamiento</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-300/60"></div>

                    <!-- Paso 2 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">2</div>
                        <div>
                            <div class="text-xs font-outfit font-bold text-slate-900">Fecha</div>
                            <div class="text-[10px] text-slate-500 font-medium">Día de visita</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-300/60"></div>

                    <!-- Paso 3 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">3</div>
                        <div>
                            <div class="text-xs font-outfit font-bold text-slate-900">Horario</div>
                            <div class="text-[10px] text-slate-500 font-medium">Turno libre</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-300/60"></div>

                    <!-- Paso 4 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">4</div>
                        <div>
                            <div class="text-xs font-outfit font-bold text-slate-900">Especialista</div>
                            <div class="text-[10px] text-slate-500 font-medium">Terapeuta</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-300/60"></div>

                    <!-- Paso 5 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">5</div>
                        <div>
                            <div class="text-xs font-outfit font-bold text-slate-900">Confirmación</div>
                            <div class="text-[10px] text-slate-500 font-medium">Tus datos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid: Contenido del Wizard (Izquierda) + Resumen de Reserva (Derecha) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl mx-auto">
                <!-- Columna Izquierda: Pasos Interactivos (8 cols) -->
                <div class="lg:col-span-8 glass-card p-6 sm:p-8">

                    <!-- PASO 1: SELECCIÓN DE SERVICIO -->
                    <div class="wizard-step-content" data-step="1">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                            <div>
                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block font-outfit">Paso 01</span>
                                <h3 class="text-xl font-outfit font-bold text-slate-900">Selecciona Tu Tratamiento</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Uñas de autor, peluquería vanguardista, balayage, masajes o faciales de lujo.</p>
                            </div>
                        </div>

                        <!-- Filtro de Categorías Glass Pills -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button type="button" data-category-filter="todas" class="px-4 py-2 rounded-full text-xs font-outfit font-bold bg-slate-900 text-white shadow-sm transition-all">
                                Todas las Categorías
                            </button>
                            <?php foreach ($categories as $cat): ?>
                                <button type="button" data-category-filter="<?= htmlspecialchars($cat['slug']) ?>" class="px-4 py-2 rounded-full text-xs font-outfit font-semibold bg-white/60 hover:bg-white text-slate-700 border border-white/80 shadow-xs transition-all">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lista de Servicios Disponibles Glass Cards -->
                        <div class="space-y-3.5 max-h-[500px] overflow-y-auto pr-1">
                            <?php foreach ($services as $srv): ?>
                                <div class="service-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 cursor-pointer"
                                     data-id="<?= $srv['id'] ?>"
                                     data-name="<?= htmlspecialchars($srv['name']) ?>"
                                     data-duration="<?= $srv['duration_minutes'] ?>"
                                     data-price="<?= $srv['price'] ?>"
                                     data-category="<?= htmlspecialchars($srv['category_slug']) ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-outfit font-bold uppercase tracking-wider bg-teal-500/15 text-teal-800 border border-teal-500/30 px-2.5 py-0.5 rounded-full">
                                                <?= htmlspecialchars($srv['category_name']) ?>
                                            </span>
                                            <span class="text-xs text-slate-600 font-semibold bg-white/70 px-2.5 py-0.5 rounded-full border border-white/90">⏱ <?= $srv['duration_minutes'] ?> min</span>
                                        </div>
                                        <h4 class="font-outfit font-bold text-slate-900 text-base"><?= htmlspecialchars($srv['name']) ?></h4>
                                        <p class="text-xs text-slate-600 mt-1 line-clamp-2"><?= htmlspecialchars($srv['description']) ?></p>
                                    </div>
                                    <div class="sm:text-right shrink-0">
                                        <div class="text-xl font-outfit font-extrabold text-teal-800 bg-white/80 px-3.5 py-1 rounded-xl border border-white shadow-xs inline-block">
                                            $<?= number_format((float)$srv['price'], 2) ?> USD
                                        </div>
                                        <span class="text-[11px] font-outfit text-teal-600 font-bold block mt-1">Elegir →</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-8 pt-4 border-t border-slate-200/60 flex justify-end">
                            <button type="button" id="btn-next-to-date" disabled class="btn-glass-primary px-6 py-3 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed font-outfit">
                                <span>Continuar a Fecha</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 2: SELECCIÓN DE FECHA -->
                    <div class="wizard-step-content hidden" data-step="2">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                            <div>
                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block font-outfit">Paso 02</span>
                                <h3 class="text-xl font-outfit font-bold text-slate-900">Selecciona la Fecha</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Elige el día en que deseas programar tu tratamiento de spa.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-outfit font-bold text-teal-700 hover:underline">Cambiar Servicio</button>
                        </div>

                        <div class="glass-card-subtle p-6 max-w-md mx-auto text-center">
                            <label for="booking-date" class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-2">Fecha en el Calendario</label>
                            <input type="date" id="booking-date" class="w-full text-center text-lg font-outfit font-bold py-3 px-4 rounded-xl mb-4">
                            <div class="bg-white/60 p-3 rounded-xl border border-white/80 text-xs text-slate-600 font-medium">
                                🗓 Horario de atención: Lunes a Sábado de 09:00 AM a 08:00 PM.
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-outfit font-bold text-slate-700 hover:text-teal-700">
                                ← Volver a Servicio
                            </button>
                            <button type="button" id="btn-next-to-time" class="btn-glass-primary px-6 py-3 rounded-xl text-xs flex items-center gap-2 font-outfit">
                                <span>Ver Horarios Disponibles</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 3: SELECCIÓN DE HORARIO -->
                    <div class="wizard-step-content hidden" data-step="3">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                            <div>
                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block font-outfit">Paso 03</span>
                                <h3 class="text-xl font-outfit font-bold text-slate-900">Turnos Disponibles</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Disponibilidad en tiempo real con 15 minutos de desinfección preventiva entre turnos.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-outfit font-bold text-teal-700 hover:underline">Cambiar Fecha</button>
                        </div>

                        <!-- Contenedor dinámico de turnos -->
                        <div id="slots-loading" class="text-center py-12 text-slate-400">
                            <div class="w-8 h-8 rounded-full border-3 border-teal-200 border-t-teal-600 animate-spin mx-auto mb-2"></div>
                            <span class="font-bold text-xs text-slate-600 font-outfit">Calculando turnos disponibles en cabinas...</span>
                        </div>

                        <div id="slots-empty" class="hidden text-center py-10 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-slate-800 text-sm backdrop-blur-md">
                            <p class="font-outfit font-bold text-base text-amber-900">No hay turnos libres para esta fecha.</p>
                            <p class="text-xs mt-1 text-slate-600 font-medium">Por favor selecciona otro día en el calendario.</p>
                            <button type="button" onclick="goToWizardStep(2)" class="mt-3 text-xs font-outfit font-bold bg-slate-900 text-white px-4 py-2 rounded-xl">Cambiar fecha</button>
                        </div>

                        <div id="slots-container"></div>

                        <div class="mt-8 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-outfit font-bold text-slate-700 hover:text-teal-700">
                                ← Volver a Fecha
                            </button>
                            <button type="button" id="btn-next-to-prof" disabled class="btn-glass-primary px-6 py-3 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed font-outfit">
                                <span>Continuar a Especialista</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 4: SELECCIÓN DE PROFESIONAL -->
                    <div class="wizard-step-content hidden" data-step="4">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                            <div>
                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block font-outfit">Paso 04</span>
                                <h3 class="text-xl font-outfit font-bold text-slate-900">Selección de Terapeuta</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Elige a tu terapeuta de preferencia o utiliza la asignación inteligente con mayor disponibilidad.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-outfit font-bold text-teal-700 hover:underline">Cambiar Horario</button>
                        </div>

                        <div id="professionals-container" class="space-y-3.5">
                            <!-- Inyectado vía JavaScript con estilo Glassmorfismo -->
                        </div>

                        <div class="mt-8 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-outfit font-bold text-slate-700 hover:text-teal-700">
                                ← Volver a Horario
                            </button>
                            <button type="button" id="btn-next-to-form" class="btn-glass-primary px-6 py-3 rounded-xl text-xs flex items-center gap-2 font-outfit">
                                <span>Completar Mis Datos</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 5: DATOS DEL CLIENTE Y CONFIRMACIÓN -->
                    <div class="wizard-step-content hidden" data-step="5">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200/60">
                            <div>
                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block font-outfit">Paso 05</span>
                                <h3 class="text-xl font-outfit font-bold text-slate-900">Tus Datos de Contacto</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Emitiremos tu comprobante oficial con código único y token de autogestión por correo.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(4)" class="text-xs font-outfit font-bold text-teal-700 hover:underline">Cambiar Especialista</button>
                        </div>

                        <form id="booking-form" class="space-y-4">
                            <div>
                                <label class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-1">Nombre Completo *</label>
                                <input type="text" id="client-name" required placeholder="Ej. Lucía Fernández" class="w-full px-4 py-3 rounded-xl text-sm font-medium">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-1">Correo Electrónico *</label>
                                    <input type="email" id="client-email" required placeholder="tu-correo@ejemplo.com" class="w-full px-4 py-3 rounded-xl text-sm font-medium">
                                    <span class="text-[11px] text-slate-500 font-medium block mt-1">Recibirás aquí tu comprobante para reprogramar o cancelar.</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-1">Teléfono Móvil (WhatsApp) *</label>
                                    <input type="tel" id="client-phone" required placeholder="+1 555 123 4567" class="w-full px-4 py-3 rounded-xl text-sm font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-1">Notas o Preferencias Especiales</label>
                                <textarea id="client-notes" rows="2" placeholder="Sensibilidad a productos, alergias, diseño especial..." class="w-full px-4 py-3 rounded-xl text-sm font-medium"></textarea>
                            </div>

                            <!-- SELECCIÓN DE MÉTODO DE PAGO -->
                            <div class="pt-2">
                                <label class="block text-xs font-outfit font-bold uppercase tracking-wider text-slate-700 mb-2">Modalidad de Pago *</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label id="payment-choice-stripe-label" class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 border-teal-500 bg-teal-50/70 shadow-xs cursor-pointer transition-all">
                                        <input type="radio" name="payment_choice" value="stripe" checked class="mt-1 text-teal-600 focus:ring-teal-500">
                                        <div>
                                            <span class="block font-outfit font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                                <span>💳</span> Pagar en Línea con Stripe
                                            </span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Tarjeta de crédito o débito con confirmación instantánea.</span>
                                        </div>
                                    </label>

                                    <label id="payment-choice-venue-label" class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 border-white/80 bg-white/60 shadow-xs cursor-pointer transition-all hover:bg-white/80">
                                        <input type="radio" name="payment_choice" value="venue" class="mt-1 text-teal-600 focus:ring-teal-500">
                                        <div>
                                            <span class="block font-outfit font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                                <span>🏢</span> Pagar en Sede del Spa
                                            </span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Abona de forma presencial (efectivo o tarjeta) el día del turno.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- CAMPOS DE TARJETA PARA PAGO EN LÍNEA -->
                            <div id="stripe-card-fields-container" class="glass-subtle-box p-4 rounded-2xl space-y-3 border border-white/80">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200/50">
                                    <span class="text-xs font-outfit font-bold text-slate-800 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs">🔒</span>
                                        Datos de Tarjeta (Stripe 256-bit SSL)
                                    </span>
                                    <span id="booking-card-brand" class="font-mono-pm text-[10px] text-teal-700 font-bold uppercase bg-white/80 px-2 py-0.5 rounded-md border border-white">Stripe Segura</span>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-mono-pm font-bold uppercase text-slate-700 mb-1">Nombre del Titular *</label>
                                    <input type="text" id="booking-cardholder" placeholder="Nombre completo como en el plástico" class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-mono-pm font-bold uppercase text-slate-700 mb-1">Número de Tarjeta *</label>
                                    <div class="relative">
                                        <input type="text" id="booking-card-number" maxlength="19" placeholder="4242 •••• •••• 4242" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-white/80 bg-white/80 font-mono-pm text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                        <span class="absolute right-3 top-2.5 text-slate-400">💳</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-mono-pm font-bold uppercase text-slate-700 mb-1">Expiración (MM/AA) *</label>
                                        <input type="text" id="booking-card-exp" maxlength="5" placeholder="MM/AA" class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 font-mono-pm text-xs text-center focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-mono-pm font-bold uppercase text-slate-700 mb-1">CVC / CVV *</label>
                                        <input type="password" id="booking-card-cvc" maxlength="4" placeholder="123" class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 font-mono-pm text-xs text-center focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            <div class="glass-card-subtle p-4 text-xs text-slate-700 font-medium">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" required checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                                    <span>Acepto las políticas de Miracle Spa (reprogramación y cancelación sin costo con al menos 4 horas de anticipación).</span>
                                </label>
                            </div>

                            <div class="mt-8 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                                <button type="button" onclick="goToWizardStep(4)" class="text-xs font-outfit font-bold text-slate-700 hover:text-teal-700">
                                    ← Volver a Terapeuta
                                </button>
                                <button type="submit" id="btn-confirm-booking" class="btn-glass-primary px-8 py-3.5 rounded-xl text-sm flex items-center gap-2 font-outfit shadow-lg">
                                    <span>Confirmar Mi Reserva Ahora</span>
                                    <span>✓</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PASO 6: PANTALLA DE ÉXITO GLASSMORFISTA -->
                    <div class="wizard-step-content hidden" data-step="6">
                        <div class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white text-3xl font-extrabold flex items-center justify-center mx-auto mb-4 border border-white/60 shadow-[0_12px_28px_rgba(13,148,136,0.35)]">
                                ✓
                            </div>
                            <span class="glass-badge glass-badge-teal">¡CITA CONFIRMADA CON ÉXITO!</span>
                            <h3 class="text-2xl sm:text-3xl font-outfit font-extrabold text-slate-900 mt-3 mb-2">Tu Espacio de Bienestar está Asegurado</h3>
                            <p class="text-slate-600 text-sm max-w-md mx-auto font-medium">
                                Hemos bloqueado tu horario en la agenda del spa y despachado el comprobante oficial por correo electrónico.
                            </p>
                        </div>

                        <!-- Tarjeta de comprobante en Cristal Translúcido -->
                        <div class="glass-card-subtle p-6 max-w-lg mx-auto mb-8 border border-white/90">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-4 mb-4">
                                <span class="text-xs font-outfit uppercase font-bold text-slate-600">Código de Reserva</span>
                                <span id="confirmed-code" class="font-mono-pm font-extrabold text-lg text-teal-800 bg-white/80 px-3 py-1 rounded-xl border border-white shadow-xs">-</span>
                            </div>

                            <div class="space-y-3 text-xs font-medium">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Servicio:</span>
                                    <span id="confirmed-service" class="font-bold text-slate-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Especialista:</span>
                                    <span id="confirmed-professional" class="font-bold text-teal-700">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Fecha:</span>
                                    <span id="confirmed-date" class="font-bold text-slate-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Horario:</span>
                                    <span id="confirmed-time" class="font-bold text-slate-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Inversión:</span>
                                    <span id="confirmed-price" class="font-outfit font-extrabold text-teal-800 text-base">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Estado del Pago:</span>
                                    <span id="confirmed-payment-status" class="font-outfit font-bold text-xs">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Correo Notificado:</span>
                                    <span id="confirmed-email" class="font-mono-pm font-bold text-slate-800 text-xs">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones posteriores -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a id="confirmed-manage-link" href="#" class="btn-glass-primary px-6 py-3.5 rounded-xl text-xs text-center font-outfit shadow-md">
                                Ver y Gestionar Mi Cita (Cancelar / Reprogramar)
                            </a>
                            <a id="confirmed-receipt-link" href="#" target="_blank" class="hidden btn-glass-secondary px-6 py-3.5 rounded-xl text-xs text-center font-outfit shadow-md bg-emerald-500/10 text-emerald-800 border-emerald-400/40 hover:bg-emerald-500/20">
                                🧾 Ver Recibo Digital
                            </a>
                            <button type="button" onclick="location.reload()" class="btn-glass-secondary px-5 py-3.5 rounded-xl text-xs font-outfit">
                                Agendar Otra Cita
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Tarjeta Resumen Sticky Glassmorfista (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="glass-card p-6 sticky top-28 border border-white/80">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200/60 mb-4">
                            <h4 class="font-outfit font-bold text-base text-slate-900">Resumen de Cita</h4>
                            <span class="glass-badge glass-badge-teal text-[10px]">EN VIVO</span>
                        </div>

                        <div class="space-y-4 text-xs font-medium">
                            <div>
                                <span class="text-slate-500 font-semibold block mb-0.5">Servicio Seleccionado:</span>
                                <div id="summary-service-name" class="font-outfit font-bold text-sm text-slate-900">Ninguno seleccionado</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span id="summary-service-duration" class="text-slate-600 font-medium">-</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span id="summary-service-price" class="font-outfit font-bold text-teal-700 text-sm">-</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60">
                                <span class="text-slate-500 font-semibold block mb-0.5">Fecha:</span>
                                <div id="summary-date" class="font-bold text-slate-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60">
                                <span class="text-slate-500 font-semibold block mb-0.5">Horario del Turno:</span>
                                <div id="summary-time" class="font-bold text-slate-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60">
                                <span class="text-slate-500 font-semibold block mb-0.5">Especialista Asignado:</span>
                                <div id="summary-professional" class="font-bold text-teal-700">Cualquier especialista disponible</div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60">
                                <span class="text-slate-500 font-semibold block mb-0.5">Modalidad de Pago:</span>
                                <div id="summary-payment" class="font-bold text-teal-800">💳 En Línea con Stripe</div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60">
                                <span class="text-slate-500 font-semibold block mb-0.5">Sede:</span>
                                <div class="text-slate-700"><?= htmlspecialchars($spaAddress) ?></div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200/60 bg-white/40 -mx-6 -mb-6 p-6 rounded-b-2xl">
                            <div class="text-[11px] text-slate-700 font-medium flex items-start gap-2">
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
    <section id="consultar-cita" class="py-16 relative z-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="glass-badge glass-badge-blue mb-2">✦ AUTOGESTIÓN DE CLIENTES ✦</span>
            <h2 class="text-3xl font-outfit font-extrabold text-slate-900 mt-2 mb-3">¿Ya tienes una cita agendada?</h2>
            <p class="text-slate-600 text-sm max-w-xl mx-auto mb-8 font-medium">
                Revisa los detalles de tu turno, reprograma hacia una nueva fecha disponible o cancela utilizando tu código oficial de reserva.
            </p>

            <form action="gestionar-cita.php" method="GET" class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <input type="text" name="code" required placeholder="Ej. MRC-2026-1007" class="w-full sm:w-2/3 px-4 py-3 rounded-xl text-sm font-mono-pm uppercase font-bold tracking-wider text-center sm:text-left">
                <button type="submit" class="w-full sm:w-auto btn-glass-primary px-6 py-3 rounded-xl text-xs font-outfit shadow-md">
                    Consultar Cita
                </button>
            </form>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS (VITRINA GLASSMORFISTA) -->
    <section id="servicios-destacados" class="py-20 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="glass-badge glass-badge-teal mb-2">✦ CARTA DE AUTOR ✦</span>
                <h2 class="text-3xl sm:text-4xl font-outfit font-extrabold text-slate-900 mt-2">Tratamientos Destacados</h2>
                <p class="text-slate-600 text-sm mt-2 font-medium">
                    Servicios botánicos con productos orgánicos de origen europeo y cabinas privadas climatizadas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Uñas -->
                <div class="glass-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 text-white flex items-center justify-center text-xl mb-4 shadow-[0_6px_20px_rgba(244,63,94,0.3)] font-bold">
                            💅
                        </div>
                        <span class="text-[10px] font-outfit uppercase tracking-wider text-rose-600 font-bold block mb-1">Nail Studio</span>
                        <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2">Uñas & Manicura de Lujo</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium mb-4">
                            Esmaltado semipermanente de alta duración, esculpidas y nutrición botánica con parafina.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-outfit font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                        Reservar Manicura →
                    </a>
                </div>

                <!-- Cabello -->
                <div class="glass-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 text-white flex items-center justify-center text-xl mb-4 shadow-[0_6px_20px_rgba(14,165,233,0.3)] font-bold">
                            ✂️
                        </div>
                        <span class="text-[10px] font-outfit uppercase tracking-wider text-sky-600 font-bold block mb-1">Peluquería</span>
                        <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2">Corte & Estilo</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium mb-4">
                            Cortes arquitectónicos de diseño para damas y caballeros con lavado revitalizante.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-outfit font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Reservar Corte →
                    </a>
                </div>

                <!-- Pintura & Color -->
                <div class="glass-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 text-white flex items-center justify-center text-xl mb-4 shadow-[0_6px_20px_rgba(168,85,247,0.3)] font-bold">
                            🎨
                        </div>
                        <span class="text-[10px] font-outfit uppercase tracking-wider text-purple-600 font-bold block mb-1">Colorimetría</span>
                        <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2">Pintura & Balayage</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium mb-4">
                            Coloración sin amoníaco, mechas babylights y balayage con degradados luminosos de autor.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-outfit font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                        Reservar Color →
                    </a>
                </div>

                <!-- Masajes -->
                <div class="glass-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-600 text-white flex items-center justify-center text-xl mb-4 shadow-[0_6px_20px_rgba(13,148,136,0.3)] font-bold">
                            🌿
                        </div>
                        <span class="text-[10px] font-outfit uppercase tracking-wider text-teal-600 font-bold block mb-1">Relax & Cuerpo</span>
                        <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2">Masajes & Bienestar</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium mb-4">
                            Piedras volcánicas tibias, liberación descontracturante profunda y aromaterapia de bosque.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-outfit font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                        Reservar Masaje →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER GLASSMORFISTA -->
    <footer class="bg-white/60 backdrop-blur-xl border-t border-white/80 text-slate-700 mt-auto relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center font-outfit font-bold text-lg border border-white/60 shadow-md">
                            M
                        </span>
                        <span class="font-outfit text-2xl font-bold tracking-tight text-slate-900"><?= htmlspecialchars($spaName) ?></span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-sm mb-6 font-medium">
                        Santuario de belleza integral y bienestar holístico. Plataforma web modular con arquitectura limpia y soporte para despliegue ágil en entornos XAMPP y servidores Cloud.
                    </p>
                    <div class="text-xs text-slate-500 space-y-1 font-mono-pm">
                        <div>📍 <?= htmlspecialchars($spaAddress) ?></div>
                        <div>📞 <?= htmlspecialchars($spaPhone) ?></div>
                        <div>✉️ citas@miraclespa.com</div>
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-outfit font-bold uppercase tracking-widest text-teal-800 mb-4">Plataforma</h5>
                    <ul class="text-xs space-y-2.5 text-slate-600 font-medium">
                        <li><a href="#booking-wizard-section" class="hover:text-teal-700 transition-colors">Agendamiento de Citas</a></li>
                        <li><a href="#consultar-cita" class="hover:text-teal-700 transition-colors">Reprogramar o Cancelar</a></li>
                        <li><a href="tests.php" class="hover:text-teal-700 transition-colors text-teal-700 font-bold">🧪 Suite Automatizada (48)</a></li>
                        <li><a href="install.php" class="hover:text-teal-700 transition-colors">Instalador Base de Datos</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-xs font-outfit font-bold uppercase tracking-widest text-sky-800 mb-4">Administración</h5>
                    <ul class="text-xs space-y-2.5 text-slate-600 font-medium">
                        <li><a href="admin/login.php" class="hover:text-teal-700 transition-colors">Iniciar Sesión Admin</a></li>
                        <li><a href="admin/index.php" class="hover:text-teal-700 transition-colors">Dashboard de Demanda</a></li>
                        <li><a href="admin/calendario.php" class="hover:text-teal-700 transition-colors">Calendario Global</a></li>
                        <li><a href="admin/correos.php" class="hover:text-teal-700 transition-colors">Bandeja de Correos</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-10 border-t border-slate-200/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-outfit">
                <div>© <?= date('Y') ?> <?= htmlspecialchars($spaName) ?> &bull; Edición Glassmorphism</div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block shadow-[0_0_8px_rgba(20,184,166,0.6)]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block shadow-[0_0_8px_rgba(14,165,233,0.6)]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block shadow-[0_0_8px_rgba(168,85,247,0.6)]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block shadow-[0_0_8px_rgba(244,63,94,0.6)]"></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script del Wizard -->
    <script src="assets/js/booking.js?v=<?= filemtime(__DIR__ . '/assets/js/booking.js') ?>"></script>
</body>
</html>
