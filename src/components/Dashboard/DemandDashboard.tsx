/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Download,
  AlertTriangle,
  Star,
  Clock,
  ArrowUp,
  CheckCircle,
  Package,
  Layers,
  CalendarCheck,
  Percent,
} from 'lucide-react';
import { Professional, Supply, Service, Appointment } from '../../types';

interface DemandDashboardProps {
  professionals: Professional[];
  supplies: Supply[];
  services: Service[];
  appointments: Appointment[];
  selectedBranch: string;
  onNavigateToInventory: () => void;
  onNavigateToCalendar: () => void;
}

type Period = 'Hoy' | 'Esta Semana' | 'Este Mes' | 'Últimos 90 días';

export const DemandDashboard: React.FC<DemandDashboardProps> = ({
  professionals,
  supplies,
  services,
  selectedBranch,
  onNavigateToInventory,
  onNavigateToCalendar,
}) => {
  const [selectedPeriod, setSelectedPeriod] = useState<Period>('Este Mes');
  const [toastMessage, setToastMessage] = useState<string | null>(null);
  const [selectedBarHour, setSelectedBarHour] = useState<string | null>(null);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => setToastMessage(null), 3500);
  };

  const handleExport = () => {
    showToast('Generando informe ejecutivo de demanda clínica (PDF / Excel)...');
    // Simulated export
    setTimeout(() => {
      const csv = `Periodo,Eficiencia Cabinas,Volumen Agendado,Ingresos Proyectados,Ticket Promedio\n${selectedPeriod},78.4%,342,$14820 USD,$43.30 USD`;
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `Aura_Spa_Reporte_${selectedPeriod.replace(/\s+/g, '_')}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }, 1000);
  };

  // Hourly distribution bars
  const hourlyData = [
    { hour: '08h', pct: 38, isPeak: false },
    { hour: '09h', pct: 52, isPeak: false },
    { hour: '10h', pct: 72, isPeak: false },
    { hour: '11h', pct: 96, isPeak: true },
    { hour: '12h', pct: 64, isPeak: false },
    { hour: '13h', pct: 48, isPeak: false },
    { hour: '14h', pct: 56, isPeak: false },
    { hour: '15h', pct: 66, isPeak: false },
    { hour: '16h', pct: 80, isPeak: false },
    { hour: '17h', pct: 94, isPeak: true },
    { hour: '18h', pct: 84, isPeak: false },
    { hour: '19h', pct: 60, isPeak: false },
    { hour: '20h', pct: 35, isPeak: false },
  ];

  // Most demanded services data from Screen 1
  const topServices = [
    {
      id: 'srv-1',
      name: 'Masaje Relajante Aromaterapia',
      category: 'Corporal / Holístico',
      totalAppointments: 89,
      revenueUSD: 4895,
      criticalSupply: 'Aceite Eucalipto',
      supplyPercent: 15,
      supplyStatus: 'critico',
      supplyLabel: '15% crítico',
      bulletColor: '#134230',
    },
    {
      id: 'srv-3',
      name: 'Manicura Spa Rusa',
      category: 'Belleza & Uñas',
      totalAppointments: 74,
      revenueUSD: 2590,
      criticalSupply: 'Fresas Carburo & Gel',
      supplyPercent: 78,
      supplyStatus: 'optimo',
      supplyLabel: '78% estable',
      bulletColor: '#7d562d',
    },
    {
      id: 'srv-6',
      name: 'Balayage Orgánico',
      category: 'Hair Salon & Color',
      totalAppointments: 42,
      revenueUSD: 3780,
      criticalSupply: 'Decolorante Botánico',
      supplyPercent: 28,
      supplyStatus: 'reorden',
      supplyLabel: '28% reorden',
      bulletColor: '#413a24',
    },
    {
      id: 'srv-8',
      name: 'Facial Glow & Hidratación Profunda',
      category: 'Dermatología Facial',
      totalAppointments: 38,
      revenueUSD: 2280,
      criticalSupply: 'Ampollas Hialurónico',
      supplyPercent: 85,
      supplyStatus: 'optimo',
      supplyLabel: '85% óptimo',
      bulletColor: '#a1d1b8',
    },
  ];

  // Sort professionals by occupancy
  const sortedProfessionals = [...professionals].sort((a, b) => b.occupancyPercent - a.occupancyPercent);

  return (
    <div className="w-full pb-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      {/* Top Bar / Executive Controls */}
      <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div className="space-y-1">
          <div className="flex items-center gap-2 flex-wrap">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#bceed3]/40 text-[#134230] text-[11px] font-bold">
              <span className="w-2 h-2 rounded-full bg-[#134230] animate-pulse" />
              Spa Abierto • {professionals.length} Especialistas en Turno
            </span>
            <span className="text-[#717973] text-xs font-medium">• {selectedBranch}</span>
          </div>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold tracking-tight">
            Panel de Demanda y Ocupación Clínica
          </h1>
          <p className="text-xs sm:text-sm text-[#414944]">
            Métricas consolidadas de rendimiento operativo, concurrencia de agendas y salud de stock.
          </p>
        </div>

        {/* Period Selector & Action Buttons */}
        <div className="flex items-center gap-2.5 flex-wrap">
          <div className="inline-flex p-1 bg-[#ebefea] rounded-xl shadow-inner text-xs font-semibold">
            {(['Hoy', 'Esta Semana', 'Este Mes', 'Últimos 90 días'] as Period[]).map(period => (
              <button
                key={period}
                onClick={() => {
                  setSelectedPeriod(period);
                  showToast(`Filtro aplicado: ${period}`);
                }}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  selectedPeriod === period
                    ? 'bg-white text-[#134230] shadow-sm font-bold'
                    : 'text-[#414944] hover:text-[#181d1a]'
                }`}
              >
                {period}
              </button>
            ))}
          </div>

          <button
            onClick={handleExport}
            className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white text-[#134230] text-xs font-semibold shadow-sm border border-[#e5e9e4] hover:bg-[#f0f5f0] transition-all"
          >
            <Download className="w-4 h-4 text-[#134230]" />
            <span>Exportar Informe</span>
          </button>
        </div>
      </div>

      {/* Primary Executive KPI Bento Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 my-8">
        {/* KPI 1: Eficiencia de Cabinas */}
        <div className="bg-white p-6 rounded-2xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between">
            <div>
              <span className="text-[11px] uppercase tracking-wider font-bold text-[#717973]">
                Eficiencia de Cabinas
              </span>
              <div className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-bold mt-1">
                78.4%
              </div>
            </div>
            <div className="w-10 h-10 rounded-xl bg-[#bceed3]/50 flex items-center justify-center text-[#134230]">
              <Percent className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4 space-y-2">
            <div className="flex items-center justify-between text-xs">
              <span className="text-[#414944]">Horas reales vs teóricas</span>
              <span className="inline-flex items-center text-[#134230] font-bold">
                <ArrowUp className="w-3.5 h-3.5 mr-0.5" /> +12.3%
              </span>
            </div>
            <div className="w-full bg-[#ebefea] h-1.5 rounded-full overflow-hidden">
              <div className="bg-[#134230] h-full rounded-full transition-all duration-500" style={{ width: '78.4%' }} />
            </div>
            <span className="text-[#717973] text-[11px] block">Meta mensual: 80.0% (Brecha -1.6%)</span>
          </div>
        </div>

        {/* KPI 2: Volumen Agendado */}
        <div className="bg-white p-6 rounded-2xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between">
            <div>
              <span className="text-[11px] uppercase tracking-wider font-bold text-[#717973]">
                Volumen Agendado
              </span>
              <div className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-bold mt-1">
                342
              </div>
            </div>
            <div className="w-10 h-10 rounded-xl bg-[#ffdcbd]/50 flex items-center justify-center text-[#7d562d]">
              <CalendarCheck className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4 pt-1 grid grid-cols-3 gap-1 text-center bg-[#f0f5f0] p-2 rounded-xl text-xs">
            <div>
              <span className="block font-bold text-[#134230]">318</span>
              <span className="text-[10px] text-[#717973]">Completadas</span>
            </div>
            <div>
              <span className="block font-bold text-[#7d562d]">18</span>
              <span className="text-[10px] text-[#717973]">En agenda</span>
            </div>
            <div>
              <span className="block font-bold text-[#414944]">6</span>
              <span className="text-[10px] text-[#717973]">&gt;8h canc.</span>
            </div>
          </div>
        </div>

        {/* KPI 3: Atomic Concurrency Guarantee */}
        <div className="bg-white p-6 rounded-2xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between">
            <div>
              <span className="text-[11px] uppercase tracking-wider font-bold text-[#717973]">
                Garantía de Cabina
              </span>
              <div className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-bold mt-1">
                0.0%
              </div>
            </div>
            <div className="w-10 h-10 rounded-xl bg-[#bceed3]/40 flex items-center justify-center text-[#134230]">
              <CheckCircle className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4 space-y-1 text-xs">
            <div className="flex items-center gap-1.5 text-[#134230] font-bold">
              <CheckCircle className="w-4 h-4 text-[#134230]" />
              <span>Bloqueo Pesimista Activo</span>
            </div>
            <p className="text-[#414944] text-[11px] leading-tight">
              Sin colisiones de agenda ni sobre-asignación de terapeutas.
            </p>
            <span className="text-[#717973] text-[10px] block">Concurrencia atómica PostgreSQL</span>
          </div>
        </div>

        {/* KPI 4: Ingresos Proyectados */}
        <div className="bg-white p-6 rounded-2xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between">
            <div>
              <span className="text-[11px] uppercase tracking-wider font-bold text-[#717973]">
                Ingresos Proyectados
              </span>
              <div className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-bold mt-1">
                $14,820 <span className="text-xs font-normal text-[#414944]">USD</span>
              </div>
            </div>
            <div className="w-10 h-10 rounded-xl bg-[#eee2c2]/60 flex items-center justify-center text-[#413a24]">
              <Layers className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4 flex items-center justify-between p-2 rounded-xl bg-[#f0f5f0] text-xs">
            <div>
              <span className="block text-[10px] text-[#717973]">Ticket Promedio</span>
              <span className="font-bold text-[#134230]">$43.30 USD / sesión</span>
            </div>
            <div className="flex items-center gap-0.5 text-[#134230] font-bold text-xs">
              <ArrowUp className="w-3.5 h-3.5" />
              <span>+4.8%</span>
            </div>
          </div>
        </div>
      </div>

      {/* Analytical Visualizations Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        {/* Chart 1: Hourly Distribution (08:00 - 20:00) */}
        <div className="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
              <h2 className="font-semibold text-lg text-[#134230]">Distribución de Horas Pico</h2>
              <p className="text-xs text-[#414944]">
                Franjas horarias de mayor afluencia (08:00 - 20:00). Picos a las 11:00 AM y 17:00 PM.
              </p>
            </div>
            <div className="flex items-center gap-3 text-xs">
              <span className="inline-flex items-center gap-1.5 text-[#414944]">
                <span className="w-2.5 h-2.5 rounded-full bg-[#134230]" /> Pico Alto (≥85%)
              </span>
              <span className="inline-flex items-center gap-1.5 text-[#414944]">
                <span className="w-2.5 h-2.5 rounded-full bg-[#a1d1b8]" /> Regular
              </span>
            </div>
          </div>

          {/* Interactive Bar Chart Container */}
          <div className="w-full h-64 flex flex-col justify-end pt-4">
            <div className="w-full h-52 flex items-end justify-between gap-1.5 sm:gap-2 px-1">
              {hourlyData.map(item => {
                const isSelected = selectedBarHour === item.hour;

                return (
                  <div
                    key={item.hour}
                    onClick={() => {
                      setSelectedBarHour(item.hour);
                      showToast(`Horario ${item.hour}: Ocupación promedio del ${item.pct}%`);
                    }}
                    className="flex-1 flex flex-col items-center h-full justify-end group cursor-pointer relative"
                  >
                    {/* Peak badge tag */}
                    {item.isPeak && (
                      <span className="absolute -top-7 px-1.5 py-0.5 rounded bg-[#134230] text-white text-[10px] font-bold shadow-sm scale-90 group-hover:scale-105 transition-transform">
                        {item.pct}%
                      </span>
                    )}

                    <div
                      className={`w-full rounded-t-lg transition-all duration-300 ${
                        item.isPeak
                          ? 'bg-[#134230] group-hover:bg-[#2d5a46]'
                          : isSelected
                          ? 'bg-[#2d5a46]'
                          : 'bg-[#a1d1b8] group-hover:bg-[#2d5a46]'
                      }`}
                      style={{ height: `${item.pct}%` }}
                    />
                    <span className={`mt-2 text-[11px] transition-colors ${
                      item.isPeak ? 'font-bold text-[#134230]' : 'text-[#717973] group-hover:text-[#134230]'
                    }`}>
                      {item.hour}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Buffer recommendation notice */}
          <div className="mt-4 p-3 bg-[#f0f5f0] rounded-xl flex items-center justify-between text-xs text-[#414944]">
            <span className="flex items-center gap-2">
              <Clock className="w-4 h-4 text-[#7d562d] shrink-0" />
              Recomendación de Buffer: Habilitar rotación de 20 min en cabinas 3 y 5 durante 11:00 y 17:00.
            </span>
            <button
              onClick={onNavigateToCalendar}
              className="font-bold text-[#134230] hover:underline whitespace-nowrap ml-2"
            >
              Ver agenda →
            </button>
          </div>
        </div>

        {/* Chart 2: Weekly Evolution (Target vs Real) */}
        <div className="lg:col-span-5 bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between mb-1">
              <h2 className="font-semibold text-lg text-[#134230]">Ocupación Semanal</h2>
              <span className="px-2.5 py-0.5 rounded-full bg-[#bceed3]/40 text-xs text-[#134230] font-bold">
                Promedio 78.4%
              </span>
            </div>
            <p className="text-xs text-[#414944]">Desempeño diario frente a la meta operacional del 80%.</p>
          </div>

          {/* SVG Evolution Graphic */}
          <div className="relative w-full h-52 flex flex-col justify-end mt-4">
            {/* Target Line indicator 80% */}
            <div className="absolute left-0 right-0 top-12 flex items-center z-10 pointer-events-none">
              <div className="w-full border-t border-dashed border-[#7d562d]/50" />
              <span className="ml-2 text-[10px] font-bold text-[#7d562d] whitespace-nowrap">Meta 80%</span>
            </div>

            <svg className="w-full h-40 overflow-visible" fill="none" viewBox="0 0 350 140">
              <defs>
                <linearGradient id="areaGrad" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stopColor="#2d5a46" stopOpacity="0.25" />
                  <stop offset="100%" stopColor="#2d5a46" stopOpacity="0.0" />
                </linearGradient>
              </defs>
              <path d="M 10,95 Q 60,60 115,80 T 225,35 T 340,15 L 340,140 L 10,140 Z" fill="url(#areaGrad)" />
              <path d="M 10,95 Q 60,60 115,80 T 225,35 T 340,15" stroke="#134230" strokeLinecap="round" strokeWidth="3" />
              <circle cx="10" cy="95" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="65" cy="65" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="120" cy="80" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="175" cy="55" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="230" cy="35" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="285" cy="22" fill="#ffffff" r="4" stroke="#134230" strokeWidth="2" />
              <circle cx="340" cy="15" fill="#134230" r="5" stroke="#ffffff" strokeWidth="2" />
            </svg>

            <div className="flex justify-between items-center text-[10px] sm:text-xs text-[#717973] mt-2">
              <span>Lun (62%)</span>
              <span>Mar (74%)</span>
              <span>Mié (68%)</span>
              <span>Jue (79%)</span>
              <span>Vie (85%)</span>
              <span>Sáb (92%)</span>
              <span className="text-[#134230] font-bold">Dom (95%)</span>
            </div>
          </div>

          <div className="mt-4 pt-2 border-t border-[#f0f5f0] text-xs text-[#717973]">
            Viernes a Domingo superan el umbral óptimo de rentabilidad.
          </div>
        </div>
      </div>

      {/* Detailed Sections: Most Demanded Services & Supply Risk */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        {/* Most Demanded Services (8 cols) */}
        <div className="lg:col-span-8 bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="font-semibold text-lg text-[#134230]">Servicios con Mayor Demanda y Rotación</h2>
              <p className="text-xs text-[#414944]">
                Correlación de volumen de citas, ingresos e inventario crítico comprometido.
              </p>
            </div>
            <button
              onClick={() => showToast('Filtrando servicios por rentabilidad...')}
              className="p-2 rounded-xl text-[#717973] hover:bg-[#f0f5f0] transition-colors"
              title="Ajustar parámetros"
            >
              <Layers className="w-5 h-5" />
            </button>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs sm:text-sm">
              <thead>
                <tr className="text-[#717973] uppercase tracking-wider text-[11px] bg-[#f0f5f0] rounded-xl font-semibold">
                  <th className="py-3 px-3 sm:px-4 rounded-l-xl">Servicio Ritual</th>
                  <th className="py-3 px-2 sm:px-3">Categoría</th>
                  <th className="py-3 px-2 sm:px-3 text-center">Citas</th>
                  <th className="py-3 px-3 text-right">Ingresos</th>
                  <th className="py-3 px-3 sm:px-4 rounded-r-xl">Insumo Crítico & Stock</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#f0f5f0]">
                {topServices.map(srv => (
                  <tr key={srv.id} className="hover:bg-[#f6fbf5] transition-colors">
                    <td className="py-3.5 px-3 sm:px-4 font-semibold text-[#134230] flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full shrink-0" style={{ backgroundColor: srv.bulletColor }} />
                      <span className="truncate max-w-[200px] sm:max-w-none">{srv.name}</span>
                    </td>
                    <td className="py-3.5 px-2 sm:px-3 text-[#414944] text-xs">{srv.category}</td>
                    <td className="py-3.5 px-2 sm:px-3 text-center font-bold text-[#134230]">{srv.totalAppointments}</td>
                    <td className="py-3.5 px-3 text-right font-semibold text-[#134230]">${srv.revenueUSD.toLocaleString()} USD</td>
                    <td className="py-3.5 px-3 sm:px-4">
                      <div className="flex flex-col gap-1 w-36 sm:w-44">
                        <div className="flex justify-between text-[11px]">
                          <span className="truncate text-[#7d562d] font-medium">{srv.criticalSupply}</span>
                          <span className={`font-semibold ${
                            srv.supplyStatus === 'critico' ? 'text-[#ba1a1a]' : 'text-[#134230]'
                          }`}>
                            {srv.supplyLabel}
                          </span>
                        </div>
                        <div className="w-full bg-[#ebefea] h-1.5 rounded-full overflow-hidden">
                          <div
                            className={`h-full rounded-full ${
                              srv.supplyStatus === 'critico' ? 'bg-[#ba1a1a]' : srv.supplyStatus === 'reorden' ? 'bg-[#ffca98]' : 'bg-[#134230]'
                            }`}
                            style={{ width: `${srv.supplyPercent}%` }}
                          />
                        </div>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Early Supply Warning Widget (4 cols) */}
        <div className="lg:col-span-4 bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm flex flex-col justify-between">
          <div>
            <div className="flex items-start justify-between">
              <div>
                <div className="flex items-center gap-1.5 text-[#7d562d] text-xs font-bold uppercase tracking-wider">
                  <AlertTriangle className="w-4 h-4 text-[#ba1a1a]" />
                  <span>Alertas de Insumos</span>
                </div>
                <h2 className="font-semibold text-lg text-[#134230] mt-1">Riesgo en Cabinas</h2>
              </div>
              <span className="px-2.5 py-1 bg-[#ffdcbd] text-[#7a532a] text-[11px] font-bold rounded-full">
                2 en Quiebre
              </span>
            </div>

            {/* Critical supplies items */}
            <div className="space-y-3 my-5">
              {supplies.slice(0, 2).map(sup => (
                <div key={sup.id} className="p-3.5 rounded-2xl bg-[#f0f5f0] space-y-2">
                  <div className="flex items-start justify-between">
                    <div>
                      <span className="block font-semibold text-xs sm:text-sm text-[#134230]">{sup.name}</span>
                      <span className="block text-[11px] text-[#414944]">
                        Stock: {sup.currentStock} {sup.unit} (Mínimo: {sup.minStock} {sup.unit})
                      </span>
                    </div>
                    <span className="px-2 py-0.5 rounded bg-[#ffdad6] text-[#ba1a1a] text-[10px] font-bold">
                      {sup.statusLabel}
                    </span>
                  </div>
                  <div className="flex items-center justify-between text-[11px] text-[#7d562d]">
                    <span>Afecta: {sup.affectedAppointmentsCount} citas agendadas</span>
                    <button
                      onClick={onNavigateToInventory}
                      className="underline font-bold hover:text-[#134230]"
                    >
                      Pedir a Bodega
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <button
            onClick={onNavigateToInventory}
            className="w-full py-2.5 px-4 rounded-xl bg-[#ebefea] hover:bg-[#dfe4df] text-[#134230] font-semibold text-xs transition-colors flex items-center justify-center gap-2"
          >
            <Package className="w-4 h-4" />
            <span>Gestionar Órdenes de Suministro</span>
          </button>
        </div>
      </div>

      {/* Specialist Performance Grid */}
      <div className="bg-white p-6 sm:p-7 rounded-3xl border border-[#e5e9e4] shadow-sm mb-6">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
          <div>
            <h2 className="font-semibold text-lg text-[#134230]">Rendimiento y Ocupación por Profesional</h2>
            <p className="text-xs text-[#414944]">
              Control individual de horas efectivas en camilla, citas cerradas y satisfacción del huésped.
            </p>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-xs text-[#717973]">Ordenar por:</span>
            <span className="px-3 py-1 rounded-lg bg-[#ebefea] text-[#134230] text-xs font-bold">
              Mayor Ocupación
            </span>
          </div>
        </div>

        {/* 4 Professional Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {sortedProfessionals.map(prof => (
            <div
              key={prof.id}
              className="p-5 rounded-2xl bg-[#f0f5f0] border border-[#e5e9e4] hover:shadow-md transition-all flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <img
                  src={prof.avatar}
                  alt={prof.name}
                  className="w-12 h-12 rounded-full object-cover shadow-sm border border-[#c0c9c2]"
                />
                <div className="min-w-0">
                  <h3 className="font-semibold text-sm text-[#134230] truncate">{prof.name}</h3>
                  <span className="block text-xs text-[#7d562d] truncate">{prof.role}</span>
                </div>
              </div>

              <div className="my-4 space-y-1.5">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-[#414944]">Ocupación Semanal</span>
                  <span className="font-bold text-sm text-[#134230]">{prof.occupancyPercent}%</span>
                </div>
                <div className="w-full bg-[#dfe4df] h-2 rounded-full overflow-hidden">
                  <div
                    className="bg-[#134230] h-full rounded-full transition-all duration-500"
                    style={{ width: `${prof.occupancyPercent}%` }}
                  />
                </div>
              </div>

              <div className="grid grid-cols-3 gap-1 pt-2 text-center bg-white p-2 rounded-xl text-xs">
                <div>
                  <span className="block font-bold text-sm text-[#134230]">{prof.weeklyCabinHours}h</span>
                  <span className="text-[10px] text-[#717973]">En cabina</span>
                </div>
                <div>
                  <span className="block font-bold text-sm text-[#134230]">{prof.completedAppointments}</span>
                  <span className="text-[10px] text-[#717973]">Citas</span>
                </div>
                <div>
                  <span className="inline-flex items-center gap-0.5 font-bold text-sm text-[#134230]">
                    <Star className="w-3 h-3 text-[#d97706] fill-current" />
                    {prof.rating}
                  </span>
                  <span className="block text-[10px] text-[#717973]">Rating</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Floating Action Toast */}
      {toastMessage && (
        <div className="fixed bottom-6 right-6 bg-[#134230] text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 z-50 animate-in fade-in slide-in-from-bottom-3 duration-200">
          <CheckCircle className="w-5 h-5 text-[#bceed3]" />
          <span className="text-xs sm:text-sm font-medium">{toastMessage}</span>
        </div>
      )}
    </div>
  );
};
