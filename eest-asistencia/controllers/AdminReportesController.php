<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminReportesController {
    public static function index() {
        require_role('admin');
        
        $filters = [
            'fecha_desde' => input('fecha_desde', ''),
            'fecha_hasta' => input('fecha_hasta', ''),
            'curso_id' => input('curso_id', ''),
            'division' => input('division', ''),
            'turno' => input('turno', ''),
            'especialidad_id' => input('especialidad_id', ''),
            'materia_id' => input('materia_id', ''),
            'preceptor_id' => input('preceptor_id', ''),
            'alumno_busqueda' => input('alumno_busqueda', ''),
            'alumno_id' => input('alumno_id', ''),
            'tipo_registro' => input('tipo_registro', ''),
            'registro_estado' => input('registro_estado', ''),
            'bloque_horario' => input('bloque_horario', ''),
            'rango_faltas' => input('rango_faltas', ''),
            'rango_asistencia' => input('rango_asistencia', ''),
            'estado_justificacion' => input('estado_justificacion', ''),
            'incluir_anuladas' => input('incluir_anuladas', 'no')
        ];

        $generar = input('generar', '');
        $page_num = intval(input('page_num', 1));
        $per_page = input('per_page', 10);
        if ($per_page !== 'all') {
            $per_page = intval($per_page);
            if (!in_array($per_page, [10, 25, 50])) {
                $per_page = 10;
            }
        }

        $modo_reporte = input('modo_reporte', 'resumen');
        $ordenamiento = input('ordenamiento', 'alumno_asc');
        $anio_grafico = input('anio_grafico', '');

        $datos_reporte = [];
        $stats = [];
        $pagination = [];

        // Obtener datos del reporte solo si "generar" está presente
        if ($generar) {
            if ($modo_reporte === 'resumen') {
                $datos_reporte = Reporte::getResumenPorAlumno($filters, $page_num, $per_page, $ordenamiento);
            } else {
                $datos_reporte = Reporte::getReporteDetallado($filters, $page_num, $per_page, $ordenamiento);
            }
            
            $pagination = [
                'total' => $datos_reporte['total'],
                'page_num' => $datos_reporte['page_num'],
                'per_page' => $datos_reporte['per_page'],
                'total_pages' => $datos_reporte['total_pages']
            ];
        }
        
        $stats = Reporte::getStats($filters);
        
        // Nuevos datos para la vista
        $report_stats = Reporte::getReportStats($filters);
        $presentismo_por_division = Reporte::getPresentismoPorDivision($filters, $anio_grafico);
        $inasistencias_por_curso = Reporte::getInasistenciasPorCurso($filters);
        $anios_disponibles = Reporte::getAniosDisponibles();
        $alumnos_disponibles = Reporte::getAlumnosDisponibles($filters);

        $cursos = Reporte::getCursos();
        $especialidades = Reporte::getEspecialidades();
        $materias = Reporte::getMaterias();
        $preceptores = Reporte::getPreceptores();

        return [
            'generar' => $generar,
            'modo_reporte' => $modo_reporte,
            'filters' => $filters,
            'ordenamiento' => $ordenamiento,
            'anio_grafico' => $anio_grafico,
            'datos_reporte' => $datos_reporte,
            'stats' => $stats,
            'report_stats' => $report_stats,
            'presentismo_por_division' => $presentismo_por_division,
            'inasistencias_por_curso' => $inasistencias_por_curso,
            'cursos' => $cursos,
            'especialidades' => $especialidades,
            'materias' => $materias,
            'preceptores' => $preceptores,
            'anios_disponibles' => $anios_disponibles,
            'alumnos_disponibles' => $alumnos_disponibles,
            'pagination' => $pagination
        ];
    }

    public static function handlePost() {
        require_role('admin');
        $action = input('action', '');

        if ($action === 'exportar_csv') {
            self::exportarCSV();
            exit;
        }
    }

    public static function autocompleteAlumnos() {
        require_role('admin');
        $search = input('search', '');
        $cursoId = input('curso_id', '');
        $filters = [];
        if (!empty($cursoId)) {
            $filters['curso_id'] = $cursoId;
        }
        $alumnos = Reporte::getAlumnosDisponibles($filters, $search);
        header('Content-Type: application/json');
        echo json_encode($alumnos);
        exit;
    }

    private static function exportarCSV() {
        $filters = [
            'fecha_desde' => input('fecha_desde', ''),
            'fecha_hasta' => input('fecha_hasta', ''),
            'curso_id' => input('curso_id', ''),
            'division' => input('division', ''),
            'turno' => input('turno', ''),
            'especialidad_id' => input('especialidad_id', ''),
            'materia_id' => input('materia_id', ''),
            'preceptor_id' => input('preceptor_id', ''),
            'alumno_busqueda' => input('alumno_busqueda', ''),
            'alumno_id' => input('alumno_id', ''),
            'tipo_registro' => input('tipo_registro', ''),
            'registro_estado' => input('registro_estado', ''),
            'bloque_horario' => input('bloque_horario', ''),
            'rango_faltas' => input('rango_faltas', ''),
            'rango_asistencia' => input('rango_asistencia', ''),
            'estado_justificacion' => input('estado_justificacion', ''),
            'incluir_anuladas' => input('incluir_anuladas', 'no')
        ];
        $modo_reporte = input('modo_reporte', 'resumen');
        $ordenamiento = input('ordenamiento', 'alumno_asc');

        // Obtener todos los registros sin paginar
        $datos_reporte = $modo_reporte === 'resumen' 
            ? Reporte::getResumenPorAlumno($filters, 1, 'all', $ordenamiento)
            : Reporte::getReporteDetallado($filters, 1, 'all', $ordenamiento);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="reporte_asistencia_' . date('Y-m-d_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // BOM para UTF-8 (para Excel)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($modo_reporte === 'resumen') {
            fputcsv($output, ['Alumno', 'Curso/División', 'Especialidad', 'Turno', 'Presentes', 'Ausentes', 'Llegadas Tarde', 'Ausentes con Presente', 'Justificados', 'Retiros Anticipados', 'Total Faltas', 'Porcentaje Asistencia'], ';');
            foreach ($datos_reporte['alumnos'] as $alumno) {
                fputcsv($output, [
                    $alumno['nombre'] . ' ' . $alumno['apellido'],
                    $alumno['anio'] . '° ' . $alumno['division'],
                    $alumno['especialidad_nombre'] ?? '-',
                    $alumno['turno'] ?? '-',
                    $alumno['presentes'],
                    $alumno['ausentes'],
                    $alumno['llegadas_tarde'],
                    $alumno['ausentes_con_presente'],
                    $alumno['justificados'],
                    $alumno['retiros_anticipados'],
                    $alumno['faltas_total'],
                    $alumno['porcentaje_asistencia'] . '%'
                ], ';');
            }
        } else {
            fputcsv($output, ['Fecha', 'Alumno', 'Curso/División', 'Especialidad', 'Turno', 'Materia', 'Bloque Horario', 'Horario', 'Preceptor', 'Estado', 'Falta Diaria', 'Observaciones'], ';');
            foreach ($datos_reporte['registros'] as $registro) {
                $bloque_horario = $registro['bloque_horario'] === 'primera_hora' ? '1ra Hora' : '2da Hora';
                $horario = $registro['hora_inicio'] . ' - ' . $registro['hora_fin'];
                fputcsv($output, [
                    $registro['fecha'],
                    $registro['alumno_nombre'] . ' ' . $registro['alumno_apellido'],
                    $registro['anio'] . '° ' . $registro['division'],
                    $registro['especialidad_nombre'] ?? '-',
                    $registro['turno'],
                    $registro['materia_nombre'],
                    $bloque_horario,
                    $horario,
                    $registro['preceptor_nombre'] . ' ' . $registro['preceptor_apellido'],
                    $registro['estado'],
                    $registro['faltas_total'],
                    $registro['detalle_observaciones'] ?? '-'
                ], ';');
            }
        }

        fclose($output);
        exit;
    }
}
