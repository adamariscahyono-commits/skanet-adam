
<?php

require 'includes/koneksi.php';

// Jika sudah login, arahkan ke dashboard
if (!empty($_SESSION['user'])) {
    go('index.php');
    exit;
}

$err = '';

// ========================================
// PROSES LOGIN
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Memeriksa username dan password
    $u = row(
        'SELECT *
         FROM users
         WHERE username = ?
           AND password = ?
         LIMIT 1',
        [$username, $password]
    );

    if ($u) {

        // Memperbarui session ID setelah login
        session_regenerate_id(true);

        $_SESSION['user'] = $u;

        go('index.php');
        exit;
    }

    $err = 'Username atau password salah.';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Login - SKANET</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="login">

    <form method="post" class="card loginbox">

        <h1>SKANET</h1>

        <p class="hint">
            Sistem Penilaian Sekolah
        </p>

        <!-- Pesan kesalahan login -->
        <?php if ($err !== ''): ?>
            <div class="alert err">
                <?= e($err) ?>
            </div>
        <?php endif; ?>

        <!-- Input username -->
        <label>
            Username

            <input
                type="text"
                name="username"
                autocomplete="username"
                autofocus
                required
            >
        </label>

        <!-- Input password -->
        <label>
            Password

            <input
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </label>

        <!-- Tombol login -->
        <button type="submit" class="btn block">
            Masuk
        </button>

    </form>

</body>
</html>
