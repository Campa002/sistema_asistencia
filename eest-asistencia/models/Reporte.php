<?php
require_once __DIR__ . '/../config/database.php';

class Reporte {
    private static $estados_validos = ['presente', 'ausente', 'llegada_tarde', 'ausente_con_presente', 'justificado', 'retirado_anticipado'];
    private static $ordenamientos_validos = ['fecha_desc', 'fecha_asc', 'alumno_asc', 'curso_asc', 'faltas_desc'];

    public static function getReporteDetallado($filters = [], $page_num = 1, $per_page = 10, $ordenamiento = 'fecha_desc') {
        $db = Database::getConnection();
        $params = [];

        // Build base query for data
        $sql = "SELECT det.id as detalle_id,
                    reg.id as registro_id,
                    reg.fecha,
                    alu.nombre as alumno_nombre,
                    alu.apellido as alumno_apellido,
                    cur.anio,
                    cur.division,
                    esp.nombre as especialidad_nombre,
                    reg.turno,
                    mat.nombre as materia_nombre,
                    reg.bloque_horario,
                    reg.hora_inicio,
                    reg.hora_fin,
                    pre.nombre as preceptor_nombre,
                    pre.apellido as preceptor_apellido,
                    det.estado,
                    det.hora_llegada,
                    det.hora_retiro,
                    reg.estado as registro_estado,
                    det.observaciones as detalle_observaciones,
                    j.estado as justificacion_estado,
                    j.tipo as justificacion_tipo,
                    j.motivo as justificacion_motivo,
                    res.faltas_total
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                JOIN materias mat ON reg.materia_id = mat.id
                JOIN usuarios pre ON reg.preceptor_id = pre.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                LEFT JOIN justificaciones j ON j.detalle_id = det.id
                LEFT JOIN (
                    SELECT alumno_id, curso_id, fecha, turno, faltas_total
                    FROM resumen_asistencia_diaria
                    GROUP BY alumno_id, curso_id, fecha, turno
                ) res ON res.alumno_id = alu.id
                    AND res.curso_id = cur.id
                    AND res.fecha = reg.fecha
                    AND res.turno = reg.turno
                WHERE 1=1";

        // Build total query separately
        $total_sql = "SELECT COUNT(DISTINCT det.id) as total
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                JOIN materias mat ON reg.materia_id = mat.id
                JOIN usuarios pre ON reg.preceptor_id = pre.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                LEFT JOIN (
                    SELECT alumno_id, curso_id, fecha, turno, faltas_total
                    FROM resumen_asistencia_diaria
                    GROUP BY alumno_id, curso_id, fecha, turno
                ) res ON res.alumno_id = alu.id 
                    AND res.curso_id = cur.id 
                    AND res.fecha = reg.fecha 
                    AND res.turno = reg.turno
                WHERE 1=1";
        
        $total_params = [];
        
        // Apply filters to both queries
        $sql = self::aplicarFiltrosComunes($sql, $filters, $params);
        $total_sql = self::aplicarFiltrosComunes($total_sql, $filters, $total_params);

        // Ordenamiento
        $sql .= self::buildOrdenamientoSQL($ordenamiento);

        // Execute total query first
        $total_stmt = $db->prepare($total_sql);
        $total_stmt->execute($total_params);
        $total = intval($total_stmt->fetch(PDO::FETCH_ASSOC)['total']);

        if ($per_page !== 'all') {
            $offset = ($page_num - 1) * $per_page;
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $per_page;
            $params[] = $offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_pages = $per_page !== 'all' ? ceil($total / $per_page) : 1;

        return [
            'registros' => $registros,
            'total' => $total,
            'page_num' => $page_num,
            'per_page' => $per_page,
            'total_pages' => $total_pages
        ];
    }

    public static function getResumenPorAlumno($filters = [], $page_num = 1, $per_page = 10, $ordenamiento = 'alumno_asc') {
        $db = Database::getConnection();
        $params = [];
        $total_params = [];

        // Build base queries. Las faltas NO se traen acá: se calculan aparte
        // (ver self::getFaltasPorAlumno) para evitar inflarlas cuando un
        // alumno tiene más de un detalle (materia/bloque) el mismo día+turno
        // — el join directo a resumen_asistencia_diaria duplicaría esa fila
        // una vez por cada detalle de ese día, multiplicando el SUM.
        $sql = "SELECT alu.id as alumno_id,
                    alu.nombre,
                    alu.apellido,
                    cur.anio,
                    cur.division,
                    esp.nombre as especialidad_nombre,
                    cur.turno,
                    SUM(CASE WHEN det.estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes,
                    SUM(CASE WHEN det.estado = 'llegada_tarde' THEN 1 ELSE 0 END) as llegadas_tarde,
                    SUM(CASE WHEN det.estado = 'ausente_con_presente' THEN 1 ELSE 0 END) as ausentes_con_presente,
                    SUM(CASE WHEN det.estado = 'justificado' THEN 1 ELSE 0 END) as justificados,
                    SUM(CASE WHEN det.estado = 'retirado_anticipado' THEN 1 ELSE 0 END) as retiros_anticipados,
                    COUNT(DISTINCT CONCAT(reg.fecha, reg.turno)) as dias_total
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                WHERE 1=1";

        $total_sql = "SELECT COUNT(DISTINCT alu.id) as total FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                WHERE 1=1";

        // Apply filters to both queries
        $sql = self::aplicarFiltrosComunes($sql, $filters, $params);
        $total_sql = self::aplicarFiltrosComunes($total_sql, $filters, $total_params, false);

        // Group and order
        $sql .= " GROUP BY alu.id, alu.nombre, alu.apellido, cur.anio, cur.division, esp.nombre, cur.turno";
        if ($ordenamiento === 'alumno_asc' || $ordenamiento !== 'faltas_desc') {
            $sql .= " ORDER BY alu.apellido ASC, alu.nombre ASC";
        }
        // 'faltas_desc' se ordena en PHP más abajo, porque faltas_total ya no
        // es una columna de esta consulta (ver nota arriba).

        // Los filtros de rango de faltas/asistencia, y el ordenamiento por
        // faltas, dependen de un valor (faltas_total) que se calcula en PHP
        // después de fusionar la consulta de faltas real — por lo que en
        // esos casos hace falta traer TODOS los alumnos que cumplen el resto
        // de los filtros y recién ahí paginar en PHP. En el caso simple se
        // pagina directamente en SQL (más eficiente) y el total real sale de
        // $total_sql.
        $requierePosprocesoPHP = !empty($filters['rango_faltas']) || !empty($filters['rango_asistencia']) || $ordenamiento === 'faltas_desc';

        if ($requierePosprocesoPHP) {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            self::mergeFaltasPorAlumno($alumnos, $filters);

            if ($ordenamiento === 'faltas_desc') {
                usort($alumnos, fn($a, $b) => $b['faltas_total'] <=> $a['faltas_total']);
            }

            $alumnos = array_values(self::filtrarAlumnosPorRangos($alumnos, $filters));

            $total = count($alumnos);
            $total_pages = $per_page !== 'all' ? max(1, (int) ceil($total / $per_page)) : 1;

            if ($per_page !== 'all') {
                $offset = ($page_num - 1) * $per_page;
                $alumnos = array_slice($alumnos, $offset, $per_page);
            }
        } else {
            // Total real (sin recortar) desde la query de conteo dedicada
            $total_stmt = $db->prepare($total_sql);
            $total_stmt->execute($total_params);
            $total = intval($total_stmt->fetch(PDO::FETCH_ASSOC)['total']);

            if ($per_page !== 'all') {
                $offset = ($page_num - 1) * $per_page;
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = $per_page;
                $params[] = $offset;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            self::mergeFaltasPorAlumno($alumnos, $filters);

            $total_pages = $per_page !== 'all' ? max(1, (int) ceil($total / $per_page)) : 1;
        }

        return [
            'alumnos' => $alumnos,
            'total' => $total,
            'page_num' => $page_num,
            'per_page' => $per_page,
            'total_pages' => $total_pages
        ];
    }

    /**
     * Calcula faltas_total real por alumno (sin fan-out) y calcula, en el
     * mismo paso, el porcentaje de asistencia. Modifica $alumnos por
     * referencia agregando 'faltas_total' y 'porcentaje_asistencia'.
     */
    private static function mergeFaltasPorAlumno(array &$alumnos, array $filters): void {
        $faltasPorAlumno = self::getFaltasPorAlumno($filters);

        foreach ($alumnos as &$alumno) {
            $faltas_total = $faltasPorAlumno[$alumno['alumno_id']] ?? 0.0;
            $alumno['faltas_total'] = $faltas_total;

            $dias_total = $alumno['dias_total'];
            if ($dias_total > 0) {
                $asistencias = max($dias_total - $faltas_total, 0);
                $alumno['porcentaje_asistencia'] = round(($asistencias / $dias_total) * 100, 2);
            } else {
                $alumno['porcentaje_asistencia'] = 0;
            }
        }
        unset($alumno);
    }

    /**
     * Devuelve un mapa [alumno_id => faltas_total] calculado a partir de
     * resumen_asistencia_diaria, deduplicando primero los pares
     * (alumno, curso, fecha, turno) que cumplen los filtros — así una
     * jornada con más de un detalle (una materia por bloque) no infla la
     * falta de ese día al sumarla más de una vez.
     */
    private static function getFaltasPorAlumno(array $filters): array {
        $db = Database::getConnection();
        $params = [];

        $sql = "SELECT dias.alumno_id, COALESCE(SUM(res.faltas_total), 0) as faltas_total
                FROM (
                    SELECT DISTINCT det.alumno_id, reg.curso_id, reg.fecha, reg.turno
                    FROM detalles_asistencia det
                    JOIN registros_asistencia reg ON det.registro_id = reg.id
                    JOIN usuarios alu ON det.alumno_id = alu.id
                    JOIN cursos cur ON reg.curso_id = cur.id
                    WHERE 1=1";
        $sql = self::aplicarFiltrosComunes($sql, $filters, $params, false);
        $sql .= "
                ) dias
                LEFT JOIN resumen_asistencia_diaria res
                    ON res.alumno_id = dias.alumno_id
                    AND res.curso_id = dias.curso_id
                    AND res.fecha = dias.fecha
                    AND res.turno = dias.turno
                GROUP BY dias.alumno_id";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $mapa = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $mapa[$fila['alumno_id']] = (float) $fila['faltas_total'];
        }
        return $mapa;
    }

    private static function filtrarAlumnosPorRangos($alumnos, $filters) {
        if (empty($filters['rango_faltas']) && empty($filters['rango_asistencia'])) {
            return $alumnos;
        }

        return array_filter($alumnos, function($alumno) use ($filters) {
            $pasa_faltas = true;
            if (!empty($filters['rango_faltas'])) {
                $faltas = $alumno['faltas_total'];
                switch ($filters['rango_faltas']) {
                    case 'sin_faltas':
                        $pasa_faltas = ($faltas == 0);
                        break;
                    case '1-9':
                        $pasa_faltas = ($faltas >= 1 && $faltas <= 9);
                        break;
                    case '10-19':
                        $pasa_faltas = ($faltas >= 10 && $faltas <= 19);
                        break;
                    case '20-27':
                        $pasa_faltas = ($faltas >= 20 && $faltas <= 27);
                        break;
                    case '28+':
                        $pasa_faltas = ($faltas >= 28);
                        break;
                }
            }

            $pasa_asistencia = true;
            if (!empty($filters['rango_asistencia'])) {
                $asistencia = $alumno['porcentaje_asistencia'];
                switch ($filters['rango_asistencia']) {
                    case '90+':
                        $pasa_asistencia = ($asistencia >= 90);
                        break;
                    case '80-89.99':
                        $pasa_asistencia = ($asistencia >= 80 && $asistencia < 90);
                        break;
                    case '70-79.99':
                        $pasa_asistencia = ($asistencia >= 70 && $asistencia < 80);
                        break;
                    case 'menos70':
                        $pasa_asistencia = ($asistencia < 70);
                        break;
                }
            }

            return $pasa_faltas && $pasa_asistencia;
        });
    }

    private static function aplicarFiltrosComunes($sql, $filters, &$params, $incluir_rangos = true) {
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND reg.fecha >= ?";
            $params[] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND reg.fecha <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        if (!empty($filters['curso_id'])) {
            $sql .= " AND reg.curso_id = ?";
            $params[] = $filters['curso_id'];
        }
        if (!empty($filters['division'])) {
            $sql .= " AND cur.division = ?";
            $params[] = $filters['division'];
        }
        if (!empty($filters['turno'])) {
            $sql .= " AND reg.turno = ?";
            $params[] = $filters['turno'];
        }
        if (!empty($filters['especialidad_id'])) {
            $sql .= " AND cur.especialidad_id = ?";
            $params[] = $filters['especialidad_id'];
        }
        if (!empty($filters['materia_id'])) {
            $sql .= " AND reg.materia_id = ?";
            $params[] = $filters['materia_id'];
        }
        if (!empty($filters['preceptor_id'])) {
            $sql .= " AND reg.preceptor_id = ?";
            $params[] = $filters['preceptor_id'];
        }
        if (!empty($filters['alumno_busqueda'])) {
            $busqueda = '%' . $filters['alumno_busqueda'] . '%';
            $sql .= " AND (alu.nombre LIKE ? OR alu.apellido LIKE ?)";
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        if (!empty($filters['alumno_id'])) {
            $sql .= " AND alu.id = ?";
            $params[] = $filters['alumno_id'];
        }
        if (!empty($filters['tipo_registro'])) {
            $sql .= " AND det.estado = ?";
            $params[] = $filters['tipo_registro'];
        }
        if (!empty($filters['registro_estado'])) {
            $sql .= " AND reg.estado = ?";
            $params[] = $filters['registro_estado'];
        }
        if (!empty($filters['bloque_horario'])) {
            $sql .= " AND reg.bloque_horario = ?";
            $params[] = $filters['bloque_horario'];
        }
        if (empty($filters['incluir_anuladas']) || $filters['incluir_anuladas'] !== 'si') {
            $sql .= " AND reg.estado != 'anulada'";
        }

        return $sql;
    }

    public static function getStats($filters = []) {
        $db = Database::getConnection();
        $params = [];

        // faltas_total NO se trae acá: se calcula aparte con self::getFaltasTotalPoblacion()
        // para no inflarla cuando un alumno tiene más de un detalle el mismo día+turno.
        $sql = "SELECT
                    COUNT(DISTINCT det.id) as registros,
                    COUNT(DISTINCT det.alumno_id) as alumnos,
                    SUM(CASE WHEN det.estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes,
                    SUM(CASE WHEN det.estado = 'llegada_tarde' THEN 1 ELSE 0 END) as llegadas_tarde,
                    SUM(CASE WHEN det.estado = 'retirado_anticipado' THEN 1 ELSE 0 END) as retiros_anticipados
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                WHERE 1=1";

        $sql = self::aplicarFiltrosComunes($sql, $filters, $params, false);

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['faltas_total'] = self::getFaltasTotalPoblacion($filters);

        return $stats;
    }

    private static function buildOrdenamientoSQL($ordenamiento) {
        $ordenamientos_permitidos = [
            'fecha_desc' => ' ORDER BY reg.fecha DESC',
            'fecha_asc' => ' ORDER BY reg.fecha ASC',
            'alumno_asc' => ' ORDER BY alu.apellido ASC, alu.nombre ASC',
            'curso_asc' => ' ORDER BY cur.anio ASC, cur.division ASC',
            'faltas_desc' => ' ORDER BY faltas_total DESC'
        ];

        return $ordenamientos_permitidos[$ordenamiento] ?? $ordenamientos_permitidos['fecha_desc'];
    }

    public static function getCursos() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.id, c.anio, c.division, c.turno, e.nombre as especialidad_nombre 
                             FROM cursos c 
                             LEFT JOIN especialidades e ON c.especialidad_id = e.id 
                             ORDER BY c.anio, c.division");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEspecialidades() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre FROM especialidades ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMaterias() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre FROM materias ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPreceptores() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'preceptor' AND estado = 'activo' ORDER BY apellido, nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAniosDisponibles() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT DISTINCT anio FROM cursos ORDER BY anio");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    public static function getAlumnosDisponibles($filters = [], $search = '') {
        $db = Database::getConnection();
        $params = [];

        $sql = "SELECT DISTINCT alu.id, alu.nombre, alu.apellido, alu.dni,
                       cur.anio, cur.division, esp.nombre as especialidad
                FROM usuarios alu
                JOIN alumno_cursos ac ON alu.id = ac.alumno_id
                JOIN cursos cur ON ac.curso_id = cur.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                WHERE alu.rol = 'alumno' AND alu.estado = 'activo'";

        if (!empty($filters['curso_id'])) {
            $sql .= " AND cur.id = ?";
            $params[] = $filters['curso_id'];
        }

        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $sql .= " AND (alu.nombre LIKE ? OR alu.apellido LIKE ? OR alu.dni LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY alu.apellido, alu.nombre LIMIT 20";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getReportStats($filters = []) {
        $db = Database::getConnection();
        $params = [];

        // faltas_total NO se trae acá: se calcula aparte con self::getFaltasTotalPoblacion()
        // para no inflarla cuando un alumno tiene más de un detalle el mismo día+turno
        // (ver nota en getResumenPorAlumno/getFaltasPorAlumno).
        // Usa aplicarFiltrosComunes() (los mismos filtros que el resto de Reportes,
        // incluida división, bloque horario e "incluir anuladas") para que las
        // KPI cards y la tabla de resultados respondan siempre a la misma población.
        $sql = "SELECT
                    COUNT(DISTINCT det.id) as registros,
                    COALESCE(SUM(CASE WHEN det.estado = 'llegada_tarde' THEN 1 ELSE 0 END), 0) as llegadas_tarde,
                    COUNT(DISTINCT j.id) as justificaciones_total,
                    COALESCE(SUM(CASE WHEN det.estado = 'retirado_anticipado' THEN 1 ELSE 0 END), 0) as retiros_anticipados,
                    COALESCE(SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END), 0) as ausentes,
                    COUNT(DISTINCT CONCAT(reg.fecha, reg.turno, det.alumno_id)) as jornadas_computables
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN usuarios alu ON det.alumno_id = alu.id
                JOIN cursos cur ON reg.curso_id = cur.id
                LEFT JOIN justificaciones j ON j.detalle_id = det.id
                WHERE 1=1";
        $sql = self::aplicarFiltrosComunes($sql, $filters, $params, false);

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $faltas_total = self::getFaltasTotalPoblacion($filters);

        $asistencia_general = 0;
        if ($stats['jornadas_computables'] > 0) {
            $asistencia_general = round((($stats['jornadas_computables'] - $stats['ausentes']) / $stats['jornadas_computables']) * 100, 1);
        }

        return [
            'asistencia_general' => $asistencia_general,
            'llegadas_tarde' => $stats['llegadas_tarde'],
            'justificaciones_total' => $stats['justificaciones_total'],
            'retiros_anticipados' => $stats['retiros_anticipados'],
            'faltas_total' => $faltas_total
        ];
    }

    /**
     * Suma total de faltas (resumen_asistencia_diaria) para toda la
     * población filtrada, deduplicando primero los pares
     * (alumno, curso, fecha, turno) — mismo criterio que getFaltasPorAlumno,
     * pero sin agrupar por alumno (para las KPI cards de Reportes).
     */
    private static function getFaltasTotalPoblacion(array $filters): float {
        $db = Database::getConnection();
        $params = [];

        $sql = "SELECT COALESCE(SUM(res.faltas_total), 0) as faltas_total
                FROM (
                    SELECT DISTINCT det.alumno_id, reg.curso_id, reg.fecha, reg.turno
                    FROM detalles_asistencia det
                    JOIN registros_asistencia reg ON det.registro_id = reg.id
                    JOIN usuarios alu ON det.alumno_id = alu.id
                    JOIN cursos cur ON reg.curso_id = cur.id
                    WHERE 1=1";
        $sql = self::aplicarFiltrosComunes($sql, $filters, $params, false);
        $sql .= "
                ) dias
                LEFT JOIN resumen_asistencia_diaria res
                    ON res.alumno_id = dias.alumno_id
                    AND res.curso_id = dias.curso_id
                    AND res.fecha = dias.fecha
                    AND res.turno = dias.turno";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['faltas_total'];
    }

    public static function getPresentismoPorDivision($filters = [], $anio_grafico = '') {
        $db = Database::getConnection();
        $params = [];
        $where = ["reg.estado != 'anulada'"];

        if (!empty($filters['fecha_desde'])) {
            $where[] = "reg.fecha >= ?";
            $params[] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $where[] = "reg.fecha <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        if (!empty($filters['curso_id'])) {
            $where[] = "reg.curso_id = ?";
            $params[] = $filters['curso_id'];
        }
        if (!empty($filters['turno'])) {
            $where[] = "reg.turno = ?";
            $params[] = $filters['turno'];
        }
        if (!empty($filters['especialidad_id'])) {
            $where[] = "cur.especialidad_id = ?";
            $params[] = $filters['especialidad_id'];
        }
        if (!empty($filters['materia_id'])) {
            $where[] = "reg.materia_id = ?";
            $params[] = $filters['materia_id'];
        }
        if (!empty($filters['preceptor_id'])) {
            $where[] = "reg.preceptor_id = ?";
            $params[] = $filters['preceptor_id'];
        }
        if (!empty($filters['alumno_id'])) {
            $where[] = "det.alumno_id = ?";
            $params[] = $filters['alumno_id'];
        }
        if (!empty($anio_grafico)) {
            $where[] = "cur.anio = ?";
            $params[] = $anio_grafico;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    cur.id,
                    CONCAT(cur.anio, '° ', cur.division) as division,
                    esp.nombre as especialidad,
                    COUNT(DISTINCT CONCAT(reg.fecha, reg.turno, det.alumno_id)) as jornadas_total,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes_total
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN cursos cur ON reg.curso_id = cur.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                WHERE $whereClause
                GROUP BY cur.id, cur.anio, cur.division, esp.nombre
                ORDER BY division";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $presentismo = [];
        foreach ($result as $row) {
            $porcentaje = 0;
            if ($row['jornadas_total'] > 0) {
                $porcentaje = round((($row['jornadas_total'] - $row['ausentes_total']) / $row['jornadas_total']) * 100, 1);
            }
            $presentismo[] = [
                'division' => $row['division'],
                'especialidad' => $row['especialidad'],
                'porcentaje' => $porcentaje
            ];
        }

        return $presentismo;
    }

    public static function getInasistenciasPorCurso($filters = []) {
        $db = Database::getConnection();
        $params = [];
        $where = ["reg.estado != 'anulada'"];

        if (!empty($filters['fecha_desde'])) {
            $where[] = "reg.fecha >= ?";
            $params[] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $where[] = "reg.fecha <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        if (!empty($filters['curso_id'])) {
            $where[] = "reg.curso_id = ?";
            $params[] = $filters['curso_id'];
        }
        if (!empty($filters['turno'])) {
            $where[] = "reg.turno = ?";
            $params[] = $filters['turno'];
        }
        if (!empty($filters['especialidad_id'])) {
            $where[] = "cur.especialidad_id = ?";
            $params[] = $filters['especialidad_id'];
        }
        if (!empty($filters['alumno_id'])) {
            $where[] = "det.alumno_id = ?";
            $params[] = $filters['alumno_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    cur.id,
                    CONCAT(cur.anio, '° ', cur.division) as division,
                    esp.nombre as especialidad,
                    cur.turno,
                    COUNT(DISTINCT CONCAT(reg.fecha, reg.turno, det.alumno_id)) as jornadas_total,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes_total
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN cursos cur ON reg.curso_id = cur.id
                LEFT JOIN especialidades esp ON cur.especialidad_id = esp.id
                WHERE $whereClause
                GROUP BY cur.id, cur.anio, cur.division, esp.nombre, cur.turno
                ORDER BY ausentes_total DESC
                LIMIT 6";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $inasistencias = [];
        $mayor_ausentismo = null;
        $mejor_asistencia = null;

        foreach ($result as $row) {
            $porcentaje = 0;
            if ($row['jornadas_total'] > 0) {
                $porcentaje = round(($row['ausentes_total'] / $row['jornadas_total']) * 100, 1);
            }
            $curso_data = [
                'division' => $row['division'],
                'especialidad' => $row['especialidad'],
                'turno' => $row['turno'],
                'porcentaje_ausentismo' => $porcentaje
            ];
            $inasistencias[] = $curso_data;

            if (!$mayor_ausentismo || $porcentaje > $mayor_ausentismo['porcentaje_ausentismo']) {
                $mayor_ausentismo = $curso_data;
            }
            if (!$mejor_asistencia || $porcentaje < $mejor_asistencia['porcentaje_ausentismo']) {
                $mejor_asistencia = $curso_data;
            }
        }

        return [
            'cursos' => $inasistencias,
            'mayor_ausentismo' => $mayor_ausentismo,
            'mejor_asistencia' => $mejor_asistencia
        ];
    }

    /**
     * Total de faltas acumuladas de UN alumno, sin filtro de fecha (todo el
     * histórico en resumen_asistencia_diaria). Es una simple SUMA sobre esa
     * tabla — no hay riesgo de fan-out porque ya tiene una sola fila por
     * (alumno, curso, fecha, turno). Usado para el límite institucional de
     * faltas (Gestión Técnica); no modifica ninguna consulta existente.
     */
    public static function getFaltasTotalAlumno(int $alumnoId): float {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COALESCE(SUM(faltas_total), 0) as total FROM resumen_asistencia_diaria WHERE alumno_id = ?");
        $stmt->execute([$alumnoId]);
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
