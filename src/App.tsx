/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { Header, AppView } from './components/Header';
import { BookingWizard } from './components/BookingFlow/BookingWizard';
import { DemandDashboard } from './components/Dashboard/DemandDashboard';
import { GlobalCalendar } from './components/AdminCalendar/GlobalCalendar';
import { ServicesStaffManager } from './components/AdminServices/ServicesStaffManager';
import { InventoryManager } from './components/AdminInventory/InventoryManager';
import { ManageReservation } from './components/ClientLookup/ManageReservation';
import { EmailInboxModal } from './components/EmailSimulator/EmailInboxModal';
import { TestRunnerView } from './components/TestRunner/TestRunnerView';
import { EmailConfigGuide } from './components/EmailSetupGuide/EmailConfigGuide';
import { SpaStorage } from './services/storage';
import { Appointment, Service, Professional, Supply, NotificationEmail } from './types';

export default function App() {
  const [currentView, setCurrentView] = useState<AppView>('dashboard');
  const [themeMode, setThemeMode] = useState<'serene' | 'memphis'>('serene');
  const [selectedBranch, setSelectedBranch] = useState('Sede Principal - Las Palmas');

  // State loaded from SpaStorage
  const [services, setServices] = useState<Service[]>([]);
  const [professionals, setProfessionals] = useState<Professional[]>([]);
  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [supplies, setSupplies] = useState<Supply[]>([]);
  const [emails, setEmails] = useState<NotificationEmail[]>([]);

  // Selected code for deep-linking into emails or lookup
  const [activeCodeTarget, setActiveCodeTarget] = useState<string>('');

  const loadData = () => {
    setServices(SpaStorage.getServices());
    setProfessionals(SpaStorage.getProfessionals());
    setAppointments(SpaStorage.getAppointments());
    setSupplies(SpaStorage.getSupplies());
    setEmails(SpaStorage.getEmails());
  };

  useEffect(() => {
    loadData();
  }, []);

  const handleBookingConfirmed = (app: Appointment) => {
    loadData();
    setActiveCodeTarget(app.code);
  };

  const handleOpenEmail = (code: string) => {
    setActiveCodeTarget(code);
    setCurrentView('emails');
  };

  const handleNavigateToLookupWithCode = (code: string) => {
    setActiveCodeTarget(code);
    setCurrentView('lookup');
  };

  return (
    <div className={`min-h-screen flex flex-col transition-colors ${
      themeMode === 'memphis' ? 'bg-[#fbf7ee] text-[#181d1a]' : 'bg-[#f6fbf5] text-[#181d1a]'
    }`}>
      {/* Top Navigation */}
      <Header
        currentView={currentView}
        onNavigate={setCurrentView}
        unreadEmailsCount={emails.length}
        themeMode={themeMode}
        onToggleTheme={() => setThemeMode(prev => (prev === 'serene' ? 'memphis' : 'serene'))}
        selectedBranch={selectedBranch}
        onChangeBranch={setSelectedBranch}
      />

      {/* Main View Container */}
      <main className="flex-1 w-full">
        {currentView === 'booking' && (
          <BookingWizard
            services={services}
            professionals={professionals}
            existingAppointments={appointments}
            onBookingConfirmed={handleBookingConfirmed}
            onOpenEmail={handleOpenEmail}
            selectedBranch={selectedBranch}
          />
        )}

        {currentView === 'dashboard' && (
          <DemandDashboard
            professionals={professionals}
            supplies={supplies}
            services={services}
            appointments={appointments}
            selectedBranch={selectedBranch}
            onNavigateToInventory={() => setCurrentView('inventory')}
            onNavigateToCalendar={() => setCurrentView('calendar')}
          />
        )}

        {currentView === 'calendar' && (
          <GlobalCalendar
            appointments={appointments}
            professionals={professionals}
            services={services}
            onAppointmentsUpdated={loadData}
            onOpenEmail={handleOpenEmail}
          />
        )}

        {currentView === 'services' && (
          <ServicesStaffManager
            services={services}
            professionals={professionals}
            onDataUpdated={loadData}
          />
        )}

        {currentView === 'inventory' && (
          <InventoryManager
            supplies={supplies}
            onDataUpdated={loadData}
          />
        )}

        {currentView === 'lookup' && (
          <ManageReservation
            appointments={appointments}
            professionals={professionals}
            services={services}
            onAppointmentsUpdated={loadData}
            onOpenEmail={handleOpenEmail}
            initialCode={activeCodeTarget}
          />
        )}

        {currentView === 'emails' && (
          <EmailInboxModal
            emails={emails}
            selectedCode={activeCodeTarget}
            onNavigateToLookupWithCode={handleNavigateToLookupWithCode}
          />
        )}

        {currentView === 'tests' && (
          <TestRunnerView
            services={services}
            professionals={professionals}
            appointments={appointments}
          />
        )}

        {currentView === 'emailGuide' && (
          <EmailConfigGuide />
        )}
      </main>

      {/* Footer */}
      <footer className="w-full bg-[#ebefea] border-t border-[#dfe4df] py-10 transition-colors">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[#414944]">
          <div className="flex items-center gap-3">
            <span className="font-serif-title text-base font-semibold text-[#134230]">Aura Spa</span>
            <span>© 2025 Aura Spa & Bienestar Sanctuary. Todos los derechos reservados.</span>
          </div>

          <div className="flex items-center gap-6">
            <button
              onClick={() => setCurrentView('booking')}
              className="hover:text-[#134230] transition-colors"
            >
              Reservar Cita
            </button>
            <button
              onClick={() => setCurrentView('lookup')}
              className="hover:text-[#134230] transition-colors"
            >
              Políticas de Cancelación (8h)
            </button>
            <button
              onClick={() => setCurrentView('tests')}
              className="hover:text-[#134230] transition-colors"
            >
              Pruebas de Disponibilidad
            </button>
            <button
              onClick={() => setCurrentView('emailGuide')}
              className="hover:text-[#134230] transition-colors font-medium text-[#134230]"
            >
              Configurar Email Real
            </button>
          </div>
        </div>
      </footer>
    </div>
  );
}
