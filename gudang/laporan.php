<?php
require_once 'config.php';
require_login();

$db = get_db();

// Filter tanggal
$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Ringkasan stok
$barang = $db->query('SELECT * FROM barang ORDER BY stok ASC')->fetch_all(MYSQLI_ASSOC);

// Total nilai inventori
$nilai_total = 0;
foreach ($barang as $b) $nilai_total += $b['stok'] * $b['harga'];

// Barang hampir habis (stok < 10)
$hampir_habis = array_filter($barang, fn($b) => $b['stok'] < 10 && $b['stok'] > 0);
$habis        = array_filter($barang, fn($b) => $b['stok'] == 0);

// Transaksi dalam rentang tanggal
$stmt = $db->prepare("
    SELECT t.*, b.nama AS nama_barang, b.satuan
    FROM transaksi t
    JOIN barang b ON t.barang_id = b.id
    WHERE DATE(t.tanggal) BETWEEN ? AND ?
    ORDER BY t.tanggal DESC
");
$stmt->bind_param('ss', $dari, $sampai);
$stmt->execute();
$transaksi = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung total masuk & keluar
$total_masuk  = array_sum(array_column(array_filter($transaksi, fn($t) => $t['tipe'] === 'MASUK'),  'jumlah'));
$total_keluar = array_sum(array_column(array_filter($transaksi, fn($t) => $t['tipe'] === 'KELUAR'), 'jumlah'));

$db->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Gudang</title>
    <?php include 'partials/main_style.php'; ?>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Laporan</h1>
        <p>Ringkasan inventori dan aktivitas transaksi gudang.</p>
    </div>

    <!-- Stats nilai -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Nilai Inventori</div>
            <div class="stat-value" style="font-size:1.4rem;">Rp <?= number_format($nilai_total, 0, ',', '.') ?></div>
            <div class="stat-sub">total nilai stok saat ini</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Total Masuk (periode)</div>
            <div class="stat-value"><?= number_format($total_masuk) ?></div>
            <div class="stat-sub">unit diterima</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">Total Keluar (periode)</div>
            <div class="stat-value"><?= number_format($total_keluar) ?></div>
            <div class="stat-sub">unit dikeluarkan</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Barang Hampir Habis</div>
            <div class="stat-value"><?= count($hampir_habis) + count($habis) ?></div>
            <div class="stat-sub">perlu restock</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">

        <!-- Barang hampir habis -->
        <div class="card">
            <div class="card-header"><h2>⚠️ Perlu Restock</h2></div>
            <table>
                <thead><tr><th>Barang</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                    <?php
                    $perlu = array_merge(array_values($habis), array_values($hampir_habis));
                    if (empty($perlu)): ?>
                    <tr><td colspan="3"><div class="empty-state" style="padding:24px;"><p>✅ Semua stok aman.</p></div></td></tr>
                    <?php else: foreach ($perlu as $b): ?>
                    <tr>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($b['nama']) ?></div>
                            <?php if ($b['kode']): ?><div class="item-code"><?= htmlspecialchars($b['kode']) ?></div><?php endif; ?>
                        </td>
                        <td><?= $b['stok'] ?> <?= htmlspecialchars($b['satuan'] ?: '') ?></td>
                        <td>
                            <?php if ($b['stok'] == 0): ?>
                                <span class="stock-badge stock-zero">⚠ Habis</span>
                            <?php else: ?>
                                <span class="stock-badge stock-low">⚡ Menipis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Top stok -->
        <div class="card">
            <div class="card-header"><h2>📦 Ringkasan Stok</h2></div>
            <table>
                <thead><tr><th>Barang</th><th>Stok</th><th>Nilai</th></tr></thead>
                <tbody>
                    <?php
                    $sorted = $barang;
                    usort($sorted, fn($a,$b) => $b['stok'] - $a['stok']);
                    foreach (array_slice($sorted, 0, 8) as $b): ?>
                    <tr>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($b['nama']) ?></div>
                        </td>
                        <td>
                            <?php if ($b['stok'] == 0): ?>
                                <span class="stock-badge stock-zero">0</span>
                            <?php elseif ($b['stok'] < 10): ?>
                                <span class="stock-badge stock-low"><?= $b['stok'] ?></span>
                            <?php else: ?>
                                <span class="stock-badge stock-ok"><?= $b['stok'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="item-harga" style="font-size:0.85rem;">
                            <?= $b['harga'] > 0 ? 'Rp ' . number_format($b['stok'] * $b['harga'], 0, ',', '.') : '—' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Laporan transaksi dengan filter -->
    <div class="card">
        <div class="card-header">
            <h2>📋 Laporan Transaksi</h2>
            <form method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <label style="font-size:0.82rem;color:#64748b;">Dari</label>
                <input type="date" name="dari" value="<?= $dari ?>"
                       style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.85rem;outline:none;">
                <label style="font-size:0.82rem;color:#64748b;">Sampai</label>
                <input type="date" name="sampai" value="<?= $sampai ?>"
                       style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.85rem;outline:none;">
                <button type="submit" class="btn btn-blue" style="padding:7px 16px;font-size:0.85rem;">Filter</button>
            </form>
        </div>
        <table>
            <thead>
                <tr><th>#</th><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Stok Sebelum</th><th>Stok Sesudah</th><th>Keterangan</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)): ?>
                <tr><td colspan="8"><div class="empty-state"><span class="empty-icon">📋</span><p>Tidak ada transaksi pada periode ini.</p></div></td></tr>
                <?php else: foreach ($transaksi as $i => $tx): ?>
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

</div>

</body>
</html>
