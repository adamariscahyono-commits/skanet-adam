<?php
require_once __DIR__ . '/koneksi.php';
if (empty($_SESSION['user'])) go('login.php');
