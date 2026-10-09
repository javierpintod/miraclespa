/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useMemo } from 'react';
import confetti from 'canvas-confetti';
import {
  Sparkles,
  CheckCircle2,
  Clock,
  Calendar as CalendarIcon,
  User,
  Mail,
  Phone,
  ShieldCheck,
  ChevronLeft,
  ChevronRight,
  ArrowRight,
  Info,
  Sun,
  Sunset,
  Moon,
  AlertTriangle,
  Star,
  Users,
  Check,
  Download,
  CalendarCheck,
} from 'lucide-react';
import { Service, Professional, Appointment } from '../../types';
import {
  DAILY_SLOTS,
  checkSlotAvailability,
  HYGIENE_BUFFER_MINUTES,
  calculateEndTime,
} from '../../services/availability';
import { SpaStorage } from '../../services/storage';
import { EmailService } from '../../services/emailService';

interface BookingWizardProps {
  services: Service[];
  professionals: Professional[];
  existingAppointments: Appointment[];
  onBookingConfirmed: (appointment: Appointment) => void;
  onOpenEmail: (appointmentCode: string) => void;
  selectedBranch: string;
}

export const BookingWizard: React.FC<BookingWizardProps> = ({
  services,
  professionals,
  existingAppointments,
  onBookingConfirmed,
  onOpenEmail,
  selectedBranch,
}) => {
  // Pre-selected services matching Image 2 (Masaje Relajante & Manicura Rusa)
  const [selectedServiceIds, setSelectedServiceIds] = useState<string[]>(['srv-1', 'srv-3']);
  const [selectedStaffId, setSelectedStaffId] = useState<string>('any'); // 'any' or prof.id
  const [selectedDate, setSelectedDate] = useState<string>('2025-10-22'); // YYYY-MM-DD
  const [selectedTimeSlot, setSelectedTimeSlot] = useState<string>('10:30');

  // Client info form
  const [clientName, setClientName] = useState('Valentina Restrepo');
  const [clientEmail, setClientEmail] = useState('valentina.restrepo@email.com');
  const [clientPhone, setClientPhone] = useState('+57 312 890 4421');
  const [clientNotes, setClientNotes] = useState('Sensibilidad moderada en piel, preferencia de presión media.');

  // Confirmation modal state
  const [confirmedApp, setConfirmedApp] = useState<Appointment | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);

  // Derived selected services
  const selectedServices = useMemo(() => {
    return services.filter(s => selectedServiceIds.includes(s.id));
  }, [services, selectedServiceIds]);

  const totalDurationMinutes = useMemo(() => {
    return selectedServices.reduce((acc, s) => acc + s.durationMinutes, 0);
  }, [selectedServices]);

  const totalPriceUSD = useMemo(() => {
    return selectedServices.reduce((acc, s) => acc + s.priceUSD, 0);
  }, [selectedServices]);

  // Selected therapist object
  const selectedTherapist = useMemo(() => {
    if (selectedStaffId === 'any') return null;
    return professionals.find(p => p.id === selectedStaffId) || null;
  }, [professionals, selectedStaffId]);

  // Toggle service selection
  const handleToggleService = (service: Service) => {
    if (!service.inStock) return; // Prevent selection of stockout services

    setSelectedServiceIds(prev => {
      if (prev.includes(service.id)) {
        if (prev.length === 1) {
          // Keep at least one or allow empty
          return [];
        }
        return prev.filter(id => id !== service.id);
      } else {
        return [...prev, service.id];
      }
    });
  };

  // Calendar dates around Oct 20-25 2025
  const calendarDays = [
    { day: 'Lun', dateNum: '20', fullDate: '2025-10-20' },
    { day: 'Mar', dateNum: '21', fullDate: '2025-10-21' },
    { day: 'Mié', dateNum: '22', fullDate: '2025-10-22' },
    { day: 'Jue', dateNum: '23', fullDate: '2025-10-23' },
    { day: 'Vie', dateNum: '24', fullDate: '2025-10-24' },
    { day: 'Sáb', dateNum: '25', fullDate: '2025-10-25' },
  ];

  // Grouped slots with real-time collision checking
  const slotsWithStatus = useMemo(() => {
    return DAILY_SLOTS.map(slot => {
      const check = checkSlotAvailability(
        slot.time,
        selectedDate,
        totalDurationMinutes || 60,
        selectedStaffId,
        selectedServices.length > 0 ? selectedServices : [services[0]],
        professionals,
        existingAppointments
      );

      return {
        ...slot,
        isAvailable: check.available,
        reason: check.reason,
        assignedProf: check.assignedProfessional,
      };
    });
  }, [selectedDate, totalDurationMinutes, selectedStaffId, selectedServices, services, professionals, existingAppointments]);

  // Confirm booking action
  const handleConfirmBooking = () => {
    setFormError(null);

    if (selectedServices.length === 0) {
      setFormError('Por favor selecciona al menos un servicio para tu cita.');
      return;
    }
    if (!clientName.trim()) {
      setFormError('Por favor ingresa tu nombre y apellido.');
      return;
    }
    if (!clientEmail.trim() || !clientEmail.includes('@')) {
      setFormError('Por favor ingresa un correo electrónico válido.');
      return;
    }

    // Check slot availability again for atomic lock
    const slotCheck = checkSlotAvailability(
      selectedTimeSlot,
      selectedDate,
      totalDurationMinutes,
      selectedStaffId,
      selectedServices,
      professionals,
      existingAppointments
    );

    if (!slotCheck.available) {
      setFormError(slotCheck.reason || 'El horario seleccionado ya no está disponible. Por favor elige otro horario.');
      return;
    }

    const assignedProf = slotCheck.assignedProfessional || professionals[0];
    const endTime = calculateEndTime(selectedTimeSlot, totalDurationMinutes);
    const appointmentCode = `AUR-${Math.floor(1000 + Math.random() * 9000)}-${Math.random().toString(36).substring(2, 4).toUpperCase()}`;

    const newAppointment: Appointment = {
      id: `app-${Date.now()}`,
      code: appointmentCode,
      clientId: `cli-${Date.now()}`,
      client: {
        id: `cli-${Date.now()}`,
        name: clientName,
        email: clientEmail,
        phone: clientPhone,
        notes: clientNotes,
        createdAt: new Date().toISOString(),
      },
      serviceIds: selectedServices.map(s => s.id),
      services: selectedServices,
      professionalId: assignedProf.id,
      assignedProfessionalName: assignedProf.name,
      date: selectedDate,
      startTime: selectedTimeSlot,
      endTime,
      totalDurationMinutes,
      bufferMinutes: HYGIENE_BUFFER_MINUTES,
      totalPriceUSD,
      status: 'agendada',
      location: `${selectedBranch}, Cabina Zen 1`,
      notes: clientNotes,
      createdAt: new Date().toISOString(),
      manageToken: `tok_${appointmentCode.toLowerCase().replace('-', '_')}`,
    };

    // Save to repository
    SpaStorage.addAppointment(newAppointment);

    // Send confirmation email simulation
    EmailService.sendBookingConfirmation(newAppointment);

    // Trigger celebration confetti
    try {
      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#2d5a46', '#d4a373', '#bceed3', '#ffca98'],
      });
    } catch {
      // Ignored if canvas-confetti has restrictions
    }

    setConfirmedApp(newAppointment);
    setShowModal(true);
    onBookingConfirmed(newAppointment);
  };

  // Download ICS Calendar file
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
    link.setAttribute('download', `Cita_${app.code}.ics`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="w-full pb-20">
      {/* Editorial Header Section */}
      <section className="text-center pt-8 pb-10 max-w-4xl mx-auto px-4">
        <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#f0f5f0] border border-[#c0c9c2]/40 text-[#7d562d] mb-3 backdrop-blur-sm">
          <Sparkles className="w-3.5 h-3.5 text-[#2d5a46]" />
          <span className="text-[11px] uppercase tracking-widest font-semibold">
            Santuario Holístico & Bienestar
          </span>
        </div>
        <h1 className="font-serif-title text-3xl sm:text-4xl lg:text-[40px] text-[#134230] font-medium tracking-tight leading-tight">
          Reserva tu momento de calma y bienestar
        </h1>
        <p className="text-sm sm:text-base text-[#414944] max-w-2xl mx-auto mt-3 font-normal leading-relaxed">
          Diseña una experiencia reparadora combinando tratamientos botánicos, terapeutas certificados y una atmósfera concebida para tu armonía sensorial.
        </p>

        {/* Stepper Indicator */}
        <div className="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 max-w-3xl mx-auto">
          <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white border border-[#e5e9e4] shadow-sm">
            <div className="w-7 h-7 rounded-full bg-[#2d5a46] text-white flex items-center justify-center text-xs font-bold shrink-0">
              1
            </div>
            <div className="text-left min-w-0">
              <span className="block text-[10px] uppercase tracking-wider text-[#2d5a46] font-bold">Paso 1</span>
              <span className="block text-xs font-semibold text-[#181d1a] truncate">Servicios</span>
            </div>
          </div>
          <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/70 border border-[#e5e9e4]">
            <div className="w-7 h-7 rounded-full bg-[#ebefea] text-[#414944] flex items-center justify-center text-xs font-bold shrink-0">
              2
            </div>
            <div className="text-left min-w-0">
              <span className="block text-[10px] uppercase tracking-wider text-[#717973] font-semibold">Paso 2</span>
              <span className="block text-xs font-semibold text-[#181d1a] truncate">Especialista</span>
            </div>
          </div>
          <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/70 border border-[#e5e9e4]">
            <div className="w-7 h-7 rounded-full bg-[#ebefea] text-[#414944] flex items-center justify-center text-xs font-bold shrink-0">
              3
            </div>
            <div className="text-left min-w-0">
              <span className="block text-[10px] uppercase tracking-wider text-[#717973] font-semibold">Paso 3</span>
              <span className="block text-xs font-semibold text-[#181d1a] truncate">Fecha y Hora</span>
            </div>
          </div>
          <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/70 border border-[#e5e9e4]">
            <div className="w-7 h-7 rounded-full bg-[#ebefea] text-[#414944] flex items-center justify-center text-xs font-bold shrink-0">
              4
            </div>
            <div className="text-left min-w-0">
              <span className="block text-[10px] uppercase tracking-wider text-[#717973] font-semibold">Paso 4</span>
              <span className="block text-xs font-semibold text-[#181d1a] truncate">Tus Datos</span>
            </div>
          </div>
        </div>
      </section>

      {/* Main Booking Split: Form Controls (8 cols) + Sticky Summary (4 cols) */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Left: Configuration Journey */}
          <div className="lg:col-span-8 flex flex-col gap-10">
            {/* SECTION 1: Servicios & Rituales */}
            <section className="bg-white rounded-3xl p-6 sm:p-8 border border-[#e5e9e4] shadow-sm">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-[#f0f5f0]">
                <div>
                  <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
                    Catálogo Terapéutico
                  </span>
                  <h2 className="font-serif-title text-2xl text-[#134230] font-semibold mt-0.5">
                    1. Selecciona tus Rituales y Servicios
                  </h2>
                  <p className="text-xs sm:text-sm text-[#414944] mt-1">
                    Puedes elegir múltiples servicios para sincronizarlos en un mismo turno continuo.
                  </p>
                </div>
                <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#ffdcbd]/60 text-[#7a532a] text-xs font-semibold self-start sm:self-auto">
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>Fórmulas botánicas 100% orgánicas</span>
                </div>
              </div>

              {/* Service Cards Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {services.map(service => {
                  const isSelected = selectedServiceIds.includes(service.id);
                  const isOutOfStock = !service.inStock;

                  return (
                    <article
                      key={service.id}
                      onClick={() => !isOutOfStock && handleToggleService(service)}
                      className={`relative p-5 rounded-2xl border transition-all flex flex-col justify-between select-none ${
                        isOutOfStock
                          ? 'bg-[#f0f5f0]/70 border-[#dfe4df] opacity-75 cursor-not-allowed'
                          : isSelected
                          ? 'bg-white border-[#2d5a46] ring-2 ring-[#2d5a46]/30 shadow-md cursor-pointer'
                          : 'bg-white border-[#e5e9e4] hover:border-[#bceed3] hover:shadow-sm cursor-pointer'
                      }`}
                    >
                      {/* Checkbox indicator */}
                      <div className="absolute top-4 right-4">
                        {isOutOfStock ? (
                          <span className="text-xs text-[#ba1a1a] font-semibold flex items-center gap-1">
                            <AlertTriangle className="w-4 h-4 text-[#ba1a1a]" />
                          </span>
                        ) : isSelected ? (
                          <div className="w-6 h-6 rounded-full bg-[#2d5a46] text-white flex items-center justify-center shadow-sm">
                            <Check className="w-4 h-4 stroke-[3]" />
                          </div>
                        ) : (
                          <div className="w-6 h-6 rounded-full border-2 border-[#c0c9c2] hover:border-[#2d5a46] transition-colors" />
                        )}
                      </div>

                      <div>
                        {/* Meta tags: Duration & stock status */}
                        <div className="flex items-center gap-2 mb-2 pr-8">
                          <span className="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-[#f0f5f0] text-[#134230]">
                            <Clock className="w-3 h-3" /> {service.durationMinutes} min
                          </span>
                          {isOutOfStock ? (
                            <span className="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-[#ffdad6] text-[#ba1a1a]">
                              Insumo agotado temporalmente
                            </span>
                          ) : (
                            <span className="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-[#bceed3]/50 text-[#134230]">
                              Disponible
                            </span>
                          )}
                        </div>

                        <h3 className={`font-semibold text-base transition-colors ${isSelected ? 'text-[#134230]' : 'text-[#181d1a]'}`}>
                          {service.name}
                        </h3>

                        <p className="text-xs text-[#414944] mt-1.5 line-clamp-2 leading-relaxed">
                          {service.description}
                        </p>

                        {/* Active Supplies Notice */}
                        <div className="mt-3 p-2 rounded-xl bg-[#f6fbf5] border border-[#e5e9e4]/80 text-[11px]">
                          <span className="block font-semibold uppercase tracking-wider text-[#717973] text-[9px]">
                            {isOutOfStock ? 'Falta en inventario central' : 'Insumos botánicos activos'}
                          </span>
                          <span className={isOutOfStock ? 'text-[#ba1a1a] font-medium' : 'text-[#181d1a]'}>
                            {isOutOfStock ? service.outOfStockReason : service.activeSuppliesDescription}
                          </span>
                        </div>
                      </div>

                      {/* Pricing and Action button */}
                      <div className="mt-4 pt-3 border-t border-[#f0f5f0] flex items-center justify-between">
                        <div>
                          <span className="block text-[10px] text-[#717973] uppercase tracking-wider font-medium">Inversión</span>
                          <span className="font-semibold text-base text-[#134230]">${service.priceUSD} USD</span>
                        </div>

                        {isOutOfStock ? (
                          <span className="text-xs px-3 py-1 rounded-full bg-[#ebefea] text-[#717973] font-medium">
                            No disponible
                          </span>
                        ) : isSelected ? (
                          <span className="text-xs px-3.5 py-1.5 rounded-full bg-[#2d5a46] text-white font-semibold">
                            Seleccionado
                          </span>
                        ) : (
                          <span className="text-xs px-3.5 py-1.5 rounded-full bg-[#f0f5f0] text-[#134230] font-semibold hover:bg-[#2d5a46] hover:text-white transition-colors">
                            Añadir +
                          </span>
                        )}
                      </div>
                    </article>
                  );
                })}
              </div>
            </section>

            {/* SECTION 2: Selección de Especialista */}
            <section className="bg-white rounded-3xl p-6 sm:p-8 border border-[#e5e9e4] shadow-sm">
              <div className="mb-6 pb-4 border-b border-[#f0f5f0]">
                <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
                  Equipo Terapéutico
                </span>
                <h2 className="font-serif-title text-2xl text-[#134230] font-semibold mt-0.5">
                  2. Selecciona tu Especialista de Confianza
                </h2>
                <p className="text-xs sm:text-sm text-[#414944] mt-1">
                  Puedes elegir tu terapeuta predilecto o permitir la asignación óptima de turno.
                </p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {/* Option 1: Any therapist */}
                <div
                  onClick={() => setSelectedStaffId('any')}
                  className={`p-4 rounded-2xl border text-center transition-all cursor-pointer flex flex-col justify-between ${
                    selectedStaffId === 'any'
                      ? 'bg-white border-[#2d5a46] ring-2 ring-[#2d5a46]/30 shadow-md'
                      : 'bg-white border-[#e5e9e4] hover:border-[#bceed3] hover:shadow-sm'
                  }`}
                >
                  <div className="flex flex-col items-center">
                    <div className="w-14 h-14 rounded-full bg-[#bceed3] text-[#134230] flex items-center justify-center mb-2.5">
                      <Users className="w-7 h-7" />
                    </div>
                    <span className="inline-block px-2 py-0.5 rounded-full bg-[#ffdcbd] text-[#7a532a] text-[10px] font-bold uppercase tracking-wider mb-1">
                      Máxima Disponibilidad
                    </span>
                    <h3 className="font-semibold text-sm text-[#181d1a]">Cualquier Terapeuta</h3>
                    <p className="text-[11px] text-[#414944] mt-1">
                      Asignación automática optimizada para tu horario preferido
                    </p>
                  </div>
                  <div className="mt-3 pt-2 text-[#2d5a46] text-xs font-semibold flex items-center justify-center gap-1 border-t border-[#f0f5f0]">
                    <Sparkles className="w-3.5 h-3.5" /> Más turnos libres
                  </div>
                </div>

                {/* Specific therapists */}
                {professionals.map(prof => {
                  const isSelected = selectedStaffId === prof.id;

                  return (
                    <div
                      key={prof.id}
                      onClick={() => setSelectedStaffId(prof.id)}
                      className={`p-4 rounded-2xl border text-center transition-all cursor-pointer flex flex-col justify-between ${
                        isSelected
                          ? 'bg-white border-[#2d5a46] ring-2 ring-[#2d5a46]/30 shadow-md'
                          : 'bg-white border-[#e5e9e4] hover:border-[#bceed3] hover:shadow-sm'
                      }`}
                    >
                      <div className="flex flex-col items-center">
                        <img
                          src={prof.avatar}
                          alt={prof.name}
                          className="w-14 h-14 rounded-full object-cover mb-2 border border-[#c0c9c2]/50 shadow-inner"
                        />
                        <div className="flex items-center gap-1 text-[#d97706] text-xs font-bold mb-1">
                          <Star className="w-3.5 h-3.5 fill-current" />
                          <span>{prof.rating} ({prof.reviewsCount})</span>
                        </div>
                        <h3 className="font-semibold text-sm text-[#181d1a]">{prof.name}</h3>
                        <p className="text-[11px] text-[#414944] mt-0.5 line-clamp-1">{prof.role}</p>
                      </div>

                      <div className="mt-3 pt-2 text-[#717973] text-xs font-medium flex items-center justify-center gap-1 border-t border-[#f0f5f0]">
                        <Clock className="w-3 h-3" /> {prof.shiftLabel}
                      </div>
                    </div>
                  );
                })}
              </div>
            </section>

            {/* SECTION 3: Fecha y Horario */}
            <section className="bg-white rounded-3xl p-6 sm:p-8 border border-[#e5e9e4] shadow-sm">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-[#f0f5f0]">
                <div>
                  <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
                    Disponibilidad en Tiempo Real
                  </span>
                  <h2 className="font-serif-title text-2xl text-[#134230] font-semibold mt-0.5">
                    3. Fecha & Horario del Turno
                  </h2>
                  <p className="text-xs sm:text-sm text-[#414944] mt-1">
                    Selecciona el día de tu visita y el bloque de inicio preferido.
                  </p>
                </div>
                <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#faedcd] text-[#7a532a] text-xs font-semibold self-start sm:self-auto">
                  <ShieldCheck className="w-3.5 h-3.5" />
                  <span>+20 min buffer de higienización entre turnos</span>
                </div>
              </div>

              {/* Weekly Date Selector Deck */}
              <div className="mb-6">
                <div className="flex items-center justify-between mb-3">
                  <div className="flex items-center gap-2">
                    <CalendarIcon className="w-4 h-4 text-[#134230]" />
                    <span className="font-semibold text-sm text-[#181d1a]">Octubre 2025</span>
                  </div>
                  <div className="flex items-center gap-1 text-xs text-[#717973]">
                    <button
                      className="p-1 rounded-full hover:bg-[#ebefea] transition-colors"
                      title="Semana previa"
                    >
                      <ChevronLeft className="w-4 h-4" />
                    </button>
                    <span>Semana activa</span>
                    <button
                      className="p-1 rounded-full hover:bg-[#ebefea] transition-colors"
                      title="Semana siguiente"
                    >
                      <ChevronRight className="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <div className="grid grid-cols-6 gap-2 sm:gap-3 text-center">
                  {calendarDays.map(item => {
                    const isDateSelected = selectedDate === item.fullDate;

                    return (
                      <button
                        key={item.fullDate}
                        onClick={() => setSelectedDate(item.fullDate)}
                        className={`p-2.5 sm:p-3.5 rounded-2xl transition-all text-center focus:outline-none ${
                          isDateSelected
                            ? 'bg-[#2d5a46] text-white shadow-md ring-2 ring-[#2d5a46]'
                            : 'bg-[#f0f5f0] text-[#181d1a] hover:bg-[#dfe4df]'
                        }`}
                      >
                        <span className={`block text-[10px] sm:text-xs font-semibold uppercase ${
                          isDateSelected ? 'text-[#bceed3]' : 'text-[#717973]'
                        }`}>
                          {item.day}
                        </span>
                        <span className="block text-base sm:text-lg font-bold mt-0.5">
                          {item.dateNum}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Time Slots Divided by Periods */}
              <div className="space-y-5">
                {/* Turno Mañana */}
                <div>
                  <div className="flex items-center gap-2 mb-2 text-xs font-semibold text-[#181d1a]">
                    <Sun className="w-3.5 h-3.5 text-[#d97706]" />
                    <span>TURNO MAÑANA (09:00 - 13:00)</span>
                  </div>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    {slotsWithStatus
                      .filter(s => s.period === 'manana')
                      .map(slot => {
                        const isSlotSelected = selectedTimeSlot === slot.time;

                        return (
                          <button
                            key={slot.time}
                            disabled={!slot.isAvailable}
                            onClick={() => setSelectedTimeSlot(slot.time)}
                            title={!slot.isAvailable ? slot.reason : 'Disponible para agendar'}
                            className={`py-2.5 px-3 rounded-full text-xs font-semibold transition-all ${
                              !slot.isAvailable
                                ? 'bg-[#ebefea] text-[#c0c9c2] line-through cursor-not-allowed border border-[#dfe4df]'
                                : isSlotSelected
                                ? 'bg-[#2d5a46] text-white shadow-md ring-2 ring-[#2d5a46]'
                                : 'bg-[#f0f5f0] text-[#181d1a] hover:bg-[#dfe4df]'
                            }`}
                          >
                            {slot.label}
                          </button>
                        );
                      })}
                  </div>
                </div>

                {/* Turno Tarde */}
                <div>
                  <div className="flex items-center gap-2 mb-2 text-xs font-semibold text-[#181d1a]">
                    <Sunset className="w-3.5 h-3.5 text-[#d97706]" />
                    <span>TURNO TARDE (14:00 - 18:30)</span>
                  </div>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    {slotsWithStatus
                      .filter(s => s.period === 'tarde')
                      .map(slot => {
                        const isSlotSelected = selectedTimeSlot === slot.time;

                        return (
                          <button
                            key={slot.time}
                            disabled={!slot.isAvailable}
                            onClick={() => setSelectedTimeSlot(slot.time)}
                            title={!slot.isAvailable ? slot.reason : 'Disponible para agendar'}
                            className={`py-2.5 px-3 rounded-full text-xs font-semibold transition-all ${
                              !slot.isAvailable
                                ? 'bg-[#ebefea] text-[#c0c9c2] line-through cursor-not-allowed border border-[#dfe4df]'
                                : isSlotSelected
                                ? 'bg-[#2d5a46] text-white shadow-md ring-2 ring-[#2d5a46]'
                                : 'bg-[#f0f5f0] text-[#181d1a] hover:bg-[#dfe4df]'
                            }`}
                          >
                            {slot.label}
                          </button>
                        );
                      })}
                  </div>
                </div>

                {/* Turno Crepúsculo */}
                <div>
                  <div className="flex items-center gap-2 mb-2 text-xs font-semibold text-[#181d1a]">
                    <Moon className="w-3.5 h-3.5 text-[#7d562d]" />
                    <span>TURNO CREPÚSCULO (19:00 - 20:30)</span>
                  </div>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    {slotsWithStatus
                      .filter(s => s.period === 'crepusculo')
                      .map(slot => {
                        const isSlotSelected = selectedTimeSlot === slot.time;

                        return (
                          <button
                            key={slot.time}
                            disabled={!slot.isAvailable}
                            onClick={() => setSelectedTimeSlot(slot.time)}
                            title={!slot.isAvailable ? slot.reason : 'Disponible para agendar'}
                            className={`py-2.5 px-3 rounded-full text-xs font-semibold transition-all ${
                              !slot.isAvailable
                                ? 'bg-[#ebefea] text-[#c0c9c2] line-through cursor-not-allowed border border-[#dfe4df]'
                                : isSlotSelected
                                ? 'bg-[#2d5a46] text-white shadow-md ring-2 ring-[#2d5a46]'
                                : 'bg-[#f0f5f0] text-[#181d1a] hover:bg-[#dfe4df]'
                            }`}
                          >
                            {slot.label}
                          </button>
                        );
                      })}
                  </div>
                </div>
              </div>

              {/* Informative buffer notice */}
              <div className="mt-6 p-3.5 rounded-2xl bg-[#f0f5f0] border border-[#e5e9e4] flex items-start gap-2.5 text-[#414944] text-xs">
                <Info className="w-4 h-4 text-[#7d562d] shrink-0 mt-0.5" />
                <p>
                  Al agendar {selectedServices.length} servicios consecutivos ({totalDurationMinutes} min totales), nuestro sistema calcula de forma atómica el orden óptimo de tratamiento y los {HYGIENE_BUFFER_MINUTES} minutos de preparación de cabina sin costo adicional.
                </p>
              </div>
            </section>

            {/* SECTION 4: Datos del Huésped */}
            <section className="bg-white rounded-3xl p-6 sm:p-8 border border-[#e5e9e4] shadow-sm">
              <div className="mb-6 pb-4 border-b border-[#f0f5f0]">
                <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
                  Información del Huésped
                </span>
                <h2 className="font-serif-title text-2xl text-[#134230] font-semibold mt-0.5">
                  4. Completa tus Datos Personales
                </h2>
                <p className="text-xs sm:text-sm text-[#414944] mt-1">
                  Enviaremos la confirmación inmediata y el token seguro para gestionar o reprogramar sin necesidad de llamadas.
                </p>
              </div>

              {formError && (
                <div className="mb-4 p-3.5 rounded-xl bg-[#ffdad6] text-[#ba1a1a] text-xs font-semibold flex items-center gap-2">
                  <AlertTriangle className="w-4 h-4 shrink-0" />
                  <span>{formError}</span>
                </div>
              )}

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {/* Name */}
                <div className="sm:col-span-2">
                  <label className="block text-xs font-semibold text-[#181d1a] mb-1.5">
                    Nombre y Apellidos
                  </label>
                  <div className="relative">
                    <User className="w-4 h-4 absolute left-3.5 top-3 text-[#717973]" />
                    <input
                      type="text"
                      value={clientName}
                      onChange={e => setClientName(e.target.value)}
                      placeholder="Ej. Valentina Restrepo"
                      className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#f6fbf5] border border-[#c0c9c2] text-sm text-[#181d1a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                    />
                  </div>
                </div>

                {/* Email */}
                <div>
                  <label className="block text-xs font-semibold text-[#181d1a] mb-1.5">
                    Correo Electrónico
                  </label>
                  <div className="relative">
                    <Mail className="w-4 h-4 absolute left-3.5 top-3 text-[#717973]" />
                    <input
                      type="email"
                      value={clientEmail}
                      onChange={e => setClientEmail(e.target.value)}
                      placeholder="tu@correo.com"
                      className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#f6fbf5] border border-[#c0c9c2] text-sm text-[#181d1a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                    />
                  </div>
                  <span className="block text-[11px] text-[#717973] mt-1">
                    Recibirás tu confirmación con opción de reprogramar.
                  </span>
                </div>

                {/* Phone */}
                <div>
                  <label className="block text-xs font-semibold text-[#181d1a] mb-1.5">
                    Teléfono Celular / WhatsApp
                  </label>
                  <div className="relative">
                    <Phone className="w-4 h-4 absolute left-3.5 top-3 text-[#717973]" />
                    <input
                      type="tel"
                      value={clientPhone}
                      onChange={e => setClientPhone(e.target.value)}
                      placeholder="+57 300 000 0000"
                      className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#f6fbf5] border border-[#c0c9c2] text-sm text-[#181d1a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                    />
                  </div>
                  <span className="block text-[11px] text-[#717973] mt-1">
                    Recordatorio de cortesía 24h antes del turno.
                  </span>
                </div>

                {/* Notes */}
                <div className="sm:col-span-2">
                  <label className="block text-xs font-semibold text-[#181d1a] mb-1.5">
                    Notas Especiales, Alergias o Preferencias (Opcional)
                  </label>
                  <textarea
                    rows={2}
                    value={clientNotes}
                    onChange={e => setClientNotes(e.target.value)}
                    placeholder="Sensibilidad a fragancias, preferencia de presión moderada, etc."
                    className="w-full p-3 rounded-xl bg-[#f6fbf5] border border-[#c0c9c2] text-sm text-[#181d1a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2d5a46] resize-none"
                  />
                </div>
              </div>

              {/* Payment reassurance */}
              <div className="mt-5 p-4 rounded-2xl bg-[#ffdcbd]/30 border border-[#ffca98] flex items-center gap-3 text-[#7a532a]">
                <ShieldCheck className="w-6 h-6 shrink-0 text-[#7d562d]" />
                <div className="text-xs">
                  <span className="block font-bold text-[#2c1600]">
                    Pago seguro en recepción al recibir tu servicio
                  </span>
                  <span className="block text-[#623f18] mt-0.5">
                    No requerimos tarjeta de crédito por adelantado. Puedes abonar en efectivo, tarjeta o transferencia al finalizar tus rituales.
                  </span>
                </div>
              </div>
            </section>
          </div>

          {/* Right: Sticky Live Experience Summary Ticket */}
          <aside className="lg:col-span-4 sticky top-28 space-y-4">
            <div className="bg-white rounded-3xl border border-[#e5e9e4] shadow-xl overflow-hidden">
              {/* Header Badge */}
              <div className="bg-[#2d5a46] p-5 text-white">
                <div className="flex items-center justify-between mb-1">
                  <span className="text-[10px] uppercase tracking-widest text-[#bceed3] font-bold">
                    Resumen de Experiencia
                  </span>
                  <span className="px-2 py-0.5 rounded-full bg-[#134230] text-[#bceed3] text-[10px] font-bold">
                    En Vivo
                  </span>
                </div>
                <h3 className="font-serif-title text-xl font-semibold">Aura Sanctuary Pass</h3>
                <p className="text-xs text-[#9fcfb6] mt-0.5">{selectedBranch} • Sala Privada</p>
              </div>

              {/* Ticket Body */}
              <div className="p-5 flex flex-col gap-4">
                {/* Services list */}
                <div>
                  <span className="block text-[11px] uppercase tracking-wider text-[#717973] font-bold mb-2">
                    Servicios Programados ({selectedServices.length})
                  </span>

                  {selectedServices.length === 0 ? (
                    <div className="p-3 rounded-xl bg-[#f0f5f0] text-center text-xs text-[#717973] italic">
                      Selecciona al menos un servicio arriba
                    </div>
                  ) : (
                    <div className="space-y-2">
                      {selectedServices.map(srv => (
                        <div
                          key={srv.id}
                          className="flex items-center justify-between p-2.5 rounded-xl bg-[#f0f5f0] text-xs"
                        >
                          <div className="min-w-0 pr-2">
                            <span className="block font-semibold text-[#181d1a] truncate">{srv.name}</span>
                            <span className="block text-[11px] text-[#717973]">
                              {srv.durationMinutes} min • {srv.categoryLabel}
                            </span>
                          </div>
                          <span className="font-bold text-[#134230] shrink-0">${srv.priceUSD} USD</span>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {/* Logistics details */}
                <div className="p-3.5 rounded-2xl bg-[#f0f5f0]/80 space-y-2 text-xs">
                  <div className="flex items-center justify-between">
                    <span className="text-[#717973] flex items-center gap-1.5">
                      <User className="w-3.5 h-3.5 text-[#134230]" /> Especialista
                    </span>
                    <span className="font-semibold text-[#134230] truncate max-w-[150px]">
                      {selectedTherapist ? selectedTherapist.name : 'Cualquier terapeuta'}
                    </span>
                  </div>

                  <div className="flex items-center justify-between">
                    <span className="text-[#717973] flex items-center gap-1.5">
                      <CalendarIcon className="w-3.5 h-3.5 text-[#134230]" /> Fecha
                    </span>
                    <span className="font-semibold text-[#181d1a]">{selectedDate}</span>
                  </div>

                  <div className="flex items-center justify-between">
                    <span className="text-[#717973] flex items-center gap-1.5">
                      <Clock className="w-3.5 h-3.5 text-[#134230]" /> Hora de inicio
                    </span>
                    <span className="font-bold text-[#7d562d]">{selectedTimeSlot}</span>
                  </div>

                  <div className="flex items-center justify-between pt-1 border-t border-[#dfe4df]">
                    <span className="text-[#717973]">Tiempo en cabina</span>
                    <span className="font-medium text-[#181d1a]">
                      {totalDurationMinutes} min (+20 min buffer)
                    </span>
                  </div>
                </div>

                {/* Price Total */}
                <div className="flex items-end justify-between pt-1">
                  <div>
                    <span className="block text-[10px] uppercase tracking-wider text-[#717973] font-bold">
                      Total a pagar
                    </span>
                    <span className="text-[11px] text-[#414944]">En recepción al finalizar</span>
                  </div>
                  <div className="text-right">
                    <span className="font-serif-title font-bold text-2xl text-[#134230]">
                      ${totalPriceUSD} USD
                    </span>
                    <span className="block text-[10px] text-[#717973]">
                      Aprox. ${(totalPriceUSD * 3950).toLocaleString('es-CO')} COP
                    </span>
                  </div>
                </div>

                {/* Primary CTA */}
                <button
                  type="button"
                  onClick={handleConfirmBooking}
                  className="w-full py-3.5 px-4 rounded-xl bg-[#2d5a46] text-white font-semibold text-sm hover:bg-[#134230] shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer"
                >
                  <span>Confirmar Reserva de Cita</span>
                  <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>

                <div className="text-center">
                  <span className="text-[11px] text-[#717973] flex items-center justify-center gap-1">
                    <CalendarCheck className="w-3.5 h-3.5 text-[#2d5a46]" />
                    Cancelación y reprogramación gratuita hasta 8h antes.
                  </span>
                </div>
              </div>
            </div>

            {/* Quick VIP concierge card */}
            <div className="p-4 rounded-2xl bg-white border border-[#e5e9e4] shadow-sm flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-full bg-[#f0f5f0] text-[#134230] flex items-center justify-center">
                  <Sparkles className="w-4 h-4" />
                </div>
                <div>
                  <span className="block text-xs font-semibold text-[#181d1a]">¿Deseas plan para parejas?</span>
                  <span className="block text-[11px] text-[#717973]">Contacta con nuestro Concierge</span>
                </div>
              </div>
              <button
                type="button"
                onClick={() => alert('Conectando con Concierge VIP Aura Spa por WhatsApp...')}
                className="text-xs font-bold text-[#134230] hover:underline"
              >
                Chat VIP
              </button>
            </div>
          </aside>
        </div>
      </div>

      {/* Confirmation Modal */}
      {showModal && confirmedApp && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-200">
          <div className="bg-white max-w-lg w-full rounded-3xl p-6 sm:p-8 shadow-2xl border border-[#e5e9e4] animate-in zoom-in-95 duration-200 relative">
            <div className="w-16 h-16 rounded-full bg-[#bceed3] text-[#134230] flex items-center justify-center mx-auto mb-4">
              <CheckCircle2 className="w-9 h-9" />
            </div>

            <div className="text-center">
              <span className="text-[11px] uppercase tracking-widest text-[#7d562d] font-bold">
                ¡Reserva Exitosa!
              </span>
              <h3 className="font-serif-title text-2xl text-[#134230] font-semibold mt-1">
                Tu momento de paz está confirmado
              </h3>
              <p className="text-xs sm:text-sm text-[#414944] mt-2">
                Hemos enviado la confirmación y tu clave de acceso digital a{' '}
                <strong className="text-[#181d1a]">{confirmedApp.client.email}</strong>.
              </p>
            </div>

            {/* Ticket details summary */}
            <div className="my-5 p-4 rounded-2xl bg-[#f0f5f0] space-y-2.5 text-xs">
              <div className="flex items-center justify-between">
                <span className="text-[#717973]">Código de Cita:</span>
                <span className="font-mono font-bold text-[#134230] text-sm">{confirmedApp.code}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-[#717973]">Horario:</span>
                <span className="font-semibold text-[#181d1a]">
                  {confirmedApp.date} • {confirmedApp.startTime} - {confirmedApp.endTime}
                </span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-[#717973]">Especialista:</span>
                <span className="font-semibold text-[#181d1a]">{confirmedApp.assignedProfessionalName}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-[#717973]">Sede:</span>
                <span>{confirmedApp.location}</span>
              </div>
              <div className="flex items-center justify-between pt-1 border-t border-[#dfe4df]">
                <span className="text-[#717973]">Total a abonar en recepción:</span>
                <span className="font-bold text-sm text-[#134230]">${confirmedApp.totalPriceUSD} USD</span>
              </div>
            </div>

            <div className="space-y-2">
              <button
                type="button"
                onClick={() => {
                  setShowModal(false);
                  onOpenEmail(confirmedApp.code);
                }}
                className="w-full py-3 rounded-xl bg-[#2d5a46] text-white font-semibold text-xs sm:text-sm hover:bg-[#134230] transition-colors flex items-center justify-center gap-2"
              >
                <Mail className="w-4 h-4" />
                <span>Ver Correo de Confirmación en Vivo</span>
              </button>

              <button
                type="button"
                onClick={() => handleDownloadICS(confirmedApp)}
                className="w-full py-2.5 rounded-xl border border-[#c0c9c2] text-[#414944] hover:bg-[#f0f5f0] font-medium text-xs transition-colors flex items-center justify-center gap-2"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Descargar en Apple / Google Calendar (.ics)</span>
              </button>

              <button
                type="button"
                onClick={() => setShowModal(false)}
                className="w-full py-2 text-center text-[#717973] hover:text-[#181d1a] text-xs transition-colors"
              >
                Cerrar y Seguir Navegando
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
