<?php
/**
 * Configuración del servicio de email transaccional vía API externa (Resend).
 * NO se usa servidor SMTP propio. NO hay ninguna API key hardcodeada acá.
 *
 * La API key se lee, en este orden de prioridad:
 *   1) Variable de entorno del sistema operativo RESEND_API_KEY
 *      (recomendado; en XAMPP se puede definir con SetEnv en httpd.conf,
 *      o con variable de entorno de Windows antes de iniciar Apache).
 *   2) config/email.local.php — archivo NO versionado (ver .gitignore),
 *      copiado a partir de config/email.example.php, que define
 *      RESEND_API_KEY directamente, únicamente para desarrollo local.
 *
 * Si ninguna de las dos existe, RESEND_API_KEY queda vacía y EmailService
 * simplemente no envía nada (falla de forma controlada, sin romper el resto
 * del sistema — ver models/EmailService.php).
 */

if (!defined('EMAIL_API_PROVIDER')) {
    define('EMAIL_API_PROVIDER', 'resend');
}

if (!defined('EMAIL_API_ENDPOINT')) {
    define('EMAIL_API_ENDPOINT', 'https://api.resend.com/emails');
}

if (!defined('RESEND_API_KEY')) {
    $apiKey = getenv('RESEND_API_KEY');

    if ($apiKey !== false && $apiKey !== '') {
        define('RESEND_API_KEY', $apiKey);
    } else {
        $archivoLocal = __DIR__ . '/email.local.php';
        if (file_exists($archivoLocal)) {
            require_once $archivoLocal; // debe definir RESEND_API_KEY directamente
        }
        if (!defined('RESEND_API_KEY')) {
            define('RESEND_API_KEY', '');
        }
    }
}
