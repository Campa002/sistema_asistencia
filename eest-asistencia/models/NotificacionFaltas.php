<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Reporte.php';
require_once __DIR__ . '/ConfiguracionSistema.php';
require_once __DIR__ . '/EmailService.php';

/**
 * Alerta institucional cuando un alumno alcanza el máximo de faltas
 * (configuracion_sistema.maximo_faltas). No usa ninguna tabla nueva: la
 * notificación "interna" se guarda en `notificaciones` (la misma tabla que
 * ya usa Comunicados Globales, con tipo='alerta'), y el título se usa como
 * clave de deduplicación para no repetir el aviso ni el email.
 */
class NotificacionFaltas {
    public static function verificarYNotificar(int $alumnoId, ?int $creadoPor = null): void {
        $umbral = (float) ConfiguracionSistema::getValor('maximo_faltas', 28);
        if ($umbral <= 0) {
            return;
        }

        $totalFaltas = Reporte::getFaltasTotalAlumno($alumnoId);
        if ($totalFaltas < $umbral) {
            return;
        }

        $db = Database::getConnection();
        $stmtAlumno = $db->prepare("SELECT id, nombre, apellido FROM usuarios WHERE id = ? AND rol = 'alumno' LIMIT 1");
        $stmtAlumno->execute([$alumnoId]);
        $alumno = $stmtAlumno->fetch(PDO::FETCH_ASSOC);
        if (!$alumno) {
            return;
        }

        $titulo = 'Límite de faltas alcanzado — alumno #' . $alumnoId;

        // Deduplicación: si ya se generó esta alerta antes para este alumno,
        // no se repite ni el aviso interno ni el email.
        $stmtExiste = $db->prepare("SELECT id FROM notificaciones WHERE tipo = 'alerta' AND titulo = ? LIMIT 1");
        $stmtExiste->execute([$titulo]);
        if ($stmtExiste->fetch()) {
            return;
        }

        $mensaje = sprintf(
            'El alumno %s, %s (ID %d) alcanzó el límite institucional de %s faltas. Total acumulado: %s.',
            $alumno['apellido'],
            $alumno['nombre'],
            $alumnoId,
            self::formatearNumero($umbral),
            self::formatearNumero($totalFaltas)
        );

        // Necesita un usuario válido en creado_por (columna NOT NULL). Si no
        // se pasó uno (ej. contexto sin admin identificado), se usa el
        // primer administrador activo como responsable del sistema.
        $creadoPorFinal = $creadoPor ?: self::obtenerAdminPorDefecto($db);
        if (!$creadoPorFinal) {
            return; // no hay ningún admin en el sistema, no se puede insertar (NOT NULL)
        }

        $stmtInsert = $db->prepare("INSERT INTO notificaciones
                (institucion_id, creado_por, titulo, contenido, tipo, prioridad, rol_destino, activo)
                VALUES (1, ?, ?, ?, 'alerta', 'urgente', 'admin,directivo', 1)");
        $stmtInsert->execute([$creadoPorFinal, $titulo, $mensaje]);

        self::enviarEmailAAdministracion($titulo, $mensaje);
    }

    private static function obtenerAdminPorDefecto(PDO $db): ?int {
        $stmt = $db->query("SELECT id FROM usuarios WHERE rol = 'admin' AND estado = 'activo' ORDER BY id LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    /**
     * Envía el email a todos los administradores y directivos activos con
     * email cargado. Es "best effort": si EmailService falla (sin API key
     * configurada, sin conexión, etc.) no interrumpe nada — la alerta
     * interna ya quedó guardada de todos modos.
     */
    private static function enviarEmailAAdministracion(string $asunto, string $mensaje): void {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT nombre, apellido, email FROM usuarios WHERE rol IN ('admin','directivo') AND estado = 'activo'");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
            if (empty($u['email'])) {
                continue;
            }
            EmailService::enviar(
                $u['email'],
                $u['apellido'] . ', ' . $u['nombre'],
                $asunto,
                '<p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            );
        }
    }

    private static function formatearNumero(float $n): string {
        return $n == floor($n) ? (string) (int) $n : number_format($n, 2);
    }
}
