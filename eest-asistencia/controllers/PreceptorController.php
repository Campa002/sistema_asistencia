<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Mensaje.php';
require_once __DIR__ . '/../models/Comunicado.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Datos reales para el portal (SPA) de Preceptor. Reutiliza los modelos
 * existentes (Curso, Asistencia, Mensaje, Comunicado) — no duplica lógica de
 * cálculo de faltas ni toca ninguna de las funciones protegidas de
 * Asistencia.php. La fuente de "cursos a cargo" es `preceptor_cursos`
 * (relación real preceptor↔curso), no el preceptor_id de
 * `asignaciones_materias` (que para 12 cursos es un placeholder documentado).
 */
class PreceptorController {

    public static function portalData(int $preceptorId): array {
        $db = Database::getConnection();
        $cicloActual = (int) date('Y');
        $hoy = self::hoyArgentina();

        $stmt = $db->prepare("SELECT pc.curso_id, pc.es_titular, c.anio, c.division, c.turno, c.aula,
                                      esp.nombre AS especialidad_nombre
                               FROM preceptor_cursos pc
                               JOIN cursos c ON c.id = pc.curso_id
                               LEFT JOIN especialidades esp ON esp.id = c.especialidad_id
                               WHERE pc.preceptor_id = ? AND pc.activo = 1 AND pc.ciclo_lectivo = ? AND c.estado = 'activo'
                               ORDER BY c.anio, c.division");
        $stmt->execute([$preceptorId, $cicloActual]);
        $cursosCargo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cursosData = [];
        $alumnosData = [];
        $cursoIds = [];
        foreach ($cursosCargo as $c) {
            $cursoId = (int) $c['curso_id'];
            $cursoIds[] = $cursoId;
            $alumnosCurso = Curso::getAlumnosByCursoId($cursoId, $cicloActual);
            $nombreCurso = $c['anio'] . '° ' . $c['division'] . '°';

            // Estado del día: ¿hay algún registro cerrado/modificado hoy para este curso?
            $stmtReg = $db->prepare("SELECT estado, updated_at FROM registros_asistencia
                                      WHERE curso_id = ? AND fecha = ?
                                      ORDER BY updated_at DESC LIMIT 1");
            $stmtReg->execute([$cursoId, $hoy]);
            $regHoy = $stmtReg->fetch(PDO::FETCH_ASSOC);
            $completa = $regHoy && in_array($regHoy['estado'], ['cerrada', 'modificada'], true);

            $cursosData[] = [
                'id' => $cursoId,
                'name' => $nombreCurso,
                'spec' => $c['especialidad_nombre'] ?? 'General',
                'turno' => 'Turno ' . ucfirst($c['turno']),
                'turnoRaw' => $c['turno'],
                'alumnos' => count($alumnosCurso),
                'status' => $completa ? 'completa' : 'pendiente',
                'updated' => $completa && $regHoy['updated_at'] ? date('H:i', strtotime($regHoy['updated_at'])) : null,
                'esTitular' => (bool) $c['es_titular'],
            ];

            foreach ($alumnosCurso as $al) {
                $alumnosData[] = [
                    'id' => (int) $al['id'],
                    'last' => mb_strtoupper($al['apellido']),
                    'first' => $al['nombre'],
                    'dni' => $al['dni'] ?? '—',
                    'email' => $al['email'],
                    'course' => $nombreCurso,
                    'courseId' => $cursoId,
                ];
            }
        }
        usort($alumnosData, fn($a, $b) => $a['last'] <=> $b['last']);

        // Asistencia de hoy (%) sobre todos los cursos a cargo
        $asistenciaHoyPct = null;
        if (!empty($cursoIds)) {
            $in = implode(',', array_fill(0, count($cursoIds), '?'));
            $stmtHoy = $db->prepare("SELECT det.estado, COUNT(*) n
                                      FROM detalles_asistencia det
                                      JOIN registros_asistencia reg ON reg.id = det.registro_id
                                      WHERE reg.curso_id IN ($in) AND reg.fecha = ? AND reg.estado != 'anulada'
                                      GROUP BY det.estado");
            $stmtHoy->execute([...$cursoIds, $hoy]);
            $porEstado = [];
            $total = 0;
            foreach ($stmtHoy->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $porEstado[$r['estado']] = (int) $r['n'];
                $total += (int) $r['n'];
            }
            if ($total > 0) {
                $presentes = ($porEstado['presente'] ?? 0) + ($porEstado['llegada_tarde'] ?? 0) + ($porEstado['ausente_con_presente'] ?? 0);
                $asistenciaHoyPct = round(($presentes / $total) * 100, 1);
            }
        }

        // Historial: registros reales tomados por este preceptor
        $historial = [];
        $totalFinalizadas = 0;
        $totalModificadas = 0;
        if (!empty($cursoIds)) {
            $resAsist = Asistencia::getAll(['preceptor_id' => $preceptorId], 1, 30);
            foreach ($resAsist['registros'] as $reg) {
                $detalles = Asistencia::getDetallesByRegistroId($reg['id']);
                $presentes = 0;
                $ausentes = 0;
                foreach ($detalles as $d) {
                    if ($d['estado'] === 'presente' || $d['estado'] === 'llegada_tarde' || $d['estado'] === 'ausente_con_presente') {
                        $presentes++;
                    } elseif ($d['estado'] === 'ausente') {
                        $ausentes++;
                    }
                }
                $esFinalizada = in_array($reg['estado'], ['cerrada', 'modificada'], true);
                if ($esFinalizada) $totalFinalizadas++;
                if ($reg['estado'] === 'modificada') $totalModificadas++;

                $horario = Asistencia::getHorarioMostrable($reg['curso_turno'], $reg['bloque_horario'], $reg['hora_inicio'], $reg['hora_fin']);
                $historial[] = [
                    'registroId' => (int) $reg['id'],
                    'cursoId' => (int) $reg['curso_id'],
                    'course' => $reg['anio'] . '° ' . $reg['division'] . '° – ' . $reg['materia_nombre'],
                    'date' => format_date_short_argentina($reg['fecha']),
                    'turno' => ucfirst($reg['curso_turno']) . ' (' . $horario . ')',
                    'presentes' => $presentes,
                    'ausentes' => $ausentes,
                    'status' => $esFinalizada ? 'completa' : 'abierta',
                    'modified' => $reg['estado'] === 'modificada',
                ];
            }
        }

        // Materias/horario reales por curso a cargo (asignaciones_materias)
        require_once __DIR__ . '/../models/Materia.php';
        $horariosPorCurso = [];
        foreach ($cursoIds as $cid) {
            $horariosPorCurso[$cid] = Materia::getAsignaciones($cid);
        }

        // Mensajes: conversaciones reales del preceptor
        $conversaciones = Mensaje::getAllConversations($preceptorId);
        $msgsData = [];
        foreach ($conversaciones as $conv) {
            $otro = $conv['otroParticipante'];
            $mensajes = Mensaje::getMensajesByConversacionId($conv['id']);
            $msgsData[] = [
                'id' => (int) $conv['id'],
                'from' => $otro ? ($otro['apellido'] . ', ' . $otro['nombre'] . ' (' . ucfirst(str_replace('_', ' ', $otro['rol'])) . ')') : ($conv['titulo'] ?? 'Conversación'),
                'alumno' => $conv['titulo'] ?? '',
                'preview' => $conv['ultimo_mensaje'] ?? '(sin mensajes)',
                'time' => $conv['ultimo_mensaje_fecha'] ? date('H:i', strtotime($conv['ultimo_mensaje_fecha'])) : '',
                'unread' => (int) $conv['no_leidos'],
                'conversation' => array_map(fn($m) => [
                    'dir' => (int) $m['remitente_id'] === $preceptorId ? 'out' : 'in',
                    'text' => $m['contenido'],
                    'time' => date('H:i', strtotime($m['created_at'])),
                ], $mensajes),
            ];
        }

        // Avisos de directivos/admin (comunicados reales dirigidos a "todos" o "preceptor")
        $comunicados = Comunicado::getAll(['estado' => 'activo']);
        $avisos = array_values(array_filter($comunicados, fn($c) => in_array($c['rol_destino'], ['todos', 'preceptor'], true)));

        // Datos del propio preceptor
        $stmtU = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmtU->execute([$preceptorId]);
        $usuario = $stmtU->fetch(PDO::FETCH_ASSOC);

        $turnos = array_unique(array_column($cursosData, 'turno'));
        $turnoResumen = count($turnos) === 1 ? $turnos[0] : (count($turnos) > 1 ? 'Múltiples turnos' : '');

        return [
            'usuario' => $usuario,
            'turnoResumen' => $turnoResumen,
            'cursos' => $cursosData,
            'alumnos' => $alumnosData,
            'horariosPorCurso' => $horariosPorCurso,
            'historial' => $historial,
            'totalFinalizadas' => $totalFinalizadas,
            'totalModificadas' => $totalModificadas,
            'asistenciaHoyPct' => $asistenciaHoyPct,
            'msgs' => $msgsData,
            'avisos' => $avisos,
            'fechaHoyLarga' => format_date_long_argentina($hoy),
        ];
    }

    private static function hoyArgentina(): string {
        return (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('Y-m-d');
    }

    /**
     * Envío real de un mensaje desde el portal de Preceptor (AJAX). Reutiliza
     * exactamente Mensaje::enviarMensaje()/crearConversacion(), igual que
     * AdminMensajesController, con la misma verificación de CSRF.
     */
    public static function enviarMensajeAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $userId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido.']);
            return;
        }

        $conversacionId = (int) input('conversacion_id', 0);
        $destinatarioId = (int) input('destinatario_id', 0);
        $contenido = trim((string) input('contenido', ''));

        if ($contenido === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El mensaje no puede estar vacío.']);
            return;
        }

        if (!$conversacionId && $destinatarioId) {
            // Solo se permite iniciar conversación con un padre/tutor de un
            // alumno propio, o con un directivo/admin — nunca con un alumno
            // ajeno arbitrario.
            $permitido = self::destinatarioPermitido($userId, $destinatarioId);
            if (!$permitido) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Destinatario no permitido.']);
                return;
            }
            $conversacionId = Mensaje::crearConversacion([$userId, $destinatarioId]);
        }

        if (!$conversacionId || !Mensaje::getConversacionById($conversacionId, $userId)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Conversación no encontrada.']);
            return;
        }

        Mensaje::enviarMensaje($conversacionId, $userId, $contenido);
        require_once __DIR__ . '/../models/LogActividad.php';
        LogActividad::registrar($userId, 'ENVIAR_MENSAJE', "Envió un mensaje en la conversación #$conversacionId", 'mensajes', $conversacionId, null, null);

        echo json_encode(['ok' => true, 'conversacionId' => (int) $conversacionId, 'hora' => date('H:i')]);
    }

    /**
     * Guardado real de una toma de asistencia desde "Tomar Asistencia"
     * (AJAX). Reutiliza Asistencia::registrarTomaPreceptor(), que a su vez
     * reutiliza updateRegistro()/calcularResumenDiario()/registrarAuditoria()
     * — no duplica lógica de faltas ni de resumen. Nunca confía en datos que
     * vienen del navegador sin verificarlos contra la BD real: curso propio,
     * materia realmente agendada para ese curso/día/horario, y alumnos
     * realmente matriculados en ese curso.
     */
    public static function guardarAsistenciaAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];
        $cicloActual = (int) date('Y');

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $cursoId = (int) input('curso_id', 0);
        $materiaId = (int) input('materia_id', 0);
        $diaSemana = (int) input('dia_semana', 0);
        $fecha = trim((string) input('fecha', ''));
        $estados = $_POST['estados'] ?? [];

        // 1) Curso realmente asignado a ESTE preceptor (nunca se confía en el
        // curso_id que llega del navegador sin este chequeo).
        $db = Database::getConnection();
        $stmtPC = $db->prepare("SELECT c.turno, c.estado AS curso_estado
                                 FROM preceptor_cursos pc
                                 JOIN cursos c ON c.id = pc.curso_id
                                 WHERE pc.preceptor_id = ? AND pc.curso_id = ? AND pc.activo = 1 AND pc.ciclo_lectivo = ?
                                 LIMIT 1");
        $stmtPC->execute([$preceptorId, $cursoId, $cicloActual]);
        $curso = $stmtPC->fetch(PDO::FETCH_ASSOC);
        if (!$curso) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'No tenés ese curso asignado.']);
            return;
        }
        if ($curso['curso_estado'] !== 'activo') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ese curso no está activo.']);
            return;
        }

        // 2) Solo se permite tomar asistencia para HOY (fecha real del
        // servidor, huso Argentina) — evita cargar/alterar fechas pasadas o
        // futuras arbitrarias desde este flujo liviano; correcciones sobre
        // otras fechas siguen siendo tarea de Admin → Historial.
        $hoy = self::hoyArgentina();
        if ($fecha !== $hoy) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Solo se puede tomar asistencia del día de hoy (' . $hoy . ') desde esta pantalla.']);
            return;
        }

        // 3) La materia/horario elegidos deben corresponder realmente al
        // horario agendado de ese curso ese día (asignaciones_materias),
        // nunca a un materia_id/horario inventado desde el navegador.
        require_once __DIR__ . '/../models/Materia.php';
        $asignaciones = Materia::getAsignaciones($cursoId);
        $asignacion = null;
        foreach ($asignaciones as $a) {
            if ((int) $a['materia_id'] === $materiaId && (int) $a['dia_semana'] === $diaSemana) {
                $asignacion = $a;
                break;
            }
        }
        if (!$asignacion) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Esa materia no está agendada para ese curso en el día seleccionado.']);
            return;
        }

        // 4) Set de alumnos válido: SIEMPRE el real (BD), nunca el que venga
        // del navegador. Se exige que estados cubra exactamente ese conjunto.
        require_once __DIR__ . '/../models/Curso.php';
        $alumnosCurso = Curso::getAlumnosByCursoId($cursoId, $cicloActual);
        $idsValidos = array_map(fn($a) => (int) $a['id'], $alumnosCurso);

        if (empty($idsValidos)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ese curso no tiene alumnos matriculados.']);
            return;
        }

        $estadosPermitidos = ['p', 'a', 't', 'ra'];
        $estadosValidados = [];
        foreach ($idsValidos as $alumnoId) {
            if (!array_key_exists((string) $alumnoId, $estados) && !array_key_exists($alumnoId, $estados)) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Falta marcar el estado de uno o más alumnos del curso.']);
                return;
            }
            $codigo = $estados[$alumnoId] ?? $estados[(string) $alumnoId];
            if (!in_array($codigo, $estadosPermitidos, true)) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Estado de asistencia inválido.']);
                return;
            }
            $estadosValidados[$alumnoId] = $codigo;
        }
        // Cualquier ID enviado que no sea un alumno real de este curso se
        // descarta silenciosamente: no se procesa, no rompe el guardado.

        require_once __DIR__ . '/../models/Asistencia.php';
        $resultado = Asistencia::registrarTomaPreceptor(
            $cursoId,
            $materiaId,
            $preceptorId,
            $fecha,
            $curso['turno'],
            substr($asignacion['hora_inicio'], 0, 5),
            substr($asignacion['hora_fin'], 0, 5),
            $cicloActual,
            $estadosValidados
        );

        if (!$resultado['ok']) {
            http_response_code(409);
            echo json_encode($resultado);
            return;
        }

        require_once __DIR__ . '/../models/LogActividad.php';
        LogActividad::registrar(
            $preceptorId,
            $resultado['accion'] === 'creado' ? 'CREAR_ASISTENCIA' : 'EDITAR_ASISTENCIA',
            "Preceptor tomó asistencia real del curso #$cursoId, materia #$materiaId, fecha $fecha (registro #{$resultado['registroId']})",
            'registros_asistencia',
            $resultado['registroId'],
            null,
            null
        );

        echo json_encode([
            'ok' => true,
            'accion' => $resultado['accion'],
            'registroId' => $resultado['registroId'],
            'total' => count($estadosValidados),
            'presentes' => count(array_filter($estadosValidados, fn($c) => $c === 'p')),
        ]);
    }

    private static function destinatarioPermitido(int $preceptorId, int $destinatarioId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT rol FROM usuarios WHERE id = ? AND estado = 'activo'");
        $stmt->execute([$destinatarioId]);
        $destRol = $stmt->fetchColumn();
        if (in_array($destRol, ['admin', 'directivo'], true)) return true;
        if ($destRol !== 'padre_tutor') return false;

        // El destinatario debe ser tutor de un alumno de alguno de los cursos del preceptor
        $stmt2 = $db->prepare("SELECT 1
            FROM vinculaciones v
            JOIN alumno_cursos ac ON ac.alumno_id = v.alumno_id
            JOIN preceptor_cursos pc ON pc.curso_id = ac.curso_id
            WHERE v.padre_tutor_id = ? AND v.estado = 'aprobado' AND pc.preceptor_id = ? AND pc.activo = 1
            LIMIT 1");
        $stmt2->execute([$destinatarioId, $preceptorId]);
        return (bool) $stmt2->fetchColumn();
    }
}
