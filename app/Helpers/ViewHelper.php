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
                'class' => 'bg-[#00E5A3] text-[#0D1117] border-2 border-[#0D1117]'
            ],
            'pendiente' => [
                'label' => '⏱ Pendiente',
                'class' => 'bg-[#D4FF00] text-[#0D1117] border-2 border-[#0D1117]'
            ],
            'completada' => [
                'label' => '★ Completada',
                'class' => 'bg-[#2548E8] text-white border-2 border-[#0D1117]'
            ],
            'cancelada' => [
                'label' => '✕ Cancelada',
                'class' => 'bg-[#FF2E7E] text-white border-2 border-[#0D1117]'
            ],
            'reprogramada' => [
                'label' => '↻ Reprogramada',
                'class' => 'bg-[#9E00FF] text-white border-2 border-[#0D1117]'
            ],
        ];

        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-white text-[#0D1117] border-2 border-[#0D1117]'];

        return "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-extrabold font-mono uppercase tracking-wide pm-shadow-xs {$info['class']}'>
                    {$info['label']}
                </span>";
    }
}
