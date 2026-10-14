<?php
/**
 * MIRACLE SPA - Comprobante / Recibo Digital de Pago en Línea (Stripe)
 * Estilo Gráfico: Glassmorphism Luxury Spa
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Models/Payment.php';
require_once __DIR__ . '/app/Models/Appointment.php';
require_once __DIR__ . '/app/Helpers/ViewHelper.php';

$txnId = $_GET['txn'] ?? null;
$token = $_GET['token'] ?? null;

$payment = null;
if (!empty($txnId)) {
    $payment = Payment::findByTransactionId($txnId);
} elseif (!empty($token)) {
    $appointment = Appointment::findByManageToken($token);
    if ($appointment) {
        $payment = Payment::findByAppointmentId((int)$appointment['id']);
    }
}

if (!$payment) {
    die("No se encontró el comprobante de pago solicitado.");
}

$appointment = Appointment::findById((int)$payment['appointment_id']);
$spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
$spaAddress = AppConfig::getSetting('spa_address', 'Av. Las Palmas 450, Centro de Bienestar');
$spaPhone = AppConfig::getSetting('spa_phone', '+1 (555) 789-2345');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago — <?= htmlspecialchars($payment['transaction_id']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 relative antialiased selection:bg-teal-500 selection:text-white">

    <!-- Orbes de Luz de Fondo Glassmorfista -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20 no-print"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-1/3 -right-24 no-print"></div>

    <div class="max-w-xl w-full glass-modal p-8 sm:p-10 rounded-3xl border border-white/80 shadow-2xl relative z-10">
        <!-- Encabezado del Recibo -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-200/60 mb-6">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-outfit font-extrabold text-2xl shadow-md border border-white/40">M</span>
                <div>
                    <h1 class="font-outfit font-extrabold text-xl text-slate-900 leading-none">Miracle Spa</h1>
                    <span class="font-mono-pm text-[10px] text-teal-700 font-bold block mt-1 uppercase tracking-wider">[ RECIBO DIGITAL OFICIAL ]</span>
                </div>
            </div>
            <div class="text-right">
                <span class="glass-badge glass-badge-teal text-xs">✓ PAGO APROBADO</span>
                <div class="font-mono-pm text-[11px] text-slate-500 mt-1"><?= date('d/m/Y H:i', strtotime($payment['paid_at'])) ?></div>
            </div>
        </div>

        <!-- Detalles de la Transacción -->
        <div class="space-y-4 text-xs font-medium">
            <div class="glass-subtle-box p-4 rounded-2xl space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-mono-pm uppercase">ID Transacción Stripe:</span>
                    <span class="font-mono-pm font-bold text-teal-800 text-sm"><?= htmlspecialchars($payment['transaction_id']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-mono-pm uppercase">Código de Reserva:</span>
                    <span class="font-mono-pm font-bold text-slate-900"><?= htmlspecialchars($payment['appointment_code'] ?? $appointment['code']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-mono-pm uppercase">Pasarela de Pago:</span>
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="text-[#635BFF] font-extrabold font-outfit text-sm">stripe</span>
                        <span>(Tarjeta <?= htmlspecialchars($payment['card_brand'] ?? 'Crédito') ?> •••• <?= htmlspecialchars($payment['card_last4'] ?? '****') ?>)</span>
                    </span>
                </div>
            </div>

            <!-- Resumen del Tratamiento -->
            <div class="border-t border-slate-200/60 pt-4 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Servicio Reservado:</span>
                    <span class="font-outfit font-bold text-slate-900 text-sm"><?= htmlspecialchars($appointment['service_name']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Fecha y Horario del Turno:</span>
                    <span class="font-mono-pm text-slate-800 font-bold"><?= ViewHelper::formatDate($appointment['date']) ?> &bull; <?= ViewHelper::formatTime($appointment['start_time']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Especialista Asignado:</span>
                    <span class="font-bold text-teal-700"><?= htmlspecialchars($appointment['professional_name']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Titular del Pago:</span>
                    <span class="font-bold text-slate-900"><?= htmlspecialchars($payment['customer_name'] ?: $appointment['client_name']) ?></span>
                </div>
            </div>

            <!-- Total Pagado -->
            <div class="border-t border-slate-200/60 pt-4 flex justify-between items-center">
                <div>
                    <span class="text-slate-500 font-mono-pm text-[11px] block uppercase">Monto Total Cargado:</span>
                    <span class="text-[10px] text-slate-400">Impuestos y cargos de gestión incluidos</span>
                </div>
                <div class="text-2xl font-mono-pm font-extrabold text-teal-700">
                    $<?= number_format((float)$payment['amount'], 2) ?> <span class="text-xs text-slate-500 font-bold"><?= htmlspecialchars($payment['currency']) ?></span>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-8 pt-6 border-t border-slate-200/60 flex flex-wrap items-center justify-between gap-3 no-print">
            <a href="gestionar-cita.php?token=<?= urlencode($appointment['manage_token']) ?>" class="text-xs font-mono-pm font-bold text-slate-600 hover:text-teal-700">
                ← Volver a Mi Cita
            </a>
            <div class="flex gap-2">
                <button type="button" onclick="window.print()" class="btn-glass-secondary px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 font-outfit">
                    <span>🖨️ Imprimir Comprobante</span>
                </button>
                <a href="index.php" class="btn-glass-primary px-4 py-2 rounded-xl text-xs font-outfit">
                    Portal Principal
                </a>
            </div>
        </div>

        <div class="mt-6 text-center text-[10px] font-mono-pm text-slate-400">
            <?= htmlspecialchars($spaName) ?> &bull; <?= htmlspecialchars($spaAddress) ?> &bull; Tel: <?= htmlspecialchars($spaPhone) ?>
        </div>
    </div>
</body>
</html>
