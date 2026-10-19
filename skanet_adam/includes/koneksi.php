<?php
// Konfigurasi database (XAMPP default)
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'skanet_adam';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (session_status() === PHP_SESSION_NONE) session_start();

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');
} catch (Throwable $e) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan database "' . DB_NAME . '" sudah di-import.');
}

function e($s) { return htmlspecialchars(trim((string)$s), ENT_QUOTES, 'UTF-8'); }
function flash($m, $t = 'ok') { $_SESSION['flash'] = [$m, $t]; }
function go($u) { header("Location: $u"); exit; }

function q($sql, $p = []) {
    global $db;
    $st = $db->prepare($sql);
    if ($p) $st->bind_param(str_repeat('s', count($p)), ...$p);
    $st->execute();
    return $st;
}
function rows($sql, $p = []) { return q($sql, $p)->get_result()->fetch_all(MYSQLI_ASSOC); }
function row($sql, $p = []) { return rows($sql, $p)[0] ?? null; }
function predikat($n) { return $n >= 90 ? 'A' : ($n >= 80 ? 'B' : ($n >= 70 ? 'C' : 'D')); }
