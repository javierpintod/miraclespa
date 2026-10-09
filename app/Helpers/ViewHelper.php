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
                'label' => 'Confirmada',
                'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'
            ],
            'pendiente' => [
                'label' => 'Pendiente',
                'class' => 'bg-amber-100 text-amber-800 border-amber-200'
            ],
            'completada' => [
                'label' => 'Completada',
                'class' => 'bg-blue-100 text-blue-800 border-blue-200'
            ],
            'cancelada' => [
                'label' => 'Cancelada',
                'class' => 'bg-rose-100 text-rose-800 border-rose-200'
            ],
            'reprogramada' => [
                'label' => 'Reprogramada',
                'class' => 'bg-purple-100 text-purple-800 border-purple-200'
            ],
        ];

        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800 border-gray-200'];

        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$info['class']}'>
                    {$info['label']}
                </span>";
    }
}
