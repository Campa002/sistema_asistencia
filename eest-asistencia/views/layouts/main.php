<?php
$page = $GLOBALS['page'] ?? '';
$isAuthPage = in_array($page, ['home', 'login', 'register_alumno', 'primer_acceso']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Asistencia - EEST N°1</title>
    <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">

    <!-- PWA -->
    <link rel="manifest" href="<?= url('manifest.webmanifest') ?>">
    <meta name="theme-color" content="#071D3A">
    <link rel="icon" type="image/png" href="<?= url('assets/img/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/img/icons/icon-192.png') ?>">
</head>
<body>
    <?= $content ?? '' ?>

    <script>
      // Registro del Service Worker. No cachea datos privados de ningún
      // usuario (ver public/service-worker.js) — solo habilita instalación
      // y cache de assets estáticos (CSS/JS/íconos).
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
          navigator.serviceWorker.register('<?= url('service-worker.js') ?>').catch(function () {
            // Silencioso: si falla el registro (ej. navegador sin soporte
            // o entorno sin HTTPS/localhost), el sitio sigue funcionando
            // normalmente sin PWA.
          });
        });
      }
    </script>
</body>
</html>
