<?php
/**
 * CRUD generik. $c: table, pk, title, fields [kolom => [label, maxlen, regex|null]],
 * dep (SQL COUNT 'n' dgn 1 param pk), dep_msg, extra (fn($row) => html)
 */
function crud(array $c) {
    $t = $c['table']; $pk = $c['pk']; $F = $c['fields'];
    $self = basename($_SERVER['PHP_SELF']);
    $act = $_GET['act'] ?? 'list';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_POST['do'] ?? '') === 'hapus') {
            $id = $_POST['id'] ?? '';
            if (!empty($c['dep']) && (int)(row($c['dep'], [$id])['n'] ?? 0) > 0) { flash($c['dep_msg'], 'err'); go($self); }
            q("DELETE FROM `$t` WHERE `$pk`=? LIMIT 1", [$id]);
            flash('Data berhasil dihapus.'); go($self);
        }
        $old = $_POST['old'] ?? '';
        $back = $self . '?act=' . ($old === '' ? 'tambah' : 'edit&id=' . urlencode($old));
        $v = [];
        foreach ($F as $k => $f) {
            $x = trim($_POST[$k] ?? '');
            if ($x === '') { flash($f[0] . ' wajib diisi.', 'err'); go($back); }
            if (mb_strlen($x) > $f[1]) { flash($f[0] . ' maksimal ' . $f[1] . ' karakter.', 'err'); go($back); }
            if (!empty($f[2]) && !preg_match($f[2], $x)) { flash($f[0] . ' tidak valid.', 'err'); go($back); }
            $v[$k] = $x;
        }
        if ($old === '') {
            if (row("SELECT 1 FROM `$t` WHERE `$pk`=?", [$v[$pk]])) { flash($F[$pk][0] . ' sudah terdaftar.', 'err'); go($back); }
            $cols = '`' . implode('`,`', array_keys($v)) . '`';
            q("INSERT INTO `$t` ($cols) VALUES (" . implode(',', array_fill(0, count($v), '?')) . ")", array_values($v));
            flash('Data berhasil ditambahkan.');
        } else {
            $set = []; $p = [];
            foreach ($v as $k => $x) if ($k !== $pk) { $set[] = "`$k`=?"; $p[] = $x; }
            $p[] = $old;
            q("UPDATE `$t` SET " . implode(',', $set) . " WHERE `$pk`=? LIMIT 1", $p);
            flash('Data berhasil diperbarui.');
        }
        go($self);
    }

    if ($act === 'tambah' || $act === 'edit') {
        $d = []; $old = '';
        if ($act === 'edit') {
            $old = $_GET['id'] ?? '';
            $d = row("SELECT * FROM `$t` WHERE `$pk`=? LIMIT 1", [$old]);
            if (!$d) { flash('Data tidak ditemukan.', 'err'); go($self); }
        }
        echo '<form method="post" class="card form"><input type="hidden" name="old" value="' . e($old) . '">';
        foreach ($F as $k => $f) {
            $ro = ($act === 'edit' && $k === $pk) ? ' readonly' : '';
            echo '<label>' . e($f[0]) . '<input name="' . $k . '" maxlength="' . $f[1] . '" value="' . e($d[$k] ?? '') . '" required' . $ro . '></label>';
        }
        echo '<div class="btns"><button class="btn">Simpan</button> <a class="btn ghost" href="' . $self . '">Batal</a></div></form>';
        return;
    }

    $s = trim($_GET['q'] ?? '');
    $where = ''; $p = [];
    if ($s !== '') {
        $w = [];
        foreach ($F as $k => $f) { $w[] = "`$k` LIKE ?"; $p[] = "%$s%"; }
        $where = ' WHERE ' . implode(' OR ', $w);
    }
    $data = rows("SELECT * FROM `$t`$where ORDER BY `$pk`+0, `$pk`", $p);
    ?>
    <div class="tools">
      <form method="get"><input name="q" placeholder="Cari..." value="<?= e($s) ?>"> <button class="btn ghost">Cari</button></form>
      <a class="btn" href="<?= $self ?>?act=tambah">+ Tambah</a>
    </div>
    <div class="card"><table>
      <thead><tr><th>#</th><?php foreach ($F as $f) echo '<th>' . e($f[0]) . '</th>'; ?><th>Aksi</th></tr></thead><tbody>
      <?php foreach ($data as $i => $r): ?>
        <tr><td><?= $i + 1 ?></td>
        <?php foreach ($F as $k => $f) echo '<td>' . e($r[$k]) . '</td>'; ?>
        <td class="act">
          <?= isset($c['extra']) ? $c['extra']($r) : '' ?>
          <a href="<?= $self ?>?act=edit&id=<?= urlencode($r[$pk]) ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus data ini?')">
            <input type="hidden" name="do" value="hapus"><input type="hidden" name="id" value="<?= e($r[$pk]) ?>">
            <button class="link danger">Hapus</button></form>
        </td></tr>
      <?php endforeach; if (!$data) echo '<tr><td colspan="' . (count($F) + 2) . '" class="empty">Belum ada data.</td></tr>'; ?>
      </tbody></table></div>
    <?php
}
