<?php
require_once __DIR__ . '/../../config/database.php';
$db = Database::getConnection();

$stmt = $db->query("
    SELECT c.id, c.anio, c.division, c.turno, e.nombre as especialidad
    FROM cursos c
    JOIN especialidades e ON c.especialidad_id = e.id
    WHERE c.estado = 'activo'
    ORDER BY c.anio, c.division
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$css_path = url('assets/css/styles.css');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — EEST N°1</title>
    <link rel="stylesheet" href="<?= $css_path ?>">
    <style>
        /* Fondo navy completo para registro */
        .auth-wrapper {
            background: #071D3A;
        }
        .auth-card-full {
            text-align: left;
        }
    </style>

    <!-- PWA -->
    <link rel="manifest" href="<?= url('manifest.webmanifest') ?>">
    <meta name="theme-color" content="#071D3A">
    <link rel="icon" type="image/png" href="<?= url('assets/img/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/img/icons/icon-192.png') ?>">
</head>
<body>
<div class="auth-wrapper" style="padding: 40px 20px;">
    <div class="auth-container auth-container-register">
        <div class="auth-card auth-card-full">
            <div style="text-align:center; margin-bottom:28px;">
                <div class="auth-logo-register">
                    <img id="nav-dashboard" class="d" src="../public/assets/img/logo.webp" alt="">
                </div>
                <div class="auth-title">Registro de Usuario</div>
                <div class="auth-subtitle">Complete sus datos para acceder al sistema institucional.</div>
            </div>

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

            <form method="POST" action="<?= url('index.php?page=register_alumno') ?>">
                <input type="hidden" name="action" value="register_alumno">

                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input type="email" id="email" name="email" placeholder="usuario@gmail.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre_padre">Nombre Completo Padre/Tutor</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        <input type="text" id="nombre_padre" name="nombre_padre" placeholder="Ej. Juan Pérez">
                    </div>
                </div>

                <div class="form-group">
                    <label for="curso_id">Curso y División</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        </svg>
                        <select id="curso_id" name="curso_id" required>
                            <option value="">Curso</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= $curso['id'] ?>">
                                    <?= "{$curso['anio']}° {$curso['division']}ª - {$curso['especialidad']} ({$curso['turno']})" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    REGISTRARSE →
                </button>
            </form>

            <div class="auth-links">
                <p>¿Ya tienes una cuenta? <a href="<?= url('index.php?page=login') ?>">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
</div>
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('<?= url('service-worker.js') ?>').catch(function () {});
    });
  }
</script>
</body>
</html>