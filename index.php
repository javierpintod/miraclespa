<?php
/**
 * MIRACLE SPA SANCTUARY - Portal Principal de Clientes & Agendamiento
 * Estilo Gráfico: Memphis Design (Neo-Memphis: Vibrante, Retro, Audaz)
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
    <title><?= htmlspecialchars($spaName) ?> - Agendamiento & Bienestar (Memphis Edition)</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Estilos Memphis Design -->
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .font-space { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-[#FDFBF7] text-[#18181B] min-h-screen flex flex-col">

    <!-- CINTILLO SUPERIOR MEMPHIS -->
    <div class="bg-[#FFBE0B] border-b-2 border-[#18181B] text-[#18181B] text-xs py-2 px-4 text-center font-extrabold tracking-wide flex items-center justify-center gap-3 overflow-hidden">
        <span class="inline-block animate-pulse">⚡</span>
        <span>¡AGENDA EN LÍNEA EN 5 PASOS! UÑAS • CABELLO • COLOR • MASAJES • FACIALES</span>
        <span class="hidden sm:inline bg-[#FF006E] text-white px-2 py-0.5 rounded-full text-[10px] border border-[#18181B] font-bold">100% LIBRE DE DOBLE RESERVA</span>
        <span class="inline-block animate-pulse">⚡</span>
    </div>

    <!-- NAVBAR MEMPHIS -->
    <header class="bg-white border-b-2 border-[#18181B] sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3 group">
                    <span class="w-11 h-11 rounded-2xl bg-[#FF006E] text-white flex items-center justify-center font-space font-bold text-xl border-2 border-[#18181B] memphis-shadow-sm group-hover:rotate-6 transition-transform">
                        M
                    </span>
                    <div>
                        <span class="font-space text-2xl font-bold tracking-tight text-[#18181B] block leading-none">Miracle Spa</span>
                        <span class="text-[10px] uppercase tracking-widest text-[#8338EC] font-extrabold block mt-0.5">Sanctuary &bull; Beauty Lab</span>
                    </div>
                </a>
            </div>

            <!-- Navegación Central -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-bold uppercase tracking-wider text-gray-800">
                <a href="#servicios-destacados" class="hover:text-[#FF006E] transition-colors py-1">Servicios</a>
                <a href="#booking-wizard-section" class="hover:text-[#FF006E] transition-colors py-1">Reservar Cita</a>
                <a href="#consultar-cita" class="hover:text-[#FF006E] transition-colors py-1">Gestionar Cita</a>
                <a href="tests.php" class="bg-[#FFE484] text-[#18181B] px-3 py-1 rounded-full border-2 border-[#18181B] memphis-shadow-sm hover:translate-x-0.5 hover:translate-y-0.5 transition-transform flex items-center gap-1 font-bold">
                    <span>🧪</span> Pruebas (28)
                </a>
            </nav>

            <!-- Acceso Admin / Reservar -->
            <div class="flex items-center gap-3">
                <a href="admin/index.php" class="text-xs font-bold text-[#18181B] bg-white border-2 border-[#18181B] px-3.5 py-2 rounded-xl memphis-shadow-sm hover:bg-[#F3F4F6] transition-all flex items-center gap-1.5">
                    <span class="text-sm">⚙️</span>
                    <span>Admin Panel</span>
                </a>
                <a href="#booking-wizard-section" class="btn-memphis-primary px-4 py-2 rounded-xl text-xs flex items-center gap-2">
                    <span>Agendar Ahora</span>
                    <span class="text-[#FFBE0B]">→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION MEMPHIS -->
    <section class="relative py-16 lg:py-20 overflow-hidden border-b-2 border-[#18181B] bg-[#FFF9E6]">
        <!-- Formas Geométricas Flotantes Memphis -->
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-[#3A86FF]/20 border-2 border-[#18181B] pointer-events-none hidden md:block"></div>
        <div class="absolute top-1/2 -left-8 w-24 h-24 rotate-12 bg-[#FF006E]/20 border-2 border-[#18181B] pointer-events-none hidden md:block"></div>
        <div class="absolute bottom-4 right-1/4 w-16 h-16 rounded-full bg-[#06D6A0]/30 border-2 border-[#18181B] pointer-events-none hidden md:block"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <!-- Stickers Memphis -->
                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <span class="memphis-sticker sticker-pink">🌸 Spa Boutique</span>
                    <span class="memphis-sticker sticker-yellow">💅 Nail Studio</span>
                    <span class="memphis-sticker sticker-blue">✂ Hair & Color</span>
                    <span class="memphis-sticker sticker-teal">✨ Bienestar Total</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-space font-bold text-[#18181B] leading-none mb-6">
                    El Ritual de Belleza que <span class="bg-[#FFBE0B] px-2 py-0.5 border-2 border-[#18181B] inline-block -rotate-1 memphis-shadow-sm">Transforma</span> Tu Energía.
                </h1>

                <p class="text-gray-700 text-base sm:text-lg leading-relaxed mb-8 max-w-2xl font-medium">
                    Agendamiento inteligente en tiempo real. Selecciona tu servicio, tu día y tu terapeuta favorito en cabinas privadas y climatizadas con garantía de cero tiempos de espera.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="#booking-wizard-section" class="btn-memphis-primary px-7 py-3.5 rounded-2xl text-sm flex items-center gap-3">
                        <span>Empezar Mi Reserva</span>
                        <span class="bg-white text-[#FF006E] w-6 h-6 rounded-full flex items-center justify-center font-bold">→</span>
                    </a>
                    <a href="#consultar-cita" class="btn-memphis-yellow px-6 py-3.5 rounded-2xl text-sm">
                        Consultar Mi Cita Existente
                    </a>
                </div>

                <!-- Badges Brutalistas / Memphis -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-10 mt-10 border-t-2 border-[#18181B]">
                    <div class="bg-white p-4 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                        <div class="text-2xl font-space font-bold text-[#FF006E]">100%</div>
                        <div class="text-xs font-bold text-[#18181B] mt-0.5">Protocolos de Esterilización Médica</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                        <div class="text-2xl font-space font-bold text-[#3A86FF]">15 min</div>
                        <div class="text-xs font-bold text-[#18181B] mt-0.5">Buffer de Amortiguamiento e Higiene</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                        <div class="text-2xl font-space font-bold text-[#06D6A0]">Directo</div>
                        <div class="text-xs font-bold text-[#18181B] mt-0.5">Confirmación y Token por Email</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN WIZARD DE RESERVAS (FLUJO COMPLETO EN MEMPHIS STYLE) -->
    <section id="booking-wizard-section" class="py-16 bg-[#FDFBF7] border-b-2 border-[#18181B]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="memphis-sticker sticker-purple mb-2">✦ Agendamiento en 5 Pasos ✦</span>
                <h2 class="text-3xl sm:text-4xl font-space font-bold text-[#18181B] mt-2">Personaliza Tu Experiencia Spa</h2>
                <p class="text-gray-600 text-sm mt-2 font-medium">
                    Sin dobles reservas ni esperas. Elige tu tratamiento, fecha, hora y terapeuta en cabina.
                </p>
            </div>

            <!-- Indicadores de Pasos (Bullets Memphis) -->
            <div class="max-w-4xl mx-auto mb-10 overflow-x-auto pb-4">
                <div class="flex items-center justify-between min-w-[560px] px-4">
                    <!-- Paso 1 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet active">1</div>
                        <div>
                            <div class="text-xs font-extrabold text-[#18181B]">Servicio</div>
                            <div class="text-[10px] text-gray-500 font-bold">Tratamiento</div>
                        </div>
                    </div>
                    <div class="w-12 h-1 bg-[#18181B]"></div>

                    <!-- Paso 2 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">2</div>
                        <div>
                            <div class="text-xs font-extrabold text-[#18181B]">Fecha</div>
                            <div class="text-[10px] text-gray-500 font-bold">Día de visita</div>
                        </div>
                    </div>
                    <div class="w-12 h-1 bg-[#18181B]"></div>

                    <!-- Paso 3 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">3</div>
                        <div>
                            <div class="text-xs font-extrabold text-[#18181B]">Horario</div>
                            <div class="text-[10px] text-gray-500 font-bold">Turno libre</div>
                        </div>
                    </div>
                    <div class="w-12 h-1 bg-[#18181B]"></div>

                    <!-- Paso 4 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">4</div>
                        <div>
                            <div class="text-xs font-extrabold text-[#18181B]">Especialista</div>
                            <div class="text-[10px] text-gray-500 font-bold">Terapeuta</div>
                        </div>
                    </div>
                    <div class="w-12 h-1 bg-[#18181B]"></div>

                    <!-- Paso 5 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">5</div>
                        <div>
                            <div class="text-xs font-extrabold text-[#18181B]">Confirmación</div>
                            <div class="text-[10px] text-gray-500 font-bold">Tus datos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid: Contenido del Wizard (Izquierda) + Resumen de Reserva (Derecha) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl mx-auto">
                <!-- Columna Izquierda: Pasos Interactivos (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-8 border-2 border-[#18181B] memphis-shadow-lg">

                    <!-- PASO 1: SELECCIÓN DE SERVICIO -->
                    <div class="wizard-step-content" data-step="1">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#18181B]">
                            <div>
                                <h3 class="text-xl font-space font-bold text-[#18181B]">1. Elige tu Tratamiento</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Uñas de autor, peluquería vanguardista, balayage, masajes o faciales.</p>
                            </div>
                        </div>

                        <!-- Filtro de Categorías Memphis Pills -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button type="button" data-category-filter="todas" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-[#18181B] text-white border-2 border-[#18181B] memphis-shadow-sm transition-all">
                                Todas las Categorías
                            </button>
                            <?php 
                            $colorsPill = ['sticker-pink', 'sticker-yellow', 'sticker-blue', 'sticker-teal', 'sticker-purple'];
                            foreach ($categories as $idx => $cat): 
                                $colorClass = $colorsPill[$idx % count($colorsPill)];
                            ?>
                                <button type="button" data-category-filter="<?= htmlspecialchars($cat['slug']) ?>" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-white text-[#18181B] border-2 border-[#18181B] memphis-shadow-sm hover:scale-105 transition-all">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lista de Servicios Disponibles Memphis Cards -->
                        <div class="space-y-3.5 max-h-[500px] overflow-y-auto pr-1">
                            <?php foreach ($services as $srv): ?>
                                <div class="service-card p-4 rounded-xl border-2 border-[#18181B] bg-white hover:bg-[#FFF9E6] cursor-pointer transition-all memphis-shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                                     data-id="<?= $srv['id'] ?>"
                                     data-name="<?= htmlspecialchars($srv['name']) ?>"
                                     data-duration="<?= $srv['duration_minutes'] ?>"
                                     data-price="<?= $srv['price'] ?>"
                                     data-category="<?= htmlspecialchars($srv['category_slug']) ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-[#FFBE0B] text-[#18181B] border border-[#18181B] px-2 py-0.5 rounded-md">
                                                <?= htmlspecialchars($srv['category_name']) ?>
                                            </span>
                                            <span class="text-xs text-gray-600 font-bold bg-gray-100 px-2 py-0.5 rounded-md border border-gray-200">⏱ <?= $srv['duration_minutes'] ?> min</span>
                                        </div>
                                        <h4 class="font-space font-bold text-gray-900 text-sm sm:text-base"><?= htmlspecialchars($srv['name']) ?></h4>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2"><?= htmlspecialchars($srv['description']) ?></p>
                                    </div>
                                    <div class="sm:text-right shrink-0">
                                        <div class="text-xl font-space font-extrabold text-[#FF006E] bg-white px-3 py-1 rounded-lg border-2 border-[#18181B] inline-block memphis-shadow-sm">
                                            $<?= number_format((float)$srv['price'], 2) ?> USD
                                        </div>
                                        <span class="text-[11px] text-[#3A86FF] font-extrabold block mt-1">Seleccionar →</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#18181B] flex justify-end">
                            <button type="button" id="btn-next-to-date" disabled class="btn-memphis-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                                <span>Continuar a Fecha</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 2: SELECCIÓN DE FECHA -->
                    <div class="wizard-step-content hidden" data-step="2">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#18181B]">
                            <div>
                                <h3 class="text-xl font-space font-bold text-[#18181B]">2. Selecciona la Fecha</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Elige el día en que deseas vivir tu experiencia de bienestar.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-bold text-[#FF006E] hover:underline">Cambiar Servicio</button>
                        </div>

                        <div class="bg-[#FFF9E6] p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm max-w-md mx-auto text-center">
                            <label for="booking-date" class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-2">Selecciona la Fecha en el Calendario</label>
                            <input type="date" id="booking-date" class="w-full text-center text-lg font-bold py-3 px-4 rounded-xl border-2 border-[#18181B] bg-white focus:ring-2 focus:ring-[#FF006E] focus:outline-none mb-4 memphis-shadow-sm">
                            <div class="bg-white p-3 rounded-xl border border-[#18181B] text-xs text-gray-700 font-semibold">
                                🗓 Horario regular: Lunes a Sábado de 09:00 AM a 08:00 PM.
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#18181B] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-extrabold text-[#18181B] hover:text-[#FF006E]">
                                ← Volver a Servicio
                            </button>
                            <button type="button" id="btn-next-to-time" class="btn-memphis-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2">
                                <span>Ver Horarios Disponibles</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 3: SELECCIÓN DE HORARIO (SLOTS MEMPHIS) -->
                    <div class="wizard-step-content hidden" data-step="3">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#18181B]">
                            <div>
                                <h3 class="text-xl font-space font-bold text-[#18181B]">3. Turnos & Horarios Disponibles</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Disponibilidad en tiempo real con 15 minutos de desinfección preventiva entre turnos.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-bold text-[#FF006E] hover:underline">Cambiar Fecha</button>
                        </div>

                        <!-- Contenedor dinámico de turnos -->
                        <div id="slots-loading" class="text-center py-12 text-gray-400">
                            <div class="w-8 h-8 rounded-full border-4 border-[#18181B] border-t-[#FF006E] animate-spin mx-auto mb-2"></div>
                            <span class="font-bold text-xs text-gray-600">Calculando turnos disponibles en cabinas...</span>
                        </div>

                        <div id="slots-empty" class="hidden text-center py-10 bg-[#FFBE0B]/20 border-2 border-[#18181B] rounded-2xl text-[#18181B] text-sm memphis-shadow-sm">
                            <p class="font-extrabold text-base">No hay turnos libres para esta fecha.</p>
                            <p class="text-xs mt-1 text-gray-600 font-medium">Por favor selecciona otro día en el calendario.</p>
                            <button type="button" onclick="goToWizardStep(2)" class="mt-3 text-xs font-extrabold bg-[#18181B] text-white px-4 py-2 rounded-xl memphis-shadow-sm">Cambiar fecha</button>
                        </div>

                        <div id="slots-container"></div>

                        <div class="mt-8 pt-4 border-t-2 border-[#18181B] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-extrabold text-[#18181B] hover:text-[#FF006E]">
                                ← Volver a Fecha
                            </button>
                            <button type="button" id="btn-next-to-prof" disabled class="btn-memphis-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                                <span>Continuar a Especialista</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 4: SELECCIÓN DE PROFESIONAL -->
                    <div class="wizard-step-content hidden" data-step="4">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#18181B]">
                            <div>
                                <h3 class="text-xl font-space font-bold text-[#18181B]">4. Elige Quién te Atenderá</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Selecciona a tu terapeuta favorito o permite la asignación automática inteligente.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-bold text-[#FF006E] hover:underline">Cambiar Horario</button>
                        </div>

                        <div id="professionals-container" class="space-y-3.5">
                            <!-- Inyectado vía JavaScript -->
                        </div>

                        <div class="mt-8 pt-4 border-t-2 border-[#18181B] flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-extrabold text-[#18181B] hover:text-[#FF006E]">
                                ← Volver a Horario
                            </button>
                            <button type="button" id="btn-next-to-form" class="btn-memphis-primary px-6 py-2.5 rounded-xl text-xs flex items-center gap-2">
                                <span>Completar Mis Datos</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 5: DATOS DEL CLIENTE Y CONFIRMACIÓN -->
                    <div class="wizard-step-content hidden" data-step="5">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-[#18181B]">
                            <div>
                                <h3 class="text-xl font-space font-bold text-[#18181B]">5. Datos Personales & Confirmación</h3>
                                <p class="text-xs text-gray-500 font-medium mt-1">Recibirás tu confirmación oficial por correo con los enlaces de autogestión.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(4)" class="text-xs font-bold text-[#FF006E] hover:underline">Cambiar Especialista</button>
                        </div>

                        <form id="booking-form" class="space-y-4">
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Nombre Completo *</label>
                                <input type="text" id="client-name" required placeholder="Ej. Lucía Fernández" class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Correo Electrónico *</label>
                                    <input type="email" id="client-email" required placeholder="tu-correo@ejemplo.com" class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm">
                                    <span class="text-[11px] text-gray-500 font-semibold block mt-1">Recibirás aquí tu comprobante para reprogramar o cancelar.</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Teléfono Móvil (WhatsApp) *</label>
                                    <input type="tel" id="client-phone" required placeholder="+1 555 123 4567" class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#18181B] mb-1">Notas o Preferencias Especiales</label>
                                <textarea id="client-notes" rows="2" placeholder="Sensibilidad a productos, alergias, diseño de uñas..." class="w-full px-4 py-3 rounded-xl border-2 border-[#18181B] text-sm focus:ring-2 focus:ring-[#FF006E] focus:outline-none memphis-shadow-sm"></textarea>
                            </div>

                            <div class="bg-[#FFF9E6] border-2 border-[#18181B] rounded-xl p-4 text-xs text-[#18181B] font-semibold memphis-shadow-sm">
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" required checked class="mt-0.5 rounded text-[#FF006E] focus:ring-[#FF006E]">
                                    <span>Acepto las políticas de Miracle Spa (reprogramación y cancelación sin costo con al menos 4 horas de anticipación).</span>
                                </label>
                            </div>

                            <div class="mt-8 pt-4 border-t-2 border-[#18181B] flex items-center justify-between">
                                <button type="button" onclick="goToWizardStep(4)" class="text-xs font-extrabold text-[#18181B] hover:text-[#FF006E]">
                                    ← Volver a Profesional
                                </button>
                                <button type="submit" id="btn-confirm-booking" class="btn-memphis-primary px-8 py-3.5 rounded-2xl text-sm flex items-center gap-2">
                                    <span>Confirmar Mi Reserva Ahora</span>
                                    <span>✓</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PASO 6: PANTALLA DE ÉXITO MEMPHIS -->
                    <div class="wizard-step-content hidden" data-step="6">
                        <div class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-[#06D6A0] text-[#18181B] text-3xl font-extrabold flex items-center justify-center mx-auto mb-4 border-2 border-[#18181B] memphis-shadow">
                                ✓
                            </div>
                            <span class="memphis-sticker sticker-teal">¡CITA CONFIRMADA CON ÉXITO!</span>
                            <h3 class="text-2xl sm:text-3xl font-space font-bold text-[#18181B] mt-3 mb-2">Tu Espacio de Bienestar está Asegurado</h3>
                            <p class="text-gray-600 text-sm max-w-md mx-auto font-medium">
                                Hemos bloqueado tu horario en la agenda del spa y despachado el comprobante oficial por correo.
                            </p>
                        </div>

                        <!-- Tarjeta de comprobante Memphis -->
                        <div class="bg-[#FFF9E6] border-2 border-[#18181B] rounded-2xl p-6 memphis-shadow max-w-lg mx-auto mb-8">
                            <div class="flex items-center justify-between border-b-2 border-[#18181B] pb-4 mb-4">
                                <span class="text-xs uppercase font-extrabold text-gray-600">Código de Reserva</span>
                                <span id="confirmed-code" class="font-mono font-extrabold text-lg text-[#18181B] bg-white px-3 py-1 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">-</span>
                            </div>

                            <div class="space-y-3 text-xs font-medium">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Servicio:</span>
                                    <span id="confirmed-service" class="font-bold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Especialista:</span>
                                    <span id="confirmed-professional" class="font-bold text-[#3A86FF]">-</span>
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
                                    <span id="confirmed-price" class="font-space font-extrabold text-[#FF006E] text-base">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Correo Notificado:</span>
                                    <span id="confirmed-email" class="font-bold text-gray-800 text-xs">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones posteriores -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a id="confirmed-manage-link" href="#" class="btn-memphis-primary px-6 py-3 rounded-xl text-xs text-center">
                                Ver y Gestionar Mi Cita (Cancelar / Reprogramar)
                            </a>
                            <button type="button" onclick="location.reload()" class="btn-memphis-yellow px-5 py-3 rounded-xl text-xs">
                                Agendar Otra Cita
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Tarjeta Resumen Sticky Memphis (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl p-6 border-2 border-[#18181B] memphis-shadow-lg sticky top-28">
                        <div class="flex items-center justify-between pb-4 border-b-2 border-[#18181B] mb-4">
                            <h4 class="font-space font-bold text-base text-[#18181B]">Resumen de Cita</h4>
                            <span class="memphis-sticker sticker-pink text-[10px]">EN VIVO</span>
                        </div>

                        <div class="space-y-4 text-xs font-medium">
                            <div>
                                <span class="text-gray-500 font-bold block mb-0.5">Servicio Seleccionado:</span>
                                <div id="summary-service-name" class="font-space font-bold text-sm text-gray-900">Ninguno seleccionado</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span id="summary-service-duration" class="text-gray-600 font-bold">-</span>
                                    <span class="text-gray-400">&bull;</span>
                                    <span id="summary-service-price" class="font-space font-bold text-[#FF006E] text-sm">-</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#18181B]/10">
                                <span class="text-gray-500 font-bold block mb-0.5">Fecha:</span>
                                <div id="summary-date" class="font-bold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#18181B]/10">
                                <span class="text-gray-500 font-bold block mb-0.5">Horario del Turno:</span>
                                <div id="summary-time" class="font-bold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#18181B]/10">
                                <span class="text-gray-500 font-bold block mb-0.5">Especialista Asignado:</span>
                                <div id="summary-professional" class="font-bold text-[#3A86FF]">Cualquier especialista disponible</div>
                            </div>

                            <div class="pt-3 border-t-2 border-[#18181B]/10">
                                <span class="text-gray-500 font-bold block mb-0.5">Ubicación del Spa:</span>
                                <div class="text-gray-700"><?= htmlspecialchars($spaAddress) ?></div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t-2 border-[#18181B] bg-[#FFF9E6] -mx-6 -mb-6 p-6 rounded-b-2xl">
                            <div class="text-[11px] text-[#18181B] font-bold flex items-start gap-2">
                                <span class="text-lg">🛡️</span>
                                <span>Bloqueo atómico anti-doble reserva con token criptográfico de autogestión.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN: CONSULTAR O GESTIONAR CITA EXISTENTE -->
    <section id="consultar-cita" class="py-16 bg-[#FFF9E6] border-b-2 border-[#18181B]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="memphis-sticker sticker-blue mb-2">✦ Portal del Cliente ✦</span>
            <h2 class="text-3xl font-space font-bold text-[#18181B] mt-2 mb-3">¿Ya tienes una cita agendada?</h2>
            <p class="text-gray-700 text-sm max-w-xl mx-auto mb-8 font-medium">
                Revisa los datos de tu turno, reprograma para otra fecha o cancela tu cita utilizando tu código de reserva.
            </p>

            <form action="gestionar-cita.php" method="GET" class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <input type="text" name="code" required placeholder="Ej. MRC-2026-1007" class="w-full sm:w-2/3 px-4 py-3 rounded-xl border-2 border-[#18181B] bg-white text-sm font-mono uppercase font-bold tracking-wider text-center sm:text-left memphis-shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FF006E]">
                <button type="submit" class="w-full sm:w-auto btn-memphis-primary px-6 py-3 rounded-xl text-xs">
                    Consultar Cita
                </button>
            </form>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS (VITRINA MEMPHIS) -->
    <section id="servicios-destacados" class="py-20 bg-white border-b-2 border-[#18181B]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="memphis-sticker sticker-pink mb-2">Carta Exclusiva</span>
                <h2 class="text-3xl sm:text-4xl font-space font-bold text-[#18181B] mt-2">Tratamientos Destacados</h2>
                <p class="text-gray-600 text-sm mt-2 font-medium">
                    Servicios de autor con productos orgánicos europeos y toallas tibias aromatizadas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Uñas -->
                <div class="bg-[#FFF9E6] p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#FF006E] text-white flex items-center justify-center text-xl mb-4 border-2 border-[#18181B] memphis-shadow-sm font-bold">
                            💅
                        </div>
                        <h3 class="font-space font-bold text-lg text-[#18181B] mb-2">Uñas & Manicura Spa</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Esmaltado semipermanente, esculpidas y nutrición profunda con parafina botánica.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-extrabold text-[#FF006E] hover:underline flex items-center gap-1">
                        Reservar Manicura →
                    </a>
                </div>

                <!-- Cabello -->
                <div class="bg-[#FFFDF9] p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#3A86FF] text-white flex items-center justify-center text-xl mb-4 border-2 border-[#18181B] memphis-shadow-sm font-bold">
                            ✂️
                        </div>
                        <h3 class="font-space font-bold text-lg text-[#18181B] mb-2">Corte & Peluquería</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Cortes de diseño para damas y caballeros, visagismo y lavado revitalizante.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-extrabold text-[#3A86FF] hover:underline flex items-center gap-1">
                        Reservar Corte →
                    </a>
                </div>

                <!-- Pintura & Color -->
                <div class="bg-[#FFF9E6] p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#FFBE0B] text-[#18181B] flex items-center justify-center text-xl mb-4 border-2 border-[#18181B] memphis-shadow-sm font-bold">
                            🎨
                        </div>
                        <h3 class="font-space font-bold text-lg text-[#18181B] mb-2">Pintura & Balayage</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Coloración sin amoníaco, mechas babylights y balayage con degradados luminosos.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-extrabold text-[#18181B] hover:underline flex items-center gap-1">
                        Reservar Color →
                    </a>
                </div>

                <!-- Masajes -->
                <div class="bg-[#FFFDF9] p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#06D6A0] text-[#18181B] flex items-center justify-center text-xl mb-4 border-2 border-[#18181B] memphis-shadow-sm font-bold">
                            🌿
                        </div>
                        <h3 class="font-space font-bold text-lg text-[#18181B] mb-2">Masajes & Relax</h3>
                        <p class="text-xs text-gray-700 leading-relaxed font-medium mb-4">
                            Piedras volcánicas calientes, liberación descontracturante y aromaterapia.
                        </p>
                    </div>
                    <a href="#booking-wizard-section" class="text-xs font-extrabold text-[#06D6A0] hover:underline flex items-center gap-1">
                        Reservar Masaje →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER MEMPHIS DESIGN -->
    <footer class="bg-[#18181B] text-white mt-auto border-t-4 border-[#FFBE0B]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-10 rounded-2xl bg-[#FF006E] text-white flex items-center justify-center font-space font-bold text-lg border-2 border-white memphis-shadow-sm">
                            M
                        </span>
                        <span class="font-space text-2xl font-bold tracking-tight text-white"><?= htmlspecialchars($spaName) ?></span>
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed max-w-sm mb-6 font-medium">
                        Santuario de belleza integral y bienestar. Aplicación web desarrollada en PHP para despliegue ágil en XAMPP y servidores de producción.
                    </p>
                    <div class="text-xs text-gray-400 space-y-1 font-mono">
                        <div>📍 <?= htmlspecialchars($spaAddress) ?></div>
                        <div>📞 <?= htmlspecialchars($spaPhone) ?></div>
                        <div>✉️ citas@miraclespa.com</div>
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-extrabold uppercase tracking-widest text-[#FFBE0B] mb-4">Plataforma</h5>
                    <ul class="text-xs space-y-2.5 text-gray-300 font-semibold">
                        <li><a href="#booking-wizard-section" class="hover:text-[#FF006E] transition-colors">Agendamiento de Citas</a></li>
                        <li><a href="#consultar-cita" class="hover:text-[#FF006E] transition-colors">Reprogramar o Cancelar</a></li>
                        <li><a href="tests.php" class="hover:text-[#06D6A0] transition-colors text-[#06D6A0]">🧪 Ejecutor de Pruebas (28)</a></li>
                        <li><a href="install.php" class="hover:text-[#FF006E] transition-colors">Instalador de Base de Datos</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-xs font-extrabold uppercase tracking-widest text-[#3A86FF] mb-4">Administración</h5>
                    <ul class="text-xs space-y-2.5 text-gray-300 font-semibold">
                        <li><a href="admin/login.php" class="hover:text-[#FF006E] transition-colors">Iniciar Sesión Admin</a></li>
                        <li><a href="admin/index.php" class="hover:text-[#FF006E] transition-colors">Dashboard de Demanda</a></li>
                        <li><a href="admin/calendario.php" class="hover:text-[#FF006E] transition-colors">Calendario Global</a></li>
                        <li><a href="admin/correos.php" class="hover:text-[#FF006E] transition-colors">Bandeja de Correos (Simulador)</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-10 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-400 font-mono">
                <div>© <?= date('Y') ?> <?= htmlspecialchars($spaName) ?> &bull; Edición Memphis Design</div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#FF006E] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-full bg-[#FFBE0B] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-full bg-[#3A86FF] inline-block border border-white"></span>
                    <span class="w-3 h-3 rounded-full bg-[#06D6A0] inline-block border border-white"></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script del Wizard -->
    <script src="assets/js/booking.js"></script>
</body>
</html>
