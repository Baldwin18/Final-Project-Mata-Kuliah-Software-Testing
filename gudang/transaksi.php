<?php
require_once 'config.php';
require_login();

$error   = '';
$success = '';

$db     = get_db();
$barang = $db->query('SELECT * FROM barang ORDER BY nama')->fetch_all(MYSQLI_ASSOC);

// Proses transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id  = (int)($_POST['barang_id']  ?? 0);
    $tipe       = $_POST['tipe']       ?? '';
    $jumlah     = (int)($_POST['jumlah']     ?? 0);
    $keterangan = trim($_POST['keterangan']  ?? '');

    if (!$barang_id || !in_array($tipe, ['MASUK','KELUAR']) || $jumlah <= 0) {
        $error = 'Lengkapi semua field dengan benar.';
    } else {
        $row = $db->query("SELECT * FROM barang WHERE id = $barang_id")->fetch_assoc();
        if (!$row) {
            $error = 'Barang tidak ditemukan.';
        } elseif ($tipe === 'KELUAR' && $row['stok'] < $jumlah) {
            $error = "Stok tidak mencukupi untuk <b>{$row['nama']}</b>. Stok saat ini: {$row['stok']}.";
        } else {
            $stok_sebelum = (int)$row['stok'];
            $stok_sesudah = $tipe === 'MASUK' ? $stok_sebelum + $jumlah : $stok_sebelum - $jumlah;

            $stmt = $db->prepare('UPDATE barang SET stok = ? WHERE id = ?');
            $stmt->bind_param('ii', $stok_sesudah, $barang_id);
            $stmt->execute(); $stmt->close();

            $stmt = $db->prepare('INSERT INTO transaksi (barang_id, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isiiss', $barang_id, $tipe, $jumlah, $stok_sebelum, $stok_sesudah, $keterangan);
            $stmt->execute(); $stmt->close();

            $success = "Transaksi <b>$tipe</b> sebanyak <b>$jumlah</b> unit untuk <b>{$row['nama']}</b> berhasil dicatat.";
            $barang  = $db->query('SELECT * FROM barang ORDER BY nama')->fetch_all(MYSQLI_ASSOC);
        }
    }
}

// Ambil riwayat transaksi
$filter_tipe = $_GET['tipe'] ?? '';
$where = $filter_tipe ? "WHERE t.tipe = '" . $db->real_escape_string($filter_tipe) . "'" : '';
$riwayat = $db->query("
    SELECT t.*, b.nama AS nama_barang, b.satuan
    FROM transaksi t
    JOIN barang b ON t.barang_id = b.id
    $where
    ORDER BY t.tanggal DESC
    LIMIT 100
")->fetch_all(MYSQLI_ASSOC);

$db->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Sistem Gudang</title>
    <?php include 'partials/main_style.php'; ?>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Transaksi</h1>
        <p>Catat stok masuk dan keluar barang.</p>
    </div>

    <?php if ($error):   ?><div class="alert alert-error">⚠️ <?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

    <div style="display:grid; grid-template-columns: 1fr 320px; gap:20px; align-items:start;">

        <!-- Riwayat -->
        <div class="card">
            <div class="card-header">
                <h2>Riwayat Transaksi</h2>
                <div style="display:flex;gap:8px;">
                    <a href="transaksi.php" class="btn btn-secondary" style="padding:6px 12px;font-size:0.8rem;">Semua</a>
                    <a href="transaksi.php?tipe=MASUK" class="btn btn-success" style="<?= $filter_tipe==='MASUK'?'outline:2px solid #16a34a;':'' ?>">↑ Masuk</a>
                    <a href="transaksi.php?tipe=KELUAR" class="btn btn-red" style="<?= $filter_tipe==='KELUAR'?'outline:2px solid #dc2626;':'' ?>">↓ Keluar</a>
                </div>
            </div>
            <table>
                <thead>
                    <tr><th>#</th><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Stok Sebelum</th><th>Stok Sesudah</th><th>Keterangan</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                    <tr><td colspan="8"><div class="empty-state"><span class="empty-icon">📋</span><p>Belum ada transaksi.</p></div></td></tr>
                    <?php else: foreach ($riwayat as $i => $tx): ?>
                    <tr>
                        <td class="row-num"><?= $i + 1 ?></td>
                        <td class="item-name"><?= htmlspecialchars($tx['nama_barang']) ?></td>
                        <td>
                            <?php if ($tx['tipe'] === 'MASUK'): ?>
                                <span class="badge-masuk">↑ Masuk</span>
                            <?php else: ?>
                                <span class="badge-keluar">↓ Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= $tx['jumlah'] ?></strong> <?= htmlspecialchars($tx['satuan'] ?: '') ?></td>
                        <td style="color:#94a3b8;"><?= $tx['stok_sebelum'] ?></td>
                        <td><strong><?= $tx['stok_sesudah'] ?></strong></td>
                        <td style="font-size:0.82rem;color:#64748b;"><?= htmlspecialchars($tx['keterangan'] ?: '—') ?></td>
                        <td style="font-size:0.8rem;color:#94a3b8;white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($tx['tanggal'])) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Form transaksi -->
        <div class="card card-body">
            <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;margin-bottom:20px;">🔄 Catat Transaksi</h2>
            <form method="post">
                <div class="form-group">
                    <label>Barang *</label>
                    <select name="barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php foreach ($barang as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama']) ?> (Stok: <?= $b['stok'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipe Transaksi *</label>
                    <select name="tipe" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="MASUK">↑ Stok Masuk</option>
                        <option value="KELUAR">↓ Stok Keluar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah *</label>
                    <input type="number" name="jumlah" placeholder="0" min="1" required>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Opsional...">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">💾 Simpan Transaksi</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
