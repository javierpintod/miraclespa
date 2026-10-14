<?php
/**
 * MIRACLE SPA - Suite de Pruebas Unitarias para Pasarela de Pagos Stripe
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/Payment.php';
require_once __DIR__ . '/../app/Models/Appointment.php';
require_once __DIR__ . '/../app/Models/Client.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Models/Professional.php';
require_once __DIR__ . '/../app/Services/StripePaymentService.php';

class PaymentTest {
    private array $results = [];

    public function runAll(): array {
        $this->testLuhnAlgorithm();
        $this->testExpirationValidation();
        $this->testCardBrandDetection();
        $this->testPaymentModelCrud();
        $this->testStripePaymentExecution();
        $this->testDoublePaymentPrevention();
        $this->testStripeRefundWorkflow();
        return $this->results;
    }

    private function assert(string $testName, bool $condition, string $message = ''): void {
        $this->results[] = [
            'suite' => 'Pasarela de Pagos en Línea (Stripe API v1 & Simulator)',
            'test' => $testName,
            'passed' => $condition,
            'message' => $message
        ];
    }

    private function testLuhnAlgorithm(): void {
        $validCard = '4242424242424242';
        $invalidCard = '4242424242424241';
        $validAmex = '378282246310005';

        $this->assert('La verificación de Luhn debe aceptar tarjetas válidas estándar (4242...)', StripePaymentService::validateLuhn($validCard) === true);
        $this->assert('La verificación de Luhn debe rechazar números con dígito de control corrupto', StripePaymentService::validateLuhn($invalidCard) === false);
        $this->assert('La verificación de Luhn debe validar números legítimos de American Express', StripePaymentService::validateLuhn($validAmex) === true);
        $this->assert('La verificación de Luhn debe rechazar cadenas vacías o no numéricas', StripePaymentService::validateLuhn('') === false);
    }

    private function testExpirationValidation(): void {
        $currentYear = (int)date('Y');
        $this->assert('Una tarjeta con vencimiento en 2030 debe ser considerada válida', StripePaymentService::validateExpiration(12, 2030) === true);
        $this->assert('Una tarjeta con año pasado (2020) debe ser rechazada como expirada', StripePaymentService::validateExpiration(5, 2020) === false);
        $this->assert('Un mes inválido (> 12 o < 1) debe ser rechazado', StripePaymentService::validateExpiration(13, $currentYear + 2) === false);
        $this->assert('Soporte de año en formato 2 dígitos (ej. 30 -> 2030)', StripePaymentService::validateExpiration(8, 30) === true);
    }

    private function testCardBrandDetection(): void {
        $visa = StripePaymentService::detectCardBrand('4242424242424242');
        $mc = StripePaymentService::detectCardBrand('5555555555554444');
        $amex = StripePaymentService::detectCardBrand('378282246310005');
        $unknown = StripePaymentService::detectCardBrand('6011000000000000');

        $this->assert('Detección automática de marca Visa (prefijo 4)', $visa === 'Visa');
        $this->assert('Detección automática de marca Mastercard (prefijo 51-55)', $mc === 'Mastercard');
        $this->assert('Detección automática de marca American Express (prefijo 34/37)', $amex === 'Amex');
        $this->assert('Respaldo para marcas desconocidas o genéricas', !empty($unknown));
    }

    private function testPaymentModelCrud(): void {
        $db = Database::getConnection();
        $services = Service::getAll(true);
        $prof = Professional::getAll(false)[0];
        $client = Client::findOrCreate('Test Payment Model', 'test_model@miraclespa.com', '+15550001111');
        $service = $services[0];
        $code = 'MRC-' . date('Y') . '-MOD' . rand(100, 999);
        $token = bin2hex(random_bytes(24));

        $stmt = $db->prepare("INSERT INTO appointments 
            (code, client_id, service_id, professional_id, date, start_time, end_time, duration_minutes, buffer_minutes, price, status, payment_status, payment_method, manage_token)
            VALUES (?, ?, ?, ?, '2026-11-20', '10:00:00', '11:00:00', ?, 15, ?, 'confirmada', 'pendiente', 'en_sede', ?)");
        $stmt->execute([$code, $client['id'], $service['id'], $prof['id'], $service['duration_minutes'], $service['price'], $token]);
        $tempAppId = (int)$db->lastInsertId();

        $uniqueTxId = 'pi_test_' . bin2hex(random_bytes(10));
        $paymentId = Payment::create([
            'appointment_id' => $tempAppId,
            'amount' => 95.50,
            'currency' => 'usd',
            'gateway' => 'stripe',
            'transaction_id' => $uniqueTxId,
            'status' => 'pagado',
            'payment_method' => 'card',
            'card_brand' => 'Visa',
            'card_last4' => '4242',
            'receipt_url' => 'recibo.php?token=' . $token,
            'gateway_response' => ['status' => 'succeeded', 'id' => $uniqueTxId]
        ]);

        $this->assert('El modelo Payment debe persistir transacciones exitosamente en la BD', $paymentId > 0);

        $found = Payment::findByTransactionId($uniqueTxId);
        $this->assert('Búsqueda de pago por Transaction ID debe recuperar los datos correctos', $found !== null && (float)$found['amount'] === 95.50);
        $this->assert('El método findByTransactionId debe decodificar automáticamente el JSON de gateway_response', is_array($found['gateway_response']) && $found['gateway_response']['id'] === $uniqueTxId);

        // Limpiar
        $db->prepare("DELETE FROM appointments WHERE id = ?")->execute([$tempAppId]);
    }

    private function testStripePaymentExecution(): void {
        $db = Database::getConnection();

        // Obtener un servicio y profesional de prueba
        $services = Service::getAll(true);
        $professionals = Professional::getAll(false);
        if (empty($services) || empty($professionals)) {
            $this->assert('Disponibilidad de servicios y profesionales para prueba de pago', false, 'No hay servicios en BD');
            return;
        }

        $service = $services[0];
        $prof = $professionals[0];
        $client = Client::findOrCreate('Test Stripe Payment', 'test_stripe@miraclespa.com', '+15550009999');

        $code = 'MRC-' . date('Y') . '-TST' . rand(100, 999);
        $token = bin2hex(random_bytes(24));
        $date = date('Y-m-d', strtotime('+7 days'));
        
        $stmt = $db->prepare("INSERT INTO appointments 
            (code, client_id, service_id, professional_id, date, start_time, end_time, duration_minutes, buffer_minutes, price, status, payment_status, payment_method, manage_token)
            VALUES (?, ?, ?, ?, ?, '11:00:00', '12:00:00', ?, 15, ?, 'confirmada', 'pendiente', 'en_sede', ?)");
        $stmt->execute([$code, $client['id'], $service['id'], $prof['id'], $date, $service['duration_minutes'], $service['price'], $token]);
        $appId = (int)$db->lastInsertId();

        $cardData = [
            'card_number' => '4242424242424242',
            'card_exp_month' => 12,
            'card_exp_year' => (int)date('Y') + 2,
            'card_cvc' => '123',
            'card_holder' => 'Test Cardholder'
        ];

        $res = StripePaymentService::processAppointmentPayment($appId, $cardData);
        $this->assert('El procesamiento de pago de cita con Stripe debe retornar éxito', $res['success'] === true);
        $this->assert('La respuesta debe contener un ID de transacción de Stripe válido', !empty($res['transaction_id']) && str_starts_with($res['transaction_id'], 'pi_'));

        $updatedApp = Appointment::findById($appId);
        $this->assert('El estado de pago de la cita en BD debe actualizarse a "pagado"', $updatedApp['payment_status'] === 'pagado');
        $this->assert('El método de pago en BD debe quedar establecido en "stripe"', $updatedApp['payment_method'] === 'stripe');
        $this->assert('La cita debe guardar la referencia del PaymentIntent en stripe_payment_intent_id', !empty($updatedApp['stripe_payment_intent_id']));

        // Limpiar registro de prueba
        $db->prepare("DELETE FROM payments WHERE appointment_id = ?")->execute([$appId]);
        $db->prepare("DELETE FROM appointments WHERE id = ?")->execute([$appId]);
    }

    private function testDoublePaymentPrevention(): void {
        $db = Database::getConnection();
        $services = Service::getAll(true);
        $prof = Professional::getAll(false)[0];
        $client = Client::findOrCreate('Test Double Payment', 'test_double@miraclespa.com', '+15550008888');

        $service = $services[0];
        $code = 'MRC-' . date('Y') . '-DBL' . rand(100, 999);
        $token = bin2hex(random_bytes(24));
        $date = date('Y-m-d', strtotime('+8 days'));

        $stmt = $db->prepare("INSERT INTO appointments 
            (code, client_id, service_id, professional_id, date, start_time, end_time, duration_minutes, buffer_minutes, price, status, payment_status, payment_method, manage_token)
            VALUES (?, ?, ?, ?, ?, '14:00:00', '15:00:00', ?, 15, ?, 'confirmada', 'pagado', 'stripe', ?)");
        $stmt->execute([$code, $client['id'], $service['id'], $prof['id'], $date, $service['duration_minutes'], $service['price'], $token]);
        $appId = (int)$db->lastInsertId();

        $res = StripePaymentService::processAppointmentPayment($appId, [
            'card_number' => '4242424242424242',
            'card_exp_month' => 12,
            'card_exp_year' => 2030,
            'card_cvc' => '123'
        ]);

        $this->assert('Debe rechazar un segundo cobro en una cita que ya figura con estado "pagado"', $res['success'] === false);

        $db->prepare("DELETE FROM appointments WHERE id = ?")->execute([$appId]);
    }

    private function testStripeRefundWorkflow(): void {
        $db = Database::getConnection();
        $services = Service::getAll(true);
        $prof = Professional::getAll(false)[0];
        $client = Client::findOrCreate('Test Refund', 'test_refund@miraclespa.com', '+15550007777');

        $service = $services[0];
        $code = 'MRC-' . date('Y') . '-RFD' . rand(100, 999);
        $token = bin2hex(random_bytes(24));
        $date = date('Y-m-d', strtotime('+9 days'));

        $stmt = $db->prepare("INSERT INTO appointments 
            (code, client_id, service_id, professional_id, date, start_time, end_time, duration_minutes, buffer_minutes, price, status, payment_status, payment_method, manage_token)
            VALUES (?, ?, ?, ?, ?, '16:00:00', '17:00:00', ?, 15, ?, 'confirmada', 'pendiente', 'en_sede', ?)");
        $stmt->execute([$code, $client['id'], $service['id'], $prof['id'], $date, $service['duration_minutes'], $service['price'], $token]);
        $appId = (int)$db->lastInsertId();

        // 1. Pagar con Stripe
        StripePaymentService::processAppointmentPayment($appId, [
            'card_number' => '4242424242424242',
            'card_exp_month' => 11,
            'card_exp_year' => 2029,
            'card_cvc' => '999'
        ]);

        // 2. Reembolsar pago
        $refundRes = StripePaymentService::refundPayment($appId, 'Cancelación voluntaria por cliente');
        $this->assert('El reembolso de Stripe debe ejecutarse con éxito', $refundRes['success'] === true);

        $app = Appointment::findById($appId);
        $this->assert('El estado de pago tras el reembolso debe transicionar a "reembolsado"', $app['payment_status'] === 'reembolsado');

        // Limpiar
        $db->prepare("DELETE FROM payments WHERE appointment_id = ?")->execute([$appId]);
        $db->prepare("DELETE FROM appointments WHERE id = ?")->execute([$appId]);
    }
}
