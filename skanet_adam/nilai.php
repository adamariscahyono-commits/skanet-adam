
<?php

$title = 'Data Nilai';

require 'includes/header.php';

$act = $_GET['act'] ?? 'list';

// ========================================
// PROSES FORM TAMBAH, EDIT, DAN HAPUS
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Hapus nilai
    if (($_POST['do'] ?? '') === 'hapus') {
        q(
            'DELETE FROM nilai WHERE id_nilai = ? LIMIT 1',
            [$_POST['id'] ?? 0]
        );

        flash('Nilai berhasil dihapus.');
        go('nilai.php');
    }

    // Mengambil data dari form
    $id     = $_POST['id_nilai'] ?? '';
    $nis    = $_POST['nis'] ?? '';
    $mp     = $_POST['id_mapel'] ?? '';
    $nip    = $_POST['nip'] ?? '';
    $nl     = $_POST['nilai'] ?? '';

    $back = 'nilai.php?act=' . (
        $id === ''
            ? 'tambah'
            : 'edit&id=' . urlencode($id)
    );

    // Validasi siswa, mata pelajaran, dan guru
    if (
        !row('SELECT 1 FROM siswa WHERE nis = ?', [$nis]) ||
        !row('SELECT 1 FROM mapel WHERE id_mapel = ?', [$mp]) ||
        !row('SELECT 1 FROM guru WHERE nip = ?', [$nip])
    ) {
        flash(
            'Siswa, mapel, dan guru harus dipilih dari daftar.',
            'err'
        );

        go($back);
    }

    // Validasi nilai
    if (
        !ctype_digit((string) $nl) ||
        (int) $nl > 100
    ) {
        flash('Nilai harus angka 0-100.', 'err');
        go($back);
    }

    // Tambah nilai baru
    if ($id === '') {

        // Mencegah nilai ganda untuk siswa dan mapel yang sama
        if (
            row(
                'SELECT 1 FROM nilai WHERE nis = ? AND id_mapel = ?',
                [$nis, $mp]
            )
        ) {
            flash(
                'Siswa ini sudah memiliki nilai untuk mapel tersebut. Gunakan Edit.',
                'err'
            );

            go($back);
        }

        q(
            'INSERT INTO nilai (nis, id_mapel, nip, nilai)
             VALUES (?, ?, ?, ?)',
            [$nis, $mp, $nip, $nl]
        );

        flash('Nilai berhasil ditambahkan.');

    } else {

        // Memperbarui nilai
        q(
            'UPDATE nilai
             SET nis = ?, id_mapel = ?, nip = ?, nilai = ?
             WHERE id_nilai = ?',
            [$nis, $mp, $nip, $nl, $id]
        );

        flash('Nilai berhasil diperbarui.');
    }

    go('nilai.php');
}

// ========================================
// FORM TAMBAH DAN EDIT NILAI
// ========================================

if ($act === 'tambah' || $act === 'edit') {

    $d = [
        'id_nilai' => '',
        'nis'      => '',
        'id_mapel' => '',
        'nip'      => '',
        'nilai'    => '',
    ];

    // Mengambil data nilai untuk diedit
    if ($act === 'edit') {

        $d = row(
            'SELECT * FROM nilai WHERE id_nilai = ?',
            [$_GET['id'] ?? 0]
        );

        if (!$d) {
            flash('Data tidak ditemukan.', 'err');
            go('nilai.php');
        }
    }

    // Data pilihan dropdown
    $sis = rows(
        'SELECT nis, nama FROM siswa ORDER BY nama'
    );

    $mps = rows(
        'SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel'
    );

    $grs = rows(
        'SELECT nip, nama FROM guru ORDER BY nama'
    );
?>

<form method="post" class="card form">

    <input
        type="hidden"
        name="id_nilai"
        value="<?= e($d['id_nilai']) ?>"
    >

    <!-- Pilih siswa -->
    <label>
        Siswa

        <select name="nis" required>
            <option value="">- pilih siswa -</option>

            <?php foreach ($sis as $x): ?>
                <option
                    value="<?= e($x['nis']) ?>"
                    <?= (string) $d['nis'] === (string) $x['nis']
                        ? 'selected'
                        : '' ?>
                >
                    <?= e($x['nis'] . ' - ' . $x['nama']) ?>
                </option>
            <?php endforeach; ?>

        </select>
    </label>

    <!-- Pilih mata pelajaran -->
    <label>
        Mata Pelajaran

        <select name="id_mapel" required>
            <option value="">- pilih mata pelajaran -</option>

            <?php foreach ($mps as $x): ?>
                <option
                    value="<?= e($x['id_mapel']) ?>"
                    <?= (string) $d['id_mapel'] === (string) $x['id_mapel']
                        ? 'selected'
                        : '' ?>
                >
                    <?= e($x['nama_mapel']) ?>
                </option>
            <?php endforeach; ?>

        </select>
    </label>

    <!-- Pilih guru -->
    <label>
        Guru Penilai

        <select name="nip" required>
            <option value="">- pilih guru -</option>

            <?php foreach ($grs as $x): ?>
                <option
                    value="<?= e($x['nip']) ?>"
                    <?= (string) $d['nip'] === (string) $x['nip']
                        ? 'selected'
                        : '' ?>
                >
                    <?= e($x['nip'] . ' - ' . $x['nama']) ?>
                </option>
            <?php endforeach; ?>

        </select>
    </label>

    <!-- Input nilai -->
    <label>
        Nilai (0-100)

        <input
            type="number"
            name="nilai"
            min="0"
            max="100"
            step="1"
            value="<?= e($d['nilai']) ?>"
            required
        >
    </label>

    <!-- Tombol form -->
    <div class="btns">
        <button type="submit" class="btn">
            <i class="bi bi-save"></i>
            Simpan
        </button>

        <a href="nilai.php" class="btn ghost">
            Batal
        </a>
    </div>

</form>

<?php

// ========================================
// DAFTAR NILAI DAN PENCARIAN
// ========================================

} else {

    $s = trim($_GET['q'] ?? '');

    $w = '';
    $p = [];

    // Filter pencarian
    if ($s !== '') {

        $w = '
            WHERE n.nis LIKE ?
               OR s.nama LIKE ?
               OR m.nama_mapel LIKE ?
               OR g.nama LIKE ?
        ';

        $p = array_fill(0, 4, "%$s%");
    }

    // Mengambil data nilai beserta relasinya
    $data = rows(
        "SELECT
            n.*,
            s.nama AS siswa,
            m.nama_mapel,
            g.nama AS guru
         FROM nilai n
         LEFT JOIN siswa s ON s.nis = n.nis
         LEFT JOIN mapel m ON m.id_mapel = n.id_mapel
         LEFT JOIN guru g ON g.nip = n.nip
         $w
         ORDER BY n.id_nilai",
        $p
    );

?>

<!-- Toolbar pencarian dan tambah -->
<div class="tools">

    <form method="get">
        <input
            type="text"
            name="q"
            placeholder="Cari siswa / mapel / guru..."
            value="<?= e($s) ?>"
        >

        <button type="submit" class="btn ghost">
            <i class="bi bi-search"></i>
            Cari
        </button>
    </form>

    <a href="nilai.php?act=tambah" class="btn">
        <i class="bi bi-plus-circle"></i>
        Tambah Nilai
    </a>

</div>

<!-- Tabel daftar nilai -->
<div class="card">

    <div style="overflow-x: auto;">
        <table>

            <thead>
                <tr>
                    <th>#</th>
                    <th>NIS</th>
                    <th>Siswa</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Nilai</th>
                    <th>Predikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($data as $i => $r): ?>

                    <tr>
                        <td><?= $i + 1 ?></td>

                        <td><?= e($r['nis']) ?></td>

                        <td>
                            <?php if ($r['siswa']): ?>
                                <?= e($r['siswa']) ?>
                            <?php else: ?>
                                <em class="warn">tidak ditemukan</em>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($r['nama_mapel']): ?>
                                <?= e($r['nama_mapel']) ?>
                            <?php else: ?>
                                <em class="warn">tidak ditemukan</em>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($r['guru']): ?>
                                <?= e($r['guru']) ?>
                            <?php else: ?>
                                <em class="warn">tidak ditemukan</em>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= (int) $r['nilai'] ?></strong>
                        </td>

                        <td>
                            <span class="pill <?= e(predikat($r['nilai'])) ?>">
                                <?= e(predikat($r['nilai'])) ?>
                            </span>
                        </td>

                        <td class="act">

                            <a href="nilai.php?act=edit&id=<?= (int) $r['id_nilai'] ?>">
                                <i class="bi bi-pencil-square"></i>
                                Edit
                            </a>

                            <form
                                method="post"
                                onsubmit="return confirm('Hapus nilai ini?')"
                            >
                                <input
                                    type="hidden"
                                    name="do"
                                    value="hapus"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $r['id_nilai'] ?>"
                                >

                                <button type="submit" class="link danger">
                                    <i class="bi bi-trash"></i>
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (!$data): ?>
                    <tr>
                        <td colspan="8" class="empty">
                            Belum ada data nilai.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>
    </div>

</div>

<p class="hint">
    <i class="bi bi-info-circle"></i>
    Baris bertanda "tidak ditemukan" adalah data lama yang merujuk
    ke siswa, mata pelajaran, atau guru yang tidak ada.
    Perbaiki melalui menu Edit.
</p>

<?php
}

require 'includes/footer.php';
?>