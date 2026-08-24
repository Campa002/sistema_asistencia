<?php
// backend/config/database.php
/**
 * Configuración de conexión a la base de datos.
 *
 * Igual que config/email.php, cada valor se resuelve en este orden de
 * prioridad, para poder cambiar de entorno (dev/prod) sin tocar código:
 *   1) Variable de entorno del sistema operativo (DB_HOST, DB_PORT, DB_NAME,
 *      DB_USER, DB_PASS, DB_CHARSET). En XAMPP se pueden definir con SetEnv
 *      en httpd.conf/httpd-vhosts.conf, o como variables de entorno de
 *      Windows antes de iniciar Apache. En un hosting Linux se definen en
 *      el panel de control o en la configuración del vhost de Apache.
 *   2) config/database.local.php — archivo NO versionado (ver .gitignore),
 *      opcional, únicamente para desarrollo local, que puede definir
 *      cualquiera de esas constantes directamente.
 *   3) Si ninguna de las dos existe, se usan los valores por defecto de
 *      XAMPP local (root sin contraseña) — así el entorno local actual
 *      sigue funcionando exactamente igual que antes, sin cambios.
 */

function eest_env_or_default(string $envKey, string $default): string {
    $valor = getenv($envKey);
    return ($valor !== false && $valor !== '') ? $valor : $default;
}

$archivoLocal = __DIR__ . '/database.local.php';
if (file_exists($archivoLocal)) {
    require_once $archivoLocal; // puede definir DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASS/DB_CHARSET directamente
}

if (!defined('DB_HOST'))    define('DB_HOST', eest_env_or_default('DB_HOST', 'localhost'));
if (!defined('DB_PORT'))    define('DB_PORT', eest_env_or_default('DB_PORT', '3306'));
if (!defined('DB_NAME'))    define('DB_NAME', eest_env_or_default('DB_NAME', 'eest_asistencia'));
if (!defined('DB_USER'))    define('DB_USER', eest_env_or_default('DB_USER', 'root'));
if (!defined('DB_PASS'))    define('DB_PASS', eest_env_or_default('DB_PASS', ''));
if (!defined('DB_CHARSET')) define('DB_CHARSET', eest_env_or_default('DB_CHARSET', 'utf8mb4'));

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        return self::$instance;
    }
}
