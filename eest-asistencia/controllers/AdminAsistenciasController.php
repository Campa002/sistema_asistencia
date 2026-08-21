<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminAsistenciasController {
    public static function index() {
        require_role('admin');
        
        $filters = [
            'curso_id' => input('curso_id', ''),
            'turno' => input('turno', ''),
            'preceptor_id' => input('preceptor_id', ''),
            'fecha_desde' => input('fecha_desde', ''),
            'fecha_hasta' => input('fecha_hasta', ''),
            'estado' => input('estado', '')
        ];

        $page_num = intval(input('page_num', 1));
        $per_page = input('per_page', 10);
        if ($per_page !== 'all') {
            $per_page = intval($per_page);
            if (!in_array($per_page, [10, 25, 50])) {
                $per_page = 10;
            }
        }

        $result = Asistencia::getAll($filters, $page_num, $per_page);
        $registros = $result['registros'];
        $pagination = [
            'total' => $result['total'],
            'page_num' => $result['page_num'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages']
        ];

        $cursos = Curso::getAll([]); // all courses
        $preceptores = self::getPreceptores();
        $materias = self::getMaterias();
        
        // Build editDataMap only with VISIBLE (paginated) registros first
        $editDataMap = [];
        foreach ($registros as $registro) {
            $registroId = (int)$registro['id'];
            $editDataMap[(string)$registroId] = Asistencia::getEditDataByRegistroId($registroId);
        }
        
        $registroSeleccionado = null;
        $detallesSeleccionados = [];
        $auditoriaSeleccionada = [];

        if (isset($_GET['registro_id'])) {
            $registroSeleccionado = Asistencia::getById(intval($_GET['registro_id']));
            if ($registroSeleccionado) {
                $detallesSeleccionados = Asistencia::getDetallesByRegistroId($registroSeleccionado['id']);
                $auditoriaSeleccionada = Asistencia::getAuditoriaByRegistroId($registroSeleccionado['id']);
                $key = (string) $registroSeleccionado['id'];
                if (!isset($editDataMap[$key])) {
                    $editDataMap[$key] = Asistencia::getEditDataByRegistroId((int) $registroSeleccionado['id']);
                }
            }
        }

        return [
            'registros' => $registros,
            'cursos' => $cursos,
            'preceptores' => $preceptores,
            'materias' => $materias,
            'filters' => $filters,
            'editDataMap' => $editDataMap,
            'registroSeleccionado' => $registroSeleccionado,
            'detallesSeleccionados' => $detallesSeleccionados,
            'auditoriaSeleccionada' => $auditoriaSeleccionada,
            'pagination' => $pagination
        ];
    }

    public static function handlePost() {
        require_role('admin');
        $action = input('action', '');
        $adminId = $_SESSION['usuario_id'];
        
        // Build redirect query
        $queryParams = [];
        foreach (['curso_id', 'turno', 'preceptor_id', 'fecha_desde', 'fecha_hasta', 'estado', 'page_num', 'per_page'] as $key) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                $queryParams[$key] = $_GET[$key];
            }
        }
        $queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';

        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/asistencias' . $queryString);
            return;
        }

        switch ($action) {
            case 'editar_asistencia':
                $registroId = intval(input('registro_id', 0));
                $data = [
                    'curso_id' => intval(input('curso_id', 0)),
                    'materia_id' => intval(input('materia_id', 0)),
                    'preceptor_id' => intval(input('preceptor_id', 0)),
                    'fecha' => input('fecha', ''),
                    'bloque_horario' => input('bloque_horario', ''),
                    'observaciones' => input('observaciones', ''),
                    'alumnos' => $_POST['alumnos'] ?? []
                ];
                $observaciones = input('observaciones_auditoria', '');
                
                if (Asistencia::updateRegistro($registroId, $data, $adminId, $observaciones)) {
                    // auditoria_asistencias ya guarda el detalle campo por
                    // campo (Asistencia::registrarAuditoria); acá solo se dej
                    // a un puntero liviano para que aparezca en Actividad
                    // Reciente, sin duplicar datos de alumnos.
                    LogActividad::registrar(
                        $adminId,
                        'EDITAR_ASISTENCIA',
                        "Editó el registro de asistencia #$registroId (curso {$data['curso_id']}, fecha {$data['fecha']})",
                        'registros_asistencia',
                        $registroId,
                        null,
                        null
                    );
                    flash('success', 'Asistencia actualizada correctamente');
                } else {
                    flash('error', 'Error al actualizar la asistencia');
                }
                redirect('index.php?page=admin/asistencias&registro_id=' . $registroId . $queryString);
                break;

            case 'anular_asistencia':
                $registroId = intval(input('registro_id', 0));
                $observaciones = input('observaciones', '');

                if (Asistencia::anularRegistro($registroId, $adminId, $observaciones)) {
                    LogActividad::registrar(
                        $adminId,
                        'ANULAR_ASISTENCIA',
                        "Anuló el registro de asistencia #$registroId",
                        'registros_asistencia',
                        $registroId,
                        null,
                        null
                    );
                    flash('success', 'Asistencia anulada correctamente');
                } else {
                    flash('error', 'Error al anular la asistencia');
                }
                redirect('index.php?page=admin/asistencias' . $queryString);
                break;
        }
    }

    private static function getPreceptores() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'preceptor' ORDER BY apellido, nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function getMaterias() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre FROM materias ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
