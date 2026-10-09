<?php
/**
 * MIRACLE SPA - Instalador Automático de Base de Datos para XAMPP
 * Ejecuta la creación del esquema y datos semilla con 1 clic.
 */

$message = '';
$status = 'idle';
$tablesCreated = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['db_host'] ?? '127.0.0.1';
    $user = $_POST['db_user'] ?? 'root';
    $pass = $_POST['db_pass'] ?? '';
    $port = $_POST['db_port'] ?? '3306';
    $dbname = 'miraclespa_db';

    try {
        // Conexión inicial sin seleccionar base de datos
        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbname}`;");

        $sqlFile = __DIR__ . '/database/database.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("No se encontró el archivo database/database.sql");
        }

        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);

        // Consultar tablas creadas
        $stmt = $pdo->query("SHOW TABLES;");
        $tablesCreated = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $status = 'success';
        $message = "¡Base de datos '{$dbname}' instalada y configurada exitosamente!";
    } catch (Exception $e) {
        $status = 'error';
        $message = "Error en la instalación: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador XAMPP - Miracle Spa Sanctuary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#f5f8f6] text-[#1c2420] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <!-- Cabecera -->
        <div class="bg-gradient-to-r from-[#12372a] to-[#1a4a39] p-8 text-white text-center">
            <span class="w-12 h-12 rounded-full bg-[#d4af37]/20 border border-[#d4af37]/40 text-[#d4af37] mx-auto flex items-center justify-center font-serif font-bold text-xl mb-3">M</span>
            <h1 class="text-2xl font-serif font-bold">Miracle Spa Sanctuary</h1>
            <p class="text-emerald-100 text-sm mt-1">Instalador Rápido de Base de Datos para XAMPP</p>
        </div>

        <div class="p-8">
            <?php if ($status === 'success'): ?>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6 text-emerald-900">
                    <div class="flex items-center gap-3 font-bold text-base mb-1">
                        <span class="text-xl">✓</span> <?= htmlspecialchars($message) ?>
                    </div>
                    <p class="text-sm text-emerald-800">
                        Se crearon y poblaron <strong><?= count($tablesCreated) ?> tablas</strong> con servicios de peluquería, manicura, pintura y masajes, horarios de especialistas y citas de ejemplo.
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 mb-6">
                    <div class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-2">Credenciales de Acceso Administrativo</div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500 block text-xs">Usuario:</span>
                            <code class="font-bold text-gray-900 bg-white px-2 py-0.5 rounded border">admin</code>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs">Contraseña:</span>
                            <code class="font-bold text-gray-900 bg-white px-2 py-0.5 rounded border">admin123</code>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <a href="index.php" class="w-full text-center bg-[#12372a] text-white py-3 rounded-xl font-bold hover:bg-[#1a4a39] transition-all shadow-md">
                        Ir al Portal de Clientes (Reservas)
                    </a>
                    <a href="admin/index.php" class="w-full text-center bg-[#d4af37] text-gray-950 py-3 rounded-xl font-bold hover:bg-[#c5a030] transition-all">
                        Ir al Panel de Administración
                    </a>
                    <a href="tests.php" class="w-full text-center text-xs text-gray-600 hover:text-gray-900 py-1">
                        Ver Suite de Pruebas de Disponibilidad
                    </a>
                </div>

            <?php else: ?>
                <?php if ($status === 'error'): ?>
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6 text-rose-900 text-sm">
                        <strong>Error:</strong> <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                    Este asistente creará automáticamente la base de datos <strong class="text-gray-800">miraclespa_db</strong> y cargará los servicios de peluquería, uñas, masajes, especialistas y el usuario administrador en tu servidor MySQL/MariaDB de XAMPP.
                </p>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Servidor MySQL (Host)</label>
                        <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Usuario MySQL</label>
                            <input type="text" name="db_user" value="root" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Contraseña (Vacía en XAMPP)</label>
                            <input type="password" name="db_pass" value="" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none" placeholder="Opcional">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Puerto MySQL</label>
                        <input type="text" name="db_port" value="3306" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#12372a] focus:outline-none">
                    </div>

                    <button type="submit" class="w-full bg-[#12372a] text-[#d4af37] font-bold py-3.5 rounded-xl hover:bg-[#1a4a39] transition-all shadow-md mt-4 text-sm flex items-center justify-center gap-2">
                        <span>⚡ Instalar Base de Datos con 1 Clic</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
