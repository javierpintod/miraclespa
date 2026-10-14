<?php
/**
 * MIRACLE SPA - Ayudante de Vistas y Formato (ViewHelper)
 */

class ViewHelper {
    public static function e(?string $string): string {
        return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
    }

    public static function formatMoney(float $amount): string {
        $currency = AppConfig::getSetting('currency_symbol', '$');
        return $currency . ' ' . number_format($amount, 2);
    }

    public static function formatDate(string $dateStr): string {
        if (empty($dateStr)) return '-';
        $timestamp = strtotime($dateStr);
        $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $diaSemana = $dias[date('w', $timestamp)];
        $dia = date('d', $timestamp);
        $mes = $meses[(int)date('m', $timestamp)];
        $anio = date('Y', $timestamp);

        return "{$diaSemana}, {$dia} {$mes} {$anio}";
    }

    public static function formatTime(string $timeStr): string {
        if (empty($timeStr)) return '-';
        return date('h:i A', strtotime($timeStr));
    }

    public static function statusBadge(string $status): string {
        $badges = [
            'confirmada' => [
                'label' => '✓ Confirmada',
                'class' => 'bg-emerald-500/15 text-emerald-800 border border-emerald-500/30 shadow-[0_2px_10px_rgba(16,185,129,0.15)]'
            ],
            'pendiente' => [
                'label' => '⏱ Pendiente',
                'class' => 'bg-amber-500/15 text-amber-800 border border-amber-500/30 shadow-[0_2px_10px_rgba(245,158,11,0.15)]'
            ],
            'completada' => [
                'label' => '★ Completada',
                'class' => 'bg-sky-500/15 text-sky-800 border border-sky-500/30 shadow-[0_2px_10px_rgba(14,165,233,0.15)]'
            ],
            'cancelada' => [
                'label' => '✕ Cancelada',
                'class' => 'bg-rose-500/15 text-rose-800 border border-rose-500/30 shadow-[0_2px_10px_rgba(244,63,94,0.15)]'
            ],
            'reprogramada' => [
                'label' => '↻ Reprogramada',
                'class' => 'bg-purple-500/15 text-purple-800 border border-purple-500/30 shadow-[0_2px_10px_rgba(168,85,247,0.15)]'
            ],
        ];

        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-white/60 text-slate-800 border border-white/80 shadow-sm'];

        return "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide backdrop-blur-md {$info['class']}'>
                    {$info['label']}
                </span>";
    }
}
