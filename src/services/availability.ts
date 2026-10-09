/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { Service, Professional, Appointment } from '../types';

export const HYGIENE_BUFFER_MINUTES = 20;
export const CANCELLATION_MIN_HOURS_BEFORE = 8;

export function timeToMinutes(timeStr: string): number {
  const [hours, minutes] = timeStr.split(':').map(Number);
  return hours * 60 + minutes;
}

export function minutesToTime(totalMinutes: number): string {
  const hours = Math.floor(totalMinutes / 60);
  const minutes = totalMinutes % 60;
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

export function calculateEndTime(startTime: string, durationMinutes: number): string {
  const startMins = timeToMinutes(startTime);
  const endMins = startMins + durationMinutes;
  return minutesToTime(endMins);
}

/**
 * Checks if two time windows overlap, taking into account the hygiene buffer of the existing slot.
 */
export function doIntervalsOverlap(
  startA: number,
  endA: number,
  startB: number,
  endB: number,
  bufferB: number = HYGIENE_BUFFER_MINUTES
): boolean {
  const effectiveEndB = endB + bufferB;
  return startA < effectiveEndB && endA > startB;
}

/**
 * Validates whether a therapist is free for the given slot on a specific date.
 */
export function isTherapistAvailable(
  professionalId: string,
  date: string,
  startTime: string,
  durationMinutes: number,
  existingAppointments: Appointment[],
  excludeAppointmentId?: string
): boolean {
  const proposedStart = timeToMinutes(startTime);
  const proposedEnd = proposedStart + durationMinutes;

  // Filter existing active appointments for this professional on this date
  const conflicting = existingAppointments.filter(app => {
    if (app.id === excludeAppointmentId) return false;
    if (app.status !== 'agendada') return false;
    if (app.date !== date) return false;
    if (app.professionalId !== professionalId) return false;

    const existingStart = timeToMinutes(app.startTime);
    const existingEnd = timeToMinutes(app.endTime);
    const existingBuffer = app.bufferMinutes ?? HYGIENE_BUFFER_MINUTES;

    return doIntervalsOverlap(proposedStart, proposedEnd, existingStart, existingEnd, existingBuffer);
  });

  return conflicting.length === 0;
}

/**
 * Finds all qualified therapists for the given services who are free during the proposed slot.
 */
export function getAvailableTherapistsForSlot(
  date: string,
  startTime: string,
  durationMinutes: number,
  services: Service[],
  professionals: Professional[],
  existingAppointments: Appointment[],
  excludeAppointmentId?: string
): Professional[] {
  // Required categories
  const categories = new Set(services.map(s => s.category));

  return professionals.filter(prof => {
    if (!prof.active) return false;

    // Check if therapist has expertise in at least one required category or generalist
    const hasCategory = prof.specialties.some(sp => categories.has(sp));
    if (!hasCategory) return false;

    // Check therapist availability
    return isTherapistAvailable(
      prof.id,
      date,
      startTime,
      durationMinutes,
      existingAppointments,
      excludeAppointmentId
    );
  });
}

/**
 * Verifies if an appointment can be cancelled or rescheduled according to the 8-hour policy.
 */
export function canModifyAppointment(
  appointment: Appointment,
  currentDate: Date = new Date()
): { allowed: boolean; reason?: string; hoursRemaining: number } {
  const [year, month, day] = appointment.date.split('-').map(Number);
  const [hours, mins] = appointment.startTime.split(':').map(Number);
  const appointmentDate = new Date(year, month - 1, day, hours, mins);

  const diffMs = appointmentDate.getTime() - currentDate.getTime();
  const diffHours = diffMs / (1000 * 60 * 60);

  if (diffHours < CANCELLATION_MIN_HOURS_BEFORE) {
    return {
      allowed: false,
      hoursRemaining: Math.max(0, Number(diffHours.toFixed(1))),
      reason: `La política de Aura Spa exige al menos ${CANCELLATION_MIN_HOURS_BEFORE} horas de anticipación. Faltan ${Math.max(0, diffHours).toFixed(1)} horas para el turno. Por favor contacta a nuestro Concierge directo.`,
    };
  }

  return {
    allowed: true,
    hoursRemaining: Number(diffHours.toFixed(1)),
  };
}

/**
 * Generates available daily slots for standard operating hours (09:00 - 20:30)
 */
export const DAILY_SLOTS = [
  // Turno Mañana
  { time: '09:00', label: '09:00 AM', period: 'manana' },
  { time: '10:30', label: '10:30 AM', period: 'manana' },
  { time: '11:45', label: '11:45 AM', period: 'manana' },
  { time: '12:30', label: '12:30 PM', period: 'manana' },
  // Turno Tarde
  { time: '14:00', label: '02:00 PM', period: 'tarde' },
  { time: '15:45', label: '03:45 PM', period: 'tarde' },
  { time: '17:15', label: '05:15 PM', period: 'tarde' },
  { time: '18:30', label: '06:30 PM', period: 'tarde' },
  // Turno Crepúsculo
  { time: '19:00', label: '07:00 PM', period: 'crepusculo' },
  { time: '20:00', label: '08:00 PM', period: 'crepusculo' },
];

/**
 * Checks slot availability given selected services and selected specialist (id or 'any').
 */
export function checkSlotAvailability(
  slotTime: string,
  date: string,
  totalDurationMinutes: number,
  selectedProfessionalId: string, // 'any' or professional ID
  services: Service[],
  professionals: Professional[],
  existingAppointments: Appointment[]
): { available: boolean; reason?: string; assignedProfessional?: Professional } {
  if (selectedProfessionalId === 'any') {
    const qualifiedAvailable = getAvailableTherapistsForSlot(
      date,
      slotTime,
      totalDurationMinutes,
      services,
      professionals,
      existingAppointments
    );

    if (qualifiedAvailable.length > 0) {
      // Pick best therapist (e.g. lowest weekly cabin hours or highest rating)
      const sorted = [...qualifiedAvailable].sort((a, b) => b.rating - a.rating);
      return { available: true, assignedProfessional: sorted[0] };
    }

    return { available: false, reason: 'Todos los especialistas calificados están ocupados en este horario.' };
  } else {
    const prof = professionals.find(p => p.id === selectedProfessionalId);
    if (!prof) {
      return { available: false, reason: 'Especialista no encontrado.' };
    }

    const free = isTherapistAvailable(
      prof.id,
      date,
      slotTime,
      totalDurationMinutes,
      existingAppointments
    );

    if (free) {
      return { available: true, assignedProfessional: prof };
    }

    return { available: false, reason: `${prof.name} tiene un turno ocupado en este horario o en su buffer de higienización.` };
  }
}
