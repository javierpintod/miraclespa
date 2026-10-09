<?php
/**
 * MIRACLE SPA - Gestión de Servicios, Precios y Duraciones (CRUD)
 */

$pageTitle = 'Catálogo de Servicios & Precios';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Models/Category.php';

$categories = Category::getAll();

// Procesar Acciones POST (Crear, Editar, Eliminar, Activar/Desactivar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        Service::create([
            'category_id' => (int)$_POST['category_id'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'duration_minutes' => (int)$_POST['duration_minutes'],
            'price' => (float)$_POST['price'],
            'active' => isset($_POST['active']) ? 1 : 0
        ]);
        AuthHelper::setFlash('success', 'Servicio creado exitosamente.');
        header('Location: servicios.php');
        exit;
    } elseif ($action === 'update') {
        $id = (int)$_POST['id'];
        Service::update($id, [
            'category_id' => (int)$_POST['category_id'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'duration_minutes' => (int)$_POST['duration_minutes'],
            'price' => (float)$_POST['price'],
            'active' => isset($_POST['active']) ? 1 : 0
        ]);
        AuthHelper::setFlash('success', 'Servicio actualizado correctamente.');
        header('Location: servicios.php');
        exit;
    } elseif ($action === 'toggle_active') {
        $id = (int)$_POST['id'];
        Service::toggleActive($id);
        AuthHelper::setFlash('success', 'Estado del servicio cambiado.');
        header('Location: servicios.php');
        exit;
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            Service::delete($id);
            AuthHelper::setFlash('success', 'Servicio eliminado.');
        } catch (Exception $e) {
            AuthHelper::setFlash('error', 'No se puede eliminar el servicio porque ya tiene citas históricas asociadas. Puedes desactivarlo en su lugar.');
        }
        header('Location: servicios.php');
        exit;
    }
}

$services = Service::getAll(false); // todos, activos e inactivos
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-serif font-bold text-gray-900">Carta de Servicios, Precios & Duraciones</h2>
            <p class="text-xs text-gray-500 mt-0.5">Define los tratamientos ofrecidos, sus tiempos en cabina y tarifas.</p>
        </div>
        <button type="button" onclick="openNewServiceModal()" class="bg-[#12372a] text-[#d4af37] px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-all flex items-center gap-2 shadow-2xs self-start sm:self-auto">
            <span>+ Crear Nuevo Servicio</span>
        </button>
    </div>

    <!-- Tabla de Servicios -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold border-b border-gray-200">
                        <th class="p-4">Servicio</th>
                        <th class="p-4">Categoría</th>
                        <th class="p-4 text-center">Duración</th>
                        <th class="p-4 text-right">Precio</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($services as $s): ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($s['name']) ?></div>
                                <div class="text-[11px] text-gray-500 line-clamp-1 max-w-sm mt-0.5"><?= htmlspecialchars($s['description']) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md font-semibold text-[11px]">
                                    <?= htmlspecialchars($s['category_name']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-semibold text-gray-700">
                                ⏱ <?= (int)$s['duration_minutes'] ?> min
                            </td>
                            <td class="p-4 text-right font-serif font-bold text-emerald-800 text-sm">
                                $<?= number_format((float)$s['price'], 2) ?> USD
                            </td>
                            <td class="p-4 text-center">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-xs font-bold border transition-colors <?= $s['active'] ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-gray-100 text-gray-500 border-gray-300' ?>">
                                        <?= $s['active'] ? 'Activo' : 'Inactivo' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button type="button" onclick='openEditServiceModal(<?= json_encode($s) ?>)' class="p-1.5 text-blue-600 hover:bg-blue-50 rounded font-semibold" title="Editar">
                                    ✎ Editar
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar este servicio?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded font-semibold" title="Eliminar">
                                        ✕
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CREAR / EDITAR SERVICIO -->
<div id="modal-service" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-serif font-bold text-lg text-[#12372a]" id="modal-service-title">Nuevo Servicio</h3>
            <button type="button" onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form method="POST" id="service-form" class="space-y-4 text-xs">
            <input type="hidden" name="action" id="service-action" value="create">
            <input type="hidden" name="id" id="service-id" value="">

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nombre del Servicio *</label>
                <input type="text" name="name" id="service-name" required placeholder="Ej. Manicura Rusa & Nivelación" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Categoría *</label>
                <select name="category_id" id="service-category" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Duración (Minutos) *</label>
                    <input type="number" name="duration_minutes" id="service-duration" required min="15" max="360" step="5" value="60" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Precio ($ USD) *</label>
                    <input type="number" name="price" id="service-price" required min="0" step="0.5" value="35.00" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Descripción del Tratamiento</label>
                <textarea name="description" id="service-description" rows="3" placeholder="Detalla los beneficios y productos empleados..." class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" id="service-active" value="1" checked class="rounded text-[#12372a] focus:ring-[#12372a]">
                <label for="service-active" class="font-bold text-gray-700">Servicio Activo en el Agendador Público</label>
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeServiceModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-semibold">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#12372a] text-[#d4af37] px-5 py-2 rounded-xl font-bold hover:bg-[#1a4a39]">
                    Guardar Servicio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNewServiceModal() {
    document.getElementById('modal-service-title').textContent = 'Nuevo Servicio';
    document.getElementById('service-action').value = 'create';
    document.getElementById('service-id').value = '';
    document.getElementById('service-name').value = '';
    document.getElementById('service-description').value = '';
    document.getElementById('service-duration').value = '60';
    document.getElementById('service-price').value = '35.00';
    document.getElementById('service-active').checked = true;
    document.getElementById('modal-service').classList.remove('hidden');
}

function openEditServiceModal(s) {
    document.getElementById('modal-service-title').textContent = 'Editar Servicio';
    document.getElementById('service-action').value = 'update';
    document.getElementById('service-id').value = s.id;
    document.getElementById('service-name').value = s.name;
    document.getElementById('service-category').value = s.category_id;
    document.getElementById('service-description').value = s.description || '';
    document.getElementById('service-duration').value = s.duration_minutes;
    document.getElementById('service-price').value = s.price;
    document.getElementById('service-active').checked = (s.active == 1);
    document.getElementById('modal-service').classList.remove('hidden');
}

function closeServiceModal() {
    document.getElementById('modal-service').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
