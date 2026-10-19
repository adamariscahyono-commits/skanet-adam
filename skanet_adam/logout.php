<?php
require 'includes/koneksi.php';
session_destroy();
header('Location: login.php');
