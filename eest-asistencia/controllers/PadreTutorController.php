<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/Comunicado.php';
require_once __DIR__ . '/../models/Mensaje.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Datos reales para el portal (SPA) de Padre/Tutor. El alumno mostrado sale
 * SIEMPRE de `vinculaciones` con estado='aprobado' para el usuario logueado
 * — nunca de un ID fijo — así ningún tutor puede ver datos de un alumno que
 * no le corresponde.
 */
class PadreTutorController {

    /**
     * $alumnoIdSeleccionado: para tutores con más de un alumno vinculado
     * (reglas_del_sistema.md permite varias vinculaciones aprobadas por
     * tutor). Nunca se confía en el valor que llega de la URL: si no está
     * realmente entre los vinculados y aprobados de ESTE tutor, se ignora y
     * se usa el primero — así ningún tutor puede ver a un alumno ajeno
     * cambiando el parámetro a mano.
     */
    public static function portalData(int $padreTutorId, ?int $alumnoIdSeleccionado = null): array {
        $db = Database::getConnection();
        $usuario = Usuario::getById($padreTutorId);

        $stmtV = $db->prepare("SELECT v.alumno_id, v.relacion, u.nombre, u.apellido
                                FROM vinculaciones v JOIN usuarios u ON u.id = v.alumno_id
                                WHERE v.padre_tutor_id = ? AND v.estado = 'aprobado'
                                ORDER BY v.created_at ASC");
        $stmtV->execute([$padreTutorId]);
        $vinculados = $stmtV->fetchAll(PDO::FETCH_ASSOC);

        if (empty($vinculados)) {
            return [
                'usuario' => $usuario,
                'sinAlumnoVinculado' => true,
                'vinculados' => [],
                'notificaciones' => [],
                'msgs' => [],
                'fechaHoyLarga' => format_date_long_argentina(),
            ];
        }

        $idsVinculados = array_map(fn($v) => (int) $v['alumno_id'], $vinculados);
        $alumnoId = ($alumnoIdSeleccionado !== null && in_array($alumnoIdSeleccionado, $idsVinculados, true))
            ? $alumnoIdSeleccionado
            : $idsVinculados[0];

        $stmtA = $db->prepare("SELECT u.*, ac.curso_id, ac.estado AS matricula_estado
                                FROM usuarios u
                                LEFT JOIN alumno_cursos ac ON ac.alumno_id = u.id AND ac.ciclo_lectivo = ?
                                WHERE u.id = ?");
        $stmtA->execute([(int) date('Y'), $alumnoId]);
        $alumno = $stmtA->fetch(PDO::FETCH_ASSOC);

        $curso = null;
        if (!empty($alumno['curso_id'])) {
            require_once __DIR__ . '/../models/Curso.php';
            $curso = Curso::getById($alumno['curso_id']);
        }

        // Faltas totales reales (misma fuente que los checkpoints de regresión)
        $faltasTotales = Reporte::getFaltasTotalAlumno($alumnoId);

        // Resumen (presentes/ausentes/tarde/porcentaje) vía el mismo camino que Admin → Reportes
        $resumen = Reporte::getResumenPorAlumno(['alumno_id' => $alumnoId], 1, 1);
        $resumenAlumno = $resumen['alumnos'][0] ?? null;
        $porcentajeAsistencia = $resumenAlumno['porcentaje_asistencia'] ?? null;
        $inasistencias = $resumenAlumno['ausentes'] ?? 0;
        $llegadasTarde = $resumenAlumno['llegadas_tarde'] ?? 0;

        // Registros recientes (últimos 3, para el resumen)
        $registrosRecientes = self::historialAlumno($db, $alumnoId, 3);

        // Historial completo agrupado por mes (para la vista "Registro")
        $historialCompleto = self::historialAlumno($db, $alumnoId, 200);
        $porMes = [];
        foreach ($historialCompleto as $r) {
            $fecha = new DateTimeImmutable($r['fecha']);
            $clave = $fecha->format('Y') . '-' . (int) $fecha->format('n');
            $porMes[$clave][] = [
                'dia' => mb_strtoupper(mb_substr(self::diaSemanaEsp($fecha), 0, 3)),
                'num' => (int) $fecha->format('j'),
                'tipo' => 'Jornada registrada',
                'ingreso' => $r['hora_llegada'] ? date('h:i A', strtotime($r['hora_llegada'])) : null,
                'estado' => $r['estadoDia'],
            ];
        }
        // El mes más reciente con datos reales (para no arrancar en un mes vacío)
        $mesesConDatos = array_keys($porMes);
        rsort($mesesConDatos);
        $mesInicial = $mesesConDatos[0] ?? (date('Y') . '-' . (int) date('n'));
        [$anioInicial, $mesNumInicial] = array_map('intval', explode('-', $mesInicial));

        // Aviso importante: el comunicado activo más reciente dirigido a padres/todos
        $comunicados = Comunicado::getAll(['estado' => 'activo']);
        $avisoImportante = null;
        foreach ($comunicados as $c) {
            if (in_array($c['rol_destino'], ['todos', 'padre_tutor'], true)) { $avisoImportante = $c; break; }
        }

        // Mensajes reales
        $conversaciones = Mensaje::getAllConversations($padreTutorId);
        $msgsData = [];
        foreach ($conversaciones as $conv) {
            $otro = $conv['otroParticipante'];
            $mensajes = Mensaje::getMensajesByConversacionId($conv['id']);
            $msgsData[] = [
                'id' => (int) $conv['id'],
                'from' => $otro ? ($otro['apellido'] . ', ' . $otro['nombre'] . ' (' . ucfirst(str_replace('_', ' ', $otro['rol'])) . ')') : ($conv['titulo'] ?? 'Conversación'),
                'role' => $otro ? ucfirst(str_replace('_', ' ', $otro['rol'])) : '',
                'time' => $conv['ultimo_mensaje_fecha'] ? date('H:i', strtotime($conv['ultimo_mensaje_fecha'])) : '',
                'unread' => (int) $conv['no_leidos'] > 0,
                'preview' => $conv['ultimo_mensaje'] ?? '(sin mensajes)',
                'conversation' => array_map(fn($m) => [
                    'dir' => (int) $m['remitente_id'] === $padreTutorId ? 'out' : 'in',
                    'text' => $m['contenido'],
                    'time' => date('H:i', strtotime($m['created_at'])),
                    'hasFile' => false,
                ], $mensajes),
            ];
        }

        // Notificaciones (panel de campana) — con estado real de lectura
        // por-usuario (notificaciones_leidas), mismo criterio que ya usa
        // Directivo.
        $stmtN = $db->prepare("SELECT n.*, (nl.id IS NOT NULL) AS leida
                                FROM notificaciones n
                                LEFT JOIN notificaciones_leidas nl ON nl.notificacion_id = n.id AND nl.usuario_id = ?
                                WHERE n.activo = 1 AND n.tipo != 'comunicado'
                                  AND (FIND_IN_SET('padre_tutor', n.rol_destino) > 0 OR FIND_IN_SET('todos', n.rol_destino) > 0)
                                ORDER BY n.created_at DESC LIMIT 15");
        $stmtN->execute([$padreTutorId]);
        $notificaciones = $stmtN->fetchAll(PDO::FETCH_ASSOC);

        // Ausencias reales del alumno que todavía se pueden justificar: estado
        // 'ausente' y sin ninguna justificación pendiente/aprobada ya cargada
        // para ese detalle puntual (evita duplicados).
        $stmtAus = $db->prepare("SELECT da.id AS detalle_id, reg.fecha, m.nombre AS materia_nombre
                                  FROM detalles_asistencia da
                                  JOIN registros_asistencia reg ON reg.id = da.registro_id
                                  JOIN materias m ON m.id = reg.materia_id
                                  WHERE da.alumno_id = ? AND da.estado = 'ausente' AND reg.estado != 'anulada'
                                    AND NOT EXISTS (
                                      SELECT 1 FROM justificaciones j
                                      WHERE j.detalle_id = da.id AND j.estado IN ('pendiente', 'aprobada')
                                    )
                                  ORDER BY reg.fecha DESC LIMIT 30");
        $stmtAus->execute([$alumnoId]);
        $ausenciasJustificables = $stmtAus->fetchAll(PDO::FETCH_ASSOC);

        // Justificaciones ya enviadas por este tutor para este alumno (para
        // que vea el estado de lo que ya mandó, no solo poder mandar nuevas).
        $stmtJEnv = $db->prepare("SELECT j.*, reg.fecha, m.nombre AS materia_nombre
                                   FROM justificaciones j
                                   JOIN detalles_asistencia da ON da.id = j.detalle_id
                                   JOIN registros_asistencia reg ON reg.id = da.registro_id
                                   JOIN materias m ON m.id = reg.materia_id
                                   WHERE j.alumno_id = ? AND j.enviado_por = ?
                                   ORDER BY j.created_at DESC LIMIT 20");
        $stmtJEnv->execute([$alumnoId, $padreTutorId]);
        $justificacionesEnviadas = $stmtJEnv->fetchAll(PDO::FETCH_ASSOC);

        return [
            'usuario' => $usuario,
            'sinAlumnoVinculado' => false,
            'vinculados' => $vinculados,
            'alumno' => $alumno,
            'curso' => $curso,
            'faltasTotales' => $faltasTotales,
            'porcentajeAsistencia' => $porcentajeAsistencia,
            'inasistencias' => $inasistencias,
            'llegadasTarde' => $llegadasTarde,
            'registrosRecientes' => $registrosRecientes,
            'porMes' => $porMes,
            'anioInicial' => $anioInicial,
            'mesInicial' => $mesNumInicial,
            'avisoImportante' => $avisoImportante,
            'msgs' => $msgsData,
            'notificaciones' => $notificaciones,
            'ausenciasJustificables' => $ausenciasJustificables,
            'justificacionesEnviadas' => $justificacionesEnviadas,
            'fechaHoyLarga' => format_date_long_argentina(),
        ];
    }

    /**
     * Historial de un alumno agregado por día (un detalle puede tener varias
     * materias/bloques en el mismo día; se colapsa a un solo estado por día
     * con prioridad ausente > llegada_tarde > presente, solo para esta
     * presentación — no altera ni recalcula faltas_total real).
     */
    private static function historialAlumno(PDO $db, int $alumnoId, int $limite): array {
        $stmt = $db->prepare("SELECT reg.fecha, det.estado, det.hora_llegada
                               FROM detalles_asistencia det
                               JOIN registros_asistencia reg ON reg.id = det.registro_id
                               WHERE det.alumno_id = ? AND reg.estado != 'anulada'
                               ORDER BY reg.fecha DESC");
        $stmt->execute([$alumnoId]);
        $porDia = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $f = $r['fecha'];
            if (!isset($porDia[$f])) $porDia[$f] = ['fecha' => $f, 'estados' => [], 'hora_llegada' => null];
            $porDia[$f]['estados'][] = $r['estado'];
            if ($r['hora_llegada'] && !$porDia[$f]['hora_llegada']) $porDia[$f]['hora_llegada'] = $r['hora_llegada'];
        }
        $result = [];
        foreach ($porDia as $dia) {
            $estados = $dia['estados'];
            if (in_array('ausente', $estados, true)) $estadoDia = 'ausente';
            elseif (in_array('llegada_tarde', $estados, true)) $estadoDia = 'tarde';
            elseif (in_array('retirado_anticipado', $estados, true) || in_array('justificado', $estados, true)) $estadoDia = 'ausente';
            else $estadoDia = 'presente';
            $result[] = ['fecha' => $dia['fecha'], 'estadoDia' => $estadoDia, 'hora_llegada' => $dia['hora_llegada']];
        }
        usort($result, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));
        return array_slice($result, 0, $limite);
    }

    private static function diaSemanaEsp(DateTimeImmutable $fecha): string {
        $dias = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];
        return $dias[$fecha->format('l')] ?? $fecha->format('l');
    }

    public static function enviarMensajeAjax(): void {
        require_role('padre_tutor');
        header('Content-Type: application/json');
        $userId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido.']);
            return;
        }

        $conversacionId = (int) input('conversacion_id', 0);
        $contenido = trim((string) input('contenido', ''));

        if ($contenido === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El mensaje no puede estar vacío.']);
            return;
        }

        if (!$conversacionId || !Mensaje::getConversacionById($conversacionId, $userId)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Conversación no encontrada.']);
            return;
        }

        Mensaje::enviarMensaje($conversacionId, $userId, $contenido);
        require_once __DIR__ . '/../models/LogActividad.php';
        LogActividad::registrar($userId, 'ENVIAR_MENSAJE', "Envió un mensaje en la conversación #$conversacionId", 'mensajes', $conversacionId, null, null);

        echo json_encode(['ok' => true, 'conversacionId' => $conversacionId, 'hora' => date('H:i')]);
    }

    /**
     * Marca una notificación institucional como leída para ESTE tutor
     * (notificaciones_leidas es por-usuario) — mismo criterio que ya usa
     * Directivo. INSERT IGNORE por la UNIQUE(notificacion_id, usuario_id):
     * un segundo click no duplica nada.
     */
    public static function marcarNotificacionLeidaAjax(): void {
        require_role('padre_tutor');
        header('Content-Type: application/json');
        $userId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido.']);
            return;
        }

        $notifId = (int) input('notificacion_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM notificaciones WHERE id = ?");
        $stmt->execute([$notifId]);
        if (!$stmt->fetchColumn()) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Notificación no encontrada.']);
            return;
        }

        $db->prepare("INSERT IGNORE INTO notificaciones_leidas (notificacion_id, usuario_id) VALUES (?, ?)")
           ->execute([$notifId, $userId]);

        echo json_encode(['ok' => true]);
    }

    /**
     * Envía una justificación real (reglas_del_sistema.md §14: "El
     * Padre/Tutor puede enviar justificaciones"). Nunca confía en el
     * alumno_id/detalle_id del navegador: el detalle elegido tiene que
     * pertenecer a un alumno realmente vinculado y aprobado para ESTE tutor.
     */
    public static function enviarJustificacionAjax(): void {
        require_role('padre_tutor');
        header('Content-Type: application/json');
        $padreTutorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $detalleId = (int) input('detalle_id', 0);
        $tipo = input('tipo', 'personal');
        $motivo = trim((string) input('motivo', ''));
        if (!in_array($tipo, ['medica', 'personal', 'academica', 'otro'], true)) $tipo = 'otro';
        if ($motivo === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ingresá el motivo de la justificación.']);
            return;
        }

        $db = Database::getConnection();

        // El detalle elegido tiene que ser una ausencia real de un alumno
        // realmente vinculado y aprobado para este tutor.
        $stmt = $db->prepare("SELECT da.id, da.alumno_id, da.estado
                               FROM detalles_asistencia da
                               JOIN vinculaciones v ON v.alumno_id = da.alumno_id AND v.padre_tutor_id = ? AND v.estado = 'aprobado'
                               WHERE da.id = ?");
        $stmt->execute([$padreTutorId, $detalleId]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detalle) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Esa asistencia no corresponde a un alumno vinculado a tu cuenta.']);
            return;
        }
        if ($detalle['estado'] !== 'ausente') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Solo se pueden justificar ausencias.']);
            return;
        }

        $stmtDup = $db->prepare("SELECT id FROM justificaciones WHERE detalle_id = ? AND estado IN ('pendiente', 'aprobada') LIMIT 1");
        $stmtDup->execute([$detalleId]);
        if ($stmtDup->fetchColumn()) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Ya existe una justificación activa para esa ausencia.']);
            return;
        }

        $db->prepare("INSERT INTO justificaciones (alumno_id, detalle_id, enviado_por, tipo, motivo, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')")
           ->execute([(int) $detalle['alumno_id'], $detalleId, $padreTutorId, $tipo, $motivo]);
        $nuevoId = (int) $db->lastInsertId();

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($padreTutorId, 'ENVIAR_JUSTIFICACION', "Envió la justificación #$nuevoId", 'justificaciones', $nuevoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true, 'justificacionId' => $nuevoId]);
    }
}
