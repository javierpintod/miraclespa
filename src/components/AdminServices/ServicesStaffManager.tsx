/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Sparkles,
  Users,
  Plus,
  Edit2,
  Trash2,
  Clock,
  DollarSign,
  CheckCircle,
  AlertTriangle,
  Star,
  Check,
} from 'lucide-react';
import { Service, Professional, ServiceCategory } from '../../types';
import { SpaStorage } from '../../services/storage';

interface ServicesStaffManagerProps {
  services: Service[];
  professionals: Professional[];
  onDataUpdated: () => void;
}

export const ServicesStaffManager: React.FC<ServicesStaffManagerProps> = ({
  services,
  professionals,
  onDataUpdated,
}) => {
  const [activeTab, setActiveTab] = useState<'services' | 'staff'>('services');

  // Service Edit / Create Modal state
  const [editingService, setEditingService] = useState<Service | null>(null);
  const [isCreatingService, setIsCreatingService] = useState(false);
  const [srvName, setSrvName] = useState('');
  const [srvDesc, setSrvDesc] = useState('');
  const [srvCategory, setSrvCategory] = useState<ServiceCategory>('masajes');
  const [srvDuration, setSrvDuration] = useState(60);
  const [srvPrice, setSrvPrice] = useState(50);
  const [srvInStock, setSrvInStock] = useState(true);

  // Staff Edit / Create Modal state
  const [editingStaff, setEditingStaff] = useState<Professional | null>(null);
  const [isCreatingStaff, setIsCreatingStaff] = useState(false);
  const [staffName, setStaffName] = useState('');
  const [staffRole, setStaffRole] = useState('');
  const [staffShift, setStaffShift] = useState<'manana' | 'tarde' | 'completo'>('completo');

  // Open Service Modal
  const handleOpenEditService = (srv: Service) => {
    setEditingService(srv);
    setIsCreatingService(false);
    setSrvName(srv.name);
    setSrvDesc(srv.description);
    setSrvCategory(srv.category);
    setSrvDuration(srv.durationMinutes);
    setSrvPrice(srv.priceUSD);
    setSrvInStock(srv.inStock);
  };

  const handleOpenCreateService = () => {
    setEditingService(null);
    setIsCreatingService(true);
    setSrvName('');
    setSrvDesc('');
    setSrvCategory('masajes');
    setSrvDuration(60);
    setSrvPrice(50);
    setSrvInStock(true);
  };

  const handleSaveService = () => {
    if (!srvName.trim()) return;

    const list = [...services];
    if (isCreatingService) {
      const newSrv: Service = {
        id: `srv-${Date.now()}`,
        name: srvName,
        description: srvDesc || 'Tratamiento botánico relajante diseñado para el bienestar holístico.',
        category: srvCategory,
        categoryLabel:
          srvCategory === 'masajes'
            ? 'Corporal / Holístico'
            : srvCategory === 'unas'
            ? 'Belleza & Uñas'
            : srvCategory === 'cabello'
            ? 'Hair Salon & Color'
            : 'Dermatología Facial',
        durationMinutes: Number(srvDuration),
        priceUSD: Number(srvPrice),
        active: true,
        activeSuppliesDescription: 'Fórmulas botánicas orgánicas y esterilización previa',
        supplies: [],
        inStock: srvInStock,
      };
      list.push(newSrv);
    } else if (editingService) {
      const idx = list.findIndex(s => s.id === editingService.id);
      if (idx !== -1) {
        list[idx] = {
          ...editingService,
          name: srvName,
          description: srvDesc,
          category: srvCategory,
          durationMinutes: Number(srvDuration),
          priceUSD: Number(srvPrice),
          inStock: srvInStock,
        };
      }
    }

    SpaStorage.saveServices(list);
    onDataUpdated();
    setEditingService(null);
    setIsCreatingService(false);
  };

  // Toggle active service status
  const handleToggleActiveService = (srv: Service) => {
    const list = services.map(s => (s.id === srv.id ? { ...s, active: !s.active } : s));
    SpaStorage.saveServices(list);
    onDataUpdated();
  };

  // Open Staff Modal
  const handleOpenEditStaff = (prof: Professional) => {
    setEditingStaff(prof);
    setIsCreatingStaff(false);
    setStaffName(prof.name);
    setStaffRole(prof.role);
    setStaffShift(prof.shift);
  };

  const handleOpenCreateStaff = () => {
    setEditingStaff(null);
    setIsCreatingStaff(true);
    setStaffName('');
    setStaffRole('Terapeuta Holística');
    setStaffShift('completo');
  };

  const handleSaveStaff = () => {
    if (!staffName.trim()) return;

    const list = [...professionals];
    const shiftLabel =
      staffShift === 'manana'
        ? 'Turno mañana (09:00 - 13:00)'
        : staffShift === 'tarde'
        ? 'Turno tarde (14:00 - 18:30)'
        : 'Turno completo (am / pm)';

    if (isCreatingStaff) {
      const newProf: Professional = {
        id: `prof-${Date.now()}`,
        name: staffName,
        role: staffRole,
        title: staffRole,
        specialties: ['masajes', 'faciales'],
        rating: 5.0,
        reviewsCount: 1,
        avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
        shift: staffShift,
        shiftLabel,
        weeklyCabinHours: 35,
        completedAppointments: 0,
        occupancyPercent: 75,
        active: true,
      };
      list.push(newProf);
    } else if (editingStaff) {
      const idx = list.findIndex(p => p.id === editingStaff.id);
      if (idx !== -1) {
        list[idx] = {
          ...editingStaff,
          name: staffName,
          role: staffRole,
          shift: staffShift,
          shiftLabel,
        };
      }
    }

    SpaStorage.saveProfessionals(list);
    onDataUpdated();
    setEditingStaff(null);
    setIsCreatingStaff(false);
  };

  return (
    <div className="w-full pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      {/* Header and Tab Switcher */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div>
          <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
            Administración del Negocio
          </span>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold mt-0.5">
            Gestión de Servicios & Profesionales
          </h1>
          <p className="text-xs sm:text-sm text-[#414944]">
            Actualiza tarifas, tiempos de cabina, stock de insumos y horarios de trabajo del equipo.
          </p>
        </div>

        {/* Tab Switcher & CTA */}
        <div className="flex items-center gap-3">
          <div className="inline-flex p-1 bg-[#ebefea] rounded-xl text-xs font-semibold">
            <button
              onClick={() => setActiveTab('services')}
              className={`px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 ${
                activeTab === 'services' ? 'bg-white text-[#134230] shadow-sm font-bold' : 'text-[#414944]'
              }`}
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Servicios ({services.length})</span>
            </button>
            <button
              onClick={() => setActiveTab('staff')}
              className={`px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 ${
                activeTab === 'staff' ? 'bg-white text-[#134230] shadow-sm font-bold' : 'text-[#414944]'
              }`}
            >
              <Users className="w-3.5 h-3.5" />
              <span>Especialistas ({professionals.length})</span>
            </button>
          </div>

          <button
            onClick={activeTab === 'services' ? handleOpenCreateService : handleOpenCreateStaff}
            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] shadow-sm transition-all"
          >
            <Plus className="w-4 h-4" />
            <span>{activeTab === 'services' ? 'Nuevo Servicio' : 'Nuevo Especialista'}</span>
          </button>
        </div>
      </div>

      {/* Services Tab View */}
      {activeTab === 'services' && (
        <div className="my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {services.map(srv => (
            <div
              key={srv.id}
              className={`p-5 rounded-3xl border bg-white shadow-sm flex flex-col justify-between transition-all ${
                !srv.active ? 'opacity-60 bg-[#f0f5f0]' : ''
              }`}
            >
              <div>
                <div className="flex items-center justify-between gap-2 mb-2">
                  <span className="text-[11px] font-semibold text-[#7d562d] uppercase tracking-wider">
                    {srv.categoryLabel}
                  </span>
                  <div className="flex items-center gap-1.5">
                    {srv.inStock ? (
                      <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#bceed3] text-[#134230]">
                        En Stock
                      </span>
                    ) : (
                      <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#ffdad6] text-[#ba1a1a]">
                        Sin Insumo
                      </span>
                    )}
                  </div>
                </div>

                <h3 className="font-semibold text-base text-[#134230]">{srv.name}</h3>
                <p className="text-xs text-[#414944] mt-1.5 line-clamp-2 leading-relaxed">
                  {srv.description}
                </p>

                <div className="mt-4 pt-3 border-t border-[#f0f5f0] flex items-center justify-between text-xs">
                  <div className="flex items-center gap-1.5 text-[#181d1a]">
                    <Clock className="w-3.5 h-3.5 text-[#717973]" />
                    <span className="font-semibold">{srv.durationMinutes} min</span>
                  </div>
                  <div className="flex items-center gap-1 text-[#134230] font-bold text-sm">
                    <DollarSign className="w-4 h-4" />
                    <span>${srv.priceUSD} USD</span>
                  </div>
                </div>
              </div>

              {/* Actions */}
              <div className="mt-4 pt-3 border-t border-[#f0f5f0] flex items-center justify-between">
                <button
                  onClick={() => handleToggleActiveService(srv)}
                  className={`text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors ${
                    srv.active ? 'text-[#ba1a1a] hover:bg-[#ffdad6]/40' : 'text-[#134230] hover:bg-[#bceed3]/40'
                  }`}
                >
                  {srv.active ? 'Desactivar' : 'Activar'}
                </button>

                <button
                  onClick={() => handleOpenEditService(srv)}
                  className="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#f0f5f0] text-[#134230] hover:bg-[#ebefea] transition-colors"
                >
                  <Edit2 className="w-3 h-3" />
                  <span>Editar</span>
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Staff Tab View */}
      {activeTab === 'staff' && (
        <div className="my-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {professionals.map(prof => (
            <div
              key={prof.id}
              className="p-5 rounded-3xl border border-[#e5e9e4] bg-white shadow-sm flex flex-col justify-between text-center"
            >
              <div>
                <img
                  src={prof.avatar}
                  alt={prof.name}
                  className="w-16 h-16 rounded-full object-cover mx-auto mb-3 border border-[#c0c9c2]"
                />
                <h3 className="font-semibold text-base text-[#134230]">{prof.name}</h3>
                <span className="block text-xs text-[#7d562d] mt-0.5">{prof.role}</span>

                <div className="my-3 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#f0f5f0] text-xs font-bold text-[#d97706]">
                  <Star className="w-3.5 h-3.5 fill-current" />
                  <span>{prof.rating} ({prof.reviewsCount} reseñas)</span>
                </div>

                <div className="p-3 rounded-2xl bg-[#f0f5f0] text-xs text-left space-y-1.5 mt-2">
                  <div className="flex justify-between">
                    <span className="text-[#717973]">Turno:</span>
                    <span className="font-semibold text-[#181d1a] capitalize">{prof.shift}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#717973]">Horas cabina:</span>
                    <span className="font-semibold text-[#134230]">{prof.weeklyCabinHours}h / sem</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#717973]">Ocupación:</span>
                    <span className="font-semibold text-[#134230]">{prof.occupancyPercent}%</span>
                  </div>
                </div>
              </div>

              <div className="mt-4 pt-3 border-t border-[#f0f5f0]">
                <button
                  onClick={() => handleOpenEditStaff(prof)}
                  className="w-full py-2 rounded-xl bg-[#f0f5f0] text-[#134230] hover:bg-[#ebefea] text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors"
                >
                  <Edit2 className="w-3.5 h-3.5" />
                  <span>Editar Horario & Rol</span>
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Modal: Edit / Create Service */}
      {(editingService || isCreatingService) && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-150">
          <div className="bg-white max-w-md w-full rounded-3xl p-6 shadow-2xl border border-[#e5e9e4]">
            <h3 className="font-serif-title text-xl text-[#134230] font-semibold mb-1">
              {isCreatingService ? 'Nuevo Servicio en Catálogo' : `Editar ${editingService?.name}`}
            </h3>
            <p className="text-xs text-[#414944] mb-4">
              Configura nombre, duración en cabina y precio del ritual.
            </p>

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Nombre del Servicio</label>
                <input
                  type="text"
                  value={srvName}
                  onChange={e => setSrvName(e.target.value)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Descripción</label>
                <textarea
                  rows={2}
                  value={srvDesc}
                  onChange={e => setSrvDesc(e.target.value)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46] resize-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Categoría</label>
                  <select
                    value={srvCategory}
                    onChange={e => setSrvCategory(e.target.value as ServiceCategory)}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  >
                    <option value="masajes">Masajes</option>
                    <option value="faciales">Faciales</option>
                    <option value="unas">Uñas & Manos</option>
                    <option value="cabello">Cabello & Color</option>
                    <option value="corporal">Corporal</option>
                  </select>
                </div>
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Duración (minutos)</label>
                  <input
                    type="number"
                    value={srvDuration}
                    onChange={e => setSrvDuration(Number(e.target.value))}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Precio (USD)</label>
                  <input
                    type="number"
                    value={srvPrice}
                    onChange={e => setSrvPrice(Number(e.target.value))}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-[#181d1a] mb-1">Estado de Stock</label>
                  <select
                    value={srvInStock ? 'true' : 'false'}
                    onChange={e => setSrvInStock(e.target.value === 'true')}
                    className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                  >
                    <option value="true">Insumos Disponibles</option>
                    <option value="false">Agotado / Bloqueado</option>
                  </select>
                </div>
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-2">
              <button
                onClick={() => {
                  setEditingService(null);
                  setIsCreatingService(false);
                }}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-[#717973] hover:bg-[#f0f5f0]"
              >
                Cancelar
              </button>
              <button
                onClick={handleSaveService}
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-[#2d5a46] text-white hover:bg-[#134230]"
              >
                Guardar Servicio
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal: Edit / Create Staff */}
      {(editingStaff || isCreatingStaff) && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-150">
          <div className="bg-white max-w-md w-full rounded-3xl p-6 shadow-2xl border border-[#e5e9e4]">
            <h3 className="font-serif-title text-xl text-[#134230] font-semibold mb-1">
              {isCreatingStaff ? 'Registrar Especialista' : `Editar a ${editingStaff?.name}`}
            </h3>
            <p className="text-xs text-[#414944] mb-4">
              Asigna turnos de trabajo y rol profesional.
            </p>

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Nombre Completo</label>
                <input
                  type="text"
                  value={staffName}
                  onChange={e => setStaffName(e.target.value)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Rol / Especialidad</label>
                <input
                  type="text"
                  value={staffRole}
                  onChange={e => setStaffRole(e.target.value)}
                  placeholder="Ej. Terapeuta Holística Senior"
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none focus:ring-2 focus:ring-[#2d5a46]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#181d1a] mb-1">Turno de Trabajo</label>
                <select
                  value={staffShift}
                  onChange={e => setStaffShift(e.target.value as any)}
                  className="w-full p-2.5 rounded-xl border border-[#c0c9c2] focus:outline-none"
                >
                  <option value="completo">Turno Completo (Mañana y Tarde)</option>
                  <option value="manana">Turno Mañana (09:00 - 13:00)</option>
                  <option value="tarde">Turno Tarde (14:00 - 18:30)</option>
                </select>
              </div>
            </div>

            <div className="mt-6 flex items-center justify-end gap-2">
              <button
                onClick={() => {
                  setEditingStaff(null);
                  setIsCreatingStaff(false);
                }}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-[#717973] hover:bg-[#f0f5f0]"
              >
                Cancelar
              </button>
              <button
                onClick={handleSaveStaff}
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-[#2d5a46] text-white hover:bg-[#134230]"
              >
                Guardar Especialista
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
