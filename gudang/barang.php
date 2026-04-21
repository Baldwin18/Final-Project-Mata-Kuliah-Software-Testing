<?php
require_once 'config.php';
require_login();

$error   = '';
$success = '';

$db = get_db();

// Tambah barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'tambah') {
    $nama   = trim($_POST['nama']   ?? '');
    $kode   = trim($_POST['kode']   ?? '') ?: null;
    $satuan = trim($_POST['satuan'] ?? '') ?: null;
    $stok   = (int)($_POST['stok']  ?? 0);
    $harga  = (float)($_POST['harga'] ?? 0);

    if (!$nama) {
        $error = 'Nama barang tidak boleh kosong.';
    } else {
        // Cek duplikat nama (case-insensitive)
        $cek = $db->prepare('SELECT id FROM barang WHERE LOWER(nama) = LOWER(?)');
        $cek->bind_param('s', $nama);
        $cek->execute();
        $cek->store_result();
        $sudah_ada = $cek->num_rows > 0;
        $cek->close();

        if ($sudah_ada) {
            $error = "Barang <b>" . htmlspecialchars($nama) . "</b> sudah ada di daftar. Gunakan fitur transaksi untuk menambah stok.";
        } else {
            $stmt = $db->prepare('INSERT INTO barang (kode, nama, satuan, harga, stok) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssdi', $kode, $nama, $satuan, $harga, $stok);
            if ($stmt->execute()) {
                $success = "Barang <b>" . htmlspecialchars($nama) . "</b> berhasil ditambahkan.";
            } else {
                $error = 'Gagal menyimpan. Kode barang mungkin sudah digunakan.';
            }
            $stmt->close();
        }
    }
}

// Hapus barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'hapus') {
    $id = (int)($_POST['id'] ?? 0);
    $db->query("DELETE FROM barang WHERE id = $id");
    $success = 'Barang berhasil dihapus.';
}

$barang = $db->query('SELECT * FROM barang ORDER BY nama')->fetch_all(MYSQLI_ASSOC);
$db->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Sistem Gudang</title>
    <?php include 'partials/main_style.php'; ?>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Data Barang</h1>
        <p>Kelola semua data barang di gudang.</p>
    </div>

    <?php if ($error):   ?><div class="alert alert-error">⚠️ <?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

    <div style="display:grid; grid-template-columns: 1fr 340px; gap:20px; align-items:start;">

        <!-- Tabel -->
        <div class="card">
            <div class="card-header">
                <h2>Daftar Barang (<?= count($barang) ?>)</h2>
                <input type="text" class="search-input" id="searchInput" placeholder="🔍 Cari barang..." onkeyup="filterTable()">
            </div>
            <table id="barangTable">
                <thead>
                    <tr><th>#</th><th>Barang</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($barang)): ?>
                    <tr><td colspan="6"><div class="empty-state"><span class="empty-icon">📭</span><p>Belum ada barang.</p></div></td></tr>
                    <?php else: foreach ($barang as $i => $b): ?>
                    <tr>
                        <td class="row-num"><?= $i + 1 ?></td>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($b['nama']) ?></div>
                            <?php if ($b['kode']): ?><div class="item-code"><?= htmlspecialchars($b['kode']) ?></div><?php endif; ?>
                        </td>
                        <td class="item-satuan"><?= htmlspecialchars($b['satuan'] ?: '—') ?></td>
                        <td class="item-harga"><?= $b['harga'] > 0 ? 'Rp ' . number_format($b['harga'], 0, ',', '.') : '—' ?></td>
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
                            <form method="post" onsubmit="return confirm('Hapus barang ini?')">
                                <input type="hidden" name="_action" value="hapus">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="font-size:0.78rem;padding:5px 12px;">🗑 Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Form tambah -->
        <div class="card card-body">
            <h2 style="font-size:1rem;font-weight:600;color:#1e3a5f;margin-bottom:20px;">➕ Tambah Barang</h2>
            <form method="post">
                <input type="hidden" name="_action" value="tambah">
                <div class="form-group">
                    <label>Nama Barang *</label>
                    <input type="text" name="nama" placeholder="Contoh: Beras 5kg" required>
                </div>
                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text" name="kode" placeholder="Contoh: BRG-001">
                    <div class="form-hint">Opsional, harus unik</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" placeholder="pcs, kg...">
                    </div>
                    <div class="form-group">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" value="0" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Harga Satuan (Rp)</label>
                    <input type="number" name="harga" placeholder="0" min="0" step="100">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">💾 Simpan Barang</button>
            </form>
        </div>

    </div>
</div>

<script>
function filterTable() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#barangTable tbody tr');
    rows.forEach(r => r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none');
}
</script>
</body>
</html>
