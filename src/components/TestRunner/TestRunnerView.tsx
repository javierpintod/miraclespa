/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import {
  FlaskConical,
  CheckCircle,
  XCircle,
  Play,
  RotateCw,
  Clock,
  ShieldCheck,
  CheckCircle2,
} from 'lucide-react';
import { Service, Professional, Appointment, TestResult } from '../../types';
import { runAvailabilityTests } from '../../services/tests/availabilityTests';

interface TestRunnerViewProps {
  services: Service[];
  professionals: Professional[];
  appointments: Appointment[];
}

export const TestRunnerView: React.FC<TestRunnerViewProps> = ({
  services,
  professionals,
  appointments,
}) => {
  const [testResults, setTestResults] = useState<TestResult[]>([]);
  const [isRunning, setIsRunning] = useState(false);

  const executeTests = () => {
    setIsRunning(true);
    setTimeout(() => {
      const res = runAvailabilityTests(services, professionals, appointments);
      setTestResults(res);
      setIsRunning(false);
    }, 250);
  };

  useEffect(() => {
    executeTests();
  }, [services, professionals, appointments]);

  const passedCount = testResults.filter(t => t.passed).length;
  const totalCount = testResults.length;
  const allPassed = totalCount > 0 && passedCount === totalCount;

  return (
    <div className="w-full pb-20 max-w-5xl mx-auto px-4 sm:px-6 pt-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-6 border-b border-[#e5e9e4]">
        <div>
          <span className="text-[11px] uppercase tracking-wider font-bold text-[#7d562d]">
            Aseguramiento de Calidad & Lógica Operacional
          </span>
          <h1 className="font-serif-title text-2xl sm:text-3xl text-[#134230] font-semibold mt-0.5">
            Suite de Pruebas de Disponibilidad & Reservas
          </h1>
          <p className="text-xs sm:text-sm text-[#414944]">
            Batería de pruebas automatizadas para colisiones, buffers de 20 minutos, cancelaciones a 8 horas y asignaciones.
          </p>
        </div>

        <button
          onClick={executeTests}
          disabled={isRunning}
          className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#2d5a46] text-white text-xs font-semibold hover:bg-[#134230] shadow-sm transition-all self-start sm:self-auto"
        >
          {isRunning ? (
            <RotateCw className="w-4 h-4 animate-spin" />
          ) : (
            <Play className="w-4 h-4 fill-current" />
          )}
          <span>{isRunning ? 'Ejecutando...' : 'Re-ejecutar Pruebas'}</span>
        </button>
      </div>

      {/* Summary Scorecard */}
      <div className="my-6 p-6 rounded-3xl bg-white border border-[#e5e9e4] shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div className="flex items-center gap-4">
          <div className={`w-14 h-14 rounded-2xl flex items-center justify-center ${
            allPassed ? 'bg-[#bceed3] text-[#134230]' : 'bg-[#ffdad6] text-[#ba1a1a]'
          }`}>
            <ShieldCheck className="w-8 h-8" />
          </div>
          <div>
            <h2 className="font-semibold text-lg text-[#134230]">
              {allPassed ? 'Todas las pruebas pasaron satisfactoriamente' : 'Algunas pruebas requieren atención'}
            </h2>
            <p className="text-xs text-[#414944]">
              {passedCount} de {totalCount} casos de prueba validados en tiempo real.
            </p>
          </div>
        </div>

        <div className="text-right">
          <span className="font-serif-title text-3xl font-bold text-[#134230]">
            {totalCount > 0 ? Math.round((passedCount / totalCount) * 100) : 0}%
          </span>
          <span className="block text-[11px] text-[#717973] uppercase font-bold tracking-wider">
            Cobertura Operativa
          </span>
        </div>
      </div>

      {/* Test Cases Grid */}
      <div className="space-y-4">
        {testResults.map(test => (
          <div
            key={test.id}
            className="p-5 rounded-2xl bg-white border border-[#e5e9e4] shadow-sm flex flex-col gap-3"
          >
            <div className="flex items-start justify-between gap-3">
              <div className="flex items-center gap-2.5">
                {test.passed ? (
                  <CheckCircle className="w-5 h-5 text-[#2e7d52] shrink-0" />
                ) : (
                  <XCircle className="w-5 h-5 text-[#ba1a1a] shrink-0" />
                )}
                <div>
                  <h3 className="font-semibold text-sm text-[#181d1a]">{test.title}</h3>
                  <p className="text-xs text-[#414944]">{test.description}</p>
                </div>
              </div>

              <div className="flex items-center gap-2 shrink-0">
                <span className="text-[11px] text-[#717973] flex items-center gap-1 font-mono">
                  <Clock className="w-3 h-3" /> {test.executionTimeMs} ms
                </span>
                <span
                  className={`text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${
                    test.passed ? 'bg-[#bceed3] text-[#002114]' : 'bg-[#ffdad6] text-[#ba1a1a]'
                  }`}
                >
                  {test.passed ? 'Passed' : 'Failed'}
                </span>
              </div>
            </div>

            <div className="p-3 rounded-xl bg-[#f0f5f0] space-y-1.5 text-xs font-mono">
              <div className="flex flex-col sm:flex-row sm:justify-between">
                <span className="text-[#717973]">Esperado:</span>
                <span className="text-[#134230] font-semibold">{test.expected}</span>
              </div>
              <div className="flex flex-col sm:flex-row sm:justify-between">
                <span className="text-[#717973]">Obtenido:</span>
                <span className="text-[#181d1a] font-semibold">{test.actual}</span>
              </div>
              <div className="pt-1 border-t border-[#dfe4df] text-[11px] text-[#414944] font-sans">
                {test.details}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};
