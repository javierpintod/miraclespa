/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Search,
  Calendar,
  Clock,
  User,
  MapPin,
  CheckCircle,
  XCircle,
  RotateCcw,
  AlertTriangle,
  Download,
  Mail,
  ShieldAlert,
} from 'lucide-react';
import { Appointment, Professional, Service } from '../../types';
import {
  canModifyAppointment,
  isTherapistAvailable,
  calculateEndTime,
} from '../../services/availability';
import { SpaStorage } from '../../services/storage';
import { EmailService } from '../../services/emailService';

interface ManageReservationProps {
  appointments: Appointment[];
  professionals: Professional[];
  services: Service[];
  onAppointmentsUpdated: () => void;
  onOpenEmail: (code: string) => void;
  initialCode?: string;
}

export const ManageReservation: React.FC<ManageReservationProps> = ({
  appointments,
  professionals,
  services,
  onAppointmentsUpdated,
  onOpenEmail,
  initialCode = '',
}) => {
  const [searchCode, setSearchCode] = useState(initialCode);
  const [searchedAppointment, setSearchedAppointment] = useState<Appointment | null>(
    initialCode ? appointments.find(a => a.code.toUpperCase() === initialCode.toUpperCase()) || null : null
  );
  const [hasSearched, setHasSearched] = useState(Boolean(initialCode));

  // Reschedule state
  const [isRescheduling, setIsRescheduling] = useState(false);
  const [newDate, setNewDate] = useState('2025-10-23');
  const [newTime, setNewTime] = useState('11:00');
  const [rescheduleError, setRescheduleError] = useState<string | null>(null);

  // Cancel state
  const [isCancelling, setIsCancelling] = useState(false);
  const [cancelReason, setCancelReason] = useState('Cambio imprevisto de agenda');
  const [cancelError, setCancelError] = useState<string | null>(null);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setHasSearched(true);
    const clean = searchCode.trim().toUpperCase();

    const found = appointments.find(
      a => a.code.toUpperCase() === clean || a.client.email.toLowerCase() === searchCode.trim().toLowerCase()
    );

    setSearchedAppointment(found || null);
    setIsRescheduling(false);
    setIsCancelling(false);
  };

  // Perform self-service reschedule
  const handleExecuteReschedule = () => {
    if (!searchedAppointment) return;
    setRescheduleError(null);

    // Check policy 8 hours
    const policy = canModifyAppointment(searchedAppointment);
    if (!policy.allowed) {
      setRescheduleError(policy.reason || 'No se puede reprogramar con menos de 8 horas.');
      return;
    }

    // Check collision
    const isFree = isTherapistAvailable(
      searchedAppointment.professionalId,
      newDate,
      newTime,
      searchedAppointment.totalDurationMinutes,
      appointments,
      searchedAppointment.id
    );

    if (!isFree) {
      setRescheduleError(
        `El terapeuta ${searchedAppointment.assignedProfessionalName} ya está ocupado en ese horario o en su buffer de 20 min. Por favor selecciona otra hora.`
      );
      return;
    }

    const prevDate = searchedAppointment.date;
    const prevTime = searchedAppointment.startTime;
    const newEnd = calculateEndTime(newTime, searchedAppointment.totalDurationMinutes);

    const updated: Appointment = {
      ...searchedAppointment,
      date: newDate,
      startTime: newTime,
      endTime: newEnd,
      status: 'reprogramada',
      rescheduledAt: new Date().toISOString(),
    };

    SpaStorage.updateAppointment(updated);
    EmailService.sendRescheduledNotification(updated, prevDate, prevTime);
    onAppointmentsUpdated();
    setSearchedAppointment(updated);
    setIsRescheduling(false);
  };

  // Perform self-service cancellation
  const handleExecuteCancel = () => {
    if (!searchedAppointment) return;
    setCancelError(null);

    // Check policy 8 hours
    const policy = canModifyAppointment(searchedAppointment);
    if (!policy.allowed) {
      setCancelError(policy.reason || 'No se puede cancelar con menos de 8 horas.');
      return;
    }

    const updated: Appointment = {
      ...searchedAppointment,
      status: 'cancelada',
      cancellationReason: cancelReason,
      cancelledAt: new Date().toISOString(),
    };

    SpaStorage.updateAppointment(updated);
    EmailService.sendCancellationNotification(updated, cancelReason);
    onAppointmentsUpdated();
    setSearchedAppointment(updated);
    setIsCancelling(false);
  };

  // ICS download
  const handleDownloadICS = (app: Appointment) => {
    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Aura Spa//Reserva de Citas//ES
BEGIN:VEVENT
SUMMARY:Aura Spa - ${app.services.map(s => s.name).join(' & ')}
DESCRIPTION:Cita confirmada en Aura Spa con ${app.assignedProfessionalName}. Código de reserva: ${app.code}
LOCATION:${app.location}
DTSTART:${app.date.replace(/-/g, '')}T${app.startTime.replace(':', '')}00
DTEND:${app.date.replace(/-/g, '')}T${app.endTime.replace(':', '')}00
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR`;

    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.setAttribute('download', `Aura_Cita_${app.code}.ics`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="w-full pb-20 max-w-4xl mx-auto px-4 sm:px-6 pt-8">
      <div className="text-center mb-8">
        <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
          Portal del Huésped
        </span>
        <h1 className="font-serif-title text-3xl text-[#134230] font-semibold mt-1">
          Consulta y Gestiona tu Cita
        </h1>
        <p className="text-xs sm:text-sm text-[#414944] max-w-md mx-auto mt-2">
          Ingresa el código de tu reserva (ej. <code className="font-bold text-[#134230]">AUR-8842-KL</code>) o tu correo para reprogramar o cancelar de forma autónoma.
        </p>

        {/* Search form */}
        <form onSubmit={handleSearch} className="mt-6 flex items-center justify-center gap-2 max-w-md mx-auto">
          <div className="relative flex-1">
            <Search className="w-4 h-4 absolute left-3.5 top-3.5 text-[#717973]" />
            <input
              type="text"
              value={searchCode}
              onChange={e => setSearchCode(e.target.value)}
              placeholder="Código de cita o tu correo..."
              className="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-[#c0c9c2] text-sm text-[#181d1a] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
            />
          </div>
          <button
            type="submit"
            className="px-5 py-2.5 rounded-2xl bg-[#2d5a46] text-white text-xs sm:text-sm font-semibold hover:bg-[#134230] shadow-sm transition-colors"
          >
            Buscar
          </button>
        </form>
      </div>

      {/* Result Display */}
      {hasSearched && !searchedAppointment && (
        <div className="p-8 rounded-3xl bg-white border border-[#e5e9e4] text-center text-[#717973]">
          <AlertTriangle className="w-8 h-8 mx-auto text-[#d97706] mb-2" />
          <h3 className="font-semibold text-base text-[#181d1a]">No encontramos ninguna reserva con esos datos</h3>
          <p className="text-xs mt-1">
            Revisa que el código coincida con el recibido en tu correo o intenta con el código de prueba: <strong>AUR-8842-KL</strong>.
          </p>
        </div>
      )}

      {searchedAppointment && (
        <div className="bg-white rounded-3xl border border-[#e5e9e4] shadow-xl overflow-hidden animate-in fade-in duration-200">
          {/* Header Ticket */}
          <div className="bg-[#2d5a46] p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <div className="flex items-center gap-2">
                <span className="font-mono text-sm tracking-wider bg-[#134230] px-2.5 py-0.5 rounded-lg text-[#bceed3] font-bold">
                  #{searchedAppointment.code}
                </span>
                <span className={`text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${
                  searchedAppointment.status === 'agendada'
                    ? 'bg-[#bceed3] text-[#002114]'
                    : searchedAppointment.status === 'reprogramada'
                    ? 'bg-[#ffca98] text-[#7a532a]'
                    : searchedAppointment.status === 'cancelada'
                    ? 'bg-[#ffdad6] text-[#ba1a1a]'
                    : 'bg-[#ebefea] text-[#414944]'
                }`}>
                  {searchedAppointment.status}
                </span>
              </div>
              <h2 className="font-serif-title text-2xl font-semibold mt-1">
                Reserva a nombre de {searchedAppointment.client.name}
              </h2>
            </div>

            <div className="text-right sm:text-right">
              <span className="text-[11px] uppercase tracking-wider text-[#9fcfb6] block">Total a pagar</span>
              <span className="font-serif-title text-2xl font-bold">${searchedAppointment.totalPriceUSD} USD</span>
            </div>
          </div>

          {/* Details body */}
          <div className="p-6 sm:p-8 space-y-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 rounded-2xl bg-[#f0f5f0] text-xs">
              <div>
                <span className="text-[#717973] flex items-center gap-1.5 mb-0.5">
                  <Calendar className="w-3.5 h-3.5 text-[#134230]" /> Fecha del Turno
                </span>
                <strong className="text-sm text-[#181d1a]">{searchedAppointment.date}</strong>
              </div>

              <div>
                <span className="text-[#717973] flex items-center gap-1.5 mb-0.5">
                  <Clock className="w-3.5 h-3.5 text-[#134230]" /> Horario de Inicio
                </span>
                <strong className="text-sm text-[#7d562d]">
                  {searchedAppointment.startTime} - {searchedAppointment.endTime}
                </strong>
              </div>

              <div>
                <span className="text-[#717973] flex items-center gap-1.5 mb-0.5">
                  <User className="w-3.5 h-3.5 text-[#134230]" /> Terapeuta Asignado
                </span>
                <strong className="text-sm text-[#181d1a]">{searchedAppointment.assignedProfessionalName}</strong>
              </div>

              <div className="sm:col-span-2 lg:col-span-3 pt-2 border-t border-[#dfe4df]">
                <span className="text-[#717973] flex items-center gap-1.5 mb-0.5">
                  <MapPin className="w-3.5 h-3.5 text-[#134230]" /> Ubicación
                </span>
                <span className="text-xs text-[#181d1a] font-medium">{searchedAppointment.location}</span>
              </div>
            </div>

            {/* Services */}
            <div>
              <h3 className="font-semibold text-sm text-[#134230] mb-3">Servicios Incluidos</h3>
              <div className="space-y-2">
                {searchedAppointment.services.map(s => (
                  <div key={s.id} className="p-3 rounded-xl border border-[#e5e9e4] flex items-center justify-between text-xs">
                    <div>
                      <span className="font-semibold text-[#181d1a] block">{s.name}</span>
                      <span className="text-[#717973]">{s.durationMinutes} min • {s.categoryLabel}</span>
                    </div>
                    <span className="font-bold text-[#134230]">${s.priceUSD} USD</span>
                  </div>
                ))}
              </div>
            </div>

            {/* 8-Hour Policy reminder */}
            <div className="p-3.5 rounded-2xl bg-[#ffdcbd]/30 border border-[#ffca98] flex items-start gap-3 text-xs text-[#7a532a]">
              <ShieldAlert className="w-4 h-4 text-[#7d562d] shrink-0 mt-0.5" />
              <div>
                <strong className="block text-[#2c1600]">Política de Cancelación & Reprogramación:</strong>
                <span>
                  Puedes reprogramar o cancelar sin costo con al menos 8 horas de antelación. Faltan más de 8 horas para tu cita.
                </span>
              </div>
            </div>

            {/* Interactive Reschedule Box */}
            {isRescheduling && (
              <div className="p-5 rounded-2xl bg-[#f0f5f0] border border-[#c0c9c2] space-y-4 animate-in fade-in duration-150">
                <h4 className="font-semibold text-sm text-[#134230]">Elige nueva fecha y horario:</h4>
                {rescheduleError && (
                  <div className="p-3 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold flex items-center gap-2">
                    <AlertTriangle className="w-4 h-4 shrink-0" />
                    <span>{rescheduleError}</span>
                  </div>
                )}
                <div className="grid grid-cols-2 gap-3 text-xs">
                  <div>
                    <label className="block font-semibold mb-1">Nueva Fecha</label>
                    <input
                      type="date"
                      value={newDate}
                      onChange={e => setNewDate(e.target.value)}
                      className="w-full p-2.5 rounded-xl border border-[#c0c9c2] bg-white"
                    />
                  </div>
                  <div>
                    <label className="block font-semibold mb-1">Nueva Hora</label>
                    <input
                      type="time"
                      value={newTime}
                      onChange={e => setNewTime(e.target.value)}
                      className="w-full p-2.5 rounded-xl border border-[#c0c9c2] bg-white"
                    />
                  </div>
                </div>
                <div className="flex justify-end gap-2 text-xs">
                  <button
                    onClick={() => setIsRescheduling(false)}
                    className="px-3 py-2 rounded-xl text-[#717973] hover:bg-white"
                  >
                    Descartar
                  </button>
                  <button
                    onClick={handleExecuteReschedule}
                    className="px-4 py-2 rounded-xl bg-[#2d5a46] text-white font-semibold hover:bg-[#134230]"
                  >
                    Guardar Nuevo Horario
                  </button>
                </div>
              </div>
            )}

            {/* Interactive Cancel Box */}
            {isCancelling && (
              <div className="p-5 rounded-2xl bg-[#ffdad6]/30 border border-[#ffdad6] space-y-4 animate-in fade-in duration-150">
                <h4 className="font-semibold text-sm text-[#ba1a1a]">Confirmar cancelación de cita:</h4>
                {cancelError && (
                  <div className="p-3 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold flex items-center gap-2">
                    <AlertTriangle className="w-4 h-4 shrink-0" />
                    <span>{cancelError}</span>
                  </div>
                )}
                <div className="text-xs">
                  <label className="block font-semibold mb-1">Motivo</label>
                  <input
                    type="text"
                    value={cancelReason}
                    onChange={e => setCancelReason(e.target.value)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] bg-white"
                  />
                </div>
                <div className="flex justify-end gap-2 text-xs">
                  <button
                    onClick={() => setIsCancelling(false)}
                    className="px-3 py-2 rounded-xl text-[#717973] hover:bg-white"
                  >
                    Volver
                  </button>
                  <button
                    onClick={handleExecuteCancel}
                    className="px-4 py-2 rounded-xl bg-[#ba1a1a] text-white font-semibold hover:bg-[#93000a]"
                  >
                    Confirmar Cancelación
                  </button>
                </div>
              </div>
            )}

            {/* Action Bar */}
            <div className="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-[#f0f5f0]">
              <div className="flex items-center gap-2">
                <button
                  onClick={() => handleDownloadICS(searchedAppointment)}
                  className="px-3 py-2 rounded-xl border border-[#c0c9c2] text-xs font-semibold text-[#414944] hover:bg-[#f0f5f0] flex items-center gap-1.5"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Descargar Calendario (.ics)</span>
                </button>

                <button
                  onClick={() => onOpenEmail(searchedAppointment.code)}
                  className="px-3 py-2 rounded-xl border border-[#c0c9c2] text-xs font-semibold text-[#414944] hover:bg-[#f0f5f0] flex items-center gap-1.5"
                >
                  <Mail className="w-3.5 h-3.5 text-[#134230]" />
                  <span>Ver Correo Notificado</span>
                </button>
              </div>

              {searchedAppointment.status === 'agendada' && !isRescheduling && !isCancelling && (
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setIsRescheduling(true)}
                    className="px-4 py-2 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] flex items-center gap-1.5"
                  >
                    <RotateCcw className="w-3.5 h-3.5" />
                    <span>Reprogramar</span>
                  </button>
                  <button
                    onClick={() => setIsCancelling(true)}
                    className="px-3 py-2 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold hover:bg-[#ffdad6]/80 flex items-center gap-1.5"
                  >
                    <XCircle className="w-3.5 h-3.5" />
                    <span>Cancelar</span>
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
