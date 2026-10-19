
<?php

$title = 'Dashboard';

require 'includes/header.php';

// ========================================
// STATISTIK DASHBOARD
// ========================================

$c = row(
    'SELECT
        (SELECT COUNT(*) FROM siswa) AS siswa,
        (SELECT COUNT(*) FROM guru) AS guru,
        (SELECT COUNT(*) FROM mapel) AS mapel,
        (SELECT COUNT(*) FROM nilai) AS nilai,
        (SELECT AVG(nilai) FROM nilai) AS rata'
);

// ========================================
// 5 SISWA DENGAN RATA-RATA TERTINGGI
// ========================================

$top = rows(
    'SELECT
        s.nis,
        s.nama,
        AVG(n.nilai) AS rata,
        COUNT(*) AS jml
     FROM nilai n
     JOIN siswa s ON s.nis = n.nis
     GROUP BY s.nis, s.nama
     ORDER BY rata DESC
     LIMIT 5'
);

?>

<!-- =====================================
     KARTU STATISTIK
====================================== -->

<div class="stats">

    <div class="stat">
        <span><?= (int) $c['siswa'] ?></span>
        Siswa
    </div>

    <div class="stat">
        <span><?= (int) $c['guru'] ?></span>
        Guru
    </div>

    <div class="stat">
        <span><?= (int) $c['mapel'] ?></span>
        Mata Pelajaran
    </div>

    <div class="stat">
        <span><?= (int) $c['nilai'] ?></span>
        Data Nilai
    </div>

    <div class="stat">
        <span>
            <?= number_format((float) $c['rata'], 1) ?>
        </span>
        Rata-rata Nilai
    </div>

</div>

<!-- =====================================
     PERINGKAT SISWA
====================================== -->

<h2>5 Siswa dengan Rata-rata Tertinggi</h2>

<div class="card">

    <div style="overflow-x: auto;">

        <table>

            <thead>
                <tr>
                    <th>#</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jumlah Nilai</th>
                    <th>Rata-rata</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($top as $i => $r): ?>

                    <tr>
                        <td><?= $i + 1 ?></td>

                        <td>
                            <?= e($r['nis']) ?>
                        </td>

                        <td>
                            <?= e($r['nama']) ?>
                        </td>

                        <td>
                            <?= (int) $r['jml'] ?>
                        </td>

                        <td>
                            <strong>
                                <?= number_format((float) $r['rata'], 1) ?>
                            </strong>
                        </td>

                        <td class="act">
                            <a href="raport.php?nis=<?= urlencode($r['nis']) ?>">
                                <i class="bi bi-file-earmark-text"></i>
                                Rapor
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (!$top): ?>
                    <tr>
                        <td colspan="6" class="empty">
                            Belum ada data siswa yang memiliki nilai.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require 'includes/footer.php'; ?>
