<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('Asia/Manila');

const DB_HOST = 'localhost';
const DB_NAME = 'fixandgo';
const DB_USER = 'root';
const DB_PASS = '';

const SHOP = [
    'name'    => 'QuickFix Phone Repair',
    'phone'   => '0917 123 4567',
    'email'   => 'fixandgo@gmail.com',
    'address' => 'Burgos st, Tacloban City, Philippines',
    'hours'   => [['Monday to Saturday', '9:00 AM to 6:00 PM'], ['Sunday', 'Closed']],
];

const FIELD = 'w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/30';
const LABEL = 'mb-1 block text-sm font-medium text-slate-700';
const BTN   = 'inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-5 py-2.5 font-semibold text-white hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2';
const BTN2  = 'inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-5 py-2.5 font-semibold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2';
const CARD  = 'rounded-lg border border-slate-200 bg-white p-5';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $ex) {
            http_response_code(500);
            exit('Database connection failed. Start MySQL in XAMPP and import database.sql.');
        }
    }
    return $pdo;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function old(string $k, string $d = ''): string {
    $s = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    return isset($s[$k]) && is_string($s[$k]) ? trim($s[$k]) : $d;
}
function peso($n): string { $n = (float)$n; return '₱' . number_format($n, floor($n) == $n ? 0 : 2); }
function hoursLabel($h): string { $h = (float)$h; return rtrim(rtrim(number_format($h, 1), '0'), '.') . ($h == 1.0 ? ' hour' : ' hours'); }
function fmtDate(string $d): string { return date('M j, Y', strtotime($d)); }
function fmtTime(string $t): string { return date('g:i A', strtotime($t)); }
function redirect(string $u): void { header('Location: ' . $u); exit; }
function statuses(): array { return ['Scheduled', 'Waiting for Inspection', 'Being Repaired', 'Ready for Pickup', 'Completed']; }
function slots(): array { return ['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00']; }
function statusBadge(string $s): string {
    $c = ['Scheduled' => 'bg-slate-200 text-slate-800', 'Waiting for Inspection' => 'bg-amber-100 text-amber-900',
          'Being Repaired' => 'bg-blue-100 text-blue-900', 'Ready for Pickup' => 'bg-teal-100 text-teal-900',
          'Completed' => 'bg-green-100 text-green-900'][$s] ?? 'bg-slate-200 text-slate-800';
    return '<span class="inline-block rounded-full px-3 py-1 text-xs font-semibold ' . $c . '">' . e($s) . '</span>';
}
function pageHead(string $title, string $desc): string {
    return '<section class="border-b border-slate-200 bg-white"><div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10"><h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">'
        . e($title) . '</h1><p class="mt-2 max-w-2xl text-slate-600">' . e($desc) . '</p></div></section>';
}
function icon(string $n, string $c = 'h-5 w-5'): string {
    static $p = [
        'menu' => '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
        'x' => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'arrow' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'smartphone' => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>',
        'battery' => '<rect width="16" height="10" x="2" y="7" rx="2" ry="2"/><line x1="22" x2="22" y1="11" y2="13"/><path d="M6 11v2"/><path d="M10 11v2"/>',
        'zap' => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
        'cpu' => '<rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/>',
        'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'calendar' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'calc' => '<rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
        'info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
        'alert' => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
        'done' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'box' => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'clip' => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
    ];
    return '<svg class="' . $c . ' shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$n] ?? '') . '</svg>';
}
