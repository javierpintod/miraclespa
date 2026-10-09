<?php
/**
 * MIRACLE SPA - Servicio de Correo Electrónico
 * Soporta modo Simulado (con visor interactivo), SMTP nativo y PHP mail()
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Models/EmailLog.php';

class MailerService {
    /**
     * Envía correo de confirmación de cita con botones directos para reprogramar o cancelar
     */
    public static function sendBookingConfirmation(array $appointment): array {
        $baseUrl = AppConfig::getBaseUrl();
        $spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
        $spaAddress = AppConfig::getSetting('spa_address', 'Av. Las Palmas 450, Centro de Bienestar');
        $spaPhone = AppConfig::getSetting('spa_phone', '+1 (555) 789-2345');
        $minCancelHours = AppConfig::getSetting('min_cancel_hours', '4');

        $rescheduleUrl = $baseUrl . "/gestionar-cita.php?token=" . urlencode($appointment['manage_token']) . "&action=reprogramar";
        $cancelUrl = $baseUrl . "/gestionar-cita.php?token=" . urlencode($appointment['manage_token']) . "&action=cancelar";
        $viewUrl = $baseUrl . "/gestionar-cita.php?token=" . urlencode($appointment['manage_token']);

        $formattedDate = self::formatDateSpanish($appointment['date']);
        $startTime12 = date('h:i A', strtotime($appointment['start_time']));
        $endTime12 = date('h:i A', strtotime($appointment['end_time']));

        $subject = "Confirmación de Cita en {$spaName} [{$appointment['code']}]";

        $html = self::buildBaseTemplate(
            title: "¡Tu Cita está Confirmada!",
            preheader: "Detalles de tu cita para {$appointment['service_name']} el {$formattedDate}",
            content: "
                <p style='color: #4a5568; font-size: 16px; line-height: 1.6; margin-top: 0;'>
                    Estimad@(a) <strong>" . htmlspecialchars($appointment['client_name']) . "</strong>,
                </p>
                <p style='color: #4a5568; font-size: 15px; line-height: 1.6;'>
                    Hemos reservado tu espacio exclusivo en <strong>" . htmlspecialchars($spaName) . "</strong>. Nuestro equipo de terapeutas y estilistas está listo para brindarte una experiencia transformadora.
                </p>

                <!-- Tarjeta de Detalles de Cita -->
                <div style='background: #f7faf8; border: 1px solid #e2e8f0; border-left: 4px solid #165b40; border-radius: 8px; padding: 20px; margin: 24px 0;'>
                    <div style='font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #718096; margin-bottom: 8px;'>Código de Reserva</div>
                    <div style='font-size: 20px; font-weight: 700; color: #1a202c; font-family: monospace; letter-spacing: 0.5px; margin-bottom: 16px;'>" . htmlspecialchars($appointment['code']) . "</div>

                    <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                        <tr>
                            <td style='padding: 8px 0; color: #718096; width: 35%;'>Servicio:</td>
                            <td style='padding: 8px 0; color: #1a202c; font-weight: 600;'>" . htmlspecialchars($appointment['service_name']) . " (" . (int)$appointment['duration_minutes'] . " min)</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #718096;'>Especialista:</td>
                            <td style='padding: 8px 0; color: #165b40; font-weight: 600;'>" . htmlspecialchars($appointment['professional_name']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #718096;'>Fecha:</td>
                            <td style='padding: 8px 0; color: #1a202c; font-weight: 600;'>" . $formattedDate . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #718096;'>Horario:</td>
                            <td style='padding: 8px 0; color: #1a202c; font-weight: 600;'>" . $startTime12 . " - " . $endTime12 . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #718096;'>Inversión:</td>
                            <td style='padding: 8px 0; color: #165b40; font-weight: 700; font-size: 16px;'>$" . number_format((float)$appointment['price'], 2) . " USD</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #718096;'>Ubicación:</td>
                            <td style='padding: 8px 0; color: #1a202c;'>" . htmlspecialchars($spaAddress) . "</td>
                        </tr>
                    </table>
                </div>

                <!-- Botones de Acción (Reprogramar / Cancelar) -->
                <div style='margin: 28px 0 20px 0; text-align: center;'>
                    <a href='" . htmlspecialchars($viewUrl) . "' style='display: inline-block; background-color: #165b40; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 4px;'>
                        Ver Mi Cita
                    </a>
                    <a href='" . htmlspecialchars($rescheduleUrl) . "' style='display: inline-block; background-color: #d97706; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 4px;'>
                        Reprogramar Cita
                    </a>
                    <a href='" . htmlspecialchars($cancelUrl) . "' style='display: inline-block; background-color: #ffffff; color: #dc2626; border: 1px solid #dc2626; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 4px;'>
                        Cancelar Cita
                    </a>
                </div>

                <div style='background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 14px; margin-top: 20px; font-size: 13px; color: #92400e; line-height: 1.5;'>
                    <strong>Políticas de Bienestar:</strong> Te sugerimos llegar 10 minutos antes para relajarte con nuestra infusión de bienvenida. Las cancelaciones o reprogramaciones deben realizarse con al menos <strong>{$minCancelHours} horas de anticipación</strong>.
                </div>
            "
        );

        return self::dispatch(
            to: $appointment['client_email'],
            name: $appointment['client_name'],
            subject: $subject,
            htmlBody: $html,
            type: 'confirmacion',
            appointmentId: (int)$appointment['id']
        );
    }

    /**
     * Envía correo de cita reprogramada
     */
    public static function sendRescheduleConfirmation(array $appointment, string $oldDate, string $oldTime): array {
        $baseUrl = AppConfig::getBaseUrl();
        $spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
        $formattedNewDate = self::formatDateSpanish($appointment['date']);
        $newTime12 = date('h:i A', strtotime($appointment['start_time']));
        $viewUrl = $baseUrl . "/gestionar-cita.php?token=" . urlencode($appointment['manage_token']);

        $subject = "Cita Reprogramada con Éxito - {$spaName} [{$appointment['code']}]";

        $html = self::buildBaseTemplate(
            title: "Cita Reprogramada",
            preheader: "Tu cita ha sido actualizada para el {$formattedNewDate}",
            content: "
                <p style='color: #4a5568; font-size: 16px; line-height: 1.6; margin-top: 0;'>
                    Hola <strong>" . htmlspecialchars($appointment['client_name']) . "</strong>,
                </p>
                <p style='color: #4a5568; font-size: 15px; line-height: 1.6;'>
                    Hemos actualizado la fecha y horario de tu cita en <strong>" . htmlspecialchars($spaName) . "</strong> según tu solicitud.
                </p>

                <div style='background: #fffdf5; border: 1px solid #fde68a; border-left: 4px solid #d97706; border-radius: 8px; padding: 20px; margin: 24px 0;'>
                    <div style='font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 12px;'>Nuevo Horario Asignado:</div>
                    <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                        <tr>
                            <td style='padding: 6px 0; color: #718096;'>Servicio:</td>
                            <td style='padding: 6px 0; color: #1a202c; font-weight: 600;'>" . htmlspecialchars($appointment['service_name']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #718096;'>Especialista:</td>
                            <td style='padding: 6px 0; color: #165b40; font-weight: 600;'>" . htmlspecialchars($appointment['professional_name']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #718096;'>Nueva Fecha:</td>
                            <td style='padding: 6px 0; color: #1a202c; font-weight: 600;'>" . $formattedNewDate . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #718096;'>Nuevo Horario:</td>
                            <td style='padding: 6px 0; color: #1a202c; font-weight: 600;'>" . $newTime12 . "</td>
                        </tr>
                    </table>
                </div>

                <div style='text-align: center; margin: 25px 0;'>
                    <a href='" . htmlspecialchars($viewUrl) . "' style='display: inline-block; background-color: #165b40; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px;'>
                        Ver Mi Cita Actualizada
                    </a>
                </div>
            "
        );

        return self::dispatch(
            to: $appointment['client_email'],
            name: $appointment['client_name'],
            subject: $subject,
            htmlBody: $html,
            type: 'reprogramacion',
            appointmentId: (int)$appointment['id']
        );
    }

    /**
     * Envía correo de cancelación de cita
     */
    public static function sendCancellationConfirmation(array $appointment, string $reason = ''): array {
        $baseUrl = AppConfig::getBaseUrl();
        $spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
        $formattedDate = self::formatDateSpanish($appointment['date']);
        $bookNewUrl = $baseUrl . "/reservar.php";

        $subject = "Cita Cancelada - {$spaName} [{$appointment['code']}]";

        $html = self::buildBaseTemplate(
            title: "Cita Cancelada",
            preheader: "Tu cita del {$formattedDate} ha sido cancelada",
            content: "
                <p style='color: #4a5568; font-size: 16px; line-height: 1.6; margin-top: 0;'>
                    Hola <strong>" . htmlspecialchars($appointment['client_name']) . "</strong>,
                </p>
                <p style='color: #4a5568; font-size: 15px; line-height: 1.6;'>
                    Te confirmamos que tu cita para <strong>" . htmlspecialchars($appointment['service_name']) . "</strong> programada para el <strong>{$formattedDate}</strong> ha sido cancelada satisfactoriamente.
                </p>

                " . ($reason ? "<p style='color: #718096; font-size: 14px; background: #f7fafc; padding: 12px; border-radius: 6px;'><em>Motivo registrado: " . htmlspecialchars($reason) . "</em></p>" : "") . "

                <p style='color: #4a5568; font-size: 15px; line-height: 1.6;'>
                    Estaremos encantados de recibirte en otra ocasión cuando desees consentirte y recargar energías.
                </p>

                <div style='text-align: center; margin: 25px 0;'>
                    <a href='" . htmlspecialchars($bookNewUrl) . "' style='display: inline-block; background-color: #165b40; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px;'>
                        Reservar Nueva Cita
                    </a>
                </div>
            "
        );

        return self::dispatch(
            to: $appointment['client_email'],
            name: $appointment['client_name'],
            subject: $subject,
            htmlBody: $html,
            type: 'cancelacion',
            appointmentId: (int)$appointment['id']
        );
    }

    /**
     * Enrutador de entrega según controlador configurado (simulado, smtp, mail)
     */
    public static function dispatch(
        string $to,
        string $name,
        string $subject,
        string $htmlBody,
        string $type = 'confirmacion',
        ?int $appointmentId = null
    ): array {
        $driver = AppConfig::getSetting('mail_driver', 'simulated');
        $status = 'simulado';
        $errorMessage = null;

        if ($driver === 'smtp') {
            $smtpResult = self::sendViaSmtp($to, $name, $subject, $htmlBody);
            if ($smtpResult['success']) {
                $status = 'enviado';
            } else {
                $status = 'fallido';
                $errorMessage = $smtpResult['error'];
            }
        } elseif ($driver === 'mail') {
            $from = AppConfig::getSetting('smtp_from_email', 'no-reply@miraclespa.com');
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . AppConfig::getSetting('spa_name', 'Miracle Spa') . " <{$from}>\r\n";

            $sent = @mail($to, $subject, $htmlBody, $headers);
            $status = $sent ? 'enviado' : 'fallido';
            if (!$sent) {
                $errorMessage = "La función mail() de PHP no pudo entregar el correo en el entorno local.";
            }
        } else {
            // Modo 'simulated' (predeterminado para XAMPP local)
            $status = 'simulado';
        }

        // Registrar siempre en la base de datos para trazabilidad y visualización en el panel
        $logId = EmailLog::create([
            'appointment_id' => $appointmentId,
            'recipient_email' => $to,
            'recipient_name' => $name,
            'subject' => $subject,
            'email_type' => $type,
            'status' => $status,
            'body_html' => $htmlBody,
            'error_message' => $errorMessage
        ]);

        return [
            'success' => ($status !== 'fallido'),
            'status' => $status,
            'log_id' => $logId,
            'error' => $errorMessage
        ];
    }

    /**
     * Cliente SMTP liviano nativo en sockets para envío directo sin dependencias externas pesadas
     */
    public static function sendViaSmtp(string $to, string $toName, string $subject, string $htmlBody): array {
        $host = AppConfig::getSetting('smtp_host', 'localhost');
        $port = (int)AppConfig::getSetting('smtp_port', '25');
        $user = AppConfig::getSetting('smtp_user', '');
        $pass = AppConfig::getSetting('smtp_pass', '');
        $secure = AppConfig::getSetting('smtp_secure', 'tls');
        $fromEmail = AppConfig::getSetting('smtp_from_email', 'citas@miraclespa.com');
        $fromName = AppConfig::getSetting('smtp_from_name', 'Miracle Spa Sanctuary');

        $protocol = ($secure === 'ssl') ? 'ssl://' : '';
        $timeout = 10;

        $socket = @fsockopen($protocol . $host, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            return ['success' => false, 'error' => "No se pudo conectar al host SMTP {$host}:{$port}. Error: {$errstr} ({$errno})"];
        }

        $read = function() use ($socket) {
            $data = '';
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') break;
            }
            return $data;
        };

        $write = function(string $cmd) use ($socket) {
            fputs($socket, $cmd . "\r\n");
        };

        $read(); // Bienvenida SMTP

        $write("EHLO " . gethostname());
        $ehlo = $read();

        if ($secure === 'tls') {
            $write("STARTTLS");
            $starttls = $read();
            if (substr($starttls, 0, 3) !== '220') {
                fclose($socket);
                return ['success' => false, 'error' => "STARTTLS falló: " . trim($starttls)];
            }
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $write("EHLO " . gethostname());
            $read();
        }

        if (!empty($user) && !empty($pass)) {
            $write("AUTH LOGIN");
            $authResp = $read();
            if (substr($authResp, 0, 3) !== '334') {
                fclose($socket);
                return ['success' => false, 'error' => "Error en AUTH LOGIN: " . trim($authResp)];
            }
            $write(base64_encode($user));
            $read();
            $write(base64_encode($pass));
            $authOk = $read();
            if (substr($authOk, 0, 3) !== '235') {
                fclose($socket);
                return ['success' => false, 'error' => "Autenticación SMTP rechazada: " . trim($authOk)];
            }
        }

        $write("MAIL FROM: <{$fromEmail}>");
        $mailFrom = $read();
        if (substr($mailFrom, 0, 3) !== '250') {
            fclose($socket);
            return ['success' => false, 'error' => "MAIL FROM rechazado: " . trim($mailFrom)];
        }

        $write("RCPT TO: <{$to}>");
        $rcptTo = $read();
        if (substr($rcptTo, 0, 3) !== '250') {
            fclose($socket);
            return ['success' => false, 'error' => "RCPT TO rechazado: " . trim($rcptTo)];
        }

        $write("DATA");
        $dataResp = $read();
        if (substr($dataResp, 0, 3) !== '354') {
            fclose($socket);
            return ['success' => false, 'error' => "DATA rechazado: " . trim($dataResp)];
        }

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $headers .= "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$to}>\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "Date: " . date('r') . "\r\n";

        $message = $headers . "\r\n" . $htmlBody . "\r\n.\r\n";
        fputs($socket, $message);
        $finalResp = $read();
        $write("QUIT");
        fclose($socket);

        if (substr($finalResp, 0, 3) === '250') {
            return ['success' => true];
        }

        return ['success' => false, 'error' => "Fallo al enviar mensaje: " . trim($finalResp)];
    }

    /**
     * Construye la plantilla HTML base con diseño editorial de lujo
     */
    private static function buildBaseTemplate(string $title, string $preheader, string $content): string {
        $spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
        $spaAddress = AppConfig::getSetting('spa_address', 'Av. Las Palmas 450, Centro de Bienestar');
        $spaPhone = AppConfig::getSetting('spa_phone', '+1 (555) 789-2345');

        return "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>" . htmlspecialchars($title) . "</title>
            <style>
                body { margin: 0; padding: 0; background-color: #f3f5f3; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
            </style>
        </head>
        <body style='margin: 0; padding: 20px; background-color: #f3f5f3;'>
            <div style='display: none; max-height: 0px; overflow: hidden;'>" . htmlspecialchars($preheader) . "</div>
            <table role='presentation' width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td align='center'>
                        <table role='presentation' width='600' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; width: 100%; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);'>
                            <!-- Cabecera -->
                            <tr>
                                <td style='background: linear-gradient(135deg, #103b2b 0%, #1a553f 100%); padding: 32px 30px; text-align: center;'>
                                    <div style='font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #d4af37; margin-bottom: 6px; font-weight: 600;'>SANCTUARY OF WELLNESS</div>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 26px; font-weight: 300; letter-spacing: 0.5px;'>
                                        " . htmlspecialchars($spaName) . "
                                    </h1>
                                </td>
                            </tr>
                            <!-- Cuerpo -->
                            <tr>
                                <td style='padding: 36px 32px;'>
                                    <h2 style='color: #1a202c; font-size: 20px; font-weight: 600; margin-top: 0; margin-bottom: 18px;'>
                                        " . htmlspecialchars($title) . "
                                    </h2>
                                    {$content}
                                </td>
                            </tr>
                            <!-- Pie -->
                            <tr>
                                <td style='background-color: #fafbfc; border-top: 1px solid #edf2f7; padding: 24px 30px; text-align: center; font-size: 12px; color: #718096; line-height: 1.6;'>
                                    <div style='font-weight: 600; color: #4a5568; margin-bottom: 4px;'>" . htmlspecialchars($spaName) . "</div>
                                    <div>" . htmlspecialchars($spaAddress) . " &bull; Tel: " . htmlspecialchars($spaPhone) . "</div>
                                    <div style='margin-top: 10px; color: #a0aec0;'>Este correo fue generado automáticamente por el sistema de reservas de Miracle Spa.</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
    }

    private static function formatDateSpanish(string $dateStr): string {
        $timestamp = strtotime($dateStr);
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $diaSemana = $dias[date('w', $timestamp)];
        $dia = date('d', $timestamp);
        $mes = $meses[(int)date('m', $timestamp)];
        $anio = date('Y', $timestamp);

        return "{$diaSemana}, {$dia} de {$mes} de {$anio}";
    }
}
