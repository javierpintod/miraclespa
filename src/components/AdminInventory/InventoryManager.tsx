/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import {
  Package,
  AlertTriangle,
  CheckCircle,
  Plus,
  RefreshCw,
  ShoppingBag,
  Clock,
} from 'lucide-react';
import { Supply } from '../../types';
import { SpaStorage } from '../../services/storage';

interface InventoryManagerProps {
  supplies: Supply[];
  onDataUpdated: () => void;
}

export const InventoryManager: React.FC<InventoryManagerProps> = ({
  supplies,
  onDataUpdated,
}) => {
  const [toastMsg, setToastMsg] = useState<string | null>(null);

  const showToast = (msg: string) => {
    setToastMsg(msg);
    setTimeout(() => setToastMsg(null), 3000);
  };

  // Restock action
  const handleRestock = (supplyId: string, addQuantity: number) => {
    const list = supplies.map(s => {
      if (s.id === supplyId) {
        const newStock = s.currentStock + addQuantity;
        const pct = Math.min(100, Math.round((newStock / s.minStock) * 100));
        const status: 'critico' | 'reorden' | 'optimo' = pct < 20 ? 'critico' : pct < 50 ? 'reorden' : 'optimo';

        return {
          ...s,
          currentStock: newStock,
          criticalPercent: pct,
          status,
          statusLabel: status === 'critico' ? `${pct}% crítico` : status === 'reorden' ? `${pct}% reorden` : `${pct}% óptimo`,
          affectedAppointmentsCount: status === 'optimo' ? 0 : Math.max(0, s.affectedAppointmentsCount - 10),
        };
      }
      return s;
    });

    SpaStorage.saveSupplies(list);
    onDataUpdated();
    showToast('Reposición registrada exitosamente. Salud de cabina restablecida.');
  };

  return (
    <div className="w-full pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div>
          <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
            Cadena de Suministro & Esterilización
          </span>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold mt-0.5">
            Insumos & Inventario Crítico
          </h1>
          <p className="text-xs sm:text-sm text-[#414944]">
            Monitoreo preventivo de activos botánicos, lencería de cabina y kits de tratamiento.
          </p>
        </div>

        <button
          onClick={() => {
            // Re-stock all critical
            supplies.forEach(s => {
              if (s.status !== 'optimo') handleRestock(s.id, s.minStock);
            });
            showToast('Reabastecimiento masivo completado.');
          }}
          className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] shadow-sm transition-all"
        >
          <RefreshCw className="w-4 h-4" />
          <span>Reabastecer Quiebres</span>
        </button>
      </div>

      {/* Supplies Grid */}
      <div className="my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        {supplies.map(sup => (
          <div
            key={sup.id}
            className="p-6 rounded-3xl border border-[#e5e9e4] bg-white shadow-sm flex flex-col justify-between"
          >
            <div>
              <div className="flex items-start justify-between gap-2 mb-2">
                <span className="text-[11px] font-semibold text-[#7d562d] uppercase tracking-wider">
                  {sup.category}
                </span>
                <span
                  className={`text-[10px] font-bold px-2.5 py-0.5 rounded-full ${
                    sup.status === 'critico'
                      ? 'bg-[#ffdad6] text-[#ba1a1a]'
                      : sup.status === 'reorden'
                      ? 'bg-[#ffdcbd] text-[#7a532a]'
                      : 'bg-[#bceed3] text-[#134230]'
                  }`}
                >
                  {sup.statusLabel}
                </span>
              </div>

              <h3 className="font-semibold text-base text-[#134230]">{sup.name}</h3>

              {/* Progress bar */}
              <div className="my-3 space-y-1">
                <div className="flex justify-between text-xs">
                  <span className="text-[#717973]">Stock en Cabinas</span>
                  <span className="font-bold text-[#181d1a]">
                    {sup.currentStock} / {sup.minStock} {sup.unit}
                  </span>
                </div>
                <div className="w-full bg-[#ebefea] h-2 rounded-full overflow-hidden">
                  <div
                    className={`h-full rounded-full transition-all duration-500 ${
                      sup.status === 'critico'
                        ? 'bg-[#ba1a1a]'
                        : sup.status === 'reorden'
                        ? 'bg-[#ffca98]'
                        : 'bg-[#134230]'
                    }`}
                    style={{ width: `${Math.min(100, (sup.currentStock / sup.minStock) * 100)}%` }}
                  />
                </div>
              </div>

              {sup.affectedAppointmentsCount > 0 && (
                <div className="p-3 rounded-xl bg-[#f0f5f0] text-xs text-[#7d562d] flex items-center gap-2 mb-3">
                  <AlertTriangle className="w-4 h-4 text-[#ba1a1a] shrink-0" />
                  <span>Afecta {sup.affectedAppointmentsCount} citas agendadas esta semana</span>
                </div>
              )}

              {sup.replenishmentETA && (
                <span className="block text-[11px] text-[#717973] flex items-center gap-1">
                  <Clock className="w-3.5 h-3.5" /> Llegada programada: {sup.replenishmentETA}
                </span>
              )}
            </div>

            <div className="mt-5 pt-3 border-t border-[#f0f5f0] flex items-center justify-between">
              <span className="text-xs text-[#717973]">
                {sup.recommendedAction || 'Stock estable'}
              </span>

              <button
                onClick={() => handleRestock(sup.id, 500)}
                className="px-3 py-1.5 rounded-xl bg-[#f0f5f0] text-[#134230] hover:bg-[#ebefea] font-semibold text-xs transition-colors flex items-center gap-1"
              >
                <Plus className="w-3 h-3" />
                <span>+ Reponer</span>
              </button>
            </div>
          </div>
        ))}
      </div>

      {toastMsg && (
        <div className="fixed bottom-6 right-6 bg-[#134230] text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-2 text-xs font-semibold z-50">
          <CheckCircle className="w-4 h-4 text-[#bceed3]" />
          <span>{toastMsg}</span>
        </div>
      )}
    </div>
  );
};
