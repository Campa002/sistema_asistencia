<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminReportesController {
    public static function index() {
        require_role('admin');

        $filters = self::buildFiltersFromRequest();

        $generar = input('generar', '');
        $page_num = max(1, intval(input('page_num', 1)));
        $per_page = input('per_page', 10);
        if ($per_page !== 'all') {
            $per_page = intval($per_page);
            if (!in_array($per_page, [10, 25, 50])) {
                $per_page = 10;
            }
        }

        // La vista principal de Reportes siempre muestra el resumen por
        // alumno (el detalle completo de todos los alumnos ya no se lista
        // de forma masiva; se consulta por alumno puntual vía el modal
        // "Ver asistencia detallada", ver self::detalleAlumno()).
        $modo_reporte = 'resumen';
        $ordenamiento = input('ordenamiento', 'alumno_asc');
        $anio_grafico = input('anio_grafico', '');

        $datos_reporte = [];
        $stats = [];
        $pagination = [];

        // Obtener datos del reporte solo si "generar" está presente
        if ($generar) {
            $datos_reporte = self::obtenerDatosReporte($filters, $modo_reporte, $ordenamiento, $page_num, $per_page);

            // Si page_num quedó fuera de rango (p.ej. al cambiar de filtro/modo con
            // menos páginas que antes), lo corregimos y volvemos a traer los datos
            // para no mostrar "sin resultados" de forma engañosa.
            if ($per_page !== 'all' && $datos_reporte['total_pages'] > 0 && $page_num > $datos_reporte['total_pages']) {
                $page_num = (int) $datos_reporte['total_pages'];
                $datos_reporte = self::obtenerDatosReporte($filters, $modo_reporte, $ordenamiento, $page_num, $per_page);
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
            'pagination' => $pagination,
            'pdf_disponible' => self::pdfDisponible()
        ];
    }

    public static function handlePost() {
        require_role('admin');

        $token = input('csrf_token', '');
        if (!verify_csrf_token($token)) {
            http_response_code(403);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Token de seguridad inválido. Recargue la página e intente nuevamente.';
            exit;
        }

        $action = input('action', '');

        if ($action === 'exportar_csv') {
            self::exportarCSV();
            exit;
        }
        if ($action === 'exportar_pdf') {
            self::exportarPDF();
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

    /**
     * Endpoint AJAX para el modal "Ver asistencia detallada" de la tabla de
     * Reportes: trae únicamente las asistencias del alumno_id indicado,
     * respetando el resto de los filtros vigentes en pantalla (período,
     * curso, turno, materia, etc. — se leen del mismo querystring que la
     * página ya tiene aplicado). No usa lógica nueva de cálculo: reutiliza
     * Reporte::getReporteDetallado(), la misma consulta que ya se usa en
     * CSV/PDF en modo detallado.
     */
    public static function detalleAlumno() {
        require_role('admin');

        $alumnoId = input('alumno_id', '');
        if (empty($alumnoId)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'alumno_id es requerido']);
            exit;
        }

        $filters = self::buildFiltersFromRequest();
        $filters['alumno_id'] = $alumnoId;

        $datos = Reporte::getReporteDetallado($filters, 1, 'all', 'fecha_desc');
        $estadoNombres = self::tipoRegistroNombres();
        $bloqueNombres = self::bloqueNombres();
        $justificacionEstados = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];
        $justificacionTipos = ['medica' => 'Médica', 'personal' => 'Personal', 'academica' => 'Académica', 'otro' => 'Otro'];

        $registros = array_map(function ($r) use ($estadoNombres, $bloqueNombres, $justificacionEstados, $justificacionTipos) {
            $justificacion = '-';
            if (!empty($r['justificacion_estado'])) {
                $tipoLabel = $justificacionTipos[$r['justificacion_tipo']] ?? $r['justificacion_tipo'];
                $justificacion = ($justificacionEstados[$r['justificacion_estado']] ?? $r['justificacion_estado']) . ' (' . $tipoLabel . ')';
            }

            return [
                'fecha' => e(date('d/m/Y', strtotime($r['fecha']))),
                'curso' => e($r['anio'] . '° ' . $r['division']),
                'especialidad' => e($r['especialidad_nombre'] ?? '-'),
                'turno' => e(ucfirst($r['turno'] ?? '-')),
                'materia' => e($r['materia_nombre']),
                'bloque' => e($bloqueNombres[$r['bloque_horario']] ?? $r['bloque_horario']),
                'horario' => e(Asistencia::getHorarioMostrable($r['turno'], $r['bloque_horario'], $r['hora_inicio'], $r['hora_fin'])),
                'preceptor' => e($r['preceptor_apellido'] . ', ' . $r['preceptor_nombre']),
                'estado' => e($estadoNombres[$r['estado']] ?? $r['estado']),
                'hora_llegada' => e($r['hora_llegada'] ? substr($r['hora_llegada'], 0, 5) : '-'),
                'hora_retiro' => e($r['hora_retiro'] ? substr($r['hora_retiro'], 0, 5) : '-'),
                'justificacion' => e($justificacion),
                'falta_diaria' => $r['faltas_total'] !== null ? (float) $r['faltas_total'] : 0,
                'observaciones' => e($r['detalle_observaciones'] ?? '-')
            ];
        }, $datos['registros']);

        header('Content-Type: application/json');
        echo json_encode([
            'total' => $datos['total'],
            'registros' => $registros
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lee todos los filtros de Reportes desde el request (GET o POST).
     * Compartido entre index(), exportarCSV() y exportarPDF() para no
     * duplicar el mismo array literal en cada método.
     */
    private static function buildFiltersFromRequest(): array {
        $periodo = input('periodo', 'cuatrimestre');
        [$fecha_desde, $fecha_hasta] = self::resolverFechasPorPeriodo(
            $periodo,
            input('fecha_desde', ''),
            input('fecha_hasta', '')
        );

        return [
            'periodo' => $periodo,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
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
            'incluir_anuladas' => input('incluir_anuladas', 'no')
        ];
    }

    /**
     * Traduce el filtro "Periodo" (Hoy / Esta Semana / Este Mes / Cuatrimestre /
     * Ciclo Lectivo / Personalizado) a un rango fecha_desde/fecha_hasta real,
     * que es lo que Reporte::aplicarFiltrosComunes() ya sabe filtrar.
     * "Personalizado" (o vacío) respeta las fechas cargadas manualmente.
     */
    private static function resolverFechasPorPeriodo(string $periodo, string $fecha_desde, string $fecha_hasta): array {
        if ($periodo === '' || $periodo === 'personalizado') {
            return [$fecha_desde, $fecha_hasta];
        }

        $tz = new DateTimeZone('America/Argentina/Buenos_Aires');
        $hoy = new DateTimeImmutable('now', $tz);

        switch ($periodo) {
            case 'hoy':
                $desde = $hasta = $hoy;
                break;
            case 'semana':
                $desde = $hoy->modify('monday this week');
                $hasta = $hoy->modify('sunday this week');
                break;
            case 'mes':
                $desde = $hoy->modify('first day of this month');
                $hasta = $hoy->modify('last day of this month');
                break;
            case 'cuatrimestre':
                // El año se divide en 3 cuatrimestres de 4 meses: Ene-Abr, May-Ago, Sep-Dic.
                $mesInicio = intdiv(((int) $hoy->format('n')) - 1, 4) * 4 + 1;
                $desde = $hoy->setDate((int) $hoy->format('Y'), $mesInicio, 1);
                $hasta = $desde->modify('+3 months')->modify('last day of this month');
                break;
            case 'ciclo':
                $desde = $hoy->setDate((int) $hoy->format('Y'), 1, 1);
                $hasta = $hoy->setDate((int) $hoy->format('Y'), 12, 31);
                break;
            default:
                return [$fecha_desde, $fecha_hasta];
        }

        return [$desde->format('Y-m-d'), $hasta->format('Y-m-d')];
    }

    /**
     * Obtiene los datos del reporte (resumen o detallado) según el modo.
     * Compartido entre index(), exportarCSV() y exportarPDF().
     */
    private static function obtenerDatosReporte(array $filters, string $modo_reporte, string $ordenamiento, $page_num = 1, $per_page = 10): array {
        return $modo_reporte === 'resumen'
            ? Reporte::getResumenPorAlumno($filters, $page_num, $per_page, $ordenamiento)
            : Reporte::getReporteDetallado($filters, $page_num, $per_page, $ordenamiento);
    }

    private static function tipoRegistroNombres(): array {
        return [
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'llegada_tarde' => 'Llegada Tarde',
            'ausente_con_presente' => 'Ausente con Presente',
            'justificado' => 'Justificado',
            'retirado_anticipado' => 'Retirado Anticipado'
        ];
    }

    private static function bloqueNombres(): array {
        return [
            'primera_hora' => '1ra Hora',
            'segunda_hora' => '2da Hora'
        ];
    }

    private static function exportarCSV() {
        $filters = self::buildFiltersFromRequest();
        $modo_reporte = input('modo_reporte', 'resumen');
        $ordenamiento = input('ordenamiento', 'alumno_asc');

        // Obtener todos los registros sin paginar
        $datos_reporte = self::obtenerDatosReporte($filters, $modo_reporte, $ordenamiento, 1, 'all');

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
                $horario = Asistencia::getHorarioMostrable($registro['turno'], $registro['bloque_horario'], $registro['hora_inicio'], $registro['hora_fin']);
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

    private static function pdfDisponible(): bool {
        return file_exists(__DIR__ . '/../vendor/autoload.php');
    }

    private static function exportarPDF() {
        if (!self::pdfDisponible()) {
            http_response_code(501);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'La generación de PDF no está disponible en este servidor (falta la librería Dompdf en vendor/). Contacte al administrador técnico.';
            exit;
        }
        require_once __DIR__ . '/../vendor/autoload.php';

        $filters = self::buildFiltersFromRequest();
        $modo_reporte = input('modo_reporte', 'resumen');
        $ordenamiento = input('ordenamiento', 'alumno_asc');

        $datos_reporte = self::obtenerDatosReporte($filters, $modo_reporte, $ordenamiento, 1, 'all');

        $html = self::buildPdfHtml($modo_reporte, $filters, $datos_reporte);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', $modo_reporte === 'detallado' ? 'landscape' : 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            $canvas->get_width() - 130,
            $canvas->get_height() - 25,
            "Página {PAGE_NUM} de {PAGE_COUNT}",
            null,
            8,
            [0.42, 0.47, 0.53]
        );

        $filename = 'reporte_asistencia_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    /**
     * Traduce los filtros crudos del request a texto legible para el
     * encabezado del PDF, resolviendo IDs a nombres reales desde la BD.
     */
    private static function buildFiltrosLegibles(array $filters): array {
        $legibles = [];

        if (!empty($filters['fecha_desde'])) {
            $legibles[] = 'Desde: ' . date('d/m/Y', strtotime($filters['fecha_desde']));
        }
        if (!empty($filters['fecha_hasta'])) {
            $legibles[] = 'Hasta: ' . date('d/m/Y', strtotime($filters['fecha_hasta']));
        }
        if (!empty($filters['curso_id'])) {
            foreach (Reporte::getCursos() as $c) {
                if ($c['id'] == $filters['curso_id']) {
                    $legibles[] = 'Curso: ' . $c['anio'] . '° ' . $c['division'] . ' (' . $c['turno'] . ')';
                    break;
                }
            }
        }
        if (!empty($filters['division'])) {
            $legibles[] = 'División: ' . $filters['division'];
        }
        if (!empty($filters['turno'])) {
            $legibles[] = 'Turno: ' . ucfirst($filters['turno']);
        }
        if (!empty($filters['especialidad_id'])) {
            foreach (Reporte::getEspecialidades() as $esp) {
                if ($esp['id'] == $filters['especialidad_id']) {
                    $legibles[] = 'Especialidad: ' . $esp['nombre'];
                    break;
                }
            }
        }
        if (!empty($filters['materia_id'])) {
            foreach (Reporte::getMaterias() as $mat) {
                if ($mat['id'] == $filters['materia_id']) {
                    $legibles[] = 'Materia: ' . $mat['nombre'];
                    break;
                }
            }
        }
        if (!empty($filters['preceptor_id'])) {
            foreach (Reporte::getPreceptores() as $pre) {
                if ($pre['id'] == $filters['preceptor_id']) {
                    $legibles[] = 'Preceptor: ' . $pre['apellido'] . ', ' . $pre['nombre'];
                    break;
                }
            }
        }
        if (!empty($filters['tipo_registro'])) {
            $tipos = self::tipoRegistroNombres();
            $legibles[] = 'Tipo de registro: ' . ($tipos[$filters['tipo_registro']] ?? $filters['tipo_registro']);
        }
        if (!empty($filters['bloque_horario'])) {
            $bloques = self::bloqueNombres();
            $legibles[] = 'Bloque horario: ' . ($bloques[$filters['bloque_horario']] ?? $filters['bloque_horario']);
        }
        if (!empty($filters['rango_faltas'])) {
            $legibles[] = 'Rango de faltas: ' . $filters['rango_faltas'];
        }
        if (!empty($filters['rango_asistencia'])) {
            $legibles[] = 'Rango de asistencia: ' . $filters['rango_asistencia'];
        }
        if (!empty($filters['alumno_busqueda'])) {
            $legibles[] = 'Búsqueda de alumno: ' . $filters['alumno_busqueda'];
        }
        if (($filters['incluir_anuladas'] ?? 'no') === 'si') {
            $legibles[] = 'Incluye asistencias anuladas';
        }

        return $legibles;
    }

    private static function buildPdfHtml(string $modo_reporte, array $filters, array $datos_reporte): string {
        $filtrosLegibles = self::buildFiltrosLegibles($filters);
        $fechaGeneracion = date('d/m/Y H:i');
        $estadoNombres = self::tipoRegistroNombres();
        $bloqueNombres = self::bloqueNombres();

        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: "DejaVu Sans", sans-serif; font-size: 10px; color: #071D3A; }
            h1 { font-size: 15px; margin: 0 0 2px 0; color: #071D3A; }
            .subtitulo { font-size: 10px; color: #6C757D; margin-bottom: 10px; }
            .filtros { font-size: 9px; color: #071D3A; background: #F8F9FA; padding: 8px 10px; border: 1px solid #E9ECEF; margin-bottom: 14px; }
            .filtros strong { color: #006397; }
            table { width: 100%; border-collapse: collapse; }
            th { background: #071D3A; color: #FFFFFF; text-align: left; padding: 5px 6px; font-size: 8.5px; text-transform: uppercase; }
            td { padding: 4px 6px; border-bottom: 1px solid #E9ECEF; font-size: 9px; }
            tr:nth-child(even) td { background: #F8F9FA; }
            .no-data { text-align: center; padding: 20px; color: #6C757D; }
        </style></head><body>';

        $html .= '<h1>Sistema de Gestión de Asistencia Escolar &mdash; Reporte de Asistencia</h1>';
        $html .= '<div class="subtitulo">Modo: ' . ($modo_reporte === 'resumen' ? 'Resumen por Alumno' : 'Detallado')
            . ' &mdash; Generado el ' . e($fechaGeneracion) . '</div>';

        if (!empty($filtrosLegibles)) {
            $html .= '<div class="filtros"><strong>Filtros aplicados:</strong> ' . e(implode('  |  ', $filtrosLegibles)) . '</div>';
        } else {
            $html .= '<div class="filtros"><strong>Filtros aplicados:</strong> Ninguno (todos los registros)</div>';
        }

        $html .= '<table><thead><tr>';
        if ($modo_reporte === 'resumen') {
            $html .= '<th>Alumno</th><th>Curso/Div.</th><th>Especialidad</th><th>Turno</th><th>Presentes</th>'
                . '<th>Ausentes</th><th>Tarde</th><th>Aus. c/Pres.</th><th>Justif.</th><th>Retiros</th>'
                . '<th>Faltas</th><th>% Asist.</th></tr></thead><tbody>';

            if (empty($datos_reporte['alumnos'])) {
                $html .= '<tr><td colspan="12" class="no-data">No hay resultados para los filtros seleccionados.</td></tr>';
            } else {
                foreach ($datos_reporte['alumnos'] as $a) {
                    $html .= '<tr>'
                        . '<td>' . e($a['apellido'] . ', ' . $a['nombre']) . '</td>'
                        . '<td>' . e($a['anio'] . '° ' . $a['division']) . '</td>'
                        . '<td>' . e($a['especialidad_nombre'] ?? '-') . '</td>'
                        . '<td>' . e(ucfirst($a['turno'] ?? '-')) . '</td>'
                        . '<td>' . e($a['presentes']) . '</td>'
                        . '<td>' . e($a['ausentes']) . '</td>'
                        . '<td>' . e($a['llegadas_tarde']) . '</td>'
                        . '<td>' . e($a['ausentes_con_presente']) . '</td>'
                        . '<td>' . e($a['justificados']) . '</td>'
                        . '<td>' . e($a['retiros_anticipados']) . '</td>'
                        . '<td>' . e($a['faltas_total']) . '</td>'
                        . '<td>' . e($a['porcentaje_asistencia']) . '%</td>'
                        . '</tr>';
                }
            }
        } else {
            $html .= '<th>Fecha</th><th>Alumno</th><th>Curso/Div.</th><th>Materia</th><th>Bloque</th>'
                . '<th>Preceptor</th><th>Estado</th><th>Observaciones</th></tr></thead><tbody>';

            if (empty($datos_reporte['registros'])) {
                $html .= '<tr><td colspan="8" class="no-data">No hay resultados para los filtros seleccionados.</td></tr>';
            } else {
                foreach ($datos_reporte['registros'] as $r) {
                    $html .= '<tr>'
                        . '<td>' . e(date('d/m/Y', strtotime($r['fecha']))) . '</td>'
                        . '<td>' . e($r['alumno_apellido'] . ', ' . $r['alumno_nombre']) . '</td>'
                        . '<td>' . e($r['anio'] . '° ' . $r['division']) . '</td>'
                        . '<td>' . e($r['materia_nombre']) . '</td>'
                        . '<td>' . e($bloqueNombres[$r['bloque_horario']] ?? $r['bloque_horario']) . '</td>'
                        . '<td>' . e($r['preceptor_apellido'] . ', ' . $r['preceptor_nombre']) . '</td>'
                        . '<td>' . e($estadoNombres[$r['estado']] ?? $r['estado']) . '</td>'
                        . '<td>' . e($r['detalle_observaciones'] ?? '-') . '</td>'
                        . '</tr>';
                }
            }
        }
        $html .= '</tbody></table></body></html>';

        return $html;
    }
}
