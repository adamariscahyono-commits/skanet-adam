<?php
$title = 'Data Guru';
require 'includes/header.php'; require 'includes/crud.php';
crud([
  'table' => 'guru', 'pk' => 'nip',
  'fields' => ['nip' => ['NIP', 12, '/^\d+$/'], 'nama' => ['Nama', 50, null], 'alamat' => ['Alamat', 50, null]],
  'dep' => 'SELECT COUNT(*) n FROM nilai WHERE nip=?',
  'dep_msg' => 'Guru tidak bisa dihapus karena tercatat pada data nilai.',
]);
require 'includes/footer.php';
