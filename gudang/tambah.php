<?php
require_once 'config.php';
require_login();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $kode   = trim($_POST['kode']   ?? '') ?: null;
    $satuan = trim($_POST['satuan'] ?? '') ?: null;
    $stok   = (int)($_POST['stok']  ?? 0);
    $harga  = (float)($_POST['harga'] ?? 0);

    if (!$nama) {
        $error = 'Nama barang tidak boleh kosong.';
    } else {
        $db   = get_db();
        $stmt = $db->prepare('INSERT INTO barang (kode, nama, satuan, harga, stok) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssdi', $kode, $nama, $satuan, $harga, $stok);

        if ($stmt->execute()) {
            $stmt->close();
            $db->close();
            redirect('index.php');
        } else {
            $error = 'Gagal menyimpan. Kode barang mungkin sudah digunakan.';
        }
        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Sistem Gudang</title>
    <?php include 'partials/main_style.php'; ?>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Tambah Barang</h1>
        <p>Tambahkan barang baru ke dalam sistem gudang.</p>
    </div>

    <div class="card card-body" style="max-width:560px;">
        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nama Barang *</label>
                <input type="text" name="nama" placeholder="Contoh: Beras 5kg" required autofocus>
            </div>
            <div class="form-group">
                <label>Kode Barang</label>
                <input type="text" name="kode" placeholder="Contoh: BRG-001">
                <div class="form-hint">Opsional, harus unik</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" name="satuan" placeholder="pcs, kg, liter">
                </div>
                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" value="0" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Harga Satuan (Rp)</label>
                <input type="number" name="harga" placeholder="0" min="0" step="100">
            </div>
            <div class="form-actions">
                <a href="barang.php" class="btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">💾 Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
