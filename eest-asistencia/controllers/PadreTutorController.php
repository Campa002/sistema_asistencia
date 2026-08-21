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

    public static function portalData(int $padreTutorId): array {
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

        $alumnoId = (int) $vinculados[0]['alumno_id'];

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

        // Notificaciones (panel de campana)
        $stmtN = $db->prepare("SELECT * FROM notificaciones
                                WHERE activo = 1 AND tipo != 'comunicado'
                                  AND (FIND_IN_SET('padre_tutor', rol_destino) > 0 OR FIND_IN_SET('todos', rol_destino) > 0)
                                ORDER BY created_at DESC LIMIT 5");
        $stmtN->execute();
        $notificaciones = $stmtN->fetchAll(PDO::FETCH_ASSOC);

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
}
