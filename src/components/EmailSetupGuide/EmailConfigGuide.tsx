/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Mail,
  Copy,
  Check,
  Server,
  Key,
  Shield,
  Send,
  Code2,
} from 'lucide-react';

export const EmailConfigGuide: React.FC = () => {
  const [copiedIndex, setCopiedIndex] = useState<number | null>(null);

  const copyToClipboard = (text: string, index: number) => {
    navigator.clipboard.writeText(text);
    setCopiedIndex(index);
    setTimeout(() => setCopiedIndex(null), 2500);
  };

  const resendCode = `// server/emailRouter.ts - Integración con Resend (Recomendado)
import { Resend } from 'resend';

const resend = new Resend(process.env.RESEND_API_KEY);

export async function sendRealConfirmationEmail(appointment) {
  const { data, error } = await resend.emails.send({
    from: 'Aura Spa <reservas@auraspa.com>',
    to: [appointment.client.email],
    subject: \`Confirmación de Cita #\${appointment.code} en Aura Spa\`,
    html: appointment.htmlContent, // Plantilla generada por EmailService
  });

  if (error) {
    console.error('Error enviando correo con Resend:', error);
    throw error;
  }
  return data;
}`;

  const nodemailerCode = `// server/mailer.ts - Integración con Nodemailer / SMTP
import nodemailer from 'nodemailer';

const transporter = nodemailer.createTransport({
  host: process.env.SMTP_HOST || 'smtp.sendgrid.net',
  port: Number(process.env.SMTP_PORT) || 587,
  secure: false, // true para 465, false para 587
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS,
  },
});

export async function sendSmtpEmail({ to, subject, html }) {
  const info = await transporter.sendMail({
    from: '"Aura Spa Sanctuary" <notificaciones@auraspa.com>',
    to,
    subject,
    html,
  });
  return info.messageId;
}`;

  const envExampleCode = `# Variables de Entorno para Correo en Producción (.env)
# 1. Si usas Resend:
RESEND_API_KEY="re_1234567890abcdef"

# 2. Si usas SMTP (SendGrid, Mailgun, Amazon SES o Gmail App Password):
SMTP_HOST="smtp.resend.com"
SMTP_PORT="587"
SMTP_USER="resend"
SMTP_PASS="re_1234567890abcdef"
FROM_EMAIL="Aura Spa <reservas@tudominio.com>"`;

  return (
    <div className="w-full pb-20 max-w-4xl mx-auto px-4 sm:px-6 pt-6">
      <div className="py-6 border-b border-[#e5e9e4]">
        <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
          Despliegue & Producción
        </span>
        <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold mt-0.5">
          Guía de Instalación y Configuración del Correo Electrónico
        </h1>
        <p className="text-xs sm:text-sm text-[#414944] mt-1">
          La aplicación incluye un motor generador de plantillas HTML responsivas y una bandeja simulada en vivo. Sigue estos pasos para conectar el envío real a clientes.
        </p>
      </div>

      <div className="my-8 space-y-8">
        {/* Step 1: Environment Variables */}
        <section className="bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full bg-[#2d5a46] text-white flex items-center justify-center font-bold text-xs">
                1
              </div>
              <h2 className="font-semibold text-base text-[#134230]">
                Configurar Variables de Entorno (.env)
              </h2>
            </div>
            <button
              onClick={() => copyToClipboard(envExampleCode, 1)}
              className="text-xs text-[#717973] hover:text-[#134230] flex items-center gap-1 font-medium"
            >
              {copiedIndex === 1 ? <Check className="w-3.5 h-3.5 text-[#2e7d52]" /> : <Copy className="w-3.5 h-3.5" />}
              <span>{copiedIndex === 1 ? 'Copiado' : 'Copiar'}</span>
            </button>
          </div>

          <p className="text-xs text-[#414944]">
            Crea un archivo <code className="bg-[#f0f5f0] px-1.5 py-0.5 rounded font-mono text-[#134230]">.env</code> en la raíz del proyecto con las credenciales de tu proveedor preferido:
          </p>

          <pre className="p-4 rounded-2xl bg-[#181d1a] text-[#bceed3] text-xs font-mono overflow-x-auto">
            {envExampleCode}
          </pre>
        </section>

        {/* Step 2: Resend Integration */}
        <section className="bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full bg-[#2d5a46] text-white flex items-center justify-center font-bold text-xs">
                2
              </div>
              <h2 className="font-semibold text-base text-[#134230]">
                Opción A: Conexión con Resend (Recomendada)
              </h2>
            </div>
            <button
              onClick={() => copyToClipboard(resendCode, 2)}
              className="text-xs text-[#717973] hover:text-[#134230] flex items-center gap-1 font-medium"
            >
              {copiedIndex === 2 ? <Check className="w-3.5 h-3.5 text-[#2e7d52]" /> : <Copy className="w-3.5 h-3.5" />}
              <span>{copiedIndex === 2 ? 'Copiado' : 'Copiar'}</span>
            </button>
          </div>

          <p className="text-xs text-[#414944]">
            Instala el paquete con <code className="bg-[#f0f5f0] px-1.5 py-0.5 rounded font-mono text-[#134230]">npm install resend</code> e implementa el despachador:
          </p>

          <pre className="p-4 rounded-2xl bg-[#181d1a] text-[#f0f5f0] text-xs font-mono overflow-x-auto">
            {resendCode}
          </pre>
        </section>

        {/* Step 3: SMTP / Nodemailer */}
        <section className="bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full bg-[#2d5a46] text-white flex items-center justify-center font-bold text-xs">
                3
              </div>
              <h2 className="font-semibold text-base text-[#134230]">
                Opción B: Conexión con SMTP Genérico / Nodemailer
              </h2>
            </div>
            <button
              onClick={() => copyToClipboard(nodemailerCode, 3)}
              className="text-xs text-[#717973] hover:text-[#134230] flex items-center gap-1 font-medium"
            >
              {copiedIndex === 3 ? <Check className="w-3.5 h-3.5 text-[#2e7d52]" /> : <Copy className="w-3.5 h-3.5" />}
              <span>{copiedIndex === 3 ? 'Copiado' : 'Copiar'}</span>
            </button>
          </div>

          <p className="text-xs text-[#414944]">
            Compatible con SendGrid, Amazon SES, Mailgun o servidores corporativos:
          </p>

          <pre className="p-4 rounded-2xl bg-[#181d1a] text-[#f0f5f0] text-xs font-mono overflow-x-auto">
            {nodemailerCode}
          </pre>
        </section>

        {/* Deliverability Checklist */}
        <section className="p-6 rounded-3xl bg-[#f0f5f0] border border-[#e5e9e4] space-y-3 text-xs text-[#414944]">
          <h3 className="font-semibold text-sm text-[#134230] flex items-center gap-2">
            <Shield className="w-4 h-4 text-[#7d562d]" />
            <span>Recomendaciones para Evitar la Carpeta de Spam</span>
          </h3>
          <ul className="space-y-1.5 list-disc list-inside">
            <li>Configura registros <strong>SPF</strong> y <strong>DKIM</strong> en tu proveedor de dominio (DNS).</li>
            <li>Utiliza un subdominio dedicado (ej. <code className="font-mono text-[#134230]">mail.tudominio.com</code>).</li>
            <li>Nuestras plantillas ya incluyen tablas inline con contrastes WCAG e información de contacto del spa al pie.</li>
          </ul>
        </section>
      </div>
    </div>
  );
};
