/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

export type ServiceCategory = 'masajes' | 'faciales' | 'unas' | 'cabello' | 'corporal';

export interface RequiredSupply {
  supplyId: string;
  name: string;
  quantityPerService: number;
  unit: string;
}

export interface Service {
  id: string;
  name: string;
  description: string;
  category: ServiceCategory;
  categoryLabel: string;
  durationMinutes: number;
  priceUSD: number;
  active: boolean;
  activeSuppliesDescription: string;
  supplies: RequiredSupply[];
  inStock: boolean;
  outOfStockReason?: string;
  featured?: boolean;
}

export interface Professional {
  id: string;
  name: string;
  role: string;
  title: string;
  specialties: ServiceCategory[];
  rating: number;
  reviewsCount: number;
  avatar: string;
  shift: 'manana' | 'tarde' | 'completo';
  shiftLabel: string;
  weeklyCabinHours: number;
  completedAppointments: number;
  occupancyPercent: number;
  active: boolean;
}

export interface WorkSchedule {
  professionalId: string;
  dayOfWeek: number; // 0: Dom, 1: Lun, 2: Mar, 3: Mie, 4: Jue, 5: Vie, 6: Sab
  startTime: string; // "09:00"
  endTime: string;   // "19:00"
  isOff: boolean;
}

export interface Client {
  id: string;
  name: string;
  email: string;
  phone: string;
  notes?: string;
  createdAt: string;
}

export type AppointmentStatus = 'agendada' | 'completada' | 'cancelada' | 'reprogramada';

export interface Appointment {
  id: string;
  code: string; // e.g. "AUR-8842-KL"
  clientId: string;
  client: Client;
  serviceIds: string[];
  services: Service[];
  professionalId: string; // id or 'any'
  assignedProfessionalName: string;
  date: string; // "YYYY-MM-DD" e.g. "2025-10-22"
  startTime: string; // "10:30"
  endTime: string;   // "12:15"
  totalDurationMinutes: number;
  bufferMinutes: number; // typically 20
  totalPriceUSD: number;
  status: AppointmentStatus;
  location: string;
  notes?: string;
  createdAt: string;
  cancellationReason?: string;
  cancelledAt?: string;
  rescheduledAt?: string;
  manageToken: string;
}

export interface Supply {
  id: string;
  name: string;
  category: string;
  currentStock: number;
  minStock: number;
  unit: string;
  criticalPercent: number;
  status: 'critico' | 'reorden' | 'optimo';
  statusLabel: string;
  affectedAppointmentsCount: number;
  replenishmentETA?: string;
  recommendedAction?: string;
}

export interface NotificationEmail {
  id: string;
  appointmentId: string;
  appointmentCode: string;
  recipientEmail: string;
  recipientName: string;
  subject: string;
  type: 'confirmacion' | 'reprogramacion' | 'cancelacion' | 'recordatorio';
  sentAt: string;
  htmlContent: string;
  manageToken: string;
  previewSnippet: string;
}

export interface TestResult {
  id: string;
  title: string;
  description: string;
  passed: boolean;
  actual: string;
  expected: string;
  details: string;
  executionTimeMs: number;
}
