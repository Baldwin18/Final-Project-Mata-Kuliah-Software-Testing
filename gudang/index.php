<?php
require_once 'config.php';
require_login();

$db     = get_db();
$barang = $db->query('SELECT * FROM barang ORDER BY nama')->fetch_all(MYSQLI_ASSOC);

$total        = count($barang);
$stok_menipis = 0;
$stok_aman    = 0;
$total_stok   = 0;
foreach ($barang as $b) {
    $total_stok += $b['stok'];
    if ($b['stok'] < 10) $stok_menipis++;
    else $stok_aman++;
}

// Transaksi cepat dari dashboard
$tx_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $barang_id = (int)($_POST['barang_id'] ?? 0);
    $jumlah    = (int)($_POST['jumlah']    ?? 0);
    $action    = $_POST['action'];

    if ($jumlah <= 0) {
        $tx_error = 'Jumlah harus lebih dari 0.';
    } else {
        $row = $db->query("SELECT * FROM barang WHERE id = $barang_id")->fetch_assoc();
        if ($row) {
            $stok_sebelum = (int)$row['stok'];
            if ($action === 'KELUAR' && $stok_sebelum < $jumlah) {
                $tx_error = "Stok tidak mencukupi untuk <b>{$row['nama']}</b>. Stok saat ini: $stok_sebelum.";
            } else {
                $stok_sesudah = $action === 'MASUK' ? $stok_sebelum + $jumlah : $stok_sebelum - $jumlah;
                $stmt = $db->prepare('UPDATE barang SET stok = ? WHERE id = ?');
                $stmt->bind_param('ii', $stok_sesudah, $barang_id);
                $stmt->execute(); $stmt->close();
                $stmt = $db->prepare('INSERT INTO transaksi (barang_id, tipe, jumlah, stok_sebelum, stok_sesudah) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('isiii', $barang_id, $action, $jumlah, $stok_sebelum, $stok_sesudah);
                $stmt->execute(); $stmt->close();
                $db->close();
                redirect('index.php');
            }
        }
    }
    $barang = $db->query('SELECT * FROM barang ORDER BY nama')->fetch_all(MYSQLI_ASSOC);
}

// 5 transaksi terakhir
$recent_tx = $db->query('
    SELECT t.*, b.nama AS nama_barang
    FROM transaksi t
    JOIN barang b ON t.barang_id = b.id
    ORDER BY t.tanggal DESC LIMIT 5
')->fetch_all(MYSQLI_ASSOC);

$db->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Gudang</title>
    <?php include 'partials/main_style.php'; ?>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>. Berikut ringkasan gudang hari ini.</p>
    </div>

    <?php if ($tx_error): ?>
        <div class="alert alert-error">⚠️ <?= $tx_error ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Total Jenis Barang</div>
            <div class="stat-value"><?= $total ?></div>
            <div class="stat-sub">item terdaftar</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value"><?= $stok_menipis ?></div>
            <div class="stat-sub">di bawah 10 unit</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Stok Aman</div>
            <div class="stat-value"><?= $stok_aman ?></div>
            <div class="stat-sub">10 unit atau lebih</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Total Stok</div>
            <div class="stat-value"><?= number_format($total_stok) ?></div>
            <div class="stat-sub">unit keseluruhan</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; align-items:start;">

        <!-- Tabel barang -->
        <div class="card">
            <div class="card-header">
                <h2>Stok Barang</h2>
                <a href="barang.php" class="btn btn-blue" style="font-size:0.8rem;padding:7px 14px;">Lihat Semua</a>
            </div>
            <table>
                <thead>
                    <tr><th>#</th><th>Barang</th><th>Stok</th><th>Transaksi Cepat</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($barang)): ?>
                    <tr><td colspan="4">
                        <div class="empty-state">
                            <span class="empty-icon">📭</span>
                            <p>Belum ada barang.</p>
                            <a href="barang.php" class="btn btn-blue">+ Tambah Barang</a>
                        </div>
                    </td></tr>
                    <?php else: foreach ($barang as $i => $b): ?>
                    <tr>
                        <td class="row-num"><?= $i + 1 ?></td>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($b['nama']) ?></div>
                            <?php if ($b['kode']): ?><div class="item-code"><?= htmlspecialchars($b['kode']) ?></div><?php endif; ?>
                        </td>
                        <td>
                            <?php if ($b['stok'] == 0): ?>
                                <span class="stock-badge stock-zero">⚠ Habis</span>
                            <?php elseif ($b['stok'] < 10): ?>
                                <span class="stock-badge stock-low">⚡ <?= $b['stok'] ?></span>
                            <?php else: ?>
                                <span class="stock-badge stock-ok">✓ <?= $b['stok'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="tx-forms">
                                <form class="tx-form" method="post">
                                    <input type="hidden" name="barang_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="action" value="MASUK">
                                    <input type="number" name="jumlah" placeholder="Qty" min="1" required>
                                    <button type="submit" class="btn btn-success">↑</button>
                                </form>
                                <form class="tx-form" method="post">
                                    <input type="hidden" name="barang_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="action" value="KELUAR">
                                    <input type="number" name="jumlah" placeholder="Qty" min="1" required>
                                    <button type="submit" class="btn btn-red">↓</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Transaksi terakhir -->
        <div class="card">
            <div class="card-header">
                <h2>Transaksi Terakhir</h2>
                <a href="transaksi.php" class="btn btn-blue" style="font-size:0.8rem;padding:7px 14px;">Lihat Semua</a>
            </div>
            <table>
                <thead>
                    <tr><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_tx)): ?>
                    <tr><td colspan="4"><div class="empty-state"><span class="empty-icon">📋</span><p>Belum ada transaksi.</p></div></td></tr>
                    <?php else: foreach ($recent_tx as $tx): ?>
                    <tr>
                        <td class="item-name"><?= htmlspecialchars($tx['nama_barang']) ?></td>
                        <td>
                            <?php if ($tx['tipe'] === 'MASUK'): ?>
                                <span class="badge-masuk">↑ Masuk</span>
                            <?php else: ?>
                                <span class="badge-keluar">↓ Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $tx['jumlah'] ?></td>
                        <td style="font-size:0.8rem;color:#94a3b8;"><?= date('d/m H:i', strtotime($tx['tanggal'])) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function filterTable() {
    const q    = document.getElementById('searchInput')?.value.toLowerCase();
    const rows = document.querySelectorAll('#barangTable tbody tr');
    rows.forEach(r => r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none');
}
</script>
</body>
</html>
