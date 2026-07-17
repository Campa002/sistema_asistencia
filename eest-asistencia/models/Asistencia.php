<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Curso.php';

class Asistencia {
    private static $bloques_horarios = [
        'mañana' => [
            'primera_hora' => ['inicio' => '07:35', 'fin' => '09:35', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '09:45', 'fin' => '11:55', 'display' => '2da Hora'],
        ],
        'tarde' => [
            'primera_hora' => ['inicio' => '12:55', 'fin' => '14:55', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '15:15', 'fin' => '17:15', 'display' => '2da Hora'],
        ],
        'vespertino' => [
            'primera_hora' => ['inicio' => '17:35', 'fin' => '19:35', 'display' => '1ra Hora'],
            'segunda_hora' => ['inicio' => '19:55', 'fin' => '21:45', 'display' => '2da Hora'],
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
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
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

        $turnos = [];
        foreach ($registros as $registro) {
            $turnos[] = self::normalizeTurno($registro['turno'] ?? null);
        }
        if (empty($turnos)) {
            $turnos[] = self::normalizeTurno($curso['turno'] ?? null);
        }
        $turnos = array_values(array_unique($turnos));

        $alumnos = Curso::getAlumnosByCursoId($cursoId, (int) $curso['ciclo_lectivo']);

        foreach ($alumnos as $alumno) {
            foreach ($turnos as $turno) {
                $faltas = 0;
                $tieneJustificacion = false;
                $detalleCalculo = [];

                foreach ($registros as $reg) {
                    if (self::normalizeTurno($reg['turno'] ?? null) !== $turno) {
                        continue;
                    }

                    $detalle = self::getDetalleAlumno($reg['id'], $alumno['id']);
                    if (!$detalle) {
                        continue;
                    }

                    $estado = self::normalizeDetalleEstado($detalle['estado'] ?? null);
                    $detalleCalculo[] = "Bloque {$reg['bloque_horario']}: $estado";

                    switch ($estado) {
                        case 'ausente':
                        case 'justificado':
                            $faltas += 1;
                            if ($estado === 'justificado') {
                                $tieneJustificacion = true;
                            }
                            break;
                        case 'ausente_con_presente':
                            $faltas += 1;
                            break;
                        case 'llegada_tarde':
                            $faltas += 0.25;
                            break;
                        case 'retirado_anticipado':
                            $faltas += 0.5;
                            break;
                    }
                }

                $faltas = min($faltas, 1);
                self::guardarResumenDiario($alumno['id'], $cursoId, $fecha, $turno, $faltas, $tieneJustificacion, implode(' | ', $detalleCalculo));
            }
        }

        return true;
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
