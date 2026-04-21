<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px 36px;
        width: 100%;
        max-width: 380px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }
    .auth-logo { text-align: center; margin-bottom: 28px; }
    .auth-logo .icon { font-size: 2.8rem; display: block; margin-bottom: 8px; }
    .auth-logo h1 { font-size: 1.4rem; font-weight: 700; color: #1e3a5f; }
    .auth-logo p  { font-size: 0.85rem; color: #94a3b8; margin-top: 4px; }
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block; font-size: 0.82rem; font-weight: 600;
        color: #475569; margin-bottom: 6px;
        text-transform: uppercase; letter-spacing: 0.4px;
    }
    .form-group input {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #e2e8f0; border-radius: 9px;
        font-size: 0.95rem; color: #1e293b; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #f8fafc;
    }
    .form-group input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        background: #fff;
    }
    .form-hint { font-size: 0.78rem; color: #94a3b8; margin-top: 4px; }
    .btn-submit {
        width: 100%; padding: 12px;
        background: #1e3a5f; color: #fff;
        border: none; border-radius: 9px;
        font-size: 0.95rem; font-weight: 600;
        cursor: pointer; margin-top: 8px;
        transition: background 0.2s, transform 0.1s;
    }
    .btn-submit:hover { background: #2d6a9f; transform: translateY(-1px); }
    .btn-submit:active { transform: translateY(0); }
    .auth-footer { text-align: center; margin-top: 20px; font-size: 0.875rem; color: #64748b; }
    .auth-footer a { color: #3b82f6; text-decoration: none; font-weight: 600; }
    .auth-footer a:hover { text-decoration: underline; }
    .alert { border-radius: 8px; padding: 10px 14px; font-size: 0.875rem; margin-bottom: 16px; }
    .alert-error   { background: #fee2e2; color: #dc2626; border-left: 3px solid #dc2626; }
    .alert-success { background: #dcfce7; color: #16a34a; border-left: 3px solid #22c55e; }
</style>
