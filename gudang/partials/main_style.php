<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --sidebar-w: 240px;
        --sidebar-bg: #1e3a5f;
        --sidebar-hover: rgba(255,255,255,0.08);
        --sidebar-active: rgba(59,130,246,0.25);
        --sidebar-active-border: #3b82f6;
        --topbar-h: 56px;
        --bg: #f0f2f5;
        --card-bg: #fff;
        --text: #1e293b;
        --muted: #64748b;
        --border: #e8ecf0;
    }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: var(--bg);
        color: var(--text);
        min-height: 100vh;
    }

    /* ── Sidebar ─────────────────────────────────────────── */
    .sidebar {
        position: fixed;
        top: 0; left: 0;
        width: var(--sidebar-w);
        height: 100vh;
        background: var(--sidebar-bg);
        display: flex;
        flex-direction: column;
        z-index: 200;
        transition: transform 0.25s ease;
        overflow-y: auto;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 22px 20px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .brand-icon { font-size: 1.6rem; }
    .brand-text { color: #fff; font-size: 1.05rem; font-weight: 700; letter-spacing: 0.3px; }

    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 8px;
    }

    .user-avatar {
        width: 36px; height: 36px;
        background: #3b82f6;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 0.95rem;
        flex-shrink: 0;
    }

    .user-name { color: #fff; font-size: 0.875rem; font-weight: 600; }
    .user-role { color: rgba(255,255,255,0.45); font-size: 0.75rem; margin-top: 1px; }

    .sidebar-label {
        padding: 8px 20px 4px;
        font-size: 0.68rem;
        font-weight: 700;
        color: rgba(255,255,255,0.3);
        letter-spacing: 1px;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        padding: 4px 12px;
        flex: 1;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        border-radius: 9px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background 0.15s, color 0.15s;
        margin-bottom: 2px;
        border-left: 3px solid transparent;
    }

    .nav-item:hover {
        background: var(--sidebar-hover);
        color: #fff;
    }

    .nav-item.active {
        background: var(--sidebar-active);
        color: #fff;
        border-left-color: var(--sidebar-active-border);
        font-weight: 600;
    }

    .nav-icon { font-size: 1.1rem; width: 22px; text-align: center; flex-shrink: 0; }

    .sidebar-bottom {
        padding: 12px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .nav-logout { color: rgba(255,100,100,0.8); }
    .nav-logout:hover { background: rgba(239,68,68,0.15); color: #fca5a5; }

    /* ── Topbar ───────────────────────────────────────────── */
    .topbar {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--topbar-h);
        background: var(--sidebar-bg);
        align-items: center;
        padding: 0 16px;
        gap: 14px;
        z-index: 150;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .topbar-toggle {
        background: none; border: none; color: #fff;
        font-size: 1.3rem; cursor: pointer; padding: 4px;
    }

    .topbar-title { color: #fff; font-size: 1rem; font-weight: 600; }

    /* ── Overlay ──────────────────────────────────────────── */
    .sidebar-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 190;
    }

    /* ── Main content ─────────────────────────────────────── */
    .main-content {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        padding: 32px;
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3a5f;
    }

    .page-header p {
        color: var(--muted);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    /* ── Alert ────────────────────────────────────────────── */
    .alert {
        border-radius: 10px; padding: 12px 16px;
        font-size: 0.875rem; margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }
    .alert-error   { background: #fee2e2; color: #dc2626; border-left: 3px solid #ef4444; }
    .alert-success { background: #dcfce7; color: #16a34a; border-left: 3px solid #22c55e; }
    .alert-warning { background: #fef9c3; color: #b45309; border-left: 3px solid #f59e0b; }

    /* ── Stats ────────────────────────────────────────────── */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        border-left: 4px solid #3b82f6;
    }
    .stat-card.warning { border-left-color: #f59e0b; }
    .stat-card.success { border-left-color: #22c55e; }
    .stat-card.purple  { border-left-color: #8b5cf6; }
    .stat-label { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px; }
    .stat-value { font-size: 1.9rem; font-weight: 700; color: #1e3a5f; line-height: 1; }
    .stat-sub   { font-size: 0.78rem; color: #94a3b8; margin-top: 4px; }

    /* ── Card ─────────────────────────────────────────────── */
    .card { background: var(--card-bg); border-radius: 14px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); overflow: hidden; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .card-header h2 { font-size: 1rem; font-weight: 600; color: #1e3a5f; }
    .card-body { padding: 24px; }

    /* ── Search ───────────────────────────────────────────── */
    .search-input {
        padding: 8px 14px; border: 1.5px solid var(--border);
        border-radius: 8px; font-size: 0.875rem; outline: none;
        width: 220px; transition: border-color 0.2s; background: #f8fafc;
    }
    .search-input:focus { border-color: #3b82f6; background: #fff; }

    /* ── Table ────────────────────────────────────────────── */
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #f8fafc; padding: 12px 18px; text-align: left;
        font-size: 0.78rem; font-weight: 600; color: var(--muted);
        text-transform: uppercase; letter-spacing: 0.4px;
        border-bottom: 1px solid var(--border); white-space: nowrap;
    }
    tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    tbody td { padding: 13px 18px; font-size: 0.9rem; vertical-align: middle; }
    .row-num     { color: #cbd5e1; font-size: 0.8rem; font-weight: 500; }
    .item-name   { font-weight: 600; color: #1e293b; }
    .item-code   { font-size: 0.78rem; color: #94a3b8; margin-top: 2px; }
    .item-satuan { color: var(--muted); font-size: 0.85rem; }
    .item-harga  { font-weight: 500; }

    /* ── Badges ───────────────────────────────────────────── */
    .stock-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.82rem; white-space: nowrap; }
    .stock-ok   { background: #dcfce7; color: #16a34a; }
    .stock-low  { background: #fef9c3; color: #b45309; }
    .stock-zero { background: #fee2e2; color: #dc2626; }

    .badge-masuk  { background: #dcfce7; color: #16a34a; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-keluar { background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }

    /* ── Transaction forms ────────────────────────────────── */
    .tx-forms { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
    .tx-form  { display: flex; align-items: center; gap: 5px; }
    .tx-form input[type="number"] { width: 72px; padding: 6px 9px; border: 1.5px solid var(--border); border-radius: 7px; font-size: 0.82rem; outline: none; background: #f8fafc; transition: border-color 0.2s; }
    .tx-form input[type="number"]:focus { border-color: #3b82f6; background: #fff; }

    /* ── Buttons ──────────────────────────────────────────── */
    .btn {
        padding: 9px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 500;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity 0.2s, transform 0.1s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn:hover { opacity: 0.88; transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }
    .btn-primary { background: #1e3a5f; color: #fff; }
    .btn-primary:hover { background: #2d6a9f; opacity: 1; }
    .btn-blue    { background: #3b82f6; color: #fff; }
    .btn-danger  { background: #ef4444; color: #fff; }
    .btn-success { background: #22c55e; color: #fff; font-size: 0.8rem; padding: 7px 12px; }
    .btn-red     { background: #ef4444; color: #fff; font-size: 0.8rem; padding: 7px 12px; }
    .btn-secondary { padding: 9px 18px; background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: background 0.2s; }
    .btn-secondary:hover { background: #e2e8f0; }

    /* ── Form ─────────────────────────────────────────────── */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 0.95rem; color: #1e293b; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #f8fafc; font-family: inherit;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); background: #fff; }
    .form-hint { font-size: 0.78rem; color: #94a3b8; margin-top: 5px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-actions { display: flex; gap: 12px; margin-top: 28px; }

    /* ── Empty state ──────────────────────────────────────── */
    .empty-state { text-align: center; padding: 56px 24px; color: #94a3b8; }
    .empty-icon  { font-size: 3rem; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 0.9rem; margin-bottom: 16px; }

    /* ── Responsive ───────────────────────────────────────── */
    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .sidebar-overlay.open { display: block; }
        .topbar { display: flex; }
        .main-content { margin-left: 0; padding: 80px 16px 24px; }
        .stats { grid-template-columns: repeat(2, 1fr); }
        .search-input { width: 150px; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
</script>
