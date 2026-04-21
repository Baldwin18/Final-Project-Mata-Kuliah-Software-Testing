<nav class="navbar">
    <div class="navbar-left">
        <span class="navbar-brand">📦 Sistem Gudang</span>
        <?php if (isset($_SESSION['username'])): ?>
        <span class="navbar-user">| <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php endif; ?>
    </div>
    <div class="navbar-actions">
        <a href="tambah.php" class="btn btn-primary">+ Tambah Barang</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>
