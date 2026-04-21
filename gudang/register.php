<?php
require_once 'config.php';

if (is_logged_in()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $db   = get_db();
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $error = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $hash);
            $stmt->execute();
            $stmt->close();
            $db->close();
            redirect('login.php');
        }
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Gudang</title>
    <?php include 'partials/auth_style.php'; ?>
    <style>
        .btn-submit { background: #22c55e; }
        .btn-submit:hover { background: #16a34a; }
        .form-group input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.12); }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">
        <span class="icon">📦</span>
        <h1>Buat Akun</h1>
        <p>Daftar untuk mulai menggunakan sistem</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Buat username" required autofocus>
            <div class="form-hint">Minimal 3 karakter, tanpa spasi</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Buat password" required>
            <div class="form-hint">Minimal 6 karakter</div>
        </div>
        <button type="submit" class="btn-submit">Daftar</button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>
</div>
</body>
</html>
