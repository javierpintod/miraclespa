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
                'class' => 'bg-[#06D6A0] text-[#18181B] border-2 border-[#18181B]'
            ],
            'pendiente' => [
                'label' => '⏱ Pendiente',
                'class' => 'bg-[#FFBE0B] text-[#18181B] border-2 border-[#18181B]'
            ],
            'completada' => [
                'label' => '★ Completada',
                'class' => 'bg-[#3A86FF] text-white border-2 border-[#18181B]'
            ],
            'cancelada' => [
                'label' => '✕ Cancelada',
                'class' => 'bg-[#FF006E] text-white border-2 border-[#18181B]'
            ],
            'reprogramada' => [
                'label' => '↻ Reprogramada',
                'class' => 'bg-[#8338EC] text-white border-2 border-[#18181B]'
            ],
        ];

        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-white text-[#18181B] border-2 border-[#18181B]'];

        return "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold memphis-shadow-sm {$info['class']}'>
                    {$info['label']}
                </span>";
    }
}
