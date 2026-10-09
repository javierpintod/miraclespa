<?php
/**
 * MIRACLE SPA SANCTUARY - Portal Principal de Clientes & Agendamiento
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
    <title><?= htmlspecialchars($spaName) ?> - Agendamiento de Citas & Bienestar</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Estilos de Lujo -->
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-[#fcfaf7] text-[#1c2420] min-h-screen flex flex-col">

    <!-- BARRA SUPERIOR / NOTIFICACIÓN -->
    <div class="bg-[#12372a] text-[#d4af37] text-xs py-2 px-4 text-center font-medium tracking-wide border-b border-[#1a4a39]/50 flex items-center justify-center gap-2">
        <span>✨ Experiencia de Bienestar de Autor</span>
        <span class="text-white/40">&bull;</span>
        <span class="text-white/90">Atención personalizada con cita previa</span>
        <span class="text-white/40">&bull;</span>
        <a href="#booking-wizard-section" class="underline hover:text-white transition-colors font-semibold">Reserva tu turno aquí</a>
    </div>

    <!-- NAVBAR PRINCIPAL -->
    <header class="bg-white/90 backdrop-blur-md border-b border-[#e6ede8] sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3 group">
                    <span class="w-10 h-10 rounded-full bg-[#12372a] text-[#d4af37] flex items-center justify-center font-serif font-bold text-lg shadow-sm group-hover:scale-105 transition-transform">
                        M
                    </span>
                    <div>
                        <span class="font-serif text-xl font-bold tracking-tight text-[#12372a] block">Miracle Spa</span>
                        <span class="text-[10px] uppercase tracking-widest text-[#a3804c] font-semibold -mt-1 block">Sanctuary of Wellness</span>
                    </div>
                </a>
            </div>

            <!-- Navegación Central -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#servicios-destacados" class="hover:text-[#12372a] transition-colors">Servicios</a>
                <a href="#booking-wizard-section" class="hover:text-[#12372a] transition-colors">Agendar Cita</a>
                <a href="#consultar-cita" class="hover:text-[#12372a] transition-colors">Gestionar mi Cita</a>
                <a href="tests.php" class="text-xs bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-full font-semibold border border-emerald-200 hover:bg-emerald-100 transition-colors">
                    🧪 Pruebas Unitarias
                </a>
            </nav>

            <!-- Acceso Admin / Reservar -->
            <div class="flex items-center gap-3">
                <a href="admin/index.php" class="text-xs font-semibold text-gray-700 hover:text-[#12372a] px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Panel Admin</span>
                </a>
                <a href="#booking-wizard-section" class="bg-[#12372a] text-[#ffffff] px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wide hover:bg-[#1a4a39] shadow-sm hover:shadow transition-all flex items-center gap-2">
                    <span>Reservar Ahora</span>
                    <span class="text-[#d4af37]">→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative py-16 lg:py-24 overflow-hidden bg-gradient-to-b from-[#f5f8f5] to-[#fcfaf7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-[#e8f3ec] text-[#12372a] px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-5 border border-[#d2e7d8]">
                    <span>🌿 Cuidado Holístico & Estética Premium</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-[#12372a] leading-tight mb-6">
                    El Arte de Cuidar Tu Cuerpo y Serenar Tu Mente.
                </h1>
                <p class="text-gray-600 text-base sm:text-lg leading-relaxed mb-8 max-w-2xl">
                    Reserva tu cita en línea en segundos. Elige tu tratamiento preferido de <strong>uñas spa, corte de cabello de autor, pintura y balayage, masajes relajantes</strong> y tratamientos faciales con los mejores especialistas del spa.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="#booking-wizard-section" class="bg-[#12372a] text-[#ffffff] px-6 py-3.5 rounded-xl font-bold text-sm hover:bg-[#1a4a39] transition-all shadow-md flex items-center gap-2">
                        <span>Iniciar Reserva en Línea</span>
                        <span class="text-[#d4af37]">✦</span>
                    </a>
                    <a href="#consultar-cita" class="bg-white text-gray-800 border border-gray-300 px-5 py-3.5 rounded-xl font-semibold text-sm hover:border-[#12372a] transition-all shadow-xs">
                        Tengo una cita agendada
                    </a>
                </div>

                <!-- Badges de Confianza -->
                <div class="grid grid-cols-3 gap-6 pt-10 mt-10 border-t border-gray-200/80 text-left">
                    <div>
                        <div class="text-xl font-serif font-bold text-[#12372a]">100%</div>
                        <div class="text-xs text-gray-500 mt-0.5">Higiene & Esterilización Grado Spa</div>
                    </div>
                    <div>
                        <div class="text-xl font-serif font-bold text-[#12372a]">15 min</div>
                        <div class="text-xs text-gray-500 mt-0.5">Buffer de Amortiguamiento entre Citas</div>
                    </div>
                    <div>
                        <div class="text-xl font-serif font-bold text-[#12372a]">Directo</div>
                        <div class="text-xs text-gray-500 mt-0.5">Confirmación Inmediata por Email</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN WIZARD DE RESERVAS (FLUJO COMPLETO) -->
    <section id="booking-wizard-section" class="py-16 bg-white border-y border-[#e6ede8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-[#a3804c] block mb-2">Agendamiento Inteligente</span>
                <h2 class="text-3xl font-serif font-bold text-[#12372a]">Reserva Tu Cita en 5 Pasos</h2>
                <p class="text-gray-500 text-sm mt-2">
                    Disponibilidad en tiempo real para evitar dobles reservas y garantizar tu turno puntual.
                </p>
            </div>

            <!-- Indicadores de Pasos (Bullets) -->
            <div class="max-w-4xl mx-auto mb-10 overflow-x-auto pb-4">
                <div class="flex items-center justify-between min-w-[560px] px-4">
                    <!-- Paso 1 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet active">1</div>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Servicio</div>
                            <div class="text-[10px] text-gray-400">Tratamiento</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-gray-200"></div>

                    <!-- Paso 2 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">2</div>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Fecha</div>
                            <div class="text-[10px] text-gray-400">Día de visita</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-gray-200"></div>

                    <!-- Paso 3 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">3</div>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Horario</div>
                            <div class="text-[10px] text-gray-400">Slot disponible</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-gray-200"></div>

                    <!-- Paso 4 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">4</div>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Especialista</div>
                            <div class="text-[10px] text-gray-400">Terapeuta</div>
                        </div>
                    </div>
                    <div class="w-12 h-0.5 bg-gray-200"></div>

                    <!-- Paso 5 -->
                    <div class="flex items-center gap-3">
                        <div class="wizard-step-bullet inactive">5</div>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Confirmación</div>
                            <div class="text-[10px] text-gray-400">Tus datos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid: Contenido del Wizard (Izquierda) + Resumen de Reserva (Derecha) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl mx-auto">
                <!-- Columna Izquierda: Pasos Interactivos (8 cols) -->
                <div class="lg:col-span-8 bg-[#fafcfb] rounded-2xl p-6 sm:p-8 border border-gray-200 shadow-xs">

                    <!-- PASO 1: SELECCIÓN DE SERVICIO -->
                    <div class="wizard-step-content" data-step="1">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-serif font-bold text-[#12372a]">1. Selecciona tu Servicio</h3>
                                <p class="text-xs text-gray-500 mt-1">Elige entre nuestras especialidades de uñas, cabello, masajes o faciales.</p>
                            </div>
                        </div>

                        <!-- Filtro de Categorías -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button type="button" data-category-filter="todas" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#12372a] text-white shadow-xs transition-colors">
                                Todos los Servicios
                            </button>
                            <?php foreach ($categories as $cat): ?>
                                <button type="button" data-category-filter="<?= htmlspecialchars($cat['slug']) ?>" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#12372a] transition-colors">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lista de Servicios Disponibles -->
                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                            <?php foreach ($services as $srv): ?>
                                <div class="service-card p-4 rounded-xl border border-gray-200 bg-white hover:border-[#12372a] cursor-pointer transition-all card-hover flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                                     data-id="<?= $srv['id'] ?>"
                                     data-name="<?= htmlspecialchars($srv['name']) ?>"
                                     data-duration="<?= $srv['duration_minutes'] ?>"
                                     data-price="<?= $srv['price'] ?>"
                                     data-category="<?= htmlspecialchars($srv['category_slug']) ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                                <?= htmlspecialchars($srv['category_name']) ?>
                                            </span>
                                            <span class="text-xs text-gray-500 font-medium">⏱ <?= $srv['duration_minutes'] ?> min</span>
                                        </div>
                                        <h4 class="font-bold text-gray-900 text-sm sm:text-base"><?= htmlspecialchars($srv['name']) ?></h4>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= htmlspecialchars($srv['description']) ?></p>
                                    </div>
                                    <div class="sm:text-right shrink-0">
                                        <div class="text-lg font-bold text-[#12372a] font-serif">$<?= number_format((float)$srv['price'], 2) ?> USD</div>
                                        <span class="text-[11px] text-[#a3804c] font-semibold">Seleccionar</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end">
                            <button type="button" id="btn-next-to-date" disabled class="bg-[#12372a] text-white px-6 py-2.5 rounded-xl font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#1a4a39] transition-all flex items-center gap-2">
                                <span>Continuar a Fecha</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 2: SELECCIÓN DE FECHA -->
                    <div class="wizard-step-content hidden" data-step="2">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-serif font-bold text-[#12372a]">2. Elige el Día de tu Cita</h3>
                                <p class="text-xs text-gray-500 mt-1">Selecciona la fecha en la que deseas recibir tu atención.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs text-gray-500 hover:text-gray-800 underline">Cambiar Servicio</button>
                        </div>

                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-xs max-w-md mx-auto text-center">
                            <label for="booking-date" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Selecciona la Fecha en el Calendario</label>
                            <input type="date" id="booking-date" class="w-full text-center text-lg font-bold py-3 px-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#12372a] focus:outline-none mb-4">
                            <p class="text-xs text-gray-500">
                                Abierto de Lunes a Sábado de 09:00 AM a 08:00 PM. Domingos libre para renovación del santuario.
                            </p>
                        </div>

                        <div class="mt-8 pt-4 border-t border-gray-200 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(1)" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                ← Volver a Servicio
                            </button>
                            <button type="button" id="btn-next-to-time" class="bg-[#12372a] text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-[#1a4a39] transition-all flex items-center gap-2">
                                <span>Ver Horarios Disponibles</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 3: SELECCIÓN DE HORARIO (SLOTS) -->
                    <div class="wizard-step-content hidden" data-step="3">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-serif font-bold text-[#12372a]">3. Selecciona tu Horario Disponible</h3>
                                <p class="text-xs text-gray-500 mt-1">Horarios validados en tiempo real. Se respeta el buffer de desinfección entre cada turno.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs text-gray-500 hover:text-gray-800 underline">Cambiar Fecha</button>
                        </div>

                        <!-- Contenedor dinámico de turnos -->
                        <div id="slots-loading" class="text-center py-12 text-gray-400">
                            <svg class="animate-spin h-6 w-6 text-[#12372a] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Calculando disponibilidad en cabinas...
                        </div>

                        <div id="slots-empty" class="hidden text-center py-10 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-sm">
                            <p class="font-bold">No hay turnos disponibles para esta fecha.</p>
                            <p class="text-xs mt-1 text-amber-700">Por favor prueba seleccionando otro día en el calendario.</p>
                            <button type="button" onclick="goToWizardStep(2)" class="mt-3 text-xs font-bold underline">Cambiar fecha</button>
                        </div>

                        <div id="slots-container"></div>

                        <div class="mt-8 pt-4 border-t border-gray-200 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(2)" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                ← Volver a Fecha
                            </button>
                            <button type="button" id="btn-next-to-prof" disabled class="bg-[#12372a] text-white px-6 py-2.5 rounded-xl font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#1a4a39] transition-all flex items-center gap-2">
                                <span>Continuar a Especialista</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 4: SELECCIÓN DE PROFESIONAL -->
                    <div class="wizard-step-content hidden" data-step="4">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-serif font-bold text-[#12372a]">4. Selecciona el Profesional</h3>
                                <p class="text-xs text-gray-500 mt-1">Elige a la persona que te atenderá o deja que el sistema asigne automáticamente al especialista óptimo.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs text-gray-500 hover:text-gray-800 underline">Cambiar Horario</button>
                        </div>

                        <div id="professionals-container" class="space-y-3">
                            <!-- Inyectado vía JavaScript -->
                        </div>

                        <div class="mt-8 pt-4 border-t border-gray-200 flex items-center justify-between">
                            <button type="button" onclick="goToWizardStep(3)" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                ← Volver a Horario
                            </button>
                            <button type="button" id="btn-next-to-form" class="bg-[#12372a] text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-[#1a4a39] transition-all flex items-center gap-2">
                                <span>Completar Mis Datos</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASO 5: DATOS DEL CLIENTE Y CONFIRMACIÓN -->
                    <div class="wizard-step-content hidden" data-step="5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-serif font-bold text-[#12372a]">5. Tus Datos para la Reserva</h3>
                                <p class="text-xs text-gray-500 mt-1">Te enviaremos el comprobante con los detalles y opciones de gestión a tu correo.</p>
                            </div>
                            <button type="button" onclick="goToWizardStep(4)" class="text-xs text-gray-500 hover:text-gray-800 underline">Cambiar Profesional</button>
                        </div>

                        <form id="booking-form" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nombre Completo *</label>
                                <input type="text" id="client-name" required placeholder="Ej. Lucía Fernández" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Correo Electrónico *</label>
                                    <input type="email" id="client-email" required placeholder="tu-correo@ejemplo.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                                    <span class="text-[11px] text-gray-400 block mt-1">Recibirás aquí tu código y enlace para cancelar o reprogramar.</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Teléfono Móvil (WhatsApp) *</label>
                                    <input type="tel" id="client-phone" required placeholder="+1 555 123 4567" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Notas o Peticiones Especiales (Opcional)</label>
                                <textarea id="client-notes" rows="2" placeholder="Sensibilidad a productos, preferencia de diseño de uñas, etc." class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none"></textarea>
                            </div>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-900 leading-relaxed">
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" required checked class="mt-0.5 rounded text-[#12372a] focus:ring-[#12372a]">
                                    <span>Acepto las políticas de cancelación de Miracle Spa (cancelación o reprogramación con al menos 4 horas de anticipación sin penalidad).</span>
                                </label>
                            </div>

                            <div class="mt-8 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <button type="button" onclick="goToWizardStep(4)" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                    ← Volver a Profesional
                                </button>
                                <button type="submit" id="btn-confirm-booking" class="bg-[#12372a] text-[#d4af37] px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-[#1a4a39] transition-all shadow-md flex items-center gap-2">
                                    <span>Confirmar Mi Reserva Ahora</span>
                                    <span>✓</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PASO 6: PANTALLA DE ÉXITO / CONFIRMADA -->
                    <div class="wizard-step-content hidden" data-step="6">
                        <div class="text-center py-6">
                            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 text-2xl font-bold flex items-center justify-center mx-auto mb-4 border-2 border-emerald-300 shadow-sm">
                                ✓
                            </div>
                            <span class="text-xs uppercase tracking-widest text-emerald-800 font-bold bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">¡Cita Confirmada con Éxito!</span>
                            <h3 class="text-2xl sm:text-3xl font-serif font-bold text-[#12372a] mt-3 mb-2">Tu Espacio de Bienestar te Espera</h3>
                            <p class="text-gray-600 text-sm max-w-md mx-auto">
                                Hemos bloqueado tu horario en la agenda y despachado el correo electrónico de confirmación.
                            </p>
                        </div>

                        <!-- Tarjeta de comprobante -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs max-w-lg mx-auto mb-8">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                                <span class="text-xs uppercase font-bold text-gray-400">Código de Cita</span>
                                <span id="confirmed-code" class="font-mono font-bold text-lg text-[#12372a] bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100">-</span>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Servicio:</span>
                                    <span id="confirmed-service" class="font-semibold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Especialista:</span>
                                    <span id="confirmed-professional" class="font-semibold text-[#12372a]">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha:</span>
                                    <span id="confirmed-date" class="font-semibold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Horario:</span>
                                    <span id="confirmed-time" class="font-semibold text-gray-900">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Inversión:</span>
                                    <span id="confirmed-price" class="font-bold text-emerald-700 font-serif text-base">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Correo Notificado:</span>
                                    <span id="confirmed-email" class="font-medium text-gray-700 text-xs">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones posteriores -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a id="confirmed-manage-link" href="#" class="w-full sm:w-auto text-center bg-[#12372a] text-white px-6 py-3 rounded-xl font-bold text-xs hover:bg-[#1a4a39] transition-all shadow-sm">
                                Ver y Gestionar Mi Cita (Cancelar / Reprogramar)
                            </a>
                            <button type="button" onclick="location.reload()" class="w-full sm:w-auto text-center bg-gray-100 text-gray-700 px-5 py-3 rounded-xl font-semibold text-xs hover:bg-gray-200 transition-all">
                                Agendar Otra Cita
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Columna Derecha: Tarjeta Resumen Sticky (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm sticky top-28">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                            <h4 class="font-serif font-bold text-base text-[#12372a]">Resumen de Cita</h4>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#a3804c] bg-[#fbf6ed] px-2 py-0.5 rounded">En Tiempo Real</span>
                        </div>

                        <div class="space-y-4 text-xs">
                            <div>
                                <span class="text-gray-400 block mb-0.5">Servicio Elegido:</span>
                                <div id="summary-service-name" class="font-bold text-sm text-gray-900">Ninguno seleccionado</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span id="summary-service-duration" class="text-gray-500">-</span>
                                    <span class="text-gray-300">&bull;</span>
                                    <span id="summary-service-price" class="font-bold text-[#12372a]">-</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <span class="text-gray-400 block mb-0.5">Fecha:</span>
                                <div id="summary-date" class="font-semibold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <span class="text-gray-400 block mb-0.5">Horario del Turno:</span>
                                <div id="summary-time" class="font-semibold text-gray-800">Pendiente</div>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <span class="text-gray-400 block mb-0.5">Especialista Asignado:</span>
                                <div id="summary-professional" class="font-semibold text-[#12372a]">Cualquier especialista disponible</div>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <span class="text-gray-400 block mb-0.5">Ubicación del Spa:</span>
                                <div class="text-gray-700"><?= htmlspecialchars($spaAddress) ?></div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 bg-[#f7f9f7] -mx-6 -mb-6 p-6 rounded-b-2xl">
                            <div class="text-[11px] text-gray-500 flex items-start gap-2">
                                <span class="text-emerald-700 font-bold">🛡️</span>
                                <span>Tus citas cuentan con bloqueo atómico anti-doble reserva y recordatorio por correo electrónico.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN: CONSULTAR O GESTIONAR CITA EXISTENTE -->
    <section id="consultar-cita" class="py-16 bg-[#f7faf8] border-b border-[#e6ede8]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-[#a3804c] block mb-2">Portal de Autogestión</span>
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-[#12372a] mb-3">¿Ya tienes una reserva agendada?</h2>
            <p class="text-gray-600 text-sm max-w-xl mx-auto mb-8">
                Consulta los detalles, reprograma para otra fecha o cancela tu cita utilizando tu código de reserva.
            </p>

            <form action="gestionar-cita.php" method="GET" class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <input type="text" name="code" required placeholder="Ej. MRC-2026-1007" class="w-full sm:w-2/3 px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none uppercase font-mono tracking-wider text-center sm:text-left">
                <button type="submit" class="w-full sm:w-auto bg-[#12372a] text-[#d4af37] px-6 py-3 rounded-xl font-bold text-xs hover:bg-[#1a4a39] transition-all shadow-sm">
                    Consultar Cita
                </button>
            </form>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS (VITRINA) -->
    <section id="servicios-destacados" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-[#a3804c] block mb-2">Carta de Tratamientos</span>
                <h2 class="text-3xl font-serif font-bold text-[#12372a]">Nuestras Especialidades de Bienestar</h2>
                <p class="text-gray-500 text-sm mt-2">
                    Cada servicio incluye productos orgánicos europeos, toallas calientes aromatizadas e infusión herbal de cortesía.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Uñas -->
                <div class="bg-[#fcfaf7] p-6 rounded-2xl border border-gray-200 hover:border-[#12372a] transition-all card-hover">
                    <div class="w-12 h-12 rounded-xl bg-amber-100/60 text-amber-800 flex items-center justify-center text-xl mb-4 font-bold">
                        💅
                    </div>
                    <h3 class="font-serif font-bold text-lg text-gray-900 mb-2">Uñas & Manicura Spa</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                        Esmaltado semipermanente, esculpidas en gel y parafina hidratante para un acabado de revista.
                    </p>
                    <a href="#booking-wizard-section" class="text-xs font-bold text-[#12372a] hover:underline flex items-center gap-1">
                        Reservar Manicura →
                    </a>
                </div>

                <!-- Cabello -->
                <div class="bg-[#fcfaf7] p-6 rounded-2xl border border-gray-200 hover:border-[#12372a] transition-all card-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100/60 text-emerald-800 flex items-center justify-center text-xl mb-4 font-bold">
                        ✂️
                    </div>
                    <h3 class="font-serif font-bold text-lg text-gray-900 mb-2">Corte & Peluquería</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                        Cortes vanguardistas de autor para damas y caballeros, visagismo y nutrición capilar intensiva.
                    </p>
                    <a href="#booking-wizard-section" class="text-xs font-bold text-[#12372a] hover:underline flex items-center gap-1">
                        Reservar Corte →
                    </a>
                </div>

                <!-- Pintura & Color -->
                <div class="bg-[#fcfaf7] p-6 rounded-2xl border border-gray-200 hover:border-[#12372a] transition-all card-hover">
                    <div class="w-12 h-12 rounded-xl bg-purple-100/60 text-purple-800 flex items-center justify-center text-xl mb-4 font-bold">
                        🎨
                    </div>
                    <h3 class="font-serif font-bold text-lg text-gray-900 mb-2">Pintura & Balayage</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                        Coloración premium sin amoníaco, mechas babylights y balayage con degradados luminosos.
                    </p>
                    <a href="#booking-wizard-section" class="text-xs font-bold text-[#12372a] hover:underline flex items-center gap-1">
                        Reservar Color →
                    </a>
                </div>

                <!-- Masajes -->
                <div class="bg-[#fcfaf7] p-6 rounded-2xl border border-gray-200 hover:border-[#12372a] transition-all card-hover">
                    <div class="w-12 h-12 rounded-xl bg-rose-100/60 text-rose-800 flex items-center justify-center text-xl mb-4 font-bold">
                        🌿
                    </div>
                    <h3 class="font-serif font-bold text-lg text-gray-900 mb-2">Masajes Terapéuticos</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                        Terapia de piedras volcánicas calientes, liberación descontracturante y aromaterapia holística.
                    </p>
                    <a href="#booking-wizard-section" class="text-xs font-bold text-[#12372a] hover:underline flex items-center gap-1">
                        Reservar Masaje →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER DE LUJO -->
    <footer class="bg-[#12372a] text-white mt-auto border-t border-[#1a4a39]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Columna 1: Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-9 h-9 rounded-full bg-[#d4af37]/20 border border-[#d4af37]/40 text-[#d4af37] flex items-center justify-center font-serif font-bold text-base">M</span>
                        <span class="font-serif text-xl font-bold tracking-tight text-white"><?= htmlspecialchars($spaName) ?></span>
                    </div>
                    <p class="text-xs text-emerald-100/80 leading-relaxed max-w-sm mb-6">
                        Santuario de belleza integral y bienestar. Aplicación web desarrollada en PHP para despliegue directo en servidor local XAMPP o producción.
                    </p>
                    <div class="text-xs text-emerald-200/90 space-y-1">
                        <div>📍 <?= htmlspecialchars($spaAddress) ?></div>
                        <div>📞 <?= htmlspecialchars($spaPhone) ?></div>
                        <div>✉️ citas@miraclespa.com</div>
                    </div>
                </div>

                <!-- Columna 2: Enlaces Rápidos -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-widest text-[#d4af37] mb-4">Plataforma</h5>
                    <ul class="text-xs space-y-2.5 text-emerald-100/80">
                        <li><a href="#booking-wizard-section" class="hover:text-white transition-colors">Agendamiento de Citas</a></li>
                        <li><a href="#consultar-cita" class="hover:text-white transition-colors">Reprogramar o Cancelar Cita</a></li>
                        <li><a href="tests.php" class="hover:text-white transition-colors font-medium text-emerald-300">🧪 Ejecutor de Pruebas</a></li>
                        <li><a href="install.php" class="hover:text-white transition-colors">Instalador de Base de Datos</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Administración -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-widest text-[#d4af37] mb-4">Gestión Interna</h5>
                    <ul class="text-xs space-y-2.5 text-emerald-100/80">
                        <li><a href="admin/login.php" class="hover:text-white transition-colors">Iniciar Sesión Administrador</a></li>
                        <li><a href="admin/index.php" class="hover:text-white transition-colors">Dashboard de Demanda & Ocupación</a></li>
                        <li><a href="admin/calendario.php" class="hover:text-white transition-colors">Calendario Global de Citas</a></li>
                        <li><a href="admin/correos.php" class="hover:text-white transition-colors">Bandeja de Correos (Simulador)</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-10 mt-10 border-t border-emerald-900/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-emerald-300/60">
                <div>© <?= date('Y') ?> <?= htmlspecialchars($spaName) ?>. Todos los derechos reservados.</div>
                <div>Desarrollado en PHP & MySQL &bull; Optimizado para XAMPP</div>
            </div>
        </div>
    </footer>

    <!-- Script del Wizard -->
    <script src="assets/js/booking.js"></script>
</body>
</html>
