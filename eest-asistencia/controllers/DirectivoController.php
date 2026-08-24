<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

/**
 * Datos reales para el portal (SPA server-rendered) de Directivo. Reutiliza
 * Reporte.php (mismas consultas que ya usa Admin → Reportes/Dashboard) y
 * Asistencia.php — no duplica lógica de cálculo de faltas.
 */
class DirectivoController {

    public static function portalData(int $directivoId): array {
        $db = Database::getConnection();
        $usuario = Usuario::getById($directivoId);

        $stats = Reporte::getReportStats([]);

        $stmtP = $db->query("SELECT estado, COUNT(*) n FROM detalles_asistencia GROUP BY estado");
        $porEstado = [];
        foreach ($stmtP->fetchAll(PDO::FETCH_ASSOC) as $r) $porEstado[$r['estado']] = (int) $r['n'];
        $presentes = ($porEstado['presente'] ?? 0) + ($porEstado['llegada_tarde'] ?? 0) + ($porEstado['ausente_con_presente'] ?? 0);
        $ausentes = $porEstado['ausente'] ?? 0;

        $solicitudesPendientes = (int) $db->query("SELECT COUNT(*) FROM solicitudes_acceso WHERE estado = 'pendiente'")->fetchColumn();
        $solicitudesTotal = (int) $db->query("SELECT COUNT(*) FROM solicitudes_acceso")->fetchColumn();
        $reemplazosSinAsignar = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado = 'sin_asignar'")->fetchColumn();
        $reemplazosAsignadosHoy = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado IN ('asignado','realizado') AND fecha = CURDATE()")->fetchColumn();
        $reemplazosTotal = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores")->fetchColumn();
        $reemplazosRealizados = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado = 'realizado'")->fetchColumn();
        $efectividadCobertura = $reemplazosTotal > 0 ? round($reemplazosRealizados / $reemplazosTotal * 100, 1) : 0;

        $preceptoresActivos = count(Usuario::getAll(['rol' => 'preceptor', 'estado' => 'activo']));
        $preceptoresTotal = count(Usuario::getAll(['rol' => 'preceptor']));
        $preceptoresDisponibles = max(0, $preceptoresActivos - self::preceptoresConReemplazoHoy($db));

        $ultimaCarga = $db->query("SELECT MAX(created_at) FROM registros_asistencia")->fetchColumn();

        // Gráfico: presentes/ausentes reales por curso, mañana vs. tarde
        $graficoManana = self::presentesAusentesPorCurso($db, 'mañana');
        $graficoTarde = self::presentesAusentesPorCurso($db, 'tarde');

        // Avisos recientes (los más nuevos, dirigidos a directivo o a todos)
        $avisos = self::notificacionesPara($db, $directivoId, ['aviso', 'alerta', 'recordatorio'], 2);

        // Solicitudes de acceso reales
        $stmtS = $db->query("SELECT sa.*, u.nombre, u.apellido, u.dni, u.email, u.telefono
                              FROM solicitudes_acceso sa
                              JOIN usuarios u ON u.id = sa.usuario_id
                              ORDER BY sa.created_at DESC LIMIT 30");
        $solicitudes = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Reemplazos reales
        $stmtR = $db->query("SELECT rp.*, c.anio, c.division, esp.nombre AS especialidad_nombre,
                                     ut.nombre AS titular_nombre, ut.apellido AS titular_apellido,
                                     ur.nombre AS reemplazante_nombre, ur.apellido AS reemplazante_apellido,
                                     m.nombre AS materia_nombre
                              FROM reemplazos_preceptores rp
                              JOIN cursos c ON c.id = rp.curso_id
                              LEFT JOIN especialidades esp ON esp.id = c.especialidad_id
                              JOIN usuarios ut ON ut.id = rp.preceptor_titular_id
                              LEFT JOIN usuarios ur ON ur.id = rp.preceptor_reemplazante_id
                              LEFT JOIN materias m ON m.id = rp.materia_id
                              ORDER BY rp.fecha DESC LIMIT 60");
        $reemplazos = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        // Prioridad dinámica: cuánto falta (o cuánto pasó) para el horario
        // REAL de la clase que necesita cobertura, calculado desde
        // asignaciones_materias (o, si el reemplazo no tiene materia
        // puntual, el horario institucional de su turno). Nunca hardcodeado.
        foreach ($reemplazos as &$r) {
            $urgencia = self::calcularUrgenciaReemplazo($db, $r);
            $r['urgenciaMinutos'] = $urgencia['minutos'];
            $r['urgenciaTexto'] = $urgencia['texto'];
            $r['horaClase'] = $urgencia['horaClase'];
        }
        unset($r);

        // Grupo 0 = necesita acción (sin_asignar), 1 = ya cubierto (asignado),
        // 2 = resuelto/cerrado (realizado/cancelado) — dentro de cada grupo
        // activo se ordena por urgencia real; el grupo resuelto se mantiene
        // por fecha más reciente primero (como ya estaba).
        usort($reemplazos, function ($a, $b) {
            $grupo = fn($r) => match ($r['estado']) {
                'sin_asignar' => 0,
                'asignado' => 1,
                default => 2,
            };
            $ga = $grupo($a);
            $gb = $grupo($b);
            if ($ga !== $gb) return $ga <=> $gb;
            if ($ga <= 1) return $a['urgenciaMinutos'] <=> $b['urgenciaMinutos'];
            return strcmp($b['fecha'], $a['fecha']);
        });

        // Asistencia institucional: registros recientes de todos los cursos
        $registrosAsistencia = self::buildRegistrosAsistencia([]);

        // Notificaciones (todo tipo salvo comunicado, ya cubierto en Admin)
        $notificaciones = self::notificacionesPara($db, $directivoId, ['aviso', 'alerta', 'recordatorio'], 30);

        // Cursos reales para el selector de "Nuevo Reemplazo".
        $cursosTodos = $db->query("SELECT c.id, c.anio, c.division
                                    FROM cursos c WHERE c.estado = 'activo'
                                    ORDER BY c.anio, c.division")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'usuario' => $usuario,
            'statsGenerales' => $stats,
            'presentes' => $presentes,
            'ausentes' => $ausentes,
            'llegadasTarde' => (int) $stats['llegadas_tarde'],
            'justificaciones' => (int) $stats['justificaciones_total'],
            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesTotal' => $solicitudesTotal,
            'reemplazosSinAsignar' => $reemplazosSinAsignar,
            'reemplazosAsignadosHoy' => $reemplazosAsignadosHoy,
            'reemplazosTotal' => $reemplazosTotal,
            'efectividadCobertura' => $efectividadCobertura,
            'preceptoresActivos' => $preceptoresActivos,
            'preceptoresTotal' => $preceptoresTotal,
            'preceptoresDisponibles' => $preceptoresDisponibles,
            'ultimaCarga' => $ultimaCarga,
            'graficoManana' => $graficoManana,
            'graficoTarde' => $graficoTarde,
            'avisos' => $avisos,
            'solicitudes' => $solicitudes,
            'reemplazos' => $reemplazos,
            'registrosAsistencia' => $registrosAsistencia,
            'notificaciones' => $notificaciones,
            'cursosTodos' => $cursosTodos,
            'fechaHoyLarga' => format_date_long_argentina(),
        ];
    }

    /**
     * Presentes/ausentes reales por curso para un turno, usados en el
     * gráfico de barras apiladas del dashboard (antes con datos inventados).
     */
    private static function presentesAusentesPorCurso(PDO $db, string $turno): array {
        $stmt = $db->prepare("SELECT CONCAT(cur.anio, '° ', cur.division) AS division,
                    SUM(CASE WHEN det.estado != 'ausente' THEN 1 ELSE 0 END) AS presentes,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN cursos cur ON reg.curso_id = cur.id
                WHERE reg.turno = ? AND reg.estado != 'anulada'
                GROUP BY cur.id, cur.anio, cur.division
                ORDER BY cur.anio, cur.division");
        $stmt->execute([$turno]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Minutos reales hasta que arranca la clase que un reemplazo necesita
     * cubrir (negativo = ya arrancó y sigue sin cobertura → más urgente
     * cuanto más negativo). Horario real desde asignaciones_materias; si el
     * reemplazo no tiene materia puntual (9 de 18 filas actuales no la
     * tienen), se usa el horario institucional de su turno
     * (Asistencia::getBloqueHorarioInfo, la misma fuente que ya usan
     * Preceptor y Admin) — nunca un horario escrito a mano acá.
     */
    private static function calcularUrgenciaReemplazo(PDO $db, array $reemplazo): array {
        $horaInicio = null;
        if (!empty($reemplazo['materia_id'])) {
            $diaSemana = (int) (new DateTimeImmutable($reemplazo['fecha']))->format('N');
            $stmt = $db->prepare("SELECT hora_inicio FROM asignaciones_materias
                                   WHERE curso_id = ? AND materia_id = ? AND dia_semana = ? AND activo = 1
                                   ORDER BY hora_inicio LIMIT 1");
            $stmt->execute([$reemplazo['curso_id'], $reemplazo['materia_id'], $diaSemana]);
            $horaInicio = $stmt->fetchColumn() ?: null;
        }
        if (!$horaInicio) {
            $bloque = Asistencia::getBloqueHorarioInfo($reemplazo['turno'], 'primera_hora');
            $horaInicio = $bloque ? $bloque['inicio'] . ':00' : '00:00:00';
        }

        $horaClase = substr($horaInicio, 0, 5);
        $momentoClase = new DateTimeImmutable($reemplazo['fecha'] . ' ' . $horaInicio, new DateTimeZone('America/Argentina/Buenos_Aires'));
        $ahora = new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
        $minutos = (int) round(($momentoClase->getTimestamp() - $ahora->getTimestamp()) / 60);

        if ($minutos < 0) {
            $atraso = abs($minutos);
            $texto = $atraso < 60 ? "Ya empezó, hace {$atraso} min" : 'Ya empezó, hace ' . intdiv($atraso, 60) . 'h ' . ($atraso % 60) . 'min';
        } elseif ($minutos === 0) {
            $texto = 'Empieza ahora';
        } elseif ($minutos < 60) {
            $texto = "Empieza en {$minutos} min";
        } else {
            $texto = 'Empieza en ' . intdiv($minutos, 60) . 'h ' . ($minutos % 60) . 'min';
        }

        return ['minutos' => $minutos, 'texto' => $texto, 'horaClase' => $horaClase];
    }

    private static function preceptoresConReemplazoHoy(PDO $db): int {
        $stmt = $db->query("SELECT COUNT(DISTINCT preceptor_titular_id) FROM reemplazos_preceptores WHERE fecha = CURDATE() AND estado != 'cancelado'");
        return (int) $stmt->fetchColumn();
    }

    private static function notificacionesPara(PDO $db, int $usuarioId, array $tipos, int $limite): array {
        $in = implode(',', array_fill(0, count($tipos), '?'));
        $stmt = $db->prepare("SELECT n.*, (nl.id IS NOT NULL) AS leida
                               FROM notificaciones n
                               LEFT JOIN notificaciones_leidas nl ON nl.notificacion_id = n.id AND nl.usuario_id = ?
                               WHERE n.tipo IN ($in) AND n.activo = 1
                                 AND (FIND_IN_SET('directivo', n.rol_destino) > 0 OR FIND_IN_SET('todos', n.rol_destino) > 0)
                               ORDER BY n.created_at DESC LIMIT " . (int) $limite);
        $stmt->execute([$usuarioId, ...$tipos]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aprueba una solicitud de acceso real (reglas_del_sistema.md: "Puede
     * aprobar o rechazar solicitudes de alumnos" / "...vinculaciones de
     * padres/tutores"). Desbloquea la cuenta (usuarios.estado) solo si
     * seguía en 'pendiente' — nunca pisa un estado distinto. Para
     * vinculacion_padre, además aprueba/crea la fila real en `vinculaciones`
     * (sin esa fila, aprobar la solicitud no tendría ningún efecto real).
     * No crea tablas nuevas, reutiliza solicitudes_acceso/usuarios/vinculaciones.
     */
    public static function aprobarSolicitudAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $solicitudId = (int) input('solicitud_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM solicitudes_acceso WHERE id = ?");
        $stmt->execute([$solicitudId]);
        $sol = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Solicitud no encontrada.']);
            return;
        }
        if ($sol['estado'] !== 'pendiente') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Esta solicitud ya fue revisada anteriormente.']);
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE solicitudes_acceso SET estado = 'aprobado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?")
               ->execute([$directivoId, $solicitudId]);

            $db->prepare("UPDATE usuarios SET estado = 'activo' WHERE id = ? AND estado = 'pendiente'")
               ->execute([$sol['usuario_id']]);

            if ($sol['tipo'] === 'vinculacion_padre') {
                $extra = json_decode((string) $sol['datos_extra'], true) ?: [];
                $alumnoId = (int) ($extra['alumno_id'] ?? 0);
                $relacion = in_array($extra['relacion'] ?? '', ['padre', 'madre', 'tutor', 'otro'], true) ? $extra['relacion'] : 'tutor';
                if ($alumnoId > 0) {
                    $chk = $db->prepare("SELECT id FROM vinculaciones WHERE padre_tutor_id = ? AND alumno_id = ?");
                    $chk->execute([$sol['usuario_id'], $alumnoId]);
                    $vinculacionId = $chk->fetchColumn();
                    if ($vinculacionId) {
                        $db->prepare("UPDATE vinculaciones SET estado = 'aprobado', relacion = ?, aprobado_por = ?, fecha_aprobacion = NOW() WHERE id = ?")
                           ->execute([$relacion, $directivoId, $vinculacionId]);
                    } else {
                        $db->prepare("INSERT INTO vinculaciones (padre_tutor_id, alumno_id, relacion, estado, aprobado_por, fecha_aprobacion) VALUES (?, ?, ?, 'aprobado', ?, NOW())")
                           ->execute([$sol['usuario_id'], $alumnoId, $relacion, $directivoId]);
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'No se pudo aprobar la solicitud.']);
            return;
        }

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'APROBAR_SOLICITUD', "Aprobó la solicitud de acceso #$solicitudId", 'solicitudes_acceso', $solicitudId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Rechaza una solicitud de acceso real. Igual que aprobar, pero además
     * exige un motivo (se guarda en motivo_rechazo, ya previsto en el
     * esquema). Si estaba 'pendiente', la cuenta pasa a 'rechazado' — no
     * puede iniciar sesión (mismo criterio que usa AuthController::login()
     * para el estado 'inactivo'/'rechazado').
     */
    public static function rechazarSolicitudAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $solicitudId = (int) input('solicitud_id', 0);
        $motivo = trim((string) input('motivo', ''));
        if ($motivo === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ingresá un motivo de rechazo.']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM solicitudes_acceso WHERE id = ?");
        $stmt->execute([$solicitudId]);
        $sol = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Solicitud no encontrada.']);
            return;
        }
        if ($sol['estado'] !== 'pendiente') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Esta solicitud ya fue revisada anteriormente.']);
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE solicitudes_acceso SET estado = 'rechazado', motivo_rechazo = ?, revisado_por = ?, fecha_revision = NOW() WHERE id = ?")
               ->execute([$motivo, $directivoId, $solicitudId]);

            $db->prepare("UPDATE usuarios SET estado = 'rechazado' WHERE id = ? AND estado = 'pendiente'")
               ->execute([$sol['usuario_id']]);

            if ($sol['tipo'] === 'vinculacion_padre') {
                $extra = json_decode((string) $sol['datos_extra'], true) ?: [];
                $alumnoId = (int) ($extra['alumno_id'] ?? 0);
                if ($alumnoId > 0) {
                    $db->prepare("UPDATE vinculaciones SET estado = 'rechazado' WHERE padre_tutor_id = ? AND alumno_id = ? AND estado = 'pendiente'")
                       ->execute([$sol['usuario_id'], $alumnoId]);
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'No se pudo rechazar la solicitud.']);
            return;
        }

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'RECHAZAR_SOLICITUD', "Rechazó la solicitud de acceso #$solicitudId: $motivo", 'solicitudes_acceso', $solicitudId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Rango horario real (inicio/fin) que un reemplazo necesita cubrir, y su
     * día de la semana — misma fuente que calcularUrgenciaReemplazo() (real,
     * desde asignaciones_materias, con el bloque institucional del turno
     * como respaldo si no hay materia puntual). Se reutiliza tanto para la
     * lista de preceptores disponibles como para el chequeo final al asignar.
     */
    private static function horarioDeReemplazo(PDO $db, array $reemplazo): array {
        $diaSemana = (int) (new DateTimeImmutable($reemplazo['fecha']))->format('N');
        $horaInicio = null;
        $horaFin = null;
        if (!empty($reemplazo['materia_id'])) {
            $stmt = $db->prepare("SELECT hora_inicio, hora_fin FROM asignaciones_materias
                                   WHERE curso_id = ? AND materia_id = ? AND dia_semana = ? AND activo = 1
                                   ORDER BY hora_inicio LIMIT 1");
            $stmt->execute([$reemplazo['curso_id'], $reemplazo['materia_id'], $diaSemana]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fila) { $horaInicio = $fila['hora_inicio']; $horaFin = $fila['hora_fin']; }
        }
        if (!$horaInicio) {
            $bloque = Asistencia::getBloqueHorarioInfo($reemplazo['turno'], 'primera_hora');
            $horaInicio = $bloque ? $bloque['inicio'] . ':00' : '00:00:00';
            $horaFin = $bloque ? $bloque['fin'] . ':00' : '23:59:00';
        }
        return ['diaSemana' => $diaSemana, 'horaInicio' => $horaInicio, 'horaFin' => $horaFin];
    }

    /**
     * Preceptores que realmente se pueden asignar como reemplazante de un
     * reemplazo puntual (reglas_del_sistema.md §9 del pedido: no puede ser
     * el propio ausente, no puede ya tener una clase propia en ese horario,
     * no puede estar ya cubriendo otro reemplazo incompatible ese mismo
     * horario). Todo calculado contra horarios reales, nunca hardcodeado.
     */
    public static function preceptoresDisponiblesAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');

        $reemplazoId = (int) input('reemplazo_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM reemplazos_preceptores WHERE id = ?");
        $stmt->execute([$reemplazoId]);
        $reemplazo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$reemplazo) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Reemplazo no encontrado.']);
            return;
        }

        $horario = self::horarioDeReemplazo($db, $reemplazo);

        // Candidatos base: preceptores activos, sin contar al titular ausente
        // ni a quien ya cubre otro reemplazo ese mismo turno/fecha.
        $stmt2 = $db->prepare("SELECT id, nombre, apellido FROM usuarios
                               WHERE rol = 'preceptor' AND estado = 'activo' AND id != ?
                                 AND id NOT IN (
                                   SELECT preceptor_reemplazante_id FROM reemplazos_preceptores
                                   WHERE fecha = ? AND turno = ? AND estado != 'cancelado' AND preceptor_reemplazante_id IS NOT NULL
                                 )
                               ORDER BY apellido, nombre");
        $stmt2->execute([(int) $reemplazo['preceptor_titular_id'], $reemplazo['fecha'], $reemplazo['turno']]);
        $candidatos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Filtrar a quienes ya tienen UNA CLASE PROPIA (como preceptor de
        // curso) que se cruza en horario real ese mismo día — no pueden
        // estar en dos lugares a la vez.
        $stmtConflicto = $db->prepare("SELECT 1
            FROM preceptor_cursos pc
            JOIN asignaciones_materias am ON am.curso_id = pc.curso_id
            WHERE pc.preceptor_id = ? AND pc.activo = 1
              AND am.activo = 1 AND am.dia_semana = ?
              AND am.hora_inicio < ? AND ? < am.hora_fin
            LIMIT 1");

        $disponibles = [];
        foreach ($candidatos as $c) {
            $stmtConflicto->execute([$c['id'], $horario['diaSemana'], $horario['horaFin'], $horario['horaInicio']]);
            if (!$stmtConflicto->fetchColumn()) {
                $disponibles[] = $c;
            }
        }

        echo json_encode(['ok' => true, 'preceptores' => $disponibles]);
    }

    /**
     * Asigna quién cubre un reemplazo (reglas_del_sistema.md §15: "Los
     * reemplazos los asigna el Directivo. Registra quién cubrió a quién.").
     * Reutiliza reemplazos_preceptores tal cual está — no crea tablas.
     */
    public static function asignarReemplazoAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $reemplazoId = (int) input('reemplazo_id', 0);
        $preceptorId = (int) input('preceptor_id', 0);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM reemplazos_preceptores WHERE id = ?");
        $stmt->execute([$reemplazoId]);
        $reemplazo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reemplazo) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Reemplazo no encontrado.']);
            return;
        }
        if ($reemplazo['estado'] !== 'sin_asignar') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Este reemplazo ya fue asignado o ya no está disponible.']);
            return;
        }
        if ($preceptorId === (int) $reemplazo['preceptor_titular_id']) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El preceptor titular no puede cubrirse a sí mismo.']);
            return;
        }

        $stmtP = $db->prepare("SELECT id FROM usuarios WHERE id = ? AND rol = 'preceptor' AND estado = 'activo'");
        $stmtP->execute([$preceptorId]);
        if (!$stmtP->fetchColumn()) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El preceptor elegido no es válido.']);
            return;
        }

        // Re-chequeo final (no confiar solo en la lista que ya se filtró en
        // el navegador): ¿el reemplazante elegido tiene una clase propia que
        // se cruza en horario real ese mismo día?
        $horario = self::horarioDeReemplazo($db, $reemplazo);
        $stmtConflicto = $db->prepare("SELECT 1 FROM preceptor_cursos pc
            JOIN asignaciones_materias am ON am.curso_id = pc.curso_id
            WHERE pc.preceptor_id = ? AND pc.activo = 1
              AND am.activo = 1 AND am.dia_semana = ?
              AND am.hora_inicio < ? AND ? < am.hora_fin
            LIMIT 1");
        $stmtConflicto->execute([$preceptorId, $horario['diaSemana'], $horario['horaFin'], $horario['horaInicio']]);
        if ($stmtConflicto->fetchColumn()) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ese preceptor ya tiene una clase propia en ese horario.']);
            return;
        }

        $db->prepare("UPDATE reemplazos_preceptores SET preceptor_reemplazante_id = ?, estado = 'asignado', asignado_por = ? WHERE id = ?")
           ->execute([$preceptorId, $directivoId, $reemplazoId]);

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'ASIGNAR_REEMPLAZO', "Asignó el reemplazo #$reemplazoId al preceptor #$preceptorId", 'reemplazos_preceptores', $reemplazoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Marca una notificación institucional como leída para ESTE directivo
     * (notificaciones_leidas ya es por-usuario, ver notificacionesPara()).
     */
    public static function marcarNotificacionLeidaAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

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
           ->execute([$notifId, $directivoId]);

        echo json_encode(['ok' => true]);
    }

    /**
     * Actualiza el perfil del propio Directivo. Mismo criterio que
     * AdminPerfilController::actualizarPerfil() (reutilizado, no duplicado
     * como lógica de negocio): whitelist explícito de campos, rol/estado
     * nunca se leen del request.
     */
    public static function actualizarPerfilAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $nombre = trim((string) input('nombre', ''));
        $apellido = trim((string) input('apellido', ''));
        $email = trim((string) input('email', ''));
        $telefono = trim((string) input('telefono', ''));

        $errores = [];
        if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
        if ($apellido === '') $errores[] = 'El apellido es obligatorio.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'El email no es válido.';

        if (!empty($errores)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => implode(' ', $errores)]);
            return;
        }

        Usuario::update($directivoId, [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $telefono,
        ]);

        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['email'] = $email;

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'ACTUALIZAR_PERFIL', 'Actualizó su propio perfil', 'usuarios', $directivoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Materias realmente agendadas para un curso en el día de la semana de
     * una fecha (asignaciones_materias) + los preceptores realmente
     * asignados a ese curso (preceptor_cursos) — alimenta el modal "Nuevo
     * Reemplazo": nunca deja elegir una materia/preceptor que no exista de
     * verdad para ese curso.
     */
    public static function materiasDeCursoFechaAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');

        $cursoId = (int) input('curso_id', 0);
        $fecha = trim((string) input('fecha', ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Fecha inválida.']);
            return;
        }
        $diaSemana = (int) (new DateTimeImmutable($fecha))->format('N');

        $db = Database::getConnection();
        $stmtC = $db->prepare("SELECT id FROM cursos WHERE id = ? AND estado = 'activo'");
        $stmtC->execute([$cursoId]);
        if (!$stmtC->fetchColumn()) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Curso no encontrado.']);
            return;
        }

        $stmtM = $db->prepare("SELECT am.materia_id, m.nombre AS materia_nombre, am.hora_inicio, am.hora_fin, am.modulo_horario
                                FROM asignaciones_materias am
                                JOIN materias m ON m.id = am.materia_id
                                WHERE am.curso_id = ? AND am.dia_semana = ? AND am.activo = 1
                                ORDER BY am.hora_inicio");
        $stmtM->execute([$cursoId, $diaSemana]);
        $materias = array_map(function ($m) {
            return [
                'materiaId' => (int) $m['materia_id'],
                'materiaNombre' => $m['materia_nombre'],
                'horaInicio' => substr($m['hora_inicio'], 0, 5),
                'horaFin' => substr($m['hora_fin'], 0, 5),
                'turno' => mb_strtolower($m['modulo_horario']),
            ];
        }, $stmtM->fetchAll(PDO::FETCH_ASSOC));

        $stmtP = $db->prepare("SELECT u.id, u.nombre, u.apellido, pc.es_titular
                                FROM preceptor_cursos pc
                                JOIN usuarios u ON u.id = pc.preceptor_id
                                WHERE pc.curso_id = ? AND pc.activo = 1
                                ORDER BY pc.es_titular DESC, u.apellido");
        $stmtP->execute([$cursoId]);
        $preceptores = array_map(fn($p) => ['id' => (int) $p['id'], 'nombre' => $p['nombre'], 'apellido' => $p['apellido'], 'esTitular' => (bool) $p['es_titular']], $stmtP->fetchAll(PDO::FETCH_ASSOC));

        echo json_encode(['ok' => true, 'materias' => $materias, 'preceptores' => $preceptores]);
    }

    /**
     * Crea un pedido de reemplazo real (botón "Nuevo Reemplazo"), con la
     * misma tabla y el mismo estado inicial ('sin_asignar') que ya generan
     * las ausencias reales del Preceptor — no es un flujo paralelo, es la
     * misma fila, solo que la crea el Directivo a mano en vez de que la
     * dispare automáticamente PreceptorController::indicarAusenciaAjax().
     */
    public static function crearReemplazoAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $fecha = trim((string) input('fecha', ''));
        $cursoId = (int) input('curso_id', 0);
        $materiaId = (int) input('materia_id', 0);
        $preceptorTitularId = (int) input('preceptor_titular_id', 0);
        $motivo = trim((string) input('motivo', ''));
        $prioridad = input('prioridad', 'normal');
        if (!in_array($prioridad, ['normal', 'alta', 'urgente'], true)) $prioridad = 'normal';

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Fecha inválida.']);
            return;
        }
        if ($motivo === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ingresá un motivo.']);
            return;
        }

        $db = Database::getConnection();

        $stmtC = $db->prepare("SELECT id FROM cursos WHERE id = ? AND estado = 'activo'");
        $stmtC->execute([$cursoId]);
        if (!$stmtC->fetchColumn()) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El curso elegido no es válido.']);
            return;
        }

        // La materia debe estar realmente agendada para ESE curso ese día
        // de la semana — de ahí sale también el turno real (nunca se acepta
        // un turno escrito a mano desde el navegador).
        $diaSemana = (int) (new DateTimeImmutable($fecha))->format('N');
        $stmtM = $db->prepare("SELECT modulo_horario FROM asignaciones_materias
                                WHERE curso_id = ? AND materia_id = ? AND dia_semana = ? AND activo = 1 LIMIT 1");
        $stmtM->execute([$cursoId, $materiaId, $diaSemana]);
        $modulo = $stmtM->fetchColumn();
        if (!$modulo) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Esa materia no está agendada para ese curso en esa fecha.']);
            return;
        }
        $turno = mb_strtolower($modulo);

        $stmtP = $db->prepare("SELECT 1 FROM preceptor_cursos WHERE preceptor_id = ? AND curso_id = ? AND activo = 1 LIMIT 1");
        $stmtP->execute([$preceptorTitularId, $cursoId]);
        if (!$stmtP->fetchColumn()) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ese preceptor no está asignado a ese curso.']);
            return;
        }

        $stmtDup = $db->prepare("SELECT id FROM reemplazos_preceptores
                                  WHERE curso_id = ? AND materia_id = ? AND fecha = ? AND estado != 'cancelado' LIMIT 1");
        $stmtDup->execute([$cursoId, $materiaId, $fecha]);
        if ($stmtDup->fetchColumn()) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Ya existe un pedido de reemplazo activo para ese curso, materia y fecha.']);
            return;
        }

        $stmtIns = $db->prepare("INSERT INTO reemplazos_preceptores
            (preceptor_titular_id, curso_id, materia_id, fecha, turno, motivo, prioridad, estado, asignado_por)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'sin_asignar', ?)");
        $stmtIns->execute([$preceptorTitularId, $cursoId, $materiaId, $fecha, $turno, $motivo, $prioridad, $directivoId]);
        $nuevoId = (int) $db->lastInsertId();

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'CREAR_REEMPLAZO', "Creó el pedido de reemplazo #$nuevoId (curso #$cursoId, materia #$materiaId, $fecha)", 'reemplazos_preceptores', $nuevoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true, 'reemplazoId' => $nuevoId]);
    }

    /**
     * Cancela un reemplazo real (nunca se borra la fila: reglas_del_sistema
     * exige conservar historial de auditoría). Solo se puede cancelar
     * mientras esté 'sin_asignar' o 'asignado' — uno ya 'realizado' o ya
     * 'cancelado' no se puede volver a tocar desde acá.
     */
    public static function cancelarReemplazoAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $reemplazoId = (int) input('reemplazo_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM reemplazos_preceptores WHERE id = ?");
        $stmt->execute([$reemplazoId]);
        $reemplazo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reemplazo) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Reemplazo no encontrado.']);
            return;
        }
        if (!in_array($reemplazo['estado'], ['sin_asignar', 'asignado'], true)) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Este reemplazo ya no se puede cancelar (estado: ' . $reemplazo['estado'] . ').']);
            return;
        }

        $usuario = Usuario::getById($directivoId);
        $quien = $usuario ? trim($usuario['apellido'] . ', ' . $usuario['nombre']) : "usuario #$directivoId";
        $nota = 'Cancelado por ' . $quien . ' el ' . (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('d/m/Y H:i') . '.';
        $notasFinal = trim(($reemplazo['notas'] ?: '') . "\n" . $nota);

        $db->prepare("UPDATE reemplazos_preceptores SET estado = 'cancelado', notas = ? WHERE id = ?")
           ->execute([$notasFinal, $reemplazoId]);

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'CANCELAR_REEMPLAZO', "Canceló el reemplazo #$reemplazoId", 'reemplazos_preceptores', $reemplazoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Marca un reemplazo ya asignado como efectivamente cubierto
     * ('realizado'). Solo aplica sobre reemplazos 'asignado' — no tiene
     * sentido marcar como realizado uno que nunca tuvo reemplazante. No se
     * borra el registro, se conserva todo (titular, reemplazante, curso,
     * materia, fecha, horario, quién asignó) y solo cambia el estado.
     */
    public static function marcarRealizadoReemplazoAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');
        $directivoId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $reemplazoId = (int) input('reemplazo_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM reemplazos_preceptores WHERE id = ?");
        $stmt->execute([$reemplazoId]);
        $reemplazo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reemplazo) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Reemplazo no encontrado.']);
            return;
        }
        if ($reemplazo['estado'] !== 'asignado') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Solo se puede marcar como realizado un reemplazo que ya tenga un preceptor asignado.']);
            return;
        }

        $db->prepare("UPDATE reemplazos_preceptores SET estado = 'realizado' WHERE id = ?")->execute([$reemplazoId]);

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($directivoId, 'MARCAR_REALIZADO_REEMPLAZO', "Marcó como realizado el reemplazo #$reemplazoId", 'reemplazos_preceptores', $reemplazoId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Arma la lista de registros de asistencia + conteo de estados para la
     * sección "Asistencia Institucional". Reutilizado por portalData() (sin
     * filtros) y por filtrarAsistenciaAjax() (con filtros reales) para no
     * duplicar la lógica de conteo.
     */
    private static function buildRegistrosAsistencia(array $filtros): array {
        $resAsist = Asistencia::getAll($filtros, 1, 20);
        $registrosAsistencia = [];
        foreach ($resAsist['registros'] as $reg) {
            $detalles = Asistencia::getDetallesByRegistroId($reg['id']);
            $conteo = ['presente' => 0, 'ausente' => 0, 'llegada_tarde' => 0, 'justificado' => 0];
            foreach ($detalles as $d) {
                if (isset($conteo[$d['estado']])) $conteo[$d['estado']]++;
            }
            $registrosAsistencia[] = ['reg' => $reg, 'conteo' => $conteo];
        }
        return $registrosAsistencia;
    }

    /**
     * Filtra en vivo la tabla de "Asistencia Institucional" (curso/año,
     * división, fecha) reutilizando Asistencia::getAll() — no duplica la
     * lógica de armado de registros. Devuelve el HTML del <tbody> ya
     * renderizado (mismo partial que usa el render inicial) para que el
     * front no tenga que reimplementar el formateo.
     */
    public static function filtrarAsistenciaAjax(): void {
        require_role('directivo');
        header('Content-Type: application/json');

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $filtros = [];
        $anio = input('anio', '');
        $division = input('division', '');
        $fecha = input('fecha', '');
        if ($anio !== '') $filtros['anio'] = (int) $anio;
        if ($division !== '') $filtros['division'] = (int) $division;
        if ($fecha !== '') {
            $filtros['fecha_desde'] = $fecha;
            $filtros['fecha_hasta'] = $fecha;
        }

        $registrosAsistencia = self::buildRegistrosAsistencia($filtros);

        ob_start();
        require __DIR__ . '/../views/directivo/_partial_asistencia_rows.php';
        $html = ob_get_clean();

        echo json_encode(['ok' => true, 'html' => $html, 'total' => count($registrosAsistencia)]);
    }
}
