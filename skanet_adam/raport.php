
<?php

$title = 'Rapor Siswa';

require 'includes/header.php';

// Mengambil NIS dari URL
$nis = $_GET['nis'] ?? '';

// Mengambil data siswa
$sis = row(
    'SELECT * FROM siswa WHERE nis = ?',
    [$nis]
);

// Jika siswa tidak ditemukan
if (!$sis) {
    echo '
        <p>
            Siswa tidak ditemukan.
            <a href="siswa.php">Kembali</a>
        </p>
    ';

    require 'includes/footer.php';
    exit;
}

// Mengambil data nilai, mata pelajaran, dan guru
$data = rows(
    'SELECT
        n.nilai,
        m.nama_mapel,
        g.nama AS guru
     FROM nilai n
     LEFT JOIN mapel m ON m.id_mapel = n.id_mapel
     LEFT JOIN guru g ON g.nip = n.nip
     WHERE n.nis = ?
     ORDER BY m.nama_mapel',
    [$nis]
);

// Menghitung rata-rata nilai
$avg = $data
    ? array_sum(array_column($data, 'nilai')) / count($data)
    : 0;

?>

<div class="card">

    <!-- Identitas siswa -->
    <p>
        <strong>NIS:</strong>
        <?= e($sis['nis']) ?>
        <br>

        <strong>Nama:</strong>
        <?= e($sis['nama']) ?>
        <br>

        <strong>Alamat:</strong>
        <?= e($sis['alamat']) ?>
    </p>

    <!-- Tabel nilai siswa -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Nilai</th>
                <th>Predikat</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($data as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>

                    <td>
                        <?= e($r['nama_mapel'] ?? '-') ?>
                    </td>

                    <td>
                        <?= e($r['guru'] ?? '-') ?>
                    </td>

                    <td>
                        <?= (int) $r['nilai'] ?>
                    </td>

                    <td>
                        <?= predikat($r['nilai']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$data): ?>
                <tr>
                    <td colspan="5" class="empty">
                        Belum ada nilai.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

        <?php if ($data): ?>
            <tfoot>
                <tr>
                    <th colspan="3">Rata-rata</th>

                    <th>
                        <?= number_format($avg, 1) ?>
                    </th>

                    <th>
                        <?= predikat($avg) ?>
                    </th>
                </tr>
            </tfoot>
        <?php endif; ?>

    </table>

</div>

<!-- Tombol navigasi dan cetak -->
<div class="tools">

    <a class="btn ghost" href="siswa.php">
        &larr; Kembali
    </a>

    <button class="btn" onclick="window.print()">
        Cetak
    </button>

</div>

<?php require 'includes/footer.php'; ?>
