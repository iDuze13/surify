<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error del servidor | Surify</title>
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <style>
        :root { --bg:#f1f5f9; --card:#ffffff; --text:#0f172a; --muted:#64748b; --brand:#28628f; --brand-dark:#1a4669; --accent:#e11d48; --border:#e2e8f0; }
        .dark { --bg:#0f172a; --card:#1e293b; --text:#f1f5f9; --muted:#94a3b8; --brand:#5ba3d6; --brand-dark:#7bb8e3; --accent:#fb7185; --border:#334155; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px;
               background: var(--bg); color: var(--text); font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .card { width: 100%; max-width: 520px; text-align: center; background: var(--card); border: 1px solid var(--border);
                border-radius: 24px; padding: 48px 32px; box-shadow: 0 10px 30px rgba(15,23,42,.08); }
        .brand { font-weight: 900; font-size: 22px; letter-spacing: -.5px; color: var(--brand); margin-bottom: 24px; }
        .code { font-size: 96px; font-weight: 900; line-height: 1; letter-spacing: -4px; color: var(--accent); }
        h1 { font-size: 24px; font-weight: 800; margin: 16px 0 8px; }
        p { color: var(--muted); font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        a, button { font: inherit; font-weight: 700; font-size: 14px; padding: 12px 22px; border-radius: 12px; cursor: pointer; text-decoration: none; }
        .primary { background: var(--brand); color: #fff; border: 1px solid var(--brand); }
        .primary:hover { background: var(--brand-dark); }
        .ghost { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .ghost:hover { background: var(--bg); }
        a:focus-visible, button:focus-visible { outline: 3px solid var(--brand); outline-offset: 2px; }
        @media (max-width: 420px) { .code { font-size: 72px; } .card { padding: 36px 20px; } }
    </style>
</head>
<body>
    <main class="card" role="main">
        <div class="brand">Surify</div>
        <div class="code" aria-hidden="true">500</div>
        <h1>Algo salió mal de nuestro lado</h1>
        <p>Tuvimos un problema inesperado y ya quedó registrado para que lo revisemos. No hiciste nada mal: probá recargar la página en unos instantes o volvé al inicio.</p>
        <div class="actions">
            <a class="primary" href="/">Volver al inicio</a>
            <button class="ghost" type="button" onclick="location.reload()">Reintentar</button>
        </div>
    </main>
</body>
</html>