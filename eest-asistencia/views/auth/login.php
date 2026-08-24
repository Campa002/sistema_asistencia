<?php
$css_path = url('assets/css/styles.css');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — EEST N°1</title>
    <link rel="stylesheet" href="<?= $css_path ?>">

    <!-- PWA -->
    <link rel="manifest" href="<?= url('manifest.webmanifest') ?>">
    <meta name="theme-color" content="#071D3A">
    <link rel="icon" type="image/png" href="<?= url('assets/img/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/img/icons/icon-192.png') ?>">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-top">
            <div class="auth-logo">
                <img id="nav-dashboard" class="d" src="../public/assets/img/logo.webp" alt="">
            </div>
            <h1>EEST N°1</h1>
            <h2>EDUARDO ADER</h2>
        </div>

        <div class="auth-card">
            <div class="auth-title">Iniciar Sesión</div>
            <div class="auth-subtitle">Sistema de Gestión de Asistencia</div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <?php echo e($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('index.php?page=login') ?>">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label for="email">Usuario o Email</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="email" id="email" name="email" placeholder="nombre.apellido.t1vl" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label for="password" style="margin-bottom:0">Contraseña</label>
                        <a href="#" class="forgot-link">¿Olvidó su clave?</a>
                    </div>
                    <div class="input-wrapper" style="margin-top:8px">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Ingresar al Sistema
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <div class="auth-links">
                <p>¿No tiene una cuenta administrativa?</p>
                <a href="<?= url('index.php?page=register_alumno') ?>">Registrarse →</a>
            </div>
        </div>

        <div class="auth-footer">
            <div class="footer-card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <div class="text"><div class="title">Centro de Ayuda</div></div>
            </div>
            <div class="footer-card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <div class="text"><div class="title">Acceso Seguro</div></div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('<?= url('service-worker.js') ?>').catch(function () {});
    });
  }
</script>
</body>
</html>