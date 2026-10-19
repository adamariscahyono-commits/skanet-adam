<?php
require_once __DIR__ . '/auth.php';
$cur = basename($_SERVER['PHP_SELF']);
$menu = ['index.php'=>'Dashboard','siswa.php'=>'Siswa','guru.php'=>'Guru','mapel.php'=>'Mapel','nilai.php'=>'Nilai'];
?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Sistem Sekolah') ?> - SKANET</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><div class="wrap bar">
  <strong class="brand">SKANET</strong>
  <nav><?php foreach ($menu as $f => $l): ?><a href="<?= $f ?>" class="<?= $cur === $f ? 'on' : '' ?>"><?= $l ?></a><?php endforeach; ?></nav>
  <span class="usr"><?= e($_SESSION['user']['nama']) ?> · <a href="logout.php">Keluar</a></span>
</div></header>
<main class="wrap">
<?php if (!empty($_SESSION['flash'])): [$m, $t] = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert <?= $t ?>"><?= e($m) ?></div>
<?php endif; ?>
<h1><?= e($title ?? '') ?></h1>
