/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { Appointment, NotificationEmail } from '../types';
import { SpaStorage } from './storage';

export const EmailService = {
  /**
   * Generates and dispatches a confirmation email for an appointment
   */
  sendBookingConfirmation(appointment: Appointment): NotificationEmail {
    const servicesListHtml = appointment.services
      .map(
        s => `
        <tr style="border-bottom: 1px solid #ebefea;">
          <td style="padding: 12px 0; font-family: 'Plus Jakarta Sans', sans-serif;">
            <strong style="color: #134230; font-size: 15px;">${s.name}</strong><br/>
            <span style="color: #717973; font-size: 13px;">${s.durationMinutes} min • ${s.categoryLabel}</span>
          </td>
          <td style="padding: 12px 0; text-align: right; color: #134230; font-weight: bold; font-size: 15px;">
            $${s.priceUSD} USD
          </td>
        </tr>`
      )
      .join('');

    const htmlContent = `
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Confirmación de Reserva - Aura Spa</title>
</head>
<body style="margin: 0; padding: 24px 0; background-color: #f6fbf5; font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; color: #181d1a;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e9e4;">
    <!-- Header -->
    <tr>
      <td style="background-color: #134230; padding: 36px 32px; text-align: center;">
        <span style="font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: #bceed3; font-weight: 600;">Santuario Holístico & Bienestar</span>
        <h1 style="color: #ffffff; font-family: 'Playfair Display', Georgia, serif; font-size: 28px; margin: 8px 0 0; font-weight: 500;">Aura Spa</h1>
      </td>
    </tr>

    <!-- Body -->
    <tr>
      <td style="padding: 36px 32px;">
        <p style="font-size: 16px; color: #414944; margin-top: 0;">Estimada/o <strong>${appointment.client.name}</strong>,</p>
        <p style="font-size: 15px; color: #414944; line-height: 1.6;">
          Tu cita ha sido confirmada y agendada exitosamente en nuestro santuario. Hemos reservado tu espacio y preparado a nuestro equipo para brindarte una experiencia reparadora y libre de estrés.
        </p>

        <!-- Ticket Card -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f5f0; border-radius: 16px; margin: 24px 0; padding: 20px;">
          <tr>
            <td style="padding: 8px 12px;">
              <span style="font-size: 11px; text-transform: uppercase; color: #717973; font-weight: 600; letter-spacing: 0.5px;">CÓDIGO DE RESERVA</span><br/>
              <strong style="font-size: 18px; color: #134230; font-family: monospace;">${appointment.code}</strong>
            </td>
            <td style="padding: 8px 12px; text-align: right;">
              <span style="font-size: 11px; text-transform: uppercase; color: #717973; font-weight: 600; letter-spacing: 0.5px;">ESTADO</span><br/>
              <span style="display: inline-block; background-color: #bceed3; color: #002114; font-size: 12px; font-weight: bold; padding: 3px 10px; border-radius: 9999px;">Confirmada</span>
            </td>
          </tr>
          <tr><td colspan="2" style="border-top: 1px dashed #c0c9c2; margin: 12px 0;"></td></tr>
          <tr>
            <td style="padding: 12px 12px;">
              <span style="font-size: 12px; color: #717973;">Fecha del Turno</span><br/>
              <strong style="color: #181d1a; font-size: 15px;">${appointment.date}</strong>
            </td>
            <td style="padding: 12px 12px; text-align: right;">
              <span style="font-size: 12px; color: #717973;">Horario</span><br/>
              <strong style="color: #7d562d; font-size: 15px;">${appointment.startTime} - ${appointment.endTime}</strong>
            </td>
          </tr>
          <tr>
            <td style="padding: 12px 12px;">
              <span style="font-size: 12px; color: #717973;">Especialista</span><br/>
              <strong style="color: #181d1a; font-size: 15px;">${appointment.assignedProfessionalName}</strong>
            </td>
            <td style="padding: 12px 12px; text-align: right;">
              <span style="font-size: 12px; color: #717973;">Lugar</span><br/>
              <strong style="color: #181d1a; font-size: 14px;">${appointment.location}</strong>
            </td>
          </tr>
        </table>

        <!-- Services Details -->
        <h3 style="font-size: 16px; color: #134230; border-bottom: 2px solid #ebefea; padding-bottom: 8px; margin-bottom: 0;">Detalle de Tratamientos</h3>
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
          ${servicesListHtml}
          <tr>
            <td style="padding: 16px 0; font-size: 15px; color: #181d1a;">
              <strong>Total a pagar en recepción:</strong><br/>
              <span style="font-size: 12px; color: #717973;">Aceptamos efectivo, tarjetas y transferencia</span>
            </td>
            <td style="padding: 16px 0; text-align: right; font-size: 20px; font-weight: bold; color: #134230;">
              $${appointment.totalPriceUSD} USD
            </td>
          </tr>
        </table>

        <!-- Hygiene buffer note -->
        <div style="background-color: #ffdcbd; color: #623f18; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 24px;">
          🌿 <strong>Protocolo de Calidad:</strong> Incluimos 20 minutos de higienización y esterilización previa sin costo, asegurando máxima pulcritud en cabina.
        </div>

        <!-- Cancellation & Reschedule Actions -->
        <h3 style="font-size: 15px; color: #134230; margin-bottom: 8px;">¿Necesitas hacer cambios?</h3>
        <p style="font-size: 13px; color: #717973; line-height: 1.5; margin-bottom: 18px;">
          Puedes reprogramar o cancelar tu cita de manera autónoma hasta con <strong>8 horas de anticipación</strong> sin ningún cargo.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="padding-right: 8px;" width="50%">
              <a href="#gestionar-cita-${appointment.code}" style="display: block; text-align: center; background-color: #2d5a46; color: #ffffff; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px;">
                Reprogramar Cita
              </a>
            </td>
            <td style="padding-left: 8px;" width="50%">
              <a href="#cancelar-cita-${appointment.code}" style="display: block; text-align: center; background-color: #e5e9e4; color: #ba1a1a; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px;">
                Cancelar Cita
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background-color: #f0f5f0; padding: 24px 32px; text-align: center; font-size: 12px; color: #717973; border-top: 1px solid #e5e9e4;">
        <p style="margin: 0 0 4px;"><strong>Aura Spa & Bienestar Sanctuary</strong></p>
        <p style="margin: 0;">Sede Las Palmas • Calle 18 #35-12, Suite 402 • Tel: +57 (4) 444-AURA</p>
      </td>
    </tr>
  </table>
</body>
</html>`;

    const email: NotificationEmail = {
      id: `email-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`,
      appointmentId: appointment.id,
      appointmentCode: appointment.code,
      recipientEmail: appointment.client.email,
      recipientName: appointment.client.name,
      subject: `Confirmación de Cita #${appointment.code} en Aura Spa`,
      type: 'confirmacion',
      sentAt: new Date().toISOString(),
      htmlContent,
      manageToken: appointment.manageToken,
      previewSnippet: `Tu cita #${appointment.code} para ${appointment.date} a las ${appointment.startTime} con ${appointment.assignedProfessionalName} ha sido confirmada.`,
    };

    SpaStorage.addEmail(email);
    return email;
  },

  /**
   * Generates and dispatches a rescheduled notice email
   */
  sendRescheduledNotification(appointment: Appointment, previousDate: string, previousTime: string): NotificationEmail {
    const htmlContent = `
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Reprogramación de Cita - Aura Spa</title></head>
<body style="margin: 0; padding: 24px 0; background-color: #f6fbf5; font-family: sans-serif; color: #181d1a;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; padding: 32px; border: 1px solid #e5e9e4;">
    <tr>
      <td style="text-align: center;">
        <h2 style="color: #134230; margin: 0 0 12px;">Tu cita ha sido reprogramada exitosamente</h2>
        <p style="color: #414944; font-size: 15px;">Hola ${appointment.client.name}, hemos actualizado la fecha y horario de tu reserva <strong>#${appointment.code}</strong>.</p>
        <div style="background-color: #f0f5f0; border-radius: 12px; padding: 16px; margin: 20px 0; text-align: left;">
          <p style="margin: 4px 0; color: #717973; font-size: 13px;">Turno anterior: <del>${previousDate} a las ${previousTime}</del></p>
          <p style="margin: 4px 0; color: #134230; font-size: 16px; font-weight: bold;">Nuevo turno: ${appointment.date} a las ${appointment.startTime}</p>
          <p style="margin: 4px 0; color: #717973; font-size: 14px;">Especialista: ${appointment.assignedProfessionalName}</p>
        </div>
      </td>
    </tr>
  </table>
</body>
</html>`;

    const email: NotificationEmail = {
      id: `email-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`,
      appointmentId: appointment.id,
      appointmentCode: appointment.code,
      recipientEmail: appointment.client.email,
      recipientName: appointment.client.name,
      subject: `Actualización: Tu cita #${appointment.code} fue reprogramada`,
      type: 'reprogramacion',
      sentAt: new Date().toISOString(),
      htmlContent,
      manageToken: appointment.manageToken,
      previewSnippet: `Tu cita #${appointment.code} ahora está agendada para el ${appointment.date} a las ${appointment.startTime}.`,
    };

    SpaStorage.addEmail(email);
    return email;
  },

  /**
   * Generates and dispatches a cancellation notice email
   */
  sendCancellationNotification(appointment: Appointment, reason: string): NotificationEmail {
    const htmlContent = `
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Cancelación de Cita - Aura Spa</title></head>
<body style="margin: 0; padding: 24px 0; background-color: #f6fbf5; font-family: sans-serif; color: #181d1a;">
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; padding: 32px; border: 1px solid #e5e9e4;">
    <tr>
      <td style="text-align: center;">
        <h2 style="color: #ba1a1a; margin: 0 0 12px;">Cita Cancelada</h2>
        <p style="color: #414944; font-size: 15px;">Hola ${appointment.client.name}, te confirmamos que la cita <strong>#${appointment.code}</strong> programada para el ${appointment.date} ha sido cancelada sin costo.</p>
        <p style="color: #717973; font-size: 13px;">Motivo registrado: "${reason}"</p>
        <p style="color: #414944; font-size: 14px; margin-top: 24px;">Esperamos recibirte pronto nuevamente para consentir tu bienestar.</p>
      </td>
    </tr>
  </table>
</body>
</html>`;

    const email: NotificationEmail = {
      id: `email-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`,
      appointmentId: appointment.id,
      appointmentCode: appointment.code,
      recipientEmail: appointment.client.email,
      recipientName: appointment.client.name,
      subject: `Cancelación confirmada para cita #${appointment.code} en Aura Spa`,
      type: 'cancelacion',
      sentAt: new Date().toISOString(),
      htmlContent,
      manageToken: appointment.manageToken,
      previewSnippet: `Tu cita #${appointment.code} programada para el ${appointment.date} ha sido cancelada sin recargo.`,
    };

    SpaStorage.addEmail(email);
    return email;
  },
};
