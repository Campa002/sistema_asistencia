<?php
/**
 * PLANTILLA — copiar este archivo a "email.local.php" (mismo directorio) y
 * completar con la API key real de Resend (https://resend.com/api-keys).
 *
 * "email.local.php" está en .gitignore: nunca se sube al repositorio.
 * En producción, preferir la variable de entorno RESEND_API_KEY en vez de
 * este archivo (ver config/email.php).
 *
 * Importante: el remitente que usa el sistema es el configurado en
 * Parámetros del Sistema (clave "email_from", ver ConfiguracionSistema).
 * Ese dominio de remitente debe estar verificado en tu cuenta de Resend
 * (https://resend.com/domains) o la API rechazará el envío. Esta plantilla
 * no cambia ni inventa ningún email remitente.
 */

define('RESEND_API_KEY', 'REEMPLAZAR_CON_TU_API_KEY_DE_RESEND');
