<?php
?>

<div style="
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #1A2B4C 0%, #071D3A 100%);
    padding: 24px;
">
    <div style="
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        max-width: 500px;
        width: 100%;
        padding: 48px 40px;
        text-align: center;
    ">
        <div style="
            width: 80px;
            height: 80px;
            background: #FFF3F3;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 24px;
        ">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DC3545" stroke-width="2">
                <path d="M12 9v4m0 4h.01"/>
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            </svg>
        </div>

        <h1 style="
            font-size: 28px;
            color: #071D3A;
            margin-bottom: 8px;
            font-weight: 800;
        ">
            Acceso No Autorizado
        </h1>

        <p style="
            color: #6C757D;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        ">
            No tienes permisos suficientes para acceder a esta sección del sistema.
        </p>

        <div style="
            display: flex;
            gap: 12px;
            flex-direction: column;
        ">
            <a href="<?= dashboard_url_by_role() ?>" style="
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 14px 24px;
                background: #071D3A;
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 700;
                text-decoration: none;
                transition: transform 0.2s, box-shadow 0.2s;
                cursor: pointer;
            ">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Volver a mi panel
            </a>

            <form method="POST" action="<?= url('index.php') ?>" style="margin: 0;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    padding: 14px 24px;
                    background: transparent;
                    color: #DC3545;
                    border: 2px solid #DC3545;
                    border-radius: 10px;
                    font-size: 16px;
                    font-weight: 700;
                    cursor: pointer;
                    width: 100%;
                    transition: background 0.2s, color 0.2s;
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
