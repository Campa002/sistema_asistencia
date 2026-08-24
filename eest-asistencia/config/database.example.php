<?php
/**
 * PLANTILLA — copiar este archivo a "database.local.php" (mismo directorio)
 * SOLO si necesitás credenciales de base de datos distintas a las de XAMPP
 * local sin usar variables de entorno.
 *
 * "database.local.php" está en .gitignore: nunca se sube al repositorio.
 * En un servidor real, preferir definir las variables de entorno DB_HOST /
 * DB_PORT / DB_NAME / DB_USER / DB_PASS / DB_CHARSET en el vhost de Apache
 * (ver config/database.php) en vez de este archivo.
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'eest_asistencia');
define('DB_USER', 'REEMPLAZAR_CON_USUARIO_REAL');
define('DB_PASS', 'REEMPLAZAR_CON_CONTRASEÑA_REAL');
define('DB_CHARSET', 'utf8mb4');
