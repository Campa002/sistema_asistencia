<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/Comunicado.php';
require_once __DIR__ . '/../models/Mensaje.php';
require_once __DIR__ . '/../models/ConfiguracionSistema.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Datos reales para el portal (SPA) de Estudiante. El "Panel de
 * calificaciones" del diseño original NO se conecta: el sistema no tiene
 * (ni tuvo nunca) tabla de notas/calificaciones — es explícitamente fuera
 * del alcance del proyecto (ver decisiones previas sobre Materias). Queda
 * documentado como estático en el informe final, no se inventa un origen
 * de datos que no existe.
 */
class AlumnoController {

    public static function portalData(int $alumnoId): array {
        $db = Database::getConnection();
        $usuario = Usuario::getById($alumnoId);
        $cicloActual = (int) date('Y');

        $stmtC = $db->prepare("SELECT ac.curso_id, ac.estado AS matricula_estado
                                FROM alumno_cursos ac WHERE ac.alumno_id = ? AND ac.ciclo_lectivo = ?");
        $stmtC->execute([$alumnoId, $cicloActual]);
        $matricula = $stmtC->fetch(PDO::FETCH_ASSOC);
        $curso = $matricula ? Curso::getById($matricula['curso_id']) : null;

        $faltasTotales = Reporte::getFaltasTotalAlumno($alumnoId);
        $resumen = Reporte::getResumenPorAlumno(['alumno_id' => $alumnoId], 1, 1);
        $resumenAlumno = $resumen['alumnos'][0] ?? null;

        // Historial diario real: un detalle puede tener varias materias el
        // mismo día; se resume a un estado por día (prioridad
        // ausente > llegada_tarde > justificado > presente, solo para esta
        // vista) y se toma la falta real de resumen_asistencia_diaria.
        $stmtH = $db->prepare("SELECT reg.fecha, det.estado, det.hora_llegada, det.hora_retiro
                                FROM detalles_asistencia det
                                JOIN registros_asistencia reg ON reg.id = det.registro_id
                                WHERE det.alumno_id = ? AND reg.estado != 'anulada'
                                ORDER BY reg.fecha DESC");
        $stmtH->execute([$alumnoId]);
        $porDia = [];
        foreach ($stmtH->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $f = $r['fecha'];
            if (!isset($porDia[$f])) $porDia[$f] = ['estados' => [], 'hora_llegada' => null, 'hora_retiro' => null];
            $porDia[$f]['estados'][] = $r['estado'];
            if ($r['hora_llegada'] && !$porDia[$f]['hora_llegada']) $porDia[$f]['hora_llegada'] = $r['hora_llegada'];
            if ($r['hora_retiro']) $porDia[$f]['hora_retiro'] = $r['hora_retiro'];
        }

        $stmtF = $db->prepare("SELECT fecha, SUM(faltas_total) AS faltas FROM resumen_asistencia_diaria WHERE alumno_id = ? GROUP BY fecha");
        $stmtF->execute([$alumnoId]);
        $faltasPorDia = [];
        foreach ($stmtF->fetchAll(PDO::FETCH_ASSOC) as $r) $faltasPorDia[$r['fecha']] = (float) $r['faltas'];

        $historial = [];
        $calendario = [];
        foreach ($porDia as $fecha => $d) {
            $estados = $d['estados'];
            if (in_array('ausente', $estados, true)) $estado = 'ausente';
            elseif (in_array('llegada_tarde', $estados, true)) $estado = 'tarde';
            elseif (in_array('justificado', $estados, true)) $estado = 'justificado';
            else $estado = 'presente';

            $calendario[$fecha] = $estado === 'justificado' ? 'ausente' : $estado; // el calendario solo soporta 3 colores
            $historial[] = [
                'fecha' => $fecha,
                'estado' => $estado,
                'hora_llegada' => $d['hora_llegada'],
                'hora_retiro' => $d['hora_retiro'],
                'falta' => $faltasPorDia[$fecha] ?? 0.0,
            ];
        }
        usort($historial, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));
        $historialReciente = array_slice($historial, 0, 6);

        // Avisos / Notificaciones (dirigidos a alumno o a todos)
        $comunicados = Comunicado::getAll(['estado' => 'activo']);
        $avisos = array_values(array_filter($comunicados, fn($c) => in_array($c['rol_destino'], ['todos', 'alumno'], true)));

        $stmtN = $db->prepare("SELECT * FROM notificaciones
                                WHERE activo = 1 AND tipo != 'comunicado'
                                  AND (FIND_IN_SET('alumno', rol_destino) > 0 OR FIND_IN_SET('todos', rol_destino) > 0)
                                ORDER BY created_at DESC LIMIT 5");
        $stmtN->execute();
        $notificaciones = $stmtN->fetchAll(PDO::FETCH_ASSOC);

        // Compañeros: otros alumnos activos de la institución (nombre + curso, sin datos sensibles)
        $stmtComp = $db->prepare("SELECT u.id, u.nombre, u.apellido, c.anio, c.division, esp.nombre AS especialidad_nombre
                                   FROM alumno_cursos ac
                                   JOIN usuarios u ON u.id = ac.alumno_id
                                   JOIN cursos c ON c.id = ac.curso_id
                                   LEFT JOIN especialidades esp ON esp.id = c.especialidad_id
                                   WHERE ac.ciclo_lectivo = ? AND u.estado = 'activo' AND u.id != ?
                                   ORDER BY u.apellido, u.nombre LIMIT 40");
        $stmtComp->execute([$cicloActual, $alumnoId]);
        $companeros = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

        // Tutor(es) vinculado(s) real(es)
        $stmtT = $db->prepare("SELECT u.nombre, u.apellido, u.email, u.telefono, v.relacion
                                FROM vinculaciones v JOIN usuarios u ON u.id = v.padre_tutor_id
                                WHERE v.alumno_id = ? AND v.estado = 'aprobado' LIMIT 1");
        $stmtT->execute([$alumnoId]);
        $tutor = $stmtT->fetch(PDO::FETCH_ASSOC);

        // Mensajes reales
        $conversaciones = Mensaje::getAllConversations($alumnoId);
        $msgsData = [];
        foreach ($conversaciones as $conv) {
            $otro = $conv['otroParticipante'];
            $mensajes = Mensaje::getMensajesByConversacionId($conv['id']);
            $msgsData[] = [
                'id' => (int) $conv['id'],
                'nombre' => $otro ? ($otro['nombre'] . ' ' . $otro['apellido']) : ($conv['titulo'] ?? 'Conversación'),
                'rol' => $otro ? ucfirst(str_replace('_', ' ', $otro['rol'])) : '',
                'preview' => $conv['ultimo_mensaje'] ?? '(sin mensajes)',
                'hora' => $conv['ultimo_mensaje_fecha'] ? date('H:i', strtotime($conv['ultimo_mensaje_fecha'])) : '',
                'conversation' => array_map(fn($m) => [
                    'dir' => (int) $m['remitente_id'] === $alumnoId ? 'enviado' : 'recibido',
                    'text' => $m['contenido'],
                    'time' => date('H:i', strtotime($m['created_at'])),
                ], $mensajes),
            ];
        }

        return [
            'usuario' => $usuario,
            'curso' => $curso,
            'matricula' => $matricula,
            'faltasTotales' => $faltasTotales,
            'porcentajeAsistencia' => $resumenAlumno['porcentaje_asistencia'] ?? null,
            'presentes' => $resumenAlumno['presentes'] ?? 0,
            'ausentes' => $resumenAlumno['ausentes'] ?? 0,
            'llegadasTarde' => $resumenAlumno['llegadas_tarde'] ?? 0,
            'justificados' => $resumenAlumno['justificados'] ?? 0,
            'retiros' => $resumenAlumno['retiros_anticipados'] ?? 0,
            'diasTotal' => $resumenAlumno['dias_total'] ?? 0,
            'maximoFaltas' => (float) ConfiguracionSistema::getValor('maximo_faltas', 28),
            'historialReciente' => $historialReciente,
            'calendario' => $calendario,
            'avisos' => $avisos,
            'notificaciones' => $notificaciones,
            'companeros' => $companeros,
            'tutor' => $tutor,
            'msgs' => $msgsData,
            'fechaHoyLarga' => format_date_long_argentina(),
        ];
    }

    public static function enviarMensajeAjax(): void {
        require_role('alumno');
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
