<?php
// Página mostrada a cualquier usuario no-admin cuando `mantenimiento_modo`
// está activo (ver public/index.php, sección 4b). El admin nunca ve esta
// pantalla. Mismo estilo visual que views/errors/unauthorized.php.
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
            background: #FFF8E5;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 24px;
        ">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C88A00" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>

        <h1 style="
            font-size: 28px;
            color: #071D3A;
            margin-bottom: 8px;
            font-weight: 800;
        ">
            Sistema en Mantenimiento
        </h1>

        <p style="
            color: #6C757D;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        ">
            El sistema se encuentra temporalmente en mantenimiento. Estamos realizando tareas de administración y volverá a estar disponible en breve. Disculpá las molestias.
        </p>

        <div style="
            display: flex;
            gap: 12px;
            flex-direction: column;
        ">
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
            <form method="POST" action="<?= url('index.php') ?>" style="margin: 0;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" style="
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
                    cursor: pointer;
                    width: 100%;
                    transition: transform 0.2s, box-shadow 0.2s;
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
            <?php else: ?>
            <a href="<?= url('index.php?page=login') ?>" style="
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
            ">
                Ir a iniciar sesión
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
