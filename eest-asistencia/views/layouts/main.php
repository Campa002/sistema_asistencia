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
</head>
<body>
    <?= $content ?? '' ?>
</body>
</html>
