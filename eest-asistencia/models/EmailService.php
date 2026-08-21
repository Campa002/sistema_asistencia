<?php
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/ConfiguracionSistema.php';

/**
 * Envío de emails institucionales vía API de email transaccional (Resend),
 * sin servidor SMTP propio. No requiere ninguna librería nueva por Composer:
 * la API de Resend es un simple POST JSON, implementado acá con cURL nativo
 * de PHP (ya usado en el resto del proyecto).
 */
class EmailService {
    /**
     * Valor de plantilla de config/email.example.php. Si RESEND_API_KEY
     * todavía tiene este texto (el archivo email.local.php fue creado pero
     * nadie reemplazó el placeholder), se trata igual que "no configurada":
     * evita gastar un intento de red real contra Resend con una key que se
     * sabe inválida.
     */
    private const PLACEHOLDER = 'REEMPLAZAR_CON_TU_API_KEY_DE_RESEND';

    /**
     * @return array{exito: bool, error?: string, http_code?: int, id?: string|null}
     */
    public static function enviar(string $destinatarioEmail, string $destinatarioNombre, string $asunto, string $contenidoHtml): array {
        if (empty(RESEND_API_KEY) || RESEND_API_KEY === self::PLACEHOLDER) {
            self::log("RESEND_API_KEY no configurada (o todavía es el placeholder de la plantilla) — no se envió el email a $destinatarioEmail. Ver config/email.example.php.");
            return ['exito' => false, 'error' => 'RESEND_API_KEY no configurada'];
        }

        if (!filter_var($destinatarioEmail, FILTER_VALIDATE_EMAIL)) {
            return ['exito' => false, 'error' => 'Email de destinatario inválido'];
        }

        $remitenteEmail = ConfiguracionSistema::getValor('email_from') ?: 'no-reply@eest.edu.ar';
        $destinatarioFormateado = $destinatarioNombre !== ''
            ? "$destinatarioNombre <$destinatarioEmail>"
            : $destinatarioEmail;

        // Formato oficial de la API de Resend (POST /emails):
        // https://resend.com/docs/api-reference/emails/send-email
        $payload = [
            'from' => 'Sistema de Asistencia EEST <' . $remitenteEmail . '>',
            'to' => [$destinatarioFormateado],
            'subject' => $asunto,
            'html' => $contenidoHtml
        ];

        $ch = curl_init(EMAIL_API_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . RESEND_API_KEY,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            self::log("Error de conexión enviando email a $destinatarioEmail: $curlError");
            return ['exito' => false, 'error' => "Error de conexión: $curlError"];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            // $response en éxito es {"id": "..."} según la API de Resend.
            $data = json_decode($response, true);
            return ['exito' => true, 'http_code' => $httpCode, 'id' => $data['id'] ?? null];
        }

        // $response es el cuerpo de la respuesta de Resend (mensaje de error
        // en JSON) — nunca incluye el header Authorization ni la API key.
        self::log("La API de email respondió HTTP $httpCode para $destinatarioEmail: $response");
        return ['exito' => false, 'error' => "La API de email respondió HTTP $httpCode", 'http_code' => $httpCode];
    }

    private static function log(string $mensaje): void {
        error_log('[EmailService] ' . $mensaje);
    }
}
