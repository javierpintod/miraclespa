/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useMemo } from 'react';
import {
  Calendar as CalendarIcon,
  Search,
  Filter,
  Plus,
  Clock,
  User,
  CheckCircle2,
  XCircle,
  AlertTriangle,
  RotateCcw,
  Sparkles,
  ChevronLeft,
  ChevronRight,
  Eye,
} from 'lucide-react';
import { Appointment, Professional, Service, AppointmentStatus } from '../../types';
import {
  isTherapistAvailable,
  calculateEndTime,
  canModifyAppointment,
} from '../../services/availability';
import { SpaStorage } from '../../services/storage';
import { EmailService } from '../../services/emailService';

interface GlobalCalendarProps {
  appointments: Appointment[];
  professionals: Professional[];
  services: Service[];
  onAppointmentsUpdated: () => void;
  onOpenEmail: (code: string) => void;
}

export const GlobalCalendar: React.FC<GlobalCalendarProps> = ({
  appointments,
  professionals,
  services,
  onAppointmentsUpdated,
  onOpenEmail,
}) => {
  const [viewMode, setViewMode] = useState<'agenda' | 'dia' | 'semana'>('agenda');
  const [searchQuery, setSearchQuery] = useState('');
  const [filterStaff, setFilterStaff] = useState<string>('all');
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [selectedDate, setSelectedDate] = useState<string>('2025-10-22');

  // Reschedule Modal state
  const [rescheduleTarget, setRescheduleTarget] = useState<Appointment | null>(null);
  const [newDate, setNewDate] = useState<string>('2025-10-23');
  const [newTime, setNewTime] = useState<string>('11:00');
  const [rescheduleError, setRescheduleError] = useState<string | null>(null);

  // Cancel Modal state
  const [cancelTarget, setCancelTarget] = useState<Appointment | null>(null);
  const [cancelReason, setCancelReason] = useState<string>('Imprevisto personal del huésped');
  const [cancelError, setCancelError] = useState<string | null>(null);

  // New admin appointment modal
  const [showNewModal, setShowNewModal] = useState(false);
  const [newClientName, setNewClientName] = useState('');
  const [newClientEmail, setNewClientEmail] = useState('');
  const [newClientPhone, setNewClientPhone] = useState('');
  const [newServiceId, setNewServiceId] = useState<string>(services[0]?.id || '');
  const [newStaffId, setNewStaffId] = useState<string>(professionals[0]?.id || '');
  const [newAdminDate, setNewAdminDate] = useState<string>('2025-10-22');
  const [newAdminTime, setNewAdminTime] = useState<string>('15:00');
  const [newAdminError, setNewAdminError] = useState<string | null>(null);

  // Filtered appointments list
  const filteredAppointments = useMemo(() => {
    return appointments.filter(app => {
      // Search
      const q = searchQuery.toLowerCase();
      const matchQuery =
        app.client.name.toLowerCase().includes(q) ||
        app.code.toLowerCase().includes(q) ||
        app.client.email.toLowerCase().includes(q) ||
        app.services.some(s => s.name.toLowerCase().includes(q));

      if (!matchQuery) return false;

      // Staff filter
      if (filterStaff !== 'all' && app.professionalId !== filterStaff) return false;

      // Status filter
      if (filterStatus !== 'all' && app.status !== filterStatus) return false;

      // Date in day view
      if (viewMode === 'dia' && app.date !== selectedDate) return false;

      return true;
    });
  }, [appointments, searchQuery, filterStaff, filterStatus, viewMode, selectedDate]);

  // Handle Reschedule submit
  const handleConfirmReschedule = () => {
    if (!rescheduleTarget) return;
    setRescheduleError(null);

    // Policy check (informative check or admin override allowed)
    const policyCheck = canModifyAppointment(rescheduleTarget);
    if (!policyCheck.allowed) {
      const confirmOverride = window.confirm(
        `Atención: Faltan ${policyCheck.hoursRemaining}h para la cita (la política regular requiere 8h). ¿Deseas aplicar una excepción administrativa?`
      );
      if (!confirmOverride) return;
    }

    // Availability collision check
    const isFree = isTherapistAvailable(
      rescheduleTarget.professionalId,
      newDate,
      newTime,
      rescheduleTarget.totalDurationMinutes,
      appointments,
      rescheduleTarget.id
    );

    if (!isFree) {
      setRescheduleError(
        `El terapeuta ${rescheduleTarget.assignedProfessionalName} no está disponible el ${newDate} a las ${newTime}. Existe colisión de horario.`
      );
      return;
    }

    const previousDate = rescheduleTarget.date;
    const previousTime = rescheduleTarget.startTime;
    const newEndTime = calculateEndTime(newTime, rescheduleTarget.totalDurationMinutes);

    const updated: Appointment = {
      ...rescheduleTarget,
      date: newDate,
      startTime: newTime,
      endTime: newEndTime,
      status: 'reprogramada',
      rescheduledAt: new Date().toISOString(),
    };

    SpaStorage.updateAppointment(updated);
    EmailService.sendRescheduledNotification(updated, previousDate, previousTime);
    onAppointmentsUpdated();
    setRescheduleTarget(null);
  };

  // Handle Cancel submit
  const handleConfirmCancel = () => {
    if (!cancelTarget) return;
    setCancelError(null);

    const updated: Appointment = {
      ...cancelTarget,
      status: 'cancelada',
      cancellationReason: cancelReason,
      cancelledAt: new Date().toISOString(),
    };

    SpaStorage.updateAppointment(updated);
    EmailService.sendCancellationNotification(updated, cancelReason);
    onAppointmentsUpdated();
    setCancelTarget(null);
  };

  // Mark Completed
  const handleMarkCompleted = (app: Appointment) => {
    const updated: Appointment = { ...app, status: 'completada' };
    SpaStorage.updateAppointment(updated);
    onAppointmentsUpdated();
  };

  // Create Admin Appointment
  const handleCreateAdminAppointment = () => {
    setNewAdminError(null);
    if (!newClientName || !newClientEmail) {
      setNewAdminError('Nombre y correo del huésped son obligatorios.');
      return;
    }

    const srv = services.find(s => s.id === newServiceId) || services[0];
    const prof = professionals.find(p => p.id === newStaffId) || professionals[0];

    const isFree = isTherapistAvailable(
      prof.id,
      newAdminDate,
      newAdminTime,
      srv.durationMinutes,
      appointments
    );

    if (!isFree) {
      setNewAdminError(`${prof.name} tiene un turno ocupado en este horario o en su buffer.`);
      return;
    }

    const code = `AUR-${Math.floor(1000 + Math.random() * 9000)}-AD`;
    const newApp: Appointment = {
      id: `app-adm-${Date.now()}`,
      code,
      clientId: `cli-${Date.now()}`,
      client: {
        id: `cli-${Date.now()}`,
        name: newClientName,
        email: newClientEmail,
        phone: newClientPhone || '+57 300 000 0000',
        createdAt: new Date().toISOString(),
      },
      serviceIds: [srv.id],
      services: [srv],
      professionalId: prof.id,
      assignedProfessionalName: prof.name,
      date: newAdminDate,
      startTime: newAdminTime,
      endTime: calculateEndTime(newAdminTime, srv.durationMinutes),
      totalDurationMinutes: srv.durationMinutes,
      bufferMinutes: 20,
      totalPriceUSD: srv.priceUSD,
      status: 'agendada',
      location: 'Sede Principal - Las Palmas, Cabina Principal',
      createdAt: new Date().toISOString(),
      manageToken: `tok_${code.toLowerCase()}`,
    };

    SpaStorage.addAppointment(newApp);
    EmailService.sendBookingConfirmation(newApp);
    onAppointmentsUpdated();
    setShowNewModal(false);
  };

  return (
    <div className="w-full pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      {/* Header and Controls */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div>
          <div className="flex items-center gap-2">
            <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
              Operaciones del Spa
            </span>
            <span className="text-xs text-[#717973]">• Control Central</span>
          </div>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold">
            Calendario Global de Citas
          </h1>
          <p className="text-xs sm:text-sm text-[#414944] mt-0.5">
            Vista integral de turnos, reprogramaciones sin colisiones y asignación de cabinas.
          </p>
        </div>

        <div className="flex items-center gap-2.5 flex-wrap">
          {/* View Mode Switcher */}
          <div className="inline-flex p-1 bg-[#ebefea] rounded-xl text-xs font-semibold">
            <button
              onClick={() => setViewMode('agenda')}
              className={`px-3 py-1.5 rounded-lg transition-all ${
                viewMode === 'agenda' ? 'bg-white text-[#134230] shadow-sm font-bold' : 'text-[#414944]'
              }`}
            >
              Agenda
            </button>
            <button
              onClick={() => setViewMode('dia')}
              className={`px-3 py-1.5 rounded-lg transition-all ${
                viewMode === 'dia' ? 'bg-white text-[#134230] shadow-sm font-bold' : 'text-[#414944]'
              }`}
            >
              Día
            </button>
          </div>

          {/* New Appointment Button */}
          <button
            onClick={() => setShowNewModal(true)}
            className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] shadow-sm transition-all"
          >
            <Plus className="w-4 h-4" />
            <span>Crear Reserva</span>
          </button>
        </div>
      </div>

      {/* Filter Toolbar */}
      <div className="my-6 p-4 rounded-2xl bg-white border border-[#e5e9e4] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {/* Search */}
        <div className="relative flex-1 max-w-sm">
          <Search className="w-4 h-4 absolute left-3.5 top-3 text-[#717973]" />
          <input
            type="text"
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
            placeholder="Buscar por cliente, código o servicio..."
            className="w-full pl-10 pr-4 py-2 rounded-xl bg-[#f0f5f0] border border-[#dfe4df] text-xs text-[#181d1a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
          />
        </div>

        <div className="flex items-center gap-3 flex-wrap text-xs">
          {/* Specialist filter */}
          <div className="flex items-center gap-1.5">
            <User className="w-3.5 h-3.5 text-[#717973]" />
            <select
              value={filterStaff}
              onChange={e => setFilterStaff(e.target.value)}
              className="py-1.5 px-2.5 rounded-lg bg-[#f0f5f0] border border-[#dfe4df] font-medium text-[#181d1a] focus:outline-none"
            >
              <option value="all">Todos los Especialistas</option>
              {professionals.map(p => (
                <option key={p.id} value={p.id}>{p.name}</option>
              ))}
            </select>
          </div>

          {/* Status filter */}
          <div className="flex items-center gap-1.5">
            <Filter className="w-3.5 h-3.5 text-[#717973]" />
            <select
              value={filterStatus}
              onChange={e => setFilterStatus(e.target.value)}
              className="py-1.5 px-2.5 rounded-lg bg-[#f0f5f0] border border-[#dfe4df] font-medium text-[#181d1a] focus:outline-none"
            >
              <option value="all">Todos los Estados</option>
              <option value="agendada">Agendadas</option>
              <option value="completada">Completadas</option>
              <option value="reprogramada">Reprogramadas</option>
              <option value="cancelada">Canceladas</option>
            </select>
          </div>
        </div>
      </div>

      {/* Appointments List / Table */}
      <div className="bg-white rounded-3xl border border-[#e5e9e4] shadow-sm overflow-hidden">
        {filteredAppointments.length === 0 ? (
          <div className="p-12 text-center text-[#717973]">
            <CalendarIcon className="w-10 h-10 mx-auto text-[#c0c9c2] mb-3" />
            <h4 className="font-semibold text-sm text-[#181d1a]">No se encontraron reservas</h4>
            <p className="text-xs mt-1">Prueba cambiando los filtros de búsqueda o fecha.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs sm:text-sm">
              <thead>
                <tr className="bg-[#f0f5f0] text-[#717973] uppercase text-[10px] sm:text-[11px] font-bold tracking-wider">
                  <th className="py-3.5 px-4">Código / Huésped</th>
                  <th className="py-3.5 px-3">Servicios</th>
                  <th className="py-3.5 px-3">Fecha & Horario</th>
                  <th className="py-3.5 px-3">Especialista</th>
                  <th className="py-3.5 px-3">Estado</th>
                  <th className="py-3.5 px-3 text-right">Inversión</th>
                  <th className="py-3.5 px-4 text-center">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#f0f5f0]">
                {filteredAppointments.map(app => {
                  const statusColors: Record<AppointmentStatus, string> = {
                    agendada: 'bg-[#bceed3] text-[#002114]',
                    reprogramada: 'bg-[#ffca98] text-[#7a532a]',
                    completada: 'bg-[#e5e9e4] text-[#414944]',
                    cancelada: 'bg-[#ffdad6] text-[#ba1a1a]',
                  };

                  return (
                    <tr key={app.id} className="hover:bg-[#f6fbf5] transition-colors">
                      {/* Code & Client */}
                      <td className="py-4 px-4">
                        <span className="font-mono font-bold text-xs text-[#134230] block">
                          {app.code}
                        </span>
                        <span className="font-semibold text-sm text-[#181d1a] block">
                          {app.client.name}
                        </span>
                        <span className="text-[11px] text-[#717973]">{app.client.email}</span>
                      </td>

                      {/* Services */}
                      <td className="py-4 px-3">
                        <div className="space-y-1 max-w-[200px]">
                          {app.services.map((s, idx) => (
                            <span key={idx} className="block text-xs text-[#181d1a] truncate font-medium">
                              • {s.name} ({s.durationMinutes}m)
                            </span>
                          ))}
                          <span className="text-[10px] text-[#717973] block">
                            Total: {app.totalDurationMinutes} min (+20 min buffer)
                          </span>
                        </div>
                      </td>

                      {/* Date & Time */}
                      <td className="py-4 px-3">
                        <div className="text-xs">
                          <span className="font-semibold text-[#181d1a] block">{app.date}</span>
                          <span className="text-[#7d562d] font-bold block">
                            {app.startTime} - {app.endTime}
                          </span>
                          <span className="text-[10px] text-[#717973]">{app.location.split(',')[0]}</span>
                        </div>
                      </td>

                      {/* Specialist */}
                      <td className="py-4 px-3">
                        <span className="font-semibold text-xs text-[#134230] block">
                          {app.assignedProfessionalName}
                        </span>
                        <span className="text-[10px] text-[#717973]">Cabina Asignada</span>
                      </td>

                      {/* Status badge */}
                      <td className="py-4 px-3">
                        <span
                          className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${
                            statusColors[app.status]
                          }`}
                        >
                          {app.status}
                        </span>
                      </td>

                      {/* Price */}
                      <td className="py-4 px-3 text-right">
                        <span className="font-bold text-sm text-[#134230]">
                          ${app.totalPriceUSD} USD
                        </span>
                      </td>

                      {/* Actions */}
                      <td className="py-4 px-4 text-center">
                        <div className="flex items-center justify-center gap-1.5">
                          {/* Open simulated email */}
                          <button
                            onClick={() => onOpenEmail(app.code)}
                            title="Ver correo enviado"
                            className="p-1.5 rounded-lg text-[#717973] hover:text-[#134230] hover:bg-[#f0f5f0] transition-colors"
                          >
                            <Eye className="w-4 h-4" />
                          </button>

                          {app.status === 'agendada' && (
                            <>
                              {/* Mark Completed */}
                              <button
                                onClick={() => handleMarkCompleted(app)}
                                title="Marcar como atendida/completada"
                                className="p-1.5 rounded-lg text-[#134230] hover:bg-[#bceed3]/30 transition-colors"
                              >
                                <CheckCircle2 className="w-4 h-4" />
                              </button>

                              {/* Reschedule */}
                              <button
                                onClick={() => {
                                  setRescheduleTarget(app);
                                  setNewDate(app.date);
                                  setNewTime(app.startTime);
                                }}
                                title="Reprogramar fecha/hora"
                                className="p-1.5 rounded-lg text-[#7d562d] hover:bg-[#ffdcbd]/40 transition-colors"
                              >
                                <RotateCcw className="w-4 h-4" />
                              </button>

                              {/* Cancel */}
                              <button
                                onClick={() => {
                                  setCancelTarget(app);
                                }}
                                title="Cancelar reserva"
                                className="p-1.5 rounded-lg text-[#ba1a1a] hover:bg-[#ffdad6]/40 transition-colors"
                              >
                                <XCircle className="w-4 h-4" />
                              </button>
                            </>
                          )}
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Modal: Reschedule Appointment */}
      {rescheduleTarget && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-150">
          <div className="bg-white max-w-md w-full rounded-3xl p-6 shadow-2xl border border-[#e5e9e4]">
            <h3 className="font-serif-title text-xl text-[#134230] font-semibold mb-1">
              Reprogramar Cita #{rescheduleTarget.code}
            </h3>
            <p className="text-xs text-[#414944] mb-4">
              Cliente: {rescheduleTarget.client.name} • Especialista: {rescheduleTarget.assignedProfessionalName}
            </p>

            {rescheduleError && (
              <div className="mb-4 p-3 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 shrink-0" />
                <span>{rescheduleError}</span>
              </div>
            )}

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Nueva Fecha</label>
                <input
                  type="date"
                  value={newDate}
                  onChange={e => setNewDate(e.target.value)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Nuevo Horario de Inicio</label>
                <input
                  type="time"
                  value={newTime}
                  onChange={e => setNewTime(e.target.value)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
                <span className="text-[11px] text-[#717973] block mt-1">
                  Duración total: {rescheduleTarget.totalDurationMinutes} min (+20 min buffer).
                </span>
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-2">
              <button
                onClick={() => setRescheduleTarget(null)}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-[#717973] hover:bg-[#f0f5f0]"
              >
                Cancelar
              </button>
              <button
                onClick={handleConfirmReschedule}
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-[#2d5a46] text-white hover:bg-[#134230]"
              >
                Confirmar Reprogramación
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal: Cancel Appointment */}
      {cancelTarget && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-150">
          <div className="bg-white max-w-md w-full rounded-3xl p-6 shadow-2xl border border-[#e5e9e4]">
            <h3 className="font-serif-title text-xl text-[#ba1a1a] font-semibold mb-1">
              Cancelar Cita #{cancelTarget.code}
            </h3>
            <p className="text-xs text-[#414944] mb-4">
              ¿Estás seguro de cancelar esta reserva? Se liberará el turno del especialista y se enviará la notificación por correo.
            </p>

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Motivo de Cancelación</label>
                <textarea
                  rows={2}
                  value={cancelReason}
                  onChange={e => setCancelReason(e.target.value)}
                  placeholder="Explica el motivo..."
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#ba1a1a] resize-none"
                />
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-2">
              <button
                onClick={() => setCancelTarget(null)}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-[#717973] hover:bg-[#f0f5f0]"
              >
                Volver
              </button>
              <button
                onClick={handleConfirmCancel}
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-[#ba1a1a] text-white hover:bg-[#93000a]"
              >
                Confirmar Cancelación
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal: Create Admin Appointment */}
      {showNewModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-150">
          <div className="bg-white max-w-lg w-full rounded-3xl p-6 shadow-2xl border border-[#e5e9e4]">
            <h3 className="font-serif-title text-xl text-[#134230] font-semibold mb-1">
              Nueva Cita Administrativa
            </h3>
            <p className="text-xs text-[#414944] mb-4">
              Agendamiento directo desde recepción para clientes presenciales o telefónicos.
            </p>

            {newAdminError && (
              <div className="mb-4 p-3 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 shrink-0" />
                <span>{newAdminError}</span>
              </div>
            )}

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Nombre del Huésped</label>
                <input
                  type="text"
                  value={newClientName}
                  onChange={e => setNewClientName(e.target.value)}
                  placeholder="Ej. Carlos Mendoza"
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Correo Electrónico</label>
                  <input
                    type="email"
                    value={newClientEmail}
                    onChange={e => setNewClientEmail(e.target.value)}
                    placeholder="carlos@correo.com"
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Teléfono</label>
                  <input
                    type="tel"
                    value={newClientPhone}
                    onChange={e => setNewClientPhone(e.target.value)}
                    placeholder="+57 310..."
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Servicio</label>
                  <select
                    value={newServiceId}
                    onChange={e => setNewServiceId(e.target.value)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  >
                    {services.map(s => (
                      <option key={s.id} value={s.id}>{s.name} (${s.priceUSD} USD)</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Especialista</label>
                  <select
                    value={newStaffId}
                    onChange={e => setNewStaffId(e.target.value)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  >
                    {professionals.map(p => (
                      <option key={p.id} value={p.id}>{p.name}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Fecha</label>
                  <input
                    type="date"
                    value={newAdminDate}
                    onChange={e => setNewAdminDate(e.target.value)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Hora Inicio</label>
                  <input
                    type="time"
                    value={newAdminTime}
                    onChange={e => setNewAdminTime(e.target.value)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  />
                </div>
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-2">
              <button
                onClick={() => setShowNewModal(false)}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-[#717973] hover:bg-[#f0f5f0]"
              >
                Cerrar
              </button>
              <button
                onClick={handleCreateAdminAppointment}
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-[#2d5a46] text-white hover:bg-[#134230]"
              >
                Agendar Cita
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
