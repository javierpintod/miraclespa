/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import {
  Service,
  Professional,
  WorkSchedule,
  Appointment,
  Supply,
  NotificationEmail,
} from '../types';

const STORAGE_KEYS = {
  SERVICES: 'aura_spa_services_v1',
  PROFESSIONALS: 'aura_spa_professionals_v1',
  SCHEDULES: 'aura_spa_schedules_v1',
  APPOINTMENTS: 'aura_spa_appointments_v1',
  SUPPLIES: 'aura_spa_supplies_v1',
  EMAILS: 'aura_spa_emails_v1',
};

// Initial services matching Image 2 & 1
const INITIAL_SERVICES: Service[] = [
  {
    id: 'srv-1',
    name: 'Masaje Relajante Aromaterapia',
    description: 'Fusión de aceites esenciales de lavanda silvestre y bergamota para liberar nudos musculares y descompresión nerviosa.',
    category: 'masajes',
    categoryLabel: 'Corporal / Holístico',
    durationMinutes: 60,
    priceUSD: 50,
    active: true,
    activeSuppliesDescription: 'Aceite bio de jojoba & aceite esencial lavanda',
    supplies: [{ supplyId: 'sup-1', name: 'Aceite Eucalipto / Lavanda', quantityPerService: 50, unit: 'ml' }],
    inStock: true,
    featured: true,
  },
  {
    id: 'srv-2',
    name: 'Limpieza Facial Profunda Hidratante',
    description: 'Exfoliación enzimática suave con mascarilla botánica de ácido hialurónico marino y vapor ozonizado.',
    category: 'faciales',
    categoryLabel: 'Dermatología Facial',
    durationMinutes: 50,
    priceUSD: 45,
    active: true,
    activeSuppliesDescription: 'Sérum hialurónico, gasas estériles, extracto té verde',
    supplies: [{ supplyId: 'sup-5', name: 'Ampollas Hialurónico', quantityPerService: 1, unit: 'ampolla' }],
    inStock: true,
  },
  {
    id: 'srv-3',
    name: 'Manicura Spa Rusa & Nutrición',
    description: 'Limpieza técnica de cutícula en seco, hidratación profunda con manteca de karité pura y esmaltado semipermanente vegano.',
    category: 'unas',
    categoryLabel: 'Belleza & Uñas',
    durationMinutes: 45,
    priceUSD: 35,
    active: true,
    activeSuppliesDescription: 'Brocas diamante, manteca karité, esmalte no-tóxico',
    supplies: [{ supplyId: 'sup-4', name: 'Fresas Carburo & Gel', quantityPerService: 1, unit: 'kit' }],
    inStock: true,
    featured: true,
  },
  {
    id: 'srv-4',
    name: 'Pedicura Deluxe con Sales Minerales',
    description: 'Inmersión en hidromasaje con sales de Epsom, exfoliación de bambú y mascarilla térmica envolvente para pies cansados.',
    category: 'unas',
    categoryLabel: 'Belleza & Uñas',
    durationMinutes: 55,
    priceUSD: 40,
    active: true,
    activeSuppliesDescription: 'Sales de Epsom, exfoliante bambú, toallas térmicas',
    supplies: [{ supplyId: 'sup-2', name: 'Toallas de Bambú', quantityPerService: 2, unit: 'unidades' }],
    inStock: true,
  },
  {
    id: 'srv-5',
    name: 'Corte de Autor & Estilo Botánico',
    description: 'Diagnóstico morfológico capilar, lavado con champú clarificante de romero y finalizado con brushing natural.',
    category: 'cabello',
    categoryLabel: 'Hair Salon & Color',
    durationMinutes: 45,
    priceUSD: 38,
    active: true,
    activeSuppliesDescription: 'Champú herbal orgánico, sérum de argán marroquí',
    supplies: [{ supplyId: 'sup-2', name: 'Toallas de Bambú', quantityPerService: 1, unit: 'unidad' }],
    inStock: true,
  },
  {
    id: 'srv-6',
    name: 'Balayage & Tinte Botánico Ecológico',
    description: 'Aclarado gradual sin amoníaco enriquecido con polifenoles de uva y aceites reconstructores de lino.',
    category: 'cabello',
    categoryLabel: 'Hair Salon & Color',
    durationMinutes: 120,
    priceUSD: 95,
    active: true,
    activeSuppliesDescription: 'Kit de pigmentos veganos tono Ámbar y Miel',
    supplies: [{ supplyId: 'sup-3', name: 'Decolorante Botánico', quantityPerService: 1, unit: 'kit' }],
    inStock: false,
    outOfStockReason: 'Falta en inventario central: Kit de pigmentos veganos tono Ámbar y Miel (Reabastecimiento: Viernes)',
  },
  {
    id: 'srv-7',
    name: 'Masaje Descontracturante Piedras Volcánicas',
    description: 'Terapia termo-mecánica con piedras de basalto volcánico caliente para contracturas cervicales y lumbares crónicas.',
    category: 'masajes',
    categoryLabel: 'Corporal / Holístico',
    durationMinutes: 75,
    priceUSD: 65,
    active: true,
    activeSuppliesDescription: 'Aceite bio de romero, piedras de basalto tratadas',
    supplies: [{ supplyId: 'sup-1', name: 'Aceite Eucalipto / Lavanda', quantityPerService: 40, unit: 'ml' }],
    inStock: true,
  },
  {
    id: 'srv-8',
    name: 'Facial Glow & Hidratación Profunda',
    description: 'Tratamiento iluminador con vitamina C activa, microcorrientes tensoras y ampolla revitalizante.',
    category: 'faciales',
    categoryLabel: 'Dermatología Facial',
    durationMinutes: 60,
    priceUSD: 55,
    active: true,
    activeSuppliesDescription: 'Ampollas Vitamina C, velo de colágeno botánico',
    supplies: [{ supplyId: 'sup-5', name: 'Ampollas Hialurónico', quantityPerService: 1, unit: 'ampolla' }],
    inStock: true,
  },
];

// Initial professionals matching Image 1 & 2
const INITIAL_PROFESSIONALS: Professional[] = [
  {
    id: 'prof-1',
    name: 'Camila Soto',
    role: 'Terapeuta Holística Senior',
    title: 'Terapeuta Holística & Aromaterapia',
    specialties: ['masajes', 'corporal'],
    rating: 4.9,
    reviewsCount: 142,
    avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAoF6KpKpdagWImJBuR0ldX2Gj_868S0_U-wgXdrK05K45_r-keVHQK9TTQ0jzn76FcFRFC6_b8DjgrkCXDKV_rWLQEx0mHzE-05uHS8riy0RL8pIJ2H72hxtHwDRhcLGbCa0bNtCqXHHt9XIks7r0ULMwVtkiLeTRrO9XvUMRFJu5INIit47JqeOzT2Q6gN7VhNLhcCxJzOnc_6TMQqeIeidgyuaXiTbr9G6F6I_uTcRKEvPAi5MrGVg',
    shift: 'completo',
    shiftLabel: 'Turno am / pm',
    weeklyCabinHours: 38,
    completedAppointments: 46,
    occupancyPercent: 88,
    active: true,
  },
  {
    id: 'prof-2',
    name: 'Elena Morales',
    role: 'Cosmiatra & Dermocosmetóloga',
    title: 'Master Color & Cuidado Capilar',
    specialties: ['faciales', 'cabello'],
    rating: 4.95,
    reviewsCount: 210,
    avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDLto_3HYMD8HSdSCvZrB4KZrqvl6d4FbhHHZIgQAJS-81MCtlFw7rFMDF__yxurwVQ1Mjt8QNpi2UGd4av2DqJogd_NlmEaDmpVCo0YsRWWwzC7sROQOTpwOa5-Lj9EgJSQB2fh9HEMUEWAdONZF0QOQ7WJET-A5fOm1HDCkyDm3Fvp-1yn_pxbAgiHUwxNYaYOHGmfMcba9Ro6bHQb3Mriw0J39iEhOLE9vyxXlQRbtHAI0FevgVlLA',
    shift: 'tarde',
    shiftLabel: 'Turno tarde',
    weeklyCabinHours: 35,
    completedAppointments: 39,
    occupancyPercent: 82,
    active: true,
  },
  {
    id: 'prof-3',
    name: 'Mateo Ríos',
    role: 'Master Colorist & Stylist',
    title: 'Cosmiatría & Uñas Spa Rusas',
    specialties: ['cabello', 'unas'],
    rating: 4.88,
    reviewsCount: 98,
    avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBFhZcZXDl34mrzhPcsm-VvKIzbOdwhjDxO0aX2zcg0GLTK4MVjnSW4Ofgc151-av_42yiG6GxqY9rIdN9_t5WkeAgezUKTdH_L8gtyIR9ksvKVms3apDcu4FfvgrDLxEIy2Mb2_ri0wl61QtgWroQ3qTRbmYkw7EZgwRTl3b3z894198Pq_jUgjxiYYZT6UqVGP87hUQD_GzyDRZWt9cXC94BICrPJ1uEAwUYIBl3JCL-4ja9-JvMcgQ',
    shift: 'manana',
    shiftLabel: 'Turno mañana',
    weeklyCabinHours: 31,
    completedAppointments: 28,
    occupancyPercent: 74,
    active: true,
  },
  {
    id: 'prof-4',
    name: 'Sofía Chen',
    role: 'Especialista en Manicura Rusa',
    title: 'Especialista en Manicura Rusa & Podología',
    specialties: ['unas'],
    rating: 4.92,
    reviewsCount: 165,
    avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCvv1hPTHFCjtkaMOVw0ALs9WKscGtMkQCLZEijIuT3p_mhEpwyqXeQk-JFlHZU-81kFx7umg7CGPP4AsV5wu_VRQGOdWUHOhBpq6-8_iqtN5lk9qcy62Q4odn8044FIJ8i5JGh6bbQSMkx4NofJIRfydZR4bR61ugmHru9xGHOYT-xgYx2dcxTnGx6vJsWzaU-fnZB8wYxunKmzlOnM_5r0uNL9Upk34YhG4_y2b6md1s3Ias9dmt5bA',
    shift: 'completo',
    shiftLabel: 'Turno am / pm',
    weeklyCabinHours: 33,
    completedAppointments: 42,
    occupancyPercent: 79,
    active: true,
  },
];

// Initial supplies matching Image 1
const INITIAL_SUPPLIES: Supply[] = [
  {
    id: 'sup-1',
    name: 'Aceite Eucalipto Orgánico',
    category: 'Aceites & Esencias',
    currentStock: 250,
    minStock: 1500,
    unit: 'ml',
    criticalPercent: 15,
    status: 'critico',
    statusLabel: '15% crítico',
    affectedAppointmentsCount: 24,
    recommendedAction: 'Pedir a Bodega central - Riesgo alto en cabinas',
  },
  {
    id: 'sup-2',
    name: 'Toallas de Bambú Pre-lavadas',
    category: 'Lencería & Textil',
    currentStock: 35,
    minStock: 160,
    unit: 'unidades',
    criticalPercent: 22,
    status: 'reorden',
    statusLabel: '22% reorden',
    affectedAppointmentsCount: 18,
    replenishmentETA: 'Mañana 08:30 AM',
    recommendedAction: 'Lote actual con 32 ciclos de lavado',
  },
  {
    id: 'sup-3',
    name: 'Decolorante Botánico',
    category: 'Color & Capilar',
    currentStock: 2,
    minStock: 20,
    unit: 'kits',
    criticalPercent: 10,
    status: 'critico',
    statusLabel: 'Agotado',
    affectedAppointmentsCount: 8,
    replenishmentETA: 'Viernes 10:00 AM',
    recommendedAction: 'Servicio Balayage bloqueado temporalmente',
  },
  {
    id: 'sup-4',
    name: 'Fresas Carburo & Gel',
    category: 'Uñas & Manicura',
    currentStock: 120,
    minStock: 150,
    unit: 'sets',
    criticalPercent: 78,
    status: 'optimo',
    statusLabel: '78% estable',
    affectedAppointmentsCount: 0,
  },
  {
    id: 'sup-5',
    name: 'Ampollas Hialurónico',
    category: 'Dermatología Facial',
    currentStock: 95,
    minStock: 100,
    unit: 'ampollas',
    criticalPercent: 85,
    status: 'optimo',
    statusLabel: '85% óptimo',
    affectedAppointmentsCount: 0,
  },
];

// Initial appointments
const INITIAL_APPOINTMENTS: Appointment[] = [
  {
    id: 'app-seed-1',
    code: 'AUR-8842-KL',
    clientId: 'cli-1',
    client: {
      id: 'cli-1',
      name: 'Valentina Restrepo',
      email: 'valentina.restrepo@email.com',
      phone: '+57 312 890 4421',
      notes: 'Sensibilidad moderada en piel, preferencia de presión media.',
      createdAt: '2025-10-18T10:00:00Z',
    },
    serviceIds: ['srv-1', 'srv-3'],
    services: [INITIAL_SERVICES[0], INITIAL_SERVICES[2]],
    professionalId: 'prof-1',
    assignedProfessionalName: 'Camila Soto',
    date: '2025-10-22',
    startTime: '10:30',
    endTime: '12:15',
    totalDurationMinutes: 105,
    bufferMinutes: 20,
    totalPriceUSD: 85,
    status: 'agendada',
    location: 'Sede Principal - Las Palmas, Sala Zen 1 & Cabina 3',
    notes: 'Pago en recepción al terminar la sesión.',
    createdAt: '2025-10-19T14:30:00Z',
    manageToken: 'tok_aur_8842_valentina',
  },
  {
    id: 'app-seed-2',
    code: 'AUR-9120-XB',
    clientId: 'cli-2',
    client: {
      id: 'cli-2',
      name: 'Andrés Felipe Gómez',
      email: 'af.gomez@empresa.com',
      phone: '+57 301 445 8899',
      createdAt: '2025-10-18T11:00:00Z',
    },
    serviceIds: ['srv-1'],
    services: [INITIAL_SERVICES[0]],
    professionalId: 'prof-1',
    assignedProfessionalName: 'Camila Soto',
    date: '2025-10-22',
    startTime: '12:30',
    endTime: '13:30',
    totalDurationMinutes: 60,
    bufferMinutes: 20,
    totalPriceUSD: 50,
    status: 'agendada',
    location: 'Sede Principal - Las Palmas, Cabina Zen 1',
    createdAt: '2025-10-18T12:00:00Z',
    manageToken: 'tok_aur_9120_andres',
  },
  {
    id: 'app-seed-3',
    code: 'AUR-7731-MN',
    clientId: 'cli-3',
    client: {
      id: 'cli-3',
      name: 'Mariana Duarte',
      email: 'marianad@gmail.com',
      phone: '+57 310 999 1122',
      createdAt: '2025-10-17T09:00:00Z',
    },
    serviceIds: ['srv-2'],
    services: [INITIAL_SERVICES[1]],
    professionalId: 'prof-2',
    assignedProfessionalName: 'Elena Morales',
    date: '2025-10-22',
    startTime: '20:00',
    endTime: '20:50',
    totalDurationMinutes: 50,
    bufferMinutes: 20,
    totalPriceUSD: 45,
    status: 'agendada',
    location: 'Sede Principal - Las Palmas, Suite Facial',
    createdAt: '2025-10-17T09:30:00Z',
    manageToken: 'tok_aur_7731_mariana',
  },
  {
    id: 'app-seed-4',
    code: 'AUR-5510-CT',
    clientId: 'cli-4',
    client: {
      id: 'cli-4',
      name: 'Carolina Toro',
      email: 'caro.toro@outlook.com',
      phone: '+57 315 220 3344',
      createdAt: '2025-10-16T15:00:00Z',
    },
    serviceIds: ['srv-4'],
    services: [INITIAL_SERVICES[3]],
    professionalId: 'prof-4',
    assignedProfessionalName: 'Sofía Chen',
    date: '2025-10-22',
    startTime: '14:00',
    endTime: '14:55',
    totalDurationMinutes: 55,
    bufferMinutes: 20,
    totalPriceUSD: 40,
    status: 'agendada',
    location: 'Sede Principal - Las Palmas, Sillón Hidromasaje',
    createdAt: '2025-10-16T16:00:00Z',
    manageToken: 'tok_aur_5510_carolina',
  },
  {
    id: 'app-seed-5',
    code: 'AUR-3329-JS',
    clientId: 'cli-5',
    client: {
      id: 'cli-5',
      name: 'Juan Sebastián Morales',
      email: 'juanse@morales.co',
      phone: '+57 320 887 6655',
      createdAt: '2025-10-15T08:00:00Z',
    },
    serviceIds: ['srv-5'],
    services: [INITIAL_SERVICES[4]],
    professionalId: 'prof-3',
    assignedProfessionalName: 'Mateo Ríos',
    date: '2025-10-22',
    startTime: '09:00',
    endTime: '09:45',
    totalDurationMinutes: 45,
    bufferMinutes: 20,
    totalPriceUSD: 38,
    status: 'completada',
    location: 'Sede Principal - Las Palmas, Estación Hair Art',
    createdAt: '2025-10-15T09:00:00Z',
    manageToken: 'tok_aur_3329_juanse',
  },
];

export const SpaStorage = {
  getServices(): Service[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.SERVICES);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.SERVICES, JSON.stringify(INITIAL_SERVICES));
        return INITIAL_SERVICES;
      }
      return JSON.parse(data);
    } catch {
      return INITIAL_SERVICES;
    }
  },

  saveServices(services: Service[]): void {
    localStorage.setItem(STORAGE_KEYS.SERVICES, JSON.stringify(services));
  },

  getProfessionals(): Professional[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.PROFESSIONALS);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.PROFESSIONALS, JSON.stringify(INITIAL_PROFESSIONALS));
        return INITIAL_PROFESSIONALS;
      }
      return JSON.parse(data);
    } catch {
      return INITIAL_PROFESSIONALS;
    }
  },

  saveProfessionals(professionals: Professional[]): void {
    localStorage.setItem(STORAGE_KEYS.PROFESSIONALS, JSON.stringify(professionals));
  },

  getSupplies(): Supply[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.SUPPLIES);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.SUPPLIES, JSON.stringify(INITIAL_SUPPLIES));
        return INITIAL_SUPPLIES;
      }
      return JSON.parse(data);
    } catch {
      return INITIAL_SUPPLIES;
    }
  },

  saveSupplies(supplies: Supply[]): void {
    localStorage.setItem(STORAGE_KEYS.SUPPLIES, JSON.stringify(supplies));
  },

  getAppointments(): Appointment[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.APPOINTMENTS);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.APPOINTMENTS, JSON.stringify(INITIAL_APPOINTMENTS));
        return INITIAL_APPOINTMENTS;
      }
      return JSON.parse(data);
    } catch {
      return INITIAL_APPOINTMENTS;
    }
  },

  saveAppointments(appointments: Appointment[]): void {
    localStorage.setItem(STORAGE_KEYS.APPOINTMENTS, JSON.stringify(appointments));
  },

  addAppointment(appointment: Appointment): void {
    const list = this.getAppointments();
    list.unshift(appointment);
    this.saveAppointments(list);
  },

  updateAppointment(appointment: Appointment): void {
    const list = this.getAppointments();
    const index = list.findIndex(a => a.id === appointment.id);
    if (index !== -1) {
      list[index] = appointment;
      this.saveAppointments(list);
    }
  },

  getEmails(): NotificationEmail[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.EMAILS);
      if (!data) return [];
      return JSON.parse(data);
    } catch {
      return [];
    }
  },

  addEmail(email: NotificationEmail): void {
    const list = this.getEmails();
    list.unshift(email);
    localStorage.setItem(STORAGE_KEYS.EMAILS, JSON.stringify(list));
  },

  resetDefaults(): void {
    localStorage.removeItem(STORAGE_KEYS.SERVICES);
    localStorage.removeItem(STORAGE_KEYS.PROFESSIONALS);
    localStorage.removeItem(STORAGE_KEYS.APPOINTMENTS);
    localStorage.removeItem(STORAGE_KEYS.SUPPLIES);
    localStorage.removeItem(STORAGE_KEYS.EMAILS);
  },
};
