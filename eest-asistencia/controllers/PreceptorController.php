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
        $diaSemanaHoy = (int) (new DateTimeImmutable($hoy))->format('N'); // 1=Lunes...7=Domingo
        foreach ($cursosCargo as $c) {
            $cursoId = (int) $c['curso_id'];
            $cursoIds[] = $cursoId;
            $alumnosCurso = Curso::getAlumnosByCursoId($cursoId, $cicloActual);
            $nombreCurso = $c['anio'] . '° ' . $c['division'] . '°';

            // Estado del día POR MATERIA/MÓDULO agendado hoy para este curso
            // (reglas_del_sistema.md §5 + tu ejemplo: Química 07:35-09:35
            // puede quedar COMPLETA mientras Historia 09:55-11:55 sigue
            // PENDIENTE, el mismo curso, el mismo día). Antes acá se calculaba
            // un único estado para todo el curso, así que en cuanto se tomaba
            // UNA materia, el curso entero pasaba a "Completa" y ocultaba que
            // todavía quedaban módulos pendientes.
            $modulosHoy = self::modulosDeHoy($cursoId, $cicloActual, $hoy, $diaSemanaHoy);
            $totalModulos = count($modulosHoy);
            $completados = count(array_filter($modulosHoy, fn($m) => $m['status'] === 'completa'));

            if ($totalModulos === 0) {
                $statusGeneral = 'sin_clases'; // no hay materias agendadas hoy para este curso
            } elseif ($completados === $totalModulos) {
                $statusGeneral = 'completa';
            } elseif ($completados > 0) {
                $statusGeneral = 'parcial';
            } else {
                $statusGeneral = 'pendiente';
            }
            $ultimaActualizacion = null;
            foreach ($modulosHoy as $m) {
                if ($m['updated'] !== null) {
                    $ultimaActualizacion = $m['updated'];
                }
            }

            $cursosData[] = [
                'id' => $cursoId,
                'name' => $nombreCurso,
                'spec' => $c['especialidad_nombre'] ?? 'General',
                'turno' => 'Turno ' . ucfirst($c['turno']),
                'turnoRaw' => $c['turno'],
                'alumnos' => count($alumnosCurso),
                'status' => $statusGeneral, // 'completa' | 'parcial' | 'pendiente' | 'sin_clases'
                'updated' => $ultimaActualizacion,
                'esTitular' => (bool) $c['es_titular'],
                'modulosHoy' => $modulosHoy,
                'totalModulosHoy' => $totalModulos,
                'completadosHoy' => $completados,
            ];

            foreach ($alumnosCurso as $al) {
                $alumnosData[] = [
                    'id' => (int) $al['id'],
                    'last' => mb_strtoupper($al['apellido']),
                    'first' => $al['nombre'],
                    // Los alumnos NO cargan DNI al registrarse (reglas_del_sistema.md
                    // §4); si la BD no tiene un DNI real cargado, se deja vacío
                    // en vez de inventar o mostrar un placeholder.
                    'dni' => $al['dni'] ?? '',
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

                $horario = self::formatHorario($reg['hora_inicio'], $reg['hora_fin']);
                $historial[] = [
                    'registroId' => (int) $reg['id'],
                    'cursoId' => (int) $reg['curso_id'],
                    'course' => $reg['anio'] . '° ' . $reg['division'] . '° – ' . $reg['materia_nombre'],
                    'date' => self::fechaCorta($reg['fecha']),
                    'turno' => ucfirst($reg['curso_turno']) . ' (' . $horario . ')',
                    'presentes' => $presentes,
                    'ausentes' => $ausentes,
                    'status' => $esFinalizada ? 'completa' : 'abierta',
                    'modified' => $reg['estado'] === 'modificada',
                    // editable_normal viene de Asistencia::decorateRegistro(): true
                    // mientras el registro esté dentro del período editable por el
                    // preceptor (≈30 días desde la fecha, ver estaFueraDelMesActivo).
                    'editable' => (bool) $reg['editable_normal'],
                ];
            }
        }

        // Materias/horario reales por curso a cargo (asignaciones_materias)
        $horariosPorCurso = [];
        foreach ($cursoIds as $cid) {
            $horariosPorCurso[$cid] = self::asignacionesDeCurso($cid, $cicloActual);
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

        // Destinatarios reales y permitidos para "Nuevo mensaje" (reemplaza el
        // prompt() del navegador): padres/tutores vinculados y aprobados de
        // alumnos de MIS cursos, más Admin/Directivo. Misma lógica de permisos
        // que ya valida destinatarioPermitido() en enviarMensajeAjax().
        $contactos = [];
        if (!empty($cursoIds)) {
            $in = implode(',', array_fill(0, count($cursoIds), '?'));
            $stmtCont = $db->prepare("SELECT DISTINCT u.id, u.nombre, u.apellido,
                                              a.nombre AS alumno_nombre, a.apellido AS alumno_apellido
                                       FROM vinculaciones v
                                       JOIN usuarios u ON u.id = v.padre_tutor_id AND u.estado = 'activo'
                                       JOIN usuarios a ON a.id = v.alumno_id
                                       JOIN alumno_cursos ac ON ac.alumno_id = v.alumno_id AND ac.ciclo_lectivo = ?
                                       WHERE v.estado = 'aprobado' AND ac.curso_id IN ($in)
                                       ORDER BY u.apellido, u.nombre");
            $stmtCont->execute([$cicloActual, ...$cursoIds]);
            foreach ($stmtCont->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $contactos[] = [
                    'id' => (int) $row['id'],
                    'nombre' => $row['apellido'] . ', ' . $row['nombre'] . ' (Padre/Tutor de ' . $row['alumno_apellido'] . ', ' . $row['alumno_nombre'] . ')',
                    'rol' => 'padre_tutor',
                ];
            }
        }
        $stmtInst = $db->prepare("SELECT id, nombre, apellido, rol FROM usuarios
                                   WHERE rol IN ('admin', 'directivo') AND estado = 'activo'
                                   ORDER BY rol, apellido, nombre");
        $stmtInst->execute();
        foreach ($stmtInst->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $contactos[] = [
                'id' => (int) $row['id'],
                'nombre' => $row['apellido'] . ', ' . $row['nombre'] . ' (' . ucfirst($row['rol']) . ')',
                'rol' => $row['rol'],
            ];
        }

        // Justificaciones reales de alumnos de MIS cursos (justificaciones.detalle_id
        // -> detalles_asistencia -> registros_asistencia -> curso). Reutiliza
        // la tabla existente tal cual, sin joins inventados.
        $justificaciones = [];
        if (!empty($cursoIds)) {
            $in = implode(',', array_fill(0, count($cursoIds), '?'));
            $stmtJ = $db->prepare("SELECT j.*, u.nombre AS alumno_nombre, u.apellido AS alumno_apellido,
                                          reg.fecha, reg.curso_id, c.anio, c.division, m.nombre AS materia_nombre,
                                          ev.nombre AS enviado_por_nombre, ev.apellido AS enviado_por_apellido, ev.rol AS enviado_por_rol
                                   FROM justificaciones j
                                   JOIN detalles_asistencia da ON da.id = j.detalle_id
                                   JOIN registros_asistencia reg ON reg.id = da.registro_id
                                   JOIN cursos c ON c.id = reg.curso_id
                                   JOIN materias m ON m.id = reg.materia_id
                                   JOIN usuarios u ON u.id = j.alumno_id
                                   JOIN usuarios ev ON ev.id = j.enviado_por
                                   WHERE reg.curso_id IN ($in)
                                   ORDER BY (j.estado = 'pendiente') DESC, j.created_at DESC
                                   LIMIT 50");
            $stmtJ->execute($cursoIds);
            $justificaciones = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
        }

        // Retiros: registros de asistencia REALES de hoy, para que el
        // Preceptor pueda marcar rápido a un alumno como retirado
        // anticipadamente sin pasar por el modal completo de edición.
        // Reutiliza detalles_asistencia/registros_asistencia tal cual.
        $retirosHoy = [];
        if (!empty($cursoIds)) {
            $in = implode(',', array_fill(0, count($cursoIds), '?'));
            $stmtR = $db->prepare("SELECT da.id AS detalle_id, da.alumno_id, da.estado, da.hora_retiro,
                                          u.nombre, u.apellido,
                                          reg.id AS registro_id, reg.curso_id, reg.hora_inicio, reg.hora_fin, reg.estado AS registro_estado, reg.fecha,
                                          c.anio, c.division, m.nombre AS materia_nombre
                                   FROM detalles_asistencia da
                                   JOIN registros_asistencia reg ON reg.id = da.registro_id
                                   JOIN cursos c ON c.id = reg.curso_id
                                   JOIN materias m ON m.id = reg.materia_id
                                   JOIN usuarios u ON u.id = da.alumno_id
                                   WHERE reg.curso_id IN ($in) AND reg.fecha = ? AND reg.estado != 'anulada'
                                   ORDER BY reg.hora_inicio, u.apellido");
            $stmtR->execute([...$cursoIds, $hoy]);
            foreach ($stmtR->fetchAll(PDO::FETCH_ASSOC) as $r) {
                // Mismo criterio "editable_normal" que ya usa el resto de
                // Preceptor: solo mientras el registro siga 'abierta' (no
                // 'modificada'/'cerrada'/'anulada').
                $r['editable'] = Asistencia::resolveEstadoRegistro(['estado' => $r['registro_estado'], 'fecha' => $r['fecha']]) === 'abierta';
                $retirosHoy[] = $r;
            }
        }

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
            'contactos' => $contactos,
            'justificaciones' => $justificaciones,
            'retirosHoy' => $retirosHoy,
            'fechaHoyLarga' => self::fechaLarga($hoy),
        ];
    }

    /**
     * Formateo de fechas 100% autocontenido (sin depender de
     * includes/helpers.php): la copia real de ese archivo en el servidor no
     * tiene format_date_short_argentina()/format_date_long_argentina() —ver
     * error "Call to undefined function"—, así que Preceptor deja de usarlas
     * en vez de arriesgar tocar un archivo compartido con el resto del sistema.
     */
    private static function fechaCorta(string $fecha): string {
        try {
            return (new DateTimeImmutable($fecha))->format('d/m/Y');
        } catch (Exception $e) {
            return $fecha;
        }
    }

    private static function fechaLarga(string $fecha): string {
        try {
            $dt = new DateTimeImmutable($fecha, new DateTimeZone('America/Argentina/Buenos_Aires'));
            $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            $diaSemana = $dias[(int) $dt->format('w')];
            $dia = (int) $dt->format('j');
            $mes = $meses[(int) $dt->format('n')];
            $anio = $dt->format('Y');
            return "{$diaSemana}, {$dia} de {$mes} de {$anio}";
        } catch (Exception $e) {
            return $fecha;
        }
    }

    private static function hoyArgentina(): string {
        return (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('Y-m-d');
    }

    /**
     * Horario real de materias de un curso (tabla asignaciones_materias),
     * consultado directamente por SQL en vez de Materia::getAsignaciones()
     * —esa copia real de models/Materia.php en el servidor no tiene ese
     * método, ver error "Call to undefined method"—. Evita tocar un modelo
     * compartido con Admin; se resuelve acá, solo para Preceptor.
     */
    private static function asignacionesDeCurso(int $cursoId, int $cicloLectivo): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT am.materia_id, m.nombre AS materia_nombre,
                                      am.dia_semana, am.hora_inicio, am.hora_fin, am.modulo_horario
                               FROM asignaciones_materias am
                               JOIN materias m ON m.id = am.materia_id
                               WHERE am.curso_id = ? AND am.ciclo_lectivo = ? AND am.activo = 1
                               ORDER BY am.dia_semana, am.hora_inicio");
        $stmt->execute([$cursoId, $cicloLectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Estado REAL de cada materia/módulo agendado hoy para un curso (una
     * entrada por cada fila de asignaciones_materias que caiga en el día de
     * la semana de hoy), consultando si existe un registro no anulado para
     * ESE curso + ESA materia + hoy + ESE módulo puntual. Esto es lo que
     * permite que, el mismo día, Química quede COMPLETA e Historia
     * PENDIENTE al mismo tiempo para el mismo curso (antes se calculaba un
     * único estado por curso, sin distinguir materia/módulo).
     */
    private static function modulosDeHoy(int $cursoId, int $cicloLectivo, string $hoy, int $diaSemanaHoy): array {
        $db = Database::getConnection();
        $asignaciones = self::asignacionesDeCurso($cursoId, $cicloLectivo);
        $modulos = [];

        // Hora real de "ahora" (huso Argentina) — solo se usa para decidir si
        // ya se puede tomar un módulo que todavía está pendiente. Una vez que
        // "ahora" alcanza la hora de inicio, el módulo queda habilitado para
        // el resto del día aunque después pase más tiempo (nunca se vuelve a
        // deshabilitar): reglas_del_sistema.md — "antes del horario: no
        // habilitar; durante/después: habilitar hasta que se complete".
        $horaActual = (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('H:i');
        $esHoyReal = ($hoy === self::hoyArgentina());

        foreach ($asignaciones as $a) {
            if ((int) $a['dia_semana'] !== $diaSemanaHoy) {
                continue;
            }

            $stmt = $db->prepare("SELECT id, estado, updated_at FROM registros_asistencia
                                   WHERE curso_id = ? AND materia_id = ? AND fecha = ? AND modulo_horario = ?
                                     AND estado != 'anulada'
                                   LIMIT 1");
            $stmt->execute([$cursoId, $a['materia_id'], $hoy, $a['modulo_horario']]);
            $reg = $stmt->fetch(PDO::FETCH_ASSOC);

            $horaInicioModulo = substr($a['hora_inicio'], 0, 5);
            // Solo se evalúa "todavía no llegó la hora" cuando modulosDeHoy se
            // está calculando para el día de HOY real; si en algún momento se
            // reutilizara para otra fecha, no tendría sentido bloquear por
            // horario de reloj.
            $habilitado = $reg || !$esHoyReal || $horaActual >= $horaInicioModulo;

            if ($reg) {
                $status = 'completa';
            } elseif ($habilitado) {
                $status = 'pendiente';
            } else {
                $status = 'no_habilitado';
            }

            $modulos[] = [
                'materiaId' => (int) $a['materia_id'],
                'materiaNombre' => $a['materia_nombre'],
                'moduloHorario' => $a['modulo_horario'],
                'horario' => $horaInicioModulo . ' - ' . substr($a['hora_fin'], 0, 5),
                'status' => $status,
                'registroId' => $reg ? (int) $reg['id'] : null,
                'updated' => ($reg && $reg['updated_at']) ? date('H:i', strtotime($reg['updated_at'])) : null,
            ];
        }

        return $modulos;
    }

    /**
     * Reemplaza a Asistencia::registrarTomaPreceptor(), que NO existe en la
     * copia real de models/Asistencia.php del servidor (ver error "Call to
     * undefined method"). Replica la misma lógica —crear el registro, o
     * actualizarlo si ya hay uno abierto para el mismo curso+materia+fecha+
     * módulo— usando solo métodos públicos ya confirmados
     * (Asistencia::getById, Asistencia::updateRegistro,
     * Asistencia::calcularResumenDiario) más el INSERT inicial por SQL
     * directo, porque no hay otro método público para crear un registro
     * nuevo. No se modifica models/Asistencia.php.
     */
    private static function registrarTomaPreceptorLocal(
        int $cursoId,
        int $materiaId,
        int $preceptorId,
        string $fecha,
        string $turno,
        string $moduloHorario,
        string $horaInicio,
        string $horaFin,
        int $cicloLectivo,
        array $estadosPorAlumno
    ): array {
        $mapaEstados = ['p' => 'presente', 'a' => 'ausente', 't' => 'llegada_tarde', 'ra' => 'retirado_anticipado'];
        $turno = in_array($turno, ['mañana', 'tarde', 'vespertino'], true) ? $turno : 'mañana';
        $bloqueHorario = ($moduloHorario === '2da Hora') ? 'segunda_hora' : 'primera_hora';

        $db = Database::getConnection();

        // ¿Ya existe una toma para este contexto exacto? (mismo índice único
        // que ya protege la tabla: curso_id + materia_id + fecha + modulo_horario)
        $stmt = $db->prepare("SELECT id FROM registros_asistencia WHERE curso_id = ? AND materia_id = ? AND fecha = ? AND modulo_horario = ? LIMIT 1");
        $stmt->execute([$cursoId, $materiaId, $fecha, $moduloHorario]);
        $existenteId = $stmt->fetchColumn();

        if ($existenteId) {
            $registroExistente = Asistencia::getById((int) $existenteId);
            if (!$registroExistente || !$registroExistente['editable_normal']) {
                return ['ok' => false, 'error' => 'Ya existe una toma de asistencia finalizada para este curso, materia, fecha y módulo. No se puede volver a cargar; pedile a Administración que la edite si hace falta corregirla.'];
            }

            $alumnosParaUpdate = [];
            foreach ($estadosPorAlumno as $alumnoId => $codigo) {
                $alumnosParaUpdate[(int) $alumnoId] = $mapaEstados[$codigo] ?? 'ausente';
            }
            $ok = Asistencia::updateRegistro((int) $existenteId, ['alumnos' => $alumnosParaUpdate], $preceptorId, 'Actualización de toma de asistencia desde el portal de Preceptor.');
            if (!$ok) {
                return ['ok' => false, 'error' => 'No se pudo actualizar la toma existente.'];
            }
            return ['ok' => true, 'accion' => 'actualizado', 'registroId' => (int) $existenteId];
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("INSERT INTO registros_asistencia
                (curso_id, materia_id, preceptor_id, fecha, modulo_horario, bloque_horario, hora_inicio, hora_fin, turno, estado, ciclo_lectivo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'abierta', ?)");
            $stmt->execute([$cursoId, $materiaId, $preceptorId, $fecha, $moduloHorario, $bloqueHorario, $horaInicio, $horaFin, $turno, $cicloLectivo]);
            $registroId = (int) $db->lastInsertId();

            $stmtDet = $db->prepare("INSERT INTO detalles_asistencia (registro_id, alumno_id, estado) VALUES (?, ?, ?)");
            foreach ($estadosPorAlumno as $alumnoId => $codigo) {
                $estado = $mapaEstados[$codigo] ?? 'ausente';
                $stmtDet->execute([$registroId, (int) $alumnoId, $estado]);
            }

            // Auditoría por SQL directo: Asistencia::registrarAuditoria() es
            // privada y no está expuesta para uso externo.
            $stmtAud = $db->prepare("INSERT INTO auditoria_asistencias
                (registro_id, alumno_id, usuario_id, accion, campo_modificado, valor_anterior, valor_nuevo, observaciones)
                VALUES (?, NULL, ?, 'crear', 'estado', NULL, 'abierta', ?)");
            $stmtAud->execute([$registroId, $preceptorId, 'Toma de asistencia creada desde el portal de Preceptor.']);

            Asistencia::calcularResumenDiario($cursoId, $fecha);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            // Condición de carrera (doble clic / dos pestañas): otra petición
            // ganó la inserción entre el SELECT y el INSERT — el índice único
            // de la tabla es la última línea de defensa contra el duplicado.
            if (str_contains($e->getMessage(), 'uq_curso_materia_fecha_modulo')) {
                return ['ok' => false, 'error' => 'Ya se registró una toma para este mismo curso, materia, fecha y módulo justo ahora. Recargá la pantalla antes de reintentar.'];
            }
            return ['ok' => false, 'error' => 'No se pudo guardar la toma de asistencia: ' . $e->getMessage()];
        }

        try {
            require_once __DIR__ . '/../models/NotificacionFaltas.php';
            foreach (array_keys($estadosPorAlumno) as $alumnoIdAfectado) {
                NotificacionFaltas::verificarYNotificar((int) $alumnoIdAfectado, (int) $preceptorId);
            }
        } catch (Exception $e) {
            // No interrumpe el flujo: la toma ya quedó guardada.
        }

        return ['ok' => true, 'accion' => 'creado', 'registroId' => $registroId];
    }

    /**
     * Arma el horario a mostrar directamente desde hora_inicio/hora_fin del
     * propio registro, SIN depender de Asistencia::getHorarioMostrable().
     * (Ese método no está presente en la copia real de models/Asistencia.php
     * del servidor — ver error "Call to undefined method" — por eso se evita
     * tocar ese archivo compartido con Admin y se resuelve acá, solo para
     * Preceptor, de forma autocontenida.)
     */
    private static function formatHorario(?string $horaInicio, ?string $horaFin): string {
        $inicio = $horaInicio ? substr($horaInicio, 0, 5) : '';
        $fin = $horaFin ? substr($horaFin, 0, 5) : '';
        $texto = trim($inicio . ' - ' . $fin, " -");
        return $texto !== '' ? $texto : '-';
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
        try {
            require_once __DIR__ . '/../models/LogActividad.php';
            LogActividad::registrar($userId, 'ENVIAR_MENSAJE', "Envió un mensaje en la conversación #$conversacionId", 'mensajes', $conversacionId, null, null);
        } catch (Exception $e) {
            // El log de actividad es best-effort: si falla, el mensaje ya
            // quedó guardado y el preceptor no debe ver un error falso.
        }

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
        $asignaciones = self::asignacionesDeCurso($cursoId, $cicloActual);
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

        $resultado = self::registrarTomaPreceptorLocal(
            $cursoId,
            $materiaId,
            $preceptorId,
            $fecha,
            $curso['turno'],
            $asignacion['modulo_horario'],
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

        try {
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
        } catch (Exception $e) {
            // El log de actividad es best-effort: si falla, no debe tirar
            // abajo una asistencia que ya quedó guardada correctamente.
        }

        echo json_encode([
            'ok' => true,
            'accion' => $resultado['accion'],
            'registroId' => $resultado['registroId'],
            'total' => count($estadosValidados),
            'presentes' => count(array_filter($estadosValidados, fn($c) => $c === 'p')),
        ]);
    }

    /**
     * Datos reales de un registro para los modales "Ver Detalle" / "Editar"
     * del Historial (antes esos enlaces no hacían nada o abrían "Tomar
     * Asistencia" en blanco). Reutiliza Asistencia::getEditDataByRegistroId(),
     * que ya arma el estado guardado de cada alumno — no duplica esa lógica.
     * Solo se expone el registro si pertenece a ESTE preceptor (mismo alcance
     * que ya usa portalData() para listar el historial).
     */
    public static function detalleAsistenciaAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];
        $registroId = (int) input('registro_id', 0);

        $registro = Asistencia::getById($registroId);
        if (!$registro || (int) $registro['preceptor_id'] !== $preceptorId) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Registro no encontrado.']);
            return;
        }

        $editData = Asistencia::getEditDataByRegistroId($registroId);
        $horario = self::formatHorario($registro['hora_inicio'], $registro['hora_fin']);

        echo json_encode([
            'ok' => true,
            'registroId' => $registroId,
            'curso' => $registro['anio'] . '° ' . $registro['division'] . '°',
            'materia' => $registro['materia_nombre'],
            'fecha' => self::fechaCorta($registro['fecha']),
            'turno' => ucfirst($registro['curso_turno']) . ' (' . $horario . ')',
            // editable_normal = dentro del período editable por el preceptor
            // (≈30 días desde la fecha, ver Asistencia::estaFueraDelMesActivo).
            // Fuera de ese plazo, el modal se muestra en modo solo lectura.
            'editable' => (bool) $registro['editable_normal'],
            'alumnos' => array_map(fn($a) => [
                'id' => $a['id'],
                'nombre' => $a['apellido'] . ', ' . $a['nombre'],
                'dni' => $a['dni'] ?: '',
                'estado' => self::codigoCortoEstado($a['estado']),
                'estadoLabel' => self::estadoLabel($a['estado']),
            ], $editData['alumnos'] ?? []),
        ]);
    }

    /**
     * Edición real de un registro ya existente desde el Historial del
     * Preceptor (reglas_del_sistema.md §3: "Puede modificar asistencias solo
     * dentro del mes activo" / aclaraciones: límite de 30 días). Reutiliza
     * Asistencia::updateRegistro() — no duplica lógica de auditoría, resumen
     * diario ni recálculo de faltas.
     */
    public static function editarAsistenciaAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $registroId = (int) input('registro_id', 0);
        $estados = $_POST['estados'] ?? [];

        $registro = Asistencia::getById($registroId);
        if (!$registro || (int) $registro['preceptor_id'] !== $preceptorId) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Registro no encontrado.']);
            return;
        }

        if (!$registro['editable_normal']) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Este registro ya no está dentro del período editable (30 días). Pedile a Administración que lo corrija.']);
            return;
        }

        // Set de alumnos válido: SIEMPRE el real (BD) del curso de este
        // registro, nunca el que venga del navegador.
        $alumnosCurso = Curso::getAlumnosByCursoId((int) $registro['curso_id'], (int) $registro['ciclo_lectivo']);
        $idsValidos = array_map(fn($a) => (int) $a['id'], $alumnosCurso);

        $mapaEstados = ['p' => 'presente', 'a' => 'ausente', 't' => 'llegada_tarde', 'ra' => 'retirado_anticipado'];
        $estadosValidados = [];
        foreach ($idsValidos as $alumnoId) {
            $codigo = $estados[$alumnoId] ?? $estados[(string) $alumnoId] ?? null;
            if ($codigo === null || !isset($mapaEstados[$codigo])) {
                continue; // el preceptor puede estar corrigiendo solo algunos alumnos
            }
            $estadosValidados[$alumnoId] = $mapaEstados[$codigo];
        }

        if (empty($estadosValidados)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'No se recibió ningún estado válido para actualizar.']);
            return;
        }

        $ok = Asistencia::updateRegistro($registroId, ['alumnos' => $estadosValidados], $preceptorId, 'Edición de asistencia desde el portal de Preceptor.');
        if (!$ok) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la edición.']);
            return;
        }

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar(
                $preceptorId,
                'EDITAR_ASISTENCIA',
                "Preceptor editó la asistencia del registro #$registroId",
                'registros_asistencia',
                $registroId,
                null,
                null
            );
        } catch (Exception $e) {
            // Best-effort: la edición ya quedó guardada aunque el log falle.
        }

        echo json_encode(['ok' => true, 'registroId' => $registroId]);
    }

    private static function codigoCortoEstado(string $estado): string {
        return match ($estado) {
            'presente' => 'p',
            'llegada_tarde' => 't',
            'retirado_anticipado' => 'ra',
            default => 'a', // ausente, ausente_con_presente, justificado -> más cercano en el set p/a/t/ra del preceptor
        };
    }

    private static function estadoLabel(string $estado): string {
        return match ($estado) {
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'llegada_tarde' => 'Llegada tarde',
            'ausente_con_presente' => 'Ausente con presente',
            'justificado' => 'Justificado',
            'retirado_anticipado' => 'Retirado anticipado',
            default => ucfirst($estado),
        };
    }

    /**
     * "Indicar ausencia" (Perfil del Preceptor): detecta, para la fecha
     * indicada, qué materias/módulos de SUS cursos quedan sin cobertura (no
     * tienen ya una asistencia tomada ese día) y genera un pedido de
     * reemplazo por cada una en `reemplazos_preceptores` — la misma tabla
     * que ya lee DirectivoController::portalData() para "Reemplazos
     * pendientes" (estado 'sin_asignar'). No se toca Directivo: alcanza con
     * insertar en la tabla que ya consulta.
     */
    public static function indicarAusenciaAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $fecha = trim((string) input('fecha', ''));
        $motivo = trim((string) input('motivo', ''));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Fecha inválida.']);
            return;
        }

        try {
            $fechaObj = new DateTimeImmutable($fecha);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Fecha inválida.']);
            return;
        }

        $cicloLectivo = (int) $fechaObj->format('Y');
        $diaSemana = (int) $fechaObj->format('N'); // 1=Lunes...7=Domingo

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT pc.curso_id, c.turno FROM preceptor_cursos pc
                               JOIN cursos c ON c.id = pc.curso_id
                               WHERE pc.preceptor_id = ? AND pc.activo = 1 AND pc.ciclo_lectivo = ? AND c.estado = 'activo'");
        $stmt->execute([$preceptorId, $cicloLectivo]);
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $generados = 0;
        foreach ($cursos as $c) {
            $cursoId = (int) $c['curso_id'];
            $asignaciones = self::asignacionesDeCurso($cursoId, $cicloLectivo);

            foreach ($asignaciones as $a) {
                if ((int) $a['dia_semana'] !== $diaSemana) {
                    continue;
                }

                // ¿Ya se tomó esta materia/módulo ese día? Si ya está
                // cubierta, no genera un pedido de reemplazo.
                $stmtReg = $db->prepare("SELECT id FROM registros_asistencia
                                          WHERE curso_id = ? AND materia_id = ? AND fecha = ? AND modulo_horario = ?
                                            AND estado != 'anulada'
                                          LIMIT 1");
                $stmtReg->execute([$cursoId, $a['materia_id'], $fecha, $a['modulo_horario']]);
                if ($stmtReg->fetchColumn()) {
                    continue;
                }

                // Evitar duplicar el mismo pedido si ya existe uno activo.
                $stmtDup = $db->prepare("SELECT id FROM reemplazos_preceptores
                                          WHERE preceptor_titular_id = ? AND curso_id = ? AND materia_id = ? AND fecha = ?
                                            AND estado != 'cancelado'
                                          LIMIT 1");
                $stmtDup->execute([$preceptorId, $cursoId, $a['materia_id'], $fecha]);
                if ($stmtDup->fetchColumn()) {
                    continue;
                }

                $stmtIns = $db->prepare("INSERT INTO reemplazos_preceptores
                    (preceptor_titular_id, curso_id, materia_id, fecha, turno, motivo, prioridad, estado)
                    VALUES (?, ?, ?, ?, ?, ?, 'normal', 'sin_asignar')");
                $stmtIns->execute([
                    $preceptorId,
                    $cursoId,
                    $a['materia_id'],
                    $fecha,
                    $c['turno'],
                    $motivo !== '' ? $motivo : 'Ausencia de preceptor',
                ]);
                $generados++;
            }
        }

        try {
            require_once __DIR__ . '/../models/LogActividad.php';
            LogActividad::registrar(
                $preceptorId,
                'INDICAR_AUSENCIA',
                "Preceptor indicó ausencia para el $fecha ($generados reemplazo(s) generado(s))",
                'reemplazos_preceptores',
                null,
                null,
                null
            );
        } catch (Exception $e) {
            // Best-effort: la ausencia ya quedó registrada aunque el log falle.
        }

        echo json_encode(['ok' => true, 'generados' => $generados, 'fecha' => $fecha]);
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

    /**
     * Verifica que un registro_asistencia (por su curso_id) sea realmente de
     * un curso a cargo de ESTE preceptor. Nunca se confía en el curso_id que
     * pueda venir implícito en un ID enviado desde el navegador.
     */
    private static function cursoEsDelPreceptor(PDO $db, int $preceptorId, int $cursoId): bool {
        $stmt = $db->prepare("SELECT 1 FROM preceptor_cursos WHERE preceptor_id = ? AND curso_id = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$preceptorId, $cursoId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Aprueba una justificación real (reglas_del_sistema.md §14: "El
     * Preceptor aprueba/rechaza. El justificado suma falta pero queda
     * marcado."). Reutiliza Asistencia::updateRegistro() para pasar el
     * detalle a estado 'justificado' — el mismo método que ya usa Admin y el
     * resto de Preceptor, así que el recálculo de resumen/auditoría es
     * exactamente el mismo, no una lógica paralela. La falta NO desaparece
     * (updateRegistro/calcularFaltaTurno ya suman 1.0 para 'justificado',
     * igual que para 'ausente' — solo cambia que queda marcada).
     */
    public static function aprobarJustificacionAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $justificacionId = (int) input('justificacion_id', 0);
        $comentario = trim((string) input('comentario', ''));

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT j.*, da.registro_id, da.alumno_id AS detalle_alumno_id, reg.curso_id
                               FROM justificaciones j
                               JOIN detalles_asistencia da ON da.id = j.detalle_id
                               JOIN registros_asistencia reg ON reg.id = da.registro_id
                               WHERE j.id = ?");
        $stmt->execute([$justificacionId]);
        $just = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$just || !self::cursoEsDelPreceptor($db, $preceptorId, (int) $just['curso_id'])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Justificación no encontrada.']);
            return;
        }
        if ($just['estado'] !== 'pendiente') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Esta justificación ya fue revisada.']);
            return;
        }

        $db->prepare("UPDATE justificaciones SET estado = 'aprobada', revisado_por = ?, fecha_revision = NOW(), comentario_revisor = ? WHERE id = ?")
           ->execute([$preceptorId, $comentario ?: null, $justificacionId]);

        require_once __DIR__ . '/../models/Asistencia.php';
        Asistencia::updateRegistro(
            (int) $just['registro_id'],
            ['alumnos' => [(int) $just['detalle_alumno_id'] => 'justificado']],
            $preceptorId,
            "Justificación #$justificacionId aprobada."
        );

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($preceptorId, 'APROBAR_JUSTIFICACION', "Aprobó la justificación #$justificacionId", 'justificaciones', $justificacionId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Rechaza una justificación real. A diferencia de aprobar, NO toca
     * detalles_asistencia — el detalle sigue 'ausente' tal como estaba.
     */
    public static function rechazarJustificacionAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $justificacionId = (int) input('justificacion_id', 0);
        $comentario = trim((string) input('comentario', ''));
        if ($comentario === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Ingresá un motivo de rechazo.']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT j.*, reg.curso_id
                               FROM justificaciones j
                               JOIN detalles_asistencia da ON da.id = j.detalle_id
                               JOIN registros_asistencia reg ON reg.id = da.registro_id
                               WHERE j.id = ?");
        $stmt->execute([$justificacionId]);
        $just = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$just || !self::cursoEsDelPreceptor($db, $preceptorId, (int) $just['curso_id'])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Justificación no encontrada.']);
            return;
        }
        if ($just['estado'] !== 'pendiente') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Esta justificación ya fue revisada.']);
            return;
        }

        $db->prepare("UPDATE justificaciones SET estado = 'rechazada', revisado_por = ?, fecha_revision = NOW(), comentario_revisor = ? WHERE id = ?")
           ->execute([$preceptorId, $comentario, $justificacionId]);

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($preceptorId, 'RECHAZAR_JUSTIFICACION', "Rechazó la justificación #$justificacionId: $comentario", 'justificaciones', $justificacionId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true]);
    }

    /**
     * Registra el retiro anticipado de un alumno sobre una asistencia YA
     * tomada hoy (sección "Retiros"). Reutiliza Asistencia::updateRegistro()
     * para el cambio de estado (misma auditoría/resumen que el resto del
     * sistema) y solo agrega directamente la hora_retiro real, que
     * updateRegistro() no gestiona — no se duplica la lógica de faltas.
     */
    public static function registrarRetiroAjax(): void {
        require_role('preceptor');
        header('Content-Type: application/json');
        $preceptorId = (int) $_SESSION['usuario_id'];

        if (!verify_csrf_token(input('csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido. Recargá la página e intentá de nuevo.']);
            return;
        }

        $detalleId = (int) input('detalle_id', 0);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT da.id, da.alumno_id, da.estado, reg.id AS registro_id, reg.curso_id, reg.estado AS registro_estado, reg.fecha
                               FROM detalles_asistencia da
                               JOIN registros_asistencia reg ON reg.id = da.registro_id
                               WHERE da.id = ?");
        $stmt->execute([$detalleId]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detalle || !self::cursoEsDelPreceptor($db, $preceptorId, (int) $detalle['curso_id'])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Registro no encontrado.']);
            return;
        }

        require_once __DIR__ . '/../models/Asistencia.php';
        $estadoCalculado = Asistencia::resolveEstadoRegistro(['estado' => $detalle['registro_estado'], 'fecha' => $detalle['fecha']]);
        if ($estadoCalculado !== 'abierta') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Esta asistencia ya no está dentro del período editable.']);
            return;
        }
        if ($detalle['estado'] === 'retirado_anticipado') {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'Ese alumno ya figura con retiro anticipado.']);
            return;
        }

        $ok = Asistencia::updateRegistro(
            (int) $detalle['registro_id'],
            ['alumnos' => [(int) $detalle['alumno_id'] => 'retirado_anticipado']],
            $preceptorId,
            'Retiro anticipado registrado desde el portal de Preceptor.'
        );
        if (!$ok) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'error' => 'No se pudo registrar el retiro.']);
            return;
        }

        $horaRetiro = (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('H:i:s');
        $db->prepare("UPDATE detalles_asistencia SET hora_retiro = ? WHERE id = ?")->execute([$horaRetiro, $detalleId]);

        require_once __DIR__ . '/../models/LogActividad.php';
        try {
            LogActividad::registrar($preceptorId, 'REGISTRAR_RETIRO', "Registró retiro anticipado (detalle #$detalleId)", 'detalles_asistencia', $detalleId, null, null);
        } catch (Exception $e) {
            // Best-effort.
        }

        echo json_encode(['ok' => true, 'horaRetiro' => substr($horaRetiro, 0, 5)]);
    }
}