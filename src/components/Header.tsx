/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Calendar,
  Search,
  TrendingUp,
  CalendarDays,
  Sparkles,
  Package,
  Mail,
  FlaskConical,
  ChevronDown,
  MapPin,
  Palette,
  FileCode,
} from 'lucide-react';

export type AppView =
  | 'booking'
  | 'lookup'
  | 'dashboard'
  | 'calendar'
  | 'services'
  | 'inventory'
  | 'emails'
  | 'tests'
  | 'emailGuide';

interface HeaderProps {
  currentView: AppView;
  onNavigate: (view: AppView) => void;
  unreadEmailsCount: number;
  themeMode: 'serene' | 'memphis';
  onToggleTheme: () => void;
  selectedBranch: string;
  onChangeBranch: (branch: string) => void;
}

export const Header: React.FC<HeaderProps> = ({
  currentView,
  onNavigate,
  unreadEmailsCount,
  themeMode,
  onToggleTheme,
  selectedBranch,
  onChangeBranch,
}) => {
  const [showAdminDropdown, setShowAdminDropdown] = useState(false);
  const [showBranchDropdown, setShowBranchDropdown] = useState(false);

  const branches = [
    'Sede Principal - Las Palmas',
    'Sede Poblado - Wellness Boutique',
    'Sede Envigado - Sanctuary',
  ];

  const isAdminView = ['dashboard', 'calendar', 'services', 'inventory'].includes(currentView);

  return (
    <header className="sticky top-0 left-0 right-0 z-40 bg-[#f6fbf5]/95 backdrop-blur-md border-b border-[#e5e9e4] shadow-[0_1px_8px_rgba(0,0,0,0.03)] transition-colors">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        {/* Brand & Branch Selector */}
        <div className="flex items-center gap-4 lg:gap-6">
          <button
            onClick={() => onNavigate('booking')}
            className="flex items-center gap-3 text-left group focus:outline-none"
          >
            <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-all ${
              themeMode === 'memphis'
                ? 'bg-[#ffca98] text-[#134230] shadow-[3px_3px_0px_#134230] border-2 border-[#134230]'
                : 'bg-[#134230] text-[#bceed3] shadow-sm'
            }`}>
              <Sparkles className="w-5 h-5" />
            </div>
            <div className="flex flex-col">
              <span className="font-serif-title font-semibold text-xl tracking-tight text-[#134230] group-hover:text-[#2d5a46] transition-colors">
                Aura Spa
              </span>
              <span className="text-[11px] uppercase tracking-wider font-semibold text-[#7d562d]">
                {isAdminView ? 'Portal Administrativo' : 'Bienestar & Armonía'}
              </span>
            </div>
          </button>

          {/* Branch Pill Selector */}
          <div className="relative hidden xl:block">
            <button
              onClick={() => setShowBranchDropdown(!showBranchDropdown)}
              className="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#ebefea] text-[#414944] text-xs font-medium hover:bg-[#dfe4df] transition-colors"
            >
              <MapPin className="w-3.5 h-3.5 text-[#134230]" />
              <span>{selectedBranch}</span>
              <ChevronDown className="w-3 h-3 text-[#717973]" />
            </button>

            {showBranchDropdown && (
              <div className="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-[#e5e9e4] p-1.5 z-50 animate-in fade-in zoom-in-95 duration-150">
                <div className="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-[#717973]">
                  Seleccionar Sucursal
                </div>
                {branches.map(branch => (
                  <button
                    key={branch}
                    onClick={() => {
                      onChangeBranch(branch);
                      setShowBranchDropdown(false);
                    }}
                    className={`w-full text-left px-3 py-2 rounded-lg text-xs transition-colors flex items-center justify-between ${
                      selectedBranch === branch
                        ? 'bg-[#f0f5f0] text-[#134230] font-semibold'
                        : 'text-[#414944] hover:bg-[#f6fbf5]'
                    }`}
                  >
                    <span>{branch}</span>
                    {selectedBranch === branch && <span className="w-1.5 h-1.5 rounded-full bg-[#134230]" />}
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Center Navigation */}
        <nav className="hidden lg:flex items-center gap-1.5">
          <button
            onClick={() => onNavigate('booking')}
            className={`px-3.5 py-2 rounded-xl text-sm font-medium transition-all ${
              currentView === 'booking'
                ? themeMode === 'memphis'
                  ? 'bg-[#2d5a46] text-white shadow-[2px_2px_0px_#181d1a] font-semibold'
                  : 'bg-[#2d5a46] text-white font-semibold shadow-sm'
                : 'text-[#414944] hover:bg-[#ebefea] hover:text-[#181d1a]'
            }`}
          >
            Reservar Cita
          </button>

          <button
            onClick={() => onNavigate('lookup')}
            className={`px-3.5 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-1.5 ${
              currentView === 'lookup'
                ? 'bg-[#2d5a46] text-white font-semibold shadow-sm'
                : 'text-[#414944] hover:bg-[#ebefea] hover:text-[#181d1a]'
            }`}
          >
            <Search className="w-4 h-4 text-current" />
            <span>Consultar Reserva</span>
          </button>

          {/* Admin Dropdown / Direct View */}
          <div className="relative">
            <button
              onClick={() => setShowAdminDropdown(!showAdminDropdown)}
              onMouseEnter={() => setShowAdminDropdown(true)}
              className={`flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition-all ${
                isAdminView
                  ? 'bg-[#2d5a46] text-white font-semibold shadow-sm'
                  : 'text-[#414944] hover:bg-[#ebefea] hover:text-[#181d1a]'
              }`}
            >
              <span>Panel Admin</span>
              <ChevronDown className="w-4 h-4 text-current opacity-70" />
            </button>

            {showAdminDropdown && (
              <div
                onMouseLeave={() => setShowAdminDropdown(false)}
                className="absolute left-0 mt-1 w-64 bg-white rounded-2xl p-2 shadow-xl border border-[#e5e9e4] z-50 animate-in fade-in slide-in-from-top-2 duration-150 flex flex-col gap-1"
              >
                <div className="px-3 py-1 text-[10px] uppercase font-bold tracking-wider text-[#717973]">
                  Operaciones del Spa
                </div>
                <button
                  onClick={() => {
                    onNavigate('dashboard');
                    setShowAdminDropdown(false);
                  }}
                  className={`w-full text-left px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-2.5 transition-colors ${
                    currentView === 'dashboard' ? 'bg-[#f0f5f0] text-[#134230] font-semibold' : 'text-[#414944] hover:bg-[#f6fbf5]'
                  }`}
                >
                  <TrendingUp className="w-4 h-4 text-[#134230]" />
                  <span>Dashboard de Demanda</span>
                </button>
                <button
                  onClick={() => {
                    onNavigate('calendar');
                    setShowAdminDropdown(false);
                  }}
                  className={`w-full text-left px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-2.5 transition-colors ${
                    currentView === 'calendar' ? 'bg-[#f0f5f0] text-[#134230] font-semibold' : 'text-[#414944] hover:bg-[#f6fbf5]'
                  }`}
                >
                  <CalendarDays className="w-4 h-4 text-[#134230]" />
                  <span>Calendario Global</span>
                </button>
                <button
                  onClick={() => {
                    onNavigate('services');
                    setShowAdminDropdown(false);
                  }}
                  className={`w-full text-left px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-2.5 transition-colors ${
                    currentView === 'services' ? 'bg-[#f0f5f0] text-[#134230] font-semibold' : 'text-[#414944] hover:bg-[#f6fbf5]'
                  }`}
                >
                  <Sparkles className="w-4 h-4 text-[#134230]" />
                  <span>Servicios & Profesionales</span>
                </button>
                <button
                  onClick={() => {
                    onNavigate('inventory');
                    setShowAdminDropdown(false);
                  }}
                  className={`w-full text-left px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-2.5 transition-colors ${
                    currentView === 'inventory' ? 'bg-[#f0f5f0] text-[#134230] font-semibold' : 'text-[#414944] hover:bg-[#f6fbf5]'
                  }`}
                >
                  <Package className="w-4 h-4 text-[#134230]" />
                  <span>Insumos & Inventario</span>
                </button>
              </div>
            )}
          </div>

          {/* Test Runner & System Tools */}
          <button
            onClick={() => onNavigate('tests')}
            title="Ejecutar pruebas unitarias de disponibilidad"
            className={`px-3 py-2 rounded-xl text-xs font-medium transition-all flex items-center gap-1.5 ${
              currentView === 'tests'
                ? 'bg-[#595139] text-[#eee2c2] font-semibold'
                : 'text-[#717973] hover:bg-[#ebefea] hover:text-[#181d1a]'
            }`}
          >
            <FlaskConical className="w-3.5 h-3.5" />
            <span>Pruebas</span>
          </button>

          <button
            onClick={() => onNavigate('emailGuide')}
            title="Instrucciones y configuración de Email en producción"
            className={`px-3 py-2 rounded-xl text-xs font-medium transition-all flex items-center gap-1.5 ${
              currentView === 'emailGuide'
                ? 'bg-[#595139] text-[#eee2c2] font-semibold'
                : 'text-[#717973] hover:bg-[#ebefea] hover:text-[#181d1a]'
            }`}
          >
            <FileCode className="w-3.5 h-3.5" />
            <span>Guía Email</span>
          </button>
        </nav>

        {/* Right Actions: Emails Inbox, Theme Toggle & Quick CTA */}
        <div className="flex items-center gap-2.5 sm:gap-3.5">
          {/* Email Inbox Preview Icon */}
          <button
            onClick={() => onNavigate('emails')}
            title="Bandeja de correos simulados enviados"
            className={`relative p-2.5 rounded-xl border transition-all ${
              currentView === 'emails'
                ? 'bg-[#134230] text-white border-[#134230]'
                : 'bg-white text-[#414944] border-[#e5e9e4] hover:bg-[#f0f5f0]'
            }`}
          >
            <Mail className="w-4 h-4" />
            {unreadEmailsCount > 0 && (
              <span className="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-[#7d562d] text-white text-[10px] font-bold flex items-center justify-center shadow-sm">
                {unreadEmailsCount}
              </span>
            )}
          </button>

          {/* Theme Palette Switcher (Serene vs Memphis) */}
          <button
            onClick={onToggleTheme}
            title={themeMode === 'serene' ? 'Cambiar a modo Memphis Vibrante' : 'Cambiar a modo Serene Sanctuary'}
            className="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-[#e5e9e4] bg-white text-xs font-semibold text-[#414944] hover:bg-[#f0f5f0] transition-colors"
          >
            <Palette className="w-3.5 h-3.5 text-[#7d562d]" />
            <span className="text-[11px]">{themeMode === 'serene' ? 'Memphis' : 'Serene'}</span>
          </button>

          {/* Agendamiento Rápido Primary CTA */}
          <button
            onClick={() => onNavigate('booking')}
            className={`inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all ${
              themeMode === 'memphis'
                ? 'bg-[#134230] text-[#bceed3] border-2 border-[#134230] shadow-[3px_3px_0px_#7d562d] hover:translate-x-[1px] hover:translate-y-[1px]'
                : 'bg-[#2d5a46] text-white hover:bg-[#134230] shadow-[0_2px_6px_rgba(31,36,33,0.08)]'
            }`}
          >
            <Calendar className="w-4 h-4" />
            <span className="whitespace-nowrap">Agendar Rápido</span>
          </button>

          {/* Admin Avatar Avatar */}
          <div className="hidden md:flex items-center gap-2.5 pl-2 border-l border-[#e5e9e4]">
            <div className="text-right">
              <span className="block text-xs font-semibold text-[#181d1a]">Dra. Marcela Ruiz</span>
              <span className="block text-[11px] text-[#7d562d]">Coordinadora Médica</span>
            </div>
            <img
              src="https://lh3.googleusercontent.com/d/14uV8K9xNn0Z-Fm4q_3N4n8T7r8lG9w6P"
              alt="Dra. Marcela Ruiz"
              className="w-9 h-9 rounded-full object-cover border border-[#c0c9c2]"
              onError={(e) => {
                // Fallback avatar if external image fails
                (e.target as HTMLElement).style.display = 'none';
              }}
            />
          </div>
        </div>
      </div>

      {/* Mobile Sub-Navigation Bar */}
      <div className="lg:hidden flex items-center justify-around px-2 py-2 bg-[#f0f5f0] border-t border-[#e5e9e4] text-xs font-medium">
        <button
          onClick={() => onNavigate('booking')}
          className={`px-2.5 py-1.5 rounded-lg ${currentView === 'booking' ? 'bg-[#2d5a46] text-white font-semibold' : 'text-[#414944]'}`}
        >
          Reservar
        </button>
        <button
          onClick={() => onNavigate('lookup')}
          className={`px-2.5 py-1.5 rounded-lg ${currentView === 'lookup' ? 'bg-[#2d5a46] text-white font-semibold' : 'text-[#414944]'}`}
        >
          Mi Cita
        </button>
        <button
          onClick={() => onNavigate('dashboard')}
          className={`px-2.5 py-1.5 rounded-lg ${currentView === 'dashboard' ? 'bg-[#2d5a46] text-white font-semibold' : 'text-[#414944]'}`}
        >
          Dashboard
        </button>
        <button
          onClick={() => onNavigate('calendar')}
          className={`px-2.5 py-1.5 rounded-lg ${currentView === 'calendar' ? 'bg-[#2d5a46] text-white font-semibold' : 'text-[#414944]'}`}
        >
          Agenda
        </button>
        <button
          onClick={() => onNavigate('emails')}
          className={`px-2.5 py-1.5 rounded-lg ${currentView === 'emails' ? 'bg-[#2d5a46] text-white font-semibold' : 'text-[#414944]'}`}
        >
          Correos ({unreadEmailsCount})
        </button>
      </div>
    </header>
  );
};
