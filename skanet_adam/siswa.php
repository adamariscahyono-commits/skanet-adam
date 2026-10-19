
<?php

$title = 'Data Siswa';

require 'includes/header.php';
require 'includes/crud.php';

crud([
    'table' => 'siswa',
    'pk'    => 'nis',

    'fields' => [
        'nis'    => ['NIS', 12, '/^\d+$/'],
        'nama'   => ['Nama', 50, null],
        'alamat' => ['Alamat', 50, null],
    ],

    'dep' => 'SELECT COUNT(*) n FROM nilai WHERE nis = ?',

    'dep_msg' => 'Siswa tidak bisa dihapus karena masih memiliki data nilai.',

    'extra' => fn($r) =>
        '<a href="raport.php?nis=' . urlencode($r['nis']) . '">Rapor</a>',
]);

require 'includes/footer.php';

?>
