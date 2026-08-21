<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Curso.php';
require_once __DIR__ . '/NotificacionFaltas.php';

class Asistencia {
    // Horarios institucionales de cada bloque, tal como figuran en los
    // Excel oficiales (Horarios ciclo básico / ciclo superior). Uso
    // exclusivo de presentación (getBloqueHorarioInfo/getHorarioMostrable):
    // no interviene en calcularResumenDiario/calcularFaltaTurno ni en
    // ninguna otra función de cálculo de faltas.
    private static $bloques_horarios = [
        'mañana' => [
            'primera_hora' => ['inicio' => '07:35', 'fin' => '09:35', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '09:55', 'fin' => '11:55', 'display' => '2da Hora'],
        ],
        'tarde' => [
            'primera_hora' => ['inicio' => '12:55', 'fin' => '14:55', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '15:15', 'fin' => '17:15', 'display' => '2da Hora'],
        ],
        'vespertino' => [
            'primera_hora' => ['inicio' => '17:35', 'fin' => '19:35', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '19:45', 'fin' => '21:45', 'display' => '2da Hora'],
        ],
    ];

    private static function getArgentinaToday(): DateTimeImmutable {
        return new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
    }

    private static function normalizeTurno(?string $turno): string {
        return match ($turno) {
            'noche' => 'vespertino',
            default => $turno ?: 'mañana',
        };
    }

    private static function normalizeRegistroEstado(?string $estado): string {
        return match ($estado) {
            'abierto' => 'abierta',
            'cerrado' => 'cerrada',
            'modificado' => 'modificada',
            'anulado' => 'anulada',
            default => $estado ?: 'abierta',
        };
    }

    private static function normalizeDetalleEstado(?string $estado): string {
        return match ($estado) {
            'tarde' => 'llegada_tarde',
            'retiro_anticipado' => 'retirado_anticipado',
            default => $estado ?: 'ausente',
        };
    }

    private static function estaFueraDelMesActivo(string $fecha, ?DateTimeImmutable $today = null): bool {
        $today = $today ?: self::getArgentinaToday();
        $fechaRegistro = new DateTimeImmutable($fecha, new DateTimeZone('America/Argentina/Buenos_Aires'));
        $fechaCierre = $fechaRegistro->modify('+1 month');
        return $today->setTime(0, 0) >= $fechaCierre->setTime(0, 0);
    }

    public static function resolveEstadoRegistro(array $registro, ?DateTimeImmutable $today = null): string {
        $estadoBase = self::normalizeRegistroEstado($registro['estado'] ?? null);

        if ($estadoBase === 'anulada' || $estadoBase === 'modificada') {
            return $estadoBase;
        }

        return self::estaFueraDelMesActivo($registro['fecha'], $today) ? 'cerrada' : 'abierta';
    }

    private static function decorateRegistro(array $registro, ?DateTimeImmutable $today = null): array {
        $registro['turno'] = self::normalizeTurno($registro['turno'] ?? null);
        $registro['curso_turno'] = self::normalizeTurno($registro['curso_turno'] ?? $registro['turno']);
        $registro['estado_db'] = self::normalizeRegistroEstado($registro['estado'] ?? null);
        $registro['estado_calculado'] = self::resolveEstadoRegistro($registro, $today);
        $registro['editable_normal'] = $registro['estado_calculado'] === 'abierta';
        $registro['editable_admin'] = $registro['estado_calculado'] !== 'anulada';
        return $registro;
    }

    private static function getByIdRaw(int $id): ?array {
        $db = Database::getConnection();
        $sql = "SELECT ra.*, 
                       c.anio, c.division, c.turno as curso_turno, c.aula,
                       m.nombre as materia_nombre, 
                       u.nombre as preceptor_nombre, u.apellido as preceptor_apellido
                FROM registros_asistencia ra
                JOIN cursos c ON ra.curso_id = c.id
                JOIN materias m ON ra.materia_id = m.id
                JOIN usuarios u ON ra.preceptor_id = u.id
                WHERE ra.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        return $registro ?: null;
    }

    public static function getAll($filters = [], $page_num = 1, $per_page = 10) {
        $db = Database::getConnection();
        $sql = "SELECT ra.*, 
                       c.anio, c.division, c.turno as curso_turno, 
                       m.nombre as materia_nombre, 
                       u.nombre as preceptor_nombre, u.apellido as preceptor_apellido
                FROM registros_asistencia ra
                JOIN cursos c ON ra.curso_id = c.id
                JOIN materias m ON ra.materia_id = m.id
                JOIN usuarios u ON ra.preceptor_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['curso_id'])) {
            $sql .= " AND ra.curso_id = ?";
            $params[] = $filters['curso_id'];
        }

        if (!empty($filters['turno'])) {
            $sql .= " AND ra.turno = ?";
            $params[] = self::normalizeTurno($filters['turno']);
        }

        if (!empty($filters['preceptor_id'])) {
            $sql .= " AND ra.preceptor_id = ?";
            $params[] = $filters['preceptor_id'];
        }

        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND ra.fecha >= ?";
            $params[] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND ra.fecha <= ?";
            $params[] = $filters['fecha_hasta'];
        }

        $sql .= " ORDER BY ra.fecha DESC, ra.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $today = self::getArgentinaToday();
        $all_registros = array_map(
            fn($registro) => self::decorateRegistro($registro, $today),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

        if (!empty($filters['estado'])) {
            $estadoFiltro = self::normalizeRegistroEstado($filters['estado']);
            $all_registros = array_values(array_filter(
                $all_registros,
                fn($registro) => $registro['estado_calculado'] === $estadoFiltro
            ));
        }

        $total = count($all_registros);
        
        if ($per_page === 'all') {
            $registros = $all_registros;
            $final_page_num = 1;
            $total_pages = 1;
        } else {
            $per_page = max(1, (int) $per_page);
            $page_num = max(1, (int) $page_num);
            $offset = ($page_num - 1) * $per_page;
            $registros = array_slice($all_registros, $offset, $per_page);
            $final_page_num = $page_num;
            $total_pages = (int)ceil($total / $per_page);
        }

        return [
            'registros' => $registros,
            'total' => $total,
            'page_num' => $final_page_num,
            'per_page' => $per_page,
            'total_pages' => $total_pages
        ];
    }

    public static function getById($id) {
        $registro = self::getByIdRaw((int) $id);
        return $registro ? self::decorateRegistro($registro) : null;
    }

    public static function getDetallesByRegistroId($registroId) {
        $db = Database::getConnection();
        $sql = "SELECT da.*, u.nombre, u.apellido, u.dni
                FROM detalles_asistencia da
                JOIN usuarios u ON da.alumno_id = u.id
                WHERE da.registro_id = ?
                ORDER BY u.apellido, u.nombre";
        $stmt = $db->prepare($sql);
        $stmt->execute([$registroId]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as &$detalle) {
            $detalle['estado'] = self::normalizeDetalleEstado($detalle['estado'] ?? null);
        }
        unset($detalle);

        return $detalles;
    }

    public static function getBloqueHorarioInfo($turno, $bloque) {
        $turno = self::normalizeTurno($turno);
        if (isset(self::$bloques_horarios[$turno][$bloque])) {
            return self::$bloques_horarios[$turno][$bloque];
        }
        return null;
    }

    /**
     * A qué bloque institucional ('primera_hora'/'segunda_hora') del turno
     * pertenece una hora de inicio real de asignaciones_materias. Solo
     * clasificación por horario de INICIO (igual criterio que ya usa
     * getHorarioMostrable para decidir "materia extendida" comparando contra
     * el fin oficial del bloque): una materia que arranca dentro de la
     * primera mitad del turno se registra como 'primera_hora' aunque después
     * se extienda más allá del recreo hacia la segunda.
     */
    public static function inferirBloqueHorario(string $turno, string $horaInicio): string {
        $turno = self::normalizeTurno($turno);
        $segunda = self::$bloques_horarios[$turno]['segunda_hora']['inicio'] ?? null;
        if ($segunda !== null && substr($horaInicio, 0, 5) >= $segunda) {
            return 'segunda_hora';
        }
        return 'primera_hora';
    }

    /**
     * Horario a mostrar para un registro de asistencia puntual: el
     * institucional del bloque (turno+bloque_horario) para una materia
     * normal, o el horario real guardado en el registro cuando se trata de
     * una materia extendida (ocupa más de un bloque, ej. 17:35–20:45).
     *
     * No reemplaza ciegamente hora_inicio/hora_fin: los compara contra el
     * horario oficial de SU PROPIO bloque. Si hora_fin real no supera el fin
     * oficial de ese bloque, es una materia "normal" y se muestra el
     * horario institucional (corrige datos genéricos/semilla que no
     * reflejan el turno real, ej. tarde mostrando horarios de mañana). Si
     * hora_fin real supera el fin oficial del bloque, se conserva el
     * horario real del registro sin recortarlo.
     */
    public static function getHorarioMostrable($turno, $bloque, ?string $horaInicioReg, ?string $horaFinReg): string {
        $oficial = self::getBloqueHorarioInfo($turno, $bloque);

        if (!$oficial) {
            $texto = trim(($horaInicioReg ?? '') . ' - ' . ($horaFinReg ?? ''), " -");
            return $texto !== '' ? $texto : '-';
        }

        if ($horaInicioReg === null || $horaFinReg === null || $horaInicioReg === '' || $horaFinReg === '') {
            return $oficial['inicio'] . ' - ' . $oficial['fin'];
        }

        $finRegistro = substr($horaFinReg, 0, 5);
        if ($finRegistro > $oficial['fin']) {
            // Materia extendida: se respeta el horario real guardado.
            return substr($horaInicioReg, 0, 5) . ' - ' . $finRegistro;
        }

        // Materia normal dentro de su propio bloque: horario institucional.
        return $oficial['inicio'] . ' - ' . $oficial['fin'];
    }

    public static function getEditDataByRegistroId(int $registroId): ?array {
        $registro = self::getById($registroId);
        if (!$registro) {
            return null;
        }

        $detalles = self::getDetallesByRegistroId($registroId);
        $detallesMap = [];
        foreach ($detalles as $detalle) {
            $detallesMap[(int) $detalle['alumno_id']] = $detalle;
        }

        $alumnosCurso = Curso::getAlumnosByCursoId($registro['curso_id'], (int) $registro['ciclo_lectivo']);
        $alumnos = [];

        foreach ($alumnosCurso as $alumno) {
            $detalle = $detallesMap[(int) $alumno['id']] ?? null;
            $alumnos[] = [
                'id' => (int) $alumno['id'],
                'dni' => $alumno['dni'] ?? null,
                'apellido' => $alumno['apellido'],
                'nombre' => $alumno['nombre'],
                'estado' => self::normalizeDetalleEstado($detalle['estado'] ?? null),
                'detalle_id' => $detalle['id'] ?? null,
                'modificado' => (int) ($detalle['modificado'] ?? 0),
            ];
        }

        return [
            'registro' => $registro,
            'alumnos' => $alumnos,
        ];
    }

    public static function getEditDataMap(array $registros): array {
        $map = [];
        foreach ($registros as $registro) {
            $editData = self::getEditDataByRegistroId((int) $registro['id']);
            if ($editData) {
                $map[(string) $registro['id']] = $editData;
            }
        }
        return $map;
    }

    public static function updateRegistro($registroId, $data, $adminId, $observaciones = '') {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $oldRegistro = self::getByIdRaw((int) $registroId);
            if (!$oldRegistro) {
                $db->rollBack();
                return false;
            }

            if (self::normalizeRegistroEstado($oldRegistro['estado']) === 'anulada') {
                $db->rollBack();
                return false;
            }

            $estadoAnteriorVisual = self::resolveEstadoRegistro($oldRegistro);
            $hayCambios = false;
            $fields = [];
            $params = [];

            foreach (['curso_id', 'materia_id', 'preceptor_id', 'fecha', 'bloque_horario', 'observaciones'] as $campo) {
                if (!array_key_exists($campo, $data)) {
                    continue;
                }

                $valorNuevo = $data[$campo];
                $valorAnterior = $oldRegistro[$campo] ?? null;

                if ((string) $valorAnterior === (string) $valorNuevo) {
                    continue;
                }

                $fields[] = "{$campo} = ?";
                $params[] = $valorNuevo;
                $hayCambios = true;
                self::registrarAuditoria($registroId, null, $adminId, 'editar', $campo, $valorAnterior, $valorNuevo, $observaciones);
            }

            if (!empty($fields)) {
                $params[] = $registroId;
                $sql = "UPDATE registros_asistencia SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }

            if (isset($data['alumnos']) && is_array($data['alumnos'])) {
                $oldDetalles = self::getDetallesByRegistroId($registroId);
                $oldDetallesMap = [];
                foreach ($oldDetalles as $detalle) {
                    $oldDetallesMap[(int) $detalle['alumno_id']] = $detalle;
                }

                foreach ($data['alumnos'] as $alumnoId => $estado) {
                    $alumnoId = (int) $alumnoId;
                    $estado = self::normalizeDetalleEstado($estado);
                    $oldEstado = isset($oldDetallesMap[$alumnoId])
                        ? self::normalizeDetalleEstado($oldDetallesMap[$alumnoId]['estado'])
                        : null;

                    if ($oldEstado === $estado) {
                        continue;
                    }

                    $hayCambios = true;

                    if (isset($oldDetallesMap[$alumnoId])) {
                        $stmt = $db->prepare("UPDATE detalles_asistencia SET estado = ?, modificado = 1, updated_at = NOW() WHERE registro_id = ? AND alumno_id = ?");
                        $stmt->execute([$estado, $registroId, $alumnoId]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO detalles_asistencia (registro_id, alumno_id, estado, modificado) VALUES (?, ?, ?, 1)");
                        $stmt->execute([$registroId, $alumnoId, $estado]);
                    }

                    self::registrarAuditoria($registroId, $alumnoId, $adminId, 'editar', 'estado', $oldEstado, $estado, $observaciones);
                }
            }

            if ($hayCambios) {
                $nuevoEstado = 'modificada';

                $stmt = $db->prepare("UPDATE registros_asistencia SET estado = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$nuevoEstado, $registroId]);

                if ($estadoAnteriorVisual !== $nuevoEstado) {
                    self::registrarAuditoria($registroId, null, $adminId, 'editar', 'estado', $estadoAnteriorVisual, $nuevoEstado, $observaciones);
                }
            }

            $cursoIds = array_unique(array_filter([
                (int) $oldRegistro['curso_id'],
                isset($data['curso_id']) ? (int) $data['curso_id'] : null,
            ]));
            $fechas = array_unique(array_filter([
                $oldRegistro['fecha'],
                $data['fecha'] ?? null,
            ]));

            foreach ($cursoIds as $cursoId) {
                foreach ($fechas as $fecha) {
                    self::calcularResumenDiario($cursoId, $fecha);
                }
            }

            $db->commit();

            // Alerta de límite de faltas: "best effort", después del commit,
            // para no afectar la edición de asistencia ya guardada si falla.
            if (isset($data['alumnos']) && is_array($data['alumnos'])) {
                foreach (array_keys($data['alumnos']) as $alumnoIdAfectado) {
                    try {
                        NotificacionFaltas::verificarYNotificar((int) $alumnoIdAfectado, (int) $adminId);
                    } catch (Exception $e) {
                        // No interrumpe el flujo: la edición ya quedó guardada.
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function anularRegistro($registroId, $adminId, $observaciones = '') {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $registro = self::getByIdRaw((int) $registroId);
            if (!$registro) {
                $db->rollBack();
                return false;
            }

            $stmt = $db->prepare("UPDATE registros_asistencia SET estado = 'anulada', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$registroId]);

            self::registrarAuditoria($registroId, null, $adminId, 'anular', 'estado', self::resolveEstadoRegistro($registro), 'anulada', $observaciones);
            self::calcularResumenDiario($registro['curso_id'], $registro['fecha']);

            $db->commit();

            foreach (self::getDetallesByRegistroId($registroId) as $detalle) {
                try {
                    NotificacionFaltas::verificarYNotificar((int) $detalle['alumno_id'], (int) $adminId);
                } catch (Exception $e) {
                    // No interrumpe el flujo: la anulación ya quedó guardada.
                }
            }

            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Crea (o, si ya existe una toma abierta para el mismo contexto, actualiza
     * de forma controlada vía updateRegistro) una toma real de asistencia
     * tomada por un preceptor desde su portal. Reutiliza updateRegistro(),
     * calcularResumenDiario() y registrarAuditoria() — no duplica ninguna
     * lógica de cálculo de faltas ni de resumen.
     *
     * $estadosPorAlumno: [alumno_id => 'p'|'a'|'t'|'ra'] ya validado por el
     * controller (alumnos reales del curso, todos presentes en el array).
     *
     * @return array{ok:bool, accion?:string, registroId?:int, error?:string}
     */
    public static function registrarTomaPreceptor(
        int $cursoId,
        int $materiaId,
        int $preceptorId,
        string $fecha,
        string $turno,
        string $horaInicio,
        string $horaFin,
        int $cicloLectivo,
        array $estadosPorAlumno
    ): array {
        $mapaEstados = ['p' => 'presente', 'a' => 'ausente', 't' => 'llegada_tarde', 'ra' => 'retirado_anticipado'];
        $bloqueHorario = self::inferirBloqueHorario($turno, $horaInicio);
        $moduloHorario = $bloqueHorario === 'primera_hora' ? '1ra Hora' : '2da Hora';

        $db = Database::getConnection();

        // ¿Ya existe una toma para este contexto exacto? (mismo índice único
        // que ya protege la tabla: curso_id + materia_id + fecha + modulo_horario)
        $stmt = $db->prepare("SELECT id FROM registros_asistencia WHERE curso_id = ? AND materia_id = ? AND fecha = ? AND modulo_horario = ? LIMIT 1");
        $stmt->execute([$cursoId, $materiaId, $fecha, $moduloHorario]);
        $existenteId = $stmt->fetchColumn();

        if ($existenteId) {
            $registroExistente = self::getByIdRaw((int) $existenteId);
            if (!$registroExistente || self::resolveEstadoRegistro($registroExistente) !== 'abierta') {
                return ['ok' => false, 'error' => 'Ya existe una toma de asistencia finalizada para este curso, materia, fecha y módulo. No se puede volver a cargar; pedile a Administración que la edite si hace falta corregirla.'];
            }

            $alumnosParaUpdate = [];
            foreach ($estadosPorAlumno as $alumnoId => $codigo) {
                $alumnosParaUpdate[(int) $alumnoId] = $mapaEstados[$codigo] ?? 'ausente';
            }
            $ok = self::updateRegistro((int) $existenteId, ['alumnos' => $alumnosParaUpdate], $preceptorId, 'Actualización de toma de asistencia desde el portal de Preceptor.');
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
            $stmt->execute([$cursoId, $materiaId, $preceptorId, $fecha, $moduloHorario, $bloqueHorario, $horaInicio, $horaFin, self::normalizeTurno($turno), $cicloLectivo]);
            $registroId = (int) $db->lastInsertId();

            $stmtDet = $db->prepare("INSERT INTO detalles_asistencia (registro_id, alumno_id, estado) VALUES (?, ?, ?)");
            foreach ($estadosPorAlumno as $alumnoId => $codigo) {
                $estado = $mapaEstados[$codigo] ?? 'ausente';
                $stmtDet->execute([$registroId, (int) $alumnoId, $estado]);
            }

            self::registrarAuditoria($registroId, null, $preceptorId, 'crear', 'estado', null, 'abierta', 'Toma de asistencia creada desde el portal de Preceptor.');

            self::calcularResumenDiario($cursoId, $fecha);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            // Condición de carrera (doble clic / dos pestañas): otra petición
            // ganó la inserción entre el SELECT y el INSERT — el índice único
            // de la tabla es la última línea de defensa contra el duplicado.
            if (str_contains($e->getMessage(), 'uq_curso_materia_fecha_modulo')) {
                return ['ok' => false, 'error' => 'Ya se registró una toma para este mismo curso, materia, fecha y módulo justo ahora. Recargá la pantalla antes de reintentar.'];
            }
            return ['ok' => false, 'error' => 'No se pudo guardar la toma de asistencia.'];
        }

        foreach (array_keys($estadosPorAlumno) as $alumnoIdAfectado) {
            try {
                NotificacionFaltas::verificarYNotificar((int) $alumnoIdAfectado, (int) $preceptorId);
            } catch (Exception $e) {
                // No interrumpe el flujo: la toma ya quedó guardada.
            }
        }

        return ['ok' => true, 'accion' => 'creado', 'registroId' => $registroId];
    }

    private static function registrarAuditoria($registroId, $alumnoId, $usuarioId, $accion, $campoModificado, $valorAnterior, $valorNuevo, $observaciones = '') {
        $db = Database::getConnection();
        $sql = "INSERT INTO auditoria_asistencias (registro_id, alumno_id, usuario_id, accion, campo_modificado, valor_anterior, valor_nuevo, observaciones)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$registroId, $alumnoId, $usuarioId, $accion, $campoModificado, $valorAnterior, $valorNuevo, $observaciones]);
    }

    public static function calcularResumenDiario($cursoId, $fecha) {
        $db = Database::getConnection();
        $curso = Curso::getById($cursoId);
        if (!$curso) {
            return false;
        }

        $stmt = $db->prepare("SELECT id, curso_id, fecha, bloque_horario, turno FROM registros_asistencia
                              WHERE curso_id = ? AND fecha = ? AND estado != 'anulada'");
        $stmt->execute([$cursoId, $fecha]);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fuente de verdad de los turnos del curso: curso_turnos (config real),
        // NO lo que casualmente tenga registrado ese día puntual. Un curso de
        // doble turno sigue siendo de doble turno aunque, un día particular,
        // solo se haya tomado asistencia en uno de los dos (el otro turno
        // simplemente no aporta detalle ese día, no cambia su condición).
        $turnos = self::getTurnosOficialesCurso($cursoId, $curso);
        $esDobleTurno = count($turnos) > 1;
        // Doble turno: cada turno vale como máximo 0.5 (dos turnos ausentes
        // suman 1.0 el día). Turno único: cada turno (el único que hay) vale
        // como máximo 1.0. Nunca depende de cuántas materias/bloques haya.
        $topePorTurno = $esDobleTurno ? 0.5 : 1.0;

        $alumnos = Curso::getAlumnosByCursoId($cursoId, (int) $curso['ciclo_lectivo']);

        foreach ($alumnos as $alumno) {
            foreach ($turnos as $turno) {
                $registrosTurno = array_values(array_filter(
                    $registros,
                    fn($reg) => self::normalizeTurno($reg['turno'] ?? null) === $turno
                ));

                [$faltas, $tieneJustificacion, $detalleCalculo] = self::calcularFaltaTurno(
                    $registrosTurno,
                    $alumno['id'],
                    $topePorTurno
                );

                self::guardarResumenDiario($alumno['id'], $cursoId, $fecha, $turno, $faltas, $tieneJustificacion, $detalleCalculo);
            }
        }

        return true;
    }

    /**
     * Turnos oficiales de un curso según curso_turnos (uno o dos turnos).
     * Si el curso no tuviera fila en curso_turnos (no debería pasar con los
     * datos actuales), cae de forma defensiva al turno único de cursos.turno.
     */
    private static function getTurnosOficialesCurso($cursoId, array $curso): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT turno FROM curso_turnos WHERE curso_id = ? ORDER BY es_turno_principal DESC, id ASC");
        $stmt->execute([$cursoId]);
        $turnos = array_map(fn($t) => self::normalizeTurno($t), $stmt->fetchAll(PDO::FETCH_COLUMN));
        $turnos = array_values(array_unique($turnos));

        if (empty($turnos)) {
            $turnos = [self::normalizeTurno($curso['turno'] ?? null)];
        }

        return $turnos;
    }

    /**
     * Calcula la falta correspondiente a UN turno (no una hora de reloj: es
     * el bloque "primera_hora"/"segunda_hora" del turno, cada uno pudiendo
     * corresponder a una materia distinta o a una misma materia larga que
     * ocupe ambos — el peso se calcula por turno, nunca se multiplica por
     * cantidad de materias/bloques registrados).
     *
     * $tope es 1.0 para curso de un solo turno, 0.5 para cada turno de un
     * curso de doble turno (ver calcularResumenDiario).
     *
     * @return array{0: float, 1: bool, 2: string} [falta, tieneJustificacion, detalleCalculo]
     */
    private static function calcularFaltaTurno(array $registrosTurno, $alumnoId, float $tope): array {
        $sumaBruta = 0;
        $tieneJustificacion = false;
        $detalleCalculo = [];
        $estadosPorBloque = [];

        foreach ($registrosTurno as $reg) {
            $detalle = self::getDetalleAlumno($reg['id'], $alumnoId);
            if (!$detalle) {
                continue;
            }

            $estado = self::normalizeDetalleEstado($detalle['estado'] ?? null);
            $detalleCalculo[] = "Bloque {$reg['bloque_horario']}: $estado";
            $estadosPorBloque[$reg['bloque_horario']][] = $estado;

            switch ($estado) {
                case 'ausente':
                case 'justificado':
                    $sumaBruta += 1;
                    if ($estado === 'justificado') {
                        $tieneJustificacion = true;
                    }
                    break;
                case 'ausente_con_presente':
                    $sumaBruta += 1;
                    break;
                case 'llegada_tarde':
                    $sumaBruta += 0.25;
                    break;
                case 'retirado_anticipado':
                    $sumaBruta += 0.5;
                    break;
            }
        }

        if (empty($detalleCalculo)) {
            return [0.0, false, ''];
        }

        // Caso especial (reglas_del_sistema.md §8, "casos especiales dentro
        // de un mismo turno"): si el primer bloque fue puramente "presente"
        // y el segundo puramente "ausente", el turno vale 0.5 — no la suma
        // genérica (que daría 1.0) — porque se interpreta como pérdida de la
        // segunda mitad del turno, mismo peso que un retiro anticipado. El
        // caso inverso (1ra ausente, 2da presente) ya coincide con la suma
        // genérica (1.0) y no necesita excepción.
        $primeraPura = self::estadoPuroDeBloque($estadosPorBloque['primera_hora'] ?? null);
        $segundaPura = self::estadoPuroDeBloque($estadosPorBloque['segunda_hora'] ?? null);

        if ($primeraPura === 'presente' && $segundaPura === 'ausente') {
            $faltas = min(0.5, $tope);
        } else {
            $faltas = min($sumaBruta, $tope);
        }

        return [$faltas, $tieneJustificacion, implode(' | ', $detalleCalculo)];
    }

    /**
     * Devuelve el estado si TODOS los registros de ese bloque (normalmente
     * uno, salvo alguna anomalía de datos con más de una materia en el mismo
     * bloque) comparten el mismo estado; null si no hay datos o están
     * mezclados (en cuyo caso no se aplica el caso especial, se usa la suma
     * genérica como respaldo seguro).
     */
    private static function estadoPuroDeBloque(?array $estados): ?string {
        if (empty($estados)) {
            return null;
        }
        $unicos = array_unique($estados);
        return count($unicos) === 1 ? $unicos[0] : null;
    }

    private static function getDetalleAlumno($registroId, $alumnoId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM detalles_asistencia WHERE registro_id = ? AND alumno_id = ? LIMIT 1");
        $stmt->execute([$registroId, $alumnoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private static function guardarResumenDiario($alumnoId, $cursoId, $fecha, $turno, $faltas, $tieneJustificacion, $detalleCalculo) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM resumen_asistencia_diaria WHERE alumno_id = ? AND curso_id = ? AND fecha = ? AND turno = ? LIMIT 1");
        $stmt->execute([$alumnoId, $cursoId, $fecha, $turno]);
        $existe = $stmt->fetch();

        if ($existe) {
            $stmt = $db->prepare("UPDATE resumen_asistencia_diaria 
                                  SET faltas_total = ?, tiene_justificacion = ?, detalle_calculo = ?, updated_at = NOW()
                                  WHERE id = ?");
            $stmt->execute([$faltas, $tieneJustificacion, $detalleCalculo, $existe['id']]);
        } else {
            $stmt = $db->prepare("INSERT INTO resumen_asistencia_diaria 
                                  (alumno_id, curso_id, fecha, turno, faltas_total, tiene_justificacion, detalle_calculo)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$alumnoId, $cursoId, $fecha, $turno, $faltas, $tieneJustificacion, $detalleCalculo]);
        }
    }

    public static function getResumenDiario($alumnoId, $cursoId, $fecha, $turno) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM resumen_asistencia_diaria 
                              WHERE alumno_id = ? AND curso_id = ? AND fecha = ? AND turno = ? LIMIT 1");
        $stmt->execute([$alumnoId, $cursoId, $fecha, self::normalizeTurno($turno)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAuditoriaByRegistroId($registroId) {
        $db = Database::getConnection();
        $sql = "SELECT aa.*, u.nombre as admin_nombre, u.apellido as admin_apellido,
                       a.nombre as alumno_nombre, a.apellido as alumno_apellido
                FROM auditoria_asistencias aa
                JOIN usuarios u ON aa.usuario_id = u.id
                LEFT JOIN usuarios a ON aa.alumno_id = a.id
                WHERE aa.registro_id = ?
                ORDER BY aa.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$registroId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
