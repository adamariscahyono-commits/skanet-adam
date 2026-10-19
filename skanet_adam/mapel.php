
<?php

$title = 'Mata Pelajaran';

require 'includes/header.php';
require 'includes/crud.php';

// ========================================
// KONFIGURASI CRUD MATA PELAJARAN
// ========================================

crud([

    // Nama tabel dan primary key
    'table' => 'mapel',
    'pk'    => 'id_mapel',

    // Daftar field formulir
    'fields' => [
        'id_mapel'   => ['ID Mapel', 8, '/^\d+$/'],
        'nama_mapel' => ['Nama Mapel', 50, null],
        'guru'       => ['Guru Pengampu', 50, null],
    ],

    // Memeriksa apakah mapel digunakan dalam data nilai
    'dep' => '
        SELECT COUNT(*) n
        FROM nilai
        WHERE id_mapel = ?
    ',

    // Pesan jika mapel masih digunakan
    'dep_msg' => 'Mapel tidak bisa dihapus karena sudah dipakai pada data nilai.',

]);

require 'includes/footer.php';

?>
