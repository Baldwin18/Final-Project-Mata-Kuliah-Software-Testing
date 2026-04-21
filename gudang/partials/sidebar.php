<?php
// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF']);
function nav_active($page, $current) {
    return $page === $current ? 'active' : '';
}
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">📦</span>
        <span class="brand-text">Sistem Gudang</span>
    </div>

    <?php if (isset($_SESSION['username'])): ?>
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
            <div class="user-role">Administrator</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="sidebar-label">MENU</div>

    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item <?= nav_active('index.php', $current) ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="barang.php" class="nav-item <?= nav_active('barang.php', $current) ?>">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Data Barang</span>
        </a>
        <a href="transaksi.php" class="nav-item <?= nav_active('transaksi.php', $current) ?>">
            <span class="nav-icon">🔄</span>
            <span class="nav-text">Transaksi</span>
        </a>
        <a href="laporan.php" class="nav-item <?= nav_active('laporan.php', $current) ?>">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Laporan</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="logout.php" class="nav-item nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<!-- Topbar (mobile + breadcrumb) -->
<div class="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()">☰</button>
    <div class="topbar-title" id="topbarTitle">Dashboard</div>
</div>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
