/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useMemo } from 'react';
import {
  Mail,
  Search,
  CheckCircle2,
  Clock,
  ArrowRight,
  ExternalLink,
  ChevronRight,
  Send,
  Eye,
  Info,
} from 'lucide-react';
import { NotificationEmail } from '../../types';

interface EmailInboxModalProps {
  emails: NotificationEmail[];
  selectedCode?: string;
  onNavigateToLookupWithCode: (code: string) => void;
  onClose?: () => void;
}

export const EmailInboxModal: React.FC<EmailInboxModalProps> = ({
  emails,
  selectedCode,
  onNavigateToLookupWithCode,
}) => {
  const [activeEmailId, setActiveEmailId] = useState<string>(
    selectedCode
      ? emails.find(e => e.appointmentCode === selectedCode)?.id || emails[0]?.id || ''
      : emails[0]?.id || ''
  );
  const [search, setSearch] = useState(selectedCode || '');

  // Filtered emails
  const filteredEmails = useMemo(() => {
    return emails.filter(e => {
      const q = search.toLowerCase();
      return (
        e.subject.toLowerCase().includes(q) ||
        e.recipientEmail.toLowerCase().includes(q) ||
        e.appointmentCode.toLowerCase().includes(q) ||
        e.recipientName.toLowerCase().includes(q)
      );
    });
  }, [emails, search]);

  const activeEmail = useMemo(() => {
    return emails.find(e => e.id === activeEmailId) || filteredEmails[0] || null;
  }, [emails, activeEmailId, filteredEmails]);

  return (
    <div className="w-full pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div>
          <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
            Servidor de Notificaciones
          </span>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold mt-0.5">
            Bandeja de Correos Electrónicos Simulados
          </h1>
          <p className="text-xs sm:text-sm text-[#414944]">
            Visualiza en tiempo real los correos HTML enviados a los clientes con sus tokens y opciones de reprogramación.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <span className="px-3 py-1.5 rounded-full bg-[#bceed3] text-[#134230] text-xs font-bold flex items-center gap-1.5">
            <CheckCircle2 className="w-3.5 h-3.5" />
            <span>Motor de Plantillas Activo ({emails.length} enviados)</span>
          </span>
        </div>
      </div>

      {emails.length === 0 ? (
        <div className="my-12 p-12 bg-white rounded-3xl border border-[#e5e9e4] text-center text-[#717973]">
          <Mail className="w-12 h-12 mx-auto text-[#c0c9c2] mb-3" />
          <h3 className="font-semibold text-base text-[#181d1a]">Aún no se han generado correos</h3>
          <p className="text-xs mt-1">Realiza una reserva en la pestaña "Reservar Cita" para ver el correo generado aquí.</p>
        </div>
      ) : (
        <div className="my-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
          {/* Email List (4 cols) */}
          <div className="lg:col-span-4 bg-white rounded-3xl border border-[#e5e9e4] shadow-sm overflow-hidden flex flex-col h-[700px]">
            {/* Search */}
            <div className="p-3.5 border-b border-[#f0f5f0]">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-3 text-[#717973]" />
                <input
                  type="text"
                  value={search}
                  onChange={e => setSearch(e.target.value)}
                  placeholder="Buscar por código, email..."
                  className="w-full pl-9 pr-3 py-2 rounded-xl bg-[#f0f5f0] border border-[#dfe4df] text-xs text-[#181d1a] focus:outline-none focus:bg-white"
                />
              </div>
            </div>

            {/* List */}
            <div className="overflow-y-auto flex-1 divide-y divide-[#f0f5f0]">
              {filteredEmails.map(mail => {
                const isSelected = activeEmail?.id === mail.id;

                return (
                  <div
                    key={mail.id}
                    onClick={() => setActiveEmailId(mail.id)}
                    className={`p-4 cursor-pointer transition-all flex flex-col gap-1 text-xs ${
                      isSelected ? 'bg-[#f0f5f0] border-l-4 border-[#2d5a46]' : 'hover:bg-[#f6fbf5]'
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <span className="font-mono font-bold text-[#134230] text-[11px]">
                        #{mail.appointmentCode}
                      </span>
                      <span className="text-[10px] text-[#717973]">
                        {new Date(mail.sentAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </div>

                    <strong className="text-[#181d1a] truncate font-semibold block">
                      {mail.subject}
                    </strong>

                    <span className="text-[#414944] text-[11px] truncate">
                      Para: {mail.recipientName} ({mail.recipientEmail})
                    </span>

                    <p className="text-[#717973] text-[11px] line-clamp-1 mt-0.5">
                      {mail.previewSnippet}
                    </p>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Email Preview Frame (8 cols) */}
          <div className="lg:col-span-8 bg-white rounded-3xl border border-[#e5e9e4] shadow-sm flex flex-col h-[700px] overflow-hidden">
            {activeEmail ? (
              <>
                {/* Email Meta Header */}
                <div className="p-4 sm:p-5 border-b border-[#e5e9e4] bg-[#f0f5f0]/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <span className="font-mono text-xs font-bold text-[#134230] bg-[#bceed3] px-2 py-0.5 rounded-full">
                        Cita #{activeEmail.appointmentCode}
                      </span>
                      <span className="text-xs text-[#717973]">
                        Enviado el {new Date(activeEmail.sentAt).toLocaleString()}
                      </span>
                    </div>
                    <h3 className="font-semibold text-base text-[#181d1a]">{activeEmail.subject}</h3>
                    <span className="text-xs text-[#414944]">
                      De: <strong>Aura Spa Sanctuary &lt;confirmaciones@auraspa.com&gt;</strong> para{' '}
                      <strong>{activeEmail.recipientName} &lt;{activeEmail.recipientEmail}&gt;</strong>
                    </span>
                  </div>

                  <button
                    onClick={() => onNavigateToLookupWithCode(activeEmail.appointmentCode)}
                    className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] shadow-sm transition-colors whitespace-nowrap self-start sm:self-auto"
                  >
                    <span>Abrir en Gestión de Cita</span>
                    <ExternalLink className="w-3.5 h-3.5" />
                  </button>
                </div>

                {/* Rendered HTML iframe / preview */}
                <div className="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#f6fbf5]">
                  <div
                    className="email-render-container shadow-md rounded-2xl overflow-hidden bg-white max-w-xl mx-auto"
                    dangerouslySetInnerHTML={{ __html: activeEmail.htmlContent }}
                  />
                </div>
              </>
            ) : (
              <div className="m-auto text-center text-[#717973]">
                <p>Selecciona un correo para visualizar el contenido</p>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};
