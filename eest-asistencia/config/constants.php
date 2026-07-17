<?php
// Definir la URL base del proyecto de forma dinámica
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_name = dirname($_SERVER['SCRIPT_NAME']);
// Asegurar que no queden barras duplicadas
$base_path = rtrim($script_name, '/');

define('BASE_URL', $protocol . '://' . $host . $base_path . '/');
define('APP_NAME', 'Sistema de Gestión de Asistencia');
define('APP_VERSION', '3.0.0');
