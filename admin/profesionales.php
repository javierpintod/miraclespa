<?php
/**
 * MIRACLE SPA - Gestión de Profesionales, Especialidades y Horarios Semanales
 */

$pageTitle = 'Profesionales & Horarios';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/Professional.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Models/Schedule.php';

$allServices = Service::getAll(true);

// Procesar Acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_prof') {
        $serviceIds = $_POST['services'] ?? [];
        $newId = Professional::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'title' => $_POST['title'] ?? 'Especialista Spa',
            'bio' => $_POST['bio'] ?? '',
            'rating' => (float)($_POST['rating'] ?? 5.0),
            'active' => isset($_POST['active']) ? 1 : 0
        ], $serviceIds);

        AuthHelper::setFlash('success', 'Especialista registrado correctamente.');
        header('Location: profesionales.php');
        exit;
    } elseif ($action === 'update_prof') {
        $id = (int)$_POST['id'];
        $serviceIds = $_POST['services'] ?? [];
        Professional::update($id, [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'title' => $_POST['title'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'active' => isset($_POST['active']) ? 1 : 0
        ], $serviceIds);

        AuthHelper::setFlash('success', 'Datos del profesional actualizados.');
        header('Location: profesionales.php');
        exit;
    } elseif ($action === 'save_schedule') {
        $profId = (int)$_POST['professional_id'];
        for ($day = 0; $day <= 6; $day++) {
            $isOff = isset($_POST["day_{$day}_is_off"]) ? 1 : 0;
            $start = $_POST["day_{$day}_start"] ?? '09:00:00';
            $end = $_POST["day_{$day}_end"] ?? '19:00:00';
            $lunchStart = $_POST["day_{$day}_lunch_start"] ?? '13:00:00';
            $lunchEnd = $_POST["day_{$day}_lunch_end"] ?? '14:00:00';

            Schedule::updateDaySchedule($profId, $day, $start, $end, $lunchStart, $lunchEnd, (bool)$isOff);
        }
        AuthHelper::setFlash('success', 'Horario semanal actualizado correctamente.');
        header('Location: profesionales.php');
        exit;
    } elseif ($action === 'toggle_active') {
        $id = (int)$_POST['id'];
        Professional::toggleActive($id);
        AuthHelper::setFlash('success', 'Estado del profesional modificado.');
        header('Location: profesionales.php');
        exit;
    }
}

$professionals = Professional::getAll(false);
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-serif font-bold text-gray-900">Equipo de Profesionales & Horarios Laborales</h2>
            <p class="text-xs text-gray-500 mt-0.5">Asigna qué servicios puede brindar cada terapeuta y sus jornadas de trabajo semanales.</p>
        </div>
        <button type="button" onclick="openNewProfModal()" class="bg-[#12372a] text-[#d4af37] px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-all flex items-center gap-2 shadow-2xs self-start sm:self-auto">
            <span>+ Registrar Nuevo Especialista</span>
        </button>
    </div>

    <!-- Lista de Profesionales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($professionals as $p): 
            $assignedServices = Professional::getAssignedServices((int)$p['id']);
            $assignedIds = array_column($assignedServices, 'id');
            $schedules = Schedule::getByProfessional((int)$p['id']);
        ?>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs flex flex-col justify-between hover:border-[#12372a] transition-colors">
                <div>
                    <!-- Encabezado con Avatar -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-full bg-[#12372a]/10 text-[#12372a] font-bold text-sm flex items-center justify-center shrink-0 border border-[#12372a]/20">
                                <?= substr($p['name'], 0, 1) ?>
                            </span>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($p['name']) ?></h3>
                                <p class="text-[11px] text-[#a3804c] font-semibold"><?= htmlspecialchars($p['title']) ?></p>
                                <span class="text-[11px] text-amber-500 font-bold">★ <?= number_format($p['rating'], 2) ?></span>
                            </div>
                        </div>

                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="toggle_active">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-bold border transition-colors <?= $p['active'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-400 border-gray-200' ?>">
                                <?= $p['active'] ? 'Activo' : 'Pausado' ?>
                            </button>
                        </form>
                    </div>

                    <!-- Contacto -->
                    <div class="text-[11px] text-gray-500 space-y-1 mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div>✉ <?= htmlspecialchars($p['email']) ?></div>
                        <div>📞 <?= htmlspecialchars($p['phone'] ?: 'No registrado') ?></div>
                    </div>

                    <!-- Servicios asignados -->
                    <div class="mb-4">
                        <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1.5">Servicios Habilitados (<?= count($assignedServices) ?>):</div>
                        <div class="flex flex-wrap gap-1 max-h-20 overflow-y-auto">
                            <?php if (empty($assignedServices)): ?>
                                <span class="text-xs text-rose-500 italic">Sin servicios asignados</span>
                            <?php else: ?>
                                <?php foreach ($assignedServices as $as): ?>
                                    <span class="text-[10px] bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded border border-emerald-100 font-medium">
                                        <?= htmlspecialchars($as['name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs font-semibold">
                    <button type="button" onclick='openEditProfModal(<?= json_encode($p) ?>, <?= json_encode($assignedIds) ?>)' class="text-blue-600 hover:underline">
                        ✎ Editar Datos
                    </button>
                    <button type="button" onclick='openScheduleModal(<?= $p['id'] ?>, "<?= htmlspecialchars($p['name']) ?>", <?= json_encode($schedules) ?>)' class="text-[#12372a] hover:underline flex items-center gap-1 font-bold">
                        📅 Horario Semanal
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MODAL CREAR / EDITAR PROFESIONAL -->
<div id="modal-prof" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-serif font-bold text-lg text-[#12372a]" id="modal-prof-title">Nuevo Profesional</h3>
            <button type="button" onclick="closeProfModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form method="POST" id="prof-form" class="space-y-4 text-xs">
            <input type="hidden" name="action" id="prof-action" value="create_prof">
            <input type="hidden" name="id" id="prof-id" value="">

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nombre y Apellidos *</label>
                <input type="text" name="name" id="prof-name" required placeholder="Ej. Valentina Restrepo" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" id="prof-email" required placeholder="valentina@miraclespa.com" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="phone" id="prof-phone" placeholder="+1 555 000 0000" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Cargo / Especialidad Principal</label>
                    <input type="text" name="title" id="prof-title" placeholder="Ej. Master Colorista" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Calificación Inicial (1 a 5)</label>
                    <input type="number" step="0.05" min="1" max="5" name="rating" id="prof-rating" value="4.95" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Biografía / Reseña Profesional</label>
                <textarea name="bio" id="prof-bio" rows="2" placeholder="Experiencia, certificaciones..." class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]"></textarea>
            </div>

            <!-- Asignación de Servicios -->
            <div>
                <label class="block font-bold text-gray-700 mb-1">Servicios que este Especialista Puede Atender:</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 bg-gray-50 border border-gray-200 rounded-xl">
                    <?php foreach ($allServices as $srv): ?>
                        <label class="flex items-center gap-2 cursor-pointer text-gray-700 hover:text-black">
                            <input type="checkbox" name="services[]" value="<?= $srv['id'] ?>" class="prof-service-checkbox rounded text-[#12372a] focus:ring-[#12372a]">
                            <span class="truncate"><?= htmlspecialchars($srv['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" id="prof-active" value="1" checked class="rounded text-[#12372a] focus:ring-[#12372a]">
                <label for="prof-active" class="font-bold text-gray-700">Especialista Activo en el Agendador</label>
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeProfModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-semibold">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#12372a] text-[#d4af37] px-5 py-2 rounded-xl font-bold hover:bg-[#1a4a39]">
                    Guardar Especialista
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL HORARIOS SEMANALES -->
<div id="modal-schedule" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400">Jornadas Laborales</span>
                <h3 class="font-serif font-bold text-lg text-[#12372a]" id="schedule-prof-name">Horario de Especialista</h3>
            </div>
            <button type="button" onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="save_schedule">
            <input type="hidden" name="professional_id" id="schedule-prof-id" value="">

            <div class="divide-y divide-gray-100">
                <?php
                $diasNombres = [
                    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                    4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 0 => 'Domingo'
                ];
                foreach ($diasNombres as $dNum => $dName):
                ?>
                    <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="w-28 flex items-center gap-2">
                            <span class="font-bold text-gray-900"><?= $dName ?></span>
                        </div>

                        <div class="flex items-center gap-2 flex-1 flex-wrap">
                            <label class="flex items-center gap-1.5 text-gray-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="day_<?= $dNum ?>_is_off" id="day_<?= $dNum ?>_is_off" onchange="toggleDayOff(<?= $dNum ?>)" class="rounded text-[#12372a]">
                                <span>Descanso / Libre</span>
                            </label>

                            <div id="day_<?= $dNum ?>_times" class="flex items-center gap-2">
                                <span class="text-gray-400 text-[10px]">Entrada:</span>
                                <input type="time" name="day_<?= $dNum ?>_start" id="day_<?= $dNum ?>_start" value="09:00" class="px-2 py-1 rounded border border-gray-300">
                                
                                <span class="text-gray-400 text-[10px]">Salida:</span>
                                <input type="time" name="day_<?= $dNum ?>_end" id="day_<?= $dNum ?>_end" value="19:00" class="px-2 py-1 rounded border border-gray-300">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-semibold">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#12372a] text-[#d4af37] px-5 py-2.5 rounded-xl font-bold hover:bg-[#1a4a39]">
                    Guardar Horario Semanal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNewProfModal() {
    document.getElementById('modal-prof-title').textContent = 'Nuevo Profesional';
    document.getElementById('prof-action').value = 'create_prof';
    document.getElementById('prof-id').value = '';
    document.getElementById('prof-name').value = '';
    document.getElementById('prof-email').value = '';
    document.getElementById('prof-phone').value = '';
    document.getElementById('prof-title').value = '';
    document.getElementById('prof-bio').value = '';
    document.getElementById('prof-rating').value = '4.95';
    document.getElementById('prof-active').checked = true;

    document.querySelectorAll('.prof-service-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('modal-prof').classList.remove('hidden');
}

function openEditProfModal(p, assignedServiceIds) {
    document.getElementById('modal-prof-title').textContent = 'Editar Profesional';
    document.getElementById('prof-action').value = 'update_prof';
    document.getElementById('prof-id').value = p.id;
    document.getElementById('prof-name').value = p.name;
    document.getElementById('prof-email').value = p.email;
    document.getElementById('prof-phone').value = p.phone || '';
    document.getElementById('prof-title').value = p.title || '';
    document.getElementById('prof-bio').value = p.bio || '';
    document.getElementById('prof-rating').value = p.rating;
    document.getElementById('prof-active').checked = (p.active == 1);

    document.querySelectorAll('.prof-service-checkbox').forEach(cb => {
        cb.checked = assignedServiceIds.includes(parseInt(cb.value));
    });

    document.getElementById('modal-prof').classList.remove('hidden');
}

function closeProfModal() {
    document.getElementById('modal-prof').classList.add('hidden');
}

function openScheduleModal(profId, profName, schedules) {
    document.getElementById('schedule-prof-id').value = profId;
    document.getElementById('schedule-prof-name').textContent = `Horario Semanal: ${profName}`;

    // Rellenar horarios existentes
    schedules.forEach(s => {
        const d = s.day_of_week;
        const isOffCb = document.getElementById(`day_${d}_is_off`);
        const startInput = document.getElementById(`day_${d}_start`);
        const endInput = document.getElementById(`day_${d}_end`);

        if (isOffCb) {
            isOffCb.checked = (s.is_off == 1);
            toggleDayOff(d);
        }
        if (startInput && s.start_time) startInput.value = s.start_time.substring(0, 5);
        if (endInput && s.end_time) endInput.value = s.end_time.substring(0, 5);
    });

    document.getElementById('modal-schedule').classList.remove('hidden');
}

function closeScheduleModal() {
    document.getElementById('modal-schedule').classList.add('hidden');
}

function toggleDayOff(dNum) {
    const isOff = document.getElementById(`day_${dNum}_is_off`).checked;
    const timesContainer = document.getElementById(`day_${dNum}_times`);
    if (timesContainer) {
        if (isOff) {
            timesContainer.classList.add('opacity-30', 'pointer-events-none');
        } else {
            timesContainer.classList.remove('opacity-30', 'pointer-events-none');
        }
    }
}
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
