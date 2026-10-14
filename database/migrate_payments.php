<?php
/**
 * MIRACLE SPA - Migración de Base de Datos: Sistema de Pagos en Línea (Stripe)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

echo "=== MIGRACIÓN: Sistema de Pagos con Stripe ===" . PHP_EOL;

try {
    $db = Database::getConnection();

    // 1. Crear tabla payments
    $db->exec("
        CREATE TABLE IF NOT EXISTS `payments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `appointment_id` INT NOT NULL,
            `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
            `amount` DECIMAL(10,2) NOT NULL,
            `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
            `gateway` VARCHAR(50) NOT NULL DEFAULT 'stripe',
            `payment_method` VARCHAR(50) NOT NULL DEFAULT 'card',
            `status` ENUM('pendiente', 'completado', 'fallido', 'reembolsado') NOT NULL DEFAULT 'completado',
            `card_brand` VARCHAR(50) NULL,
            `card_last4` VARCHAR(4) NULL,
            `receipt_url` VARCHAR(255) NULL,
            `customer_name` VARCHAR(150) NULL,
            `customer_email` VARCHAR(150) NULL,
            `metadata_json` TEXT NULL,
            `paid_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_payment_app` (`appointment_id`),
            INDEX `idx_payment_status` (`status`),
            INDEX `idx_payment_txn` (`transaction_id`),
            CONSTRAINT `fk_payment_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[✓] Tabla 'payments' creada / verificada con éxito." . PHP_EOL;

    // 2. Agregar columnas a tabla appointments (si no existen)
    $columns = $db->query("SHOW COLUMNS FROM `appointments`")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('payment_status', $columns)) {
        $db->exec("ALTER TABLE `appointments` ADD COLUMN `payment_status` ENUM('pendiente', 'pagado', 'reembolsado', 'fallido') NOT NULL DEFAULT 'pendiente' AFTER `status`;");
        echo "[✓] Columna 'payment_status' agregada a appointments." . PHP_EOL;
    }

    if (!in_array('payment_method', $columns)) {
        $db->exec("ALTER TABLE `appointments` ADD COLUMN `payment_method` VARCHAR(50) DEFAULT 'en_sede' AFTER `payment_status`;");
        echo "[✓] Columna 'payment_method' agregada a appointments." . PHP_EOL;
    }

    if (!in_array('stripe_payment_intent_id', $columns)) {
        $db->exec("ALTER TABLE `appointments` ADD COLUMN `stripe_payment_intent_id` VARCHAR(100) NULL AFTER `payment_method`;");
        echo "[✓] Columna 'stripe_payment_intent_id' agregada a appointments." . PHP_EOL;
    }

    if (!in_array('paid_at', $columns)) {
        $db->exec("ALTER TABLE `appointments` ADD COLUMN `paid_at` DATETIME NULL AFTER `stripe_payment_intent_id`;");
        echo "[✓] Columna 'paid_at' agregada a appointments." . PHP_EOL;
    }

    // 3. Insertar configuraciones de Stripe en settings
    $stripeSettings = [
        ['stripe_enabled', '1', 'Habilitar o pausar pagos en línea con Stripe'],
        ['stripe_mode', 'test', 'Modo de Stripe (test o live)'],
        ['stripe_publishable_key', 'pk_test_51MiracleSpaPublicStripeKeyDemo2026', 'Clave Pública de Stripe (Publishable Key)'],
        ['stripe_secret_key', 'sk_test_51MiracleSpaSecretStripeKeyDemo2026', 'Clave Secreta de Stripe (Secret Key)'],
        ['stripe_currency', 'USD', 'Moneda de cobro en Stripe (USD, EUR, etc.)'],
        ['allow_pay_on_venue', '1', 'Permitir a los clientes elegir pagar presencialmente en el spa']
    ];

    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM `settings` WHERE `setting_key` = ?");
    $stmtInsert = $db->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES (?, ?, ?)");

    foreach ($stripeSettings as $setting) {
        $stmtCheck->execute([$setting[0]]);
        if ($stmtCheck->fetchColumn() == 0) {
            $stmtInsert->execute($setting);
            echo "[✓] Configuración agregada: {$setting[0]}." . PHP_EOL;
        }
    }

    echo "=== Migración completada exitosamente ===" . PHP_EOL;

} catch (Exception $e) {
    echo "[!] Error en la migración: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
