<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminCursosController {
    public static function index() {
        require_role('admin');
        $filters = [
            'anio' => input('anio', ''),
            'turno' => input('turno', ''),
            'especialidad_id' => input('especialidad_id', ''),
            'estado' => input('estado', ''),
            'busqueda' => input('busqueda', '')
        ];
        
        $cursos = Curso::getAll($filters);
        $especialidades = Curso::getEspecialidades();
        
        $stats = [
            'total' => Curso::countAll(),
            'activos' => Curso::countByEstado('activo'),
            'inactivos' => Curso::countByEstado('inactivo')
        ];

        $cursoSeleccionado = null;
        $alumnosCurso = [];
        $curso_id = input('curso_id', 0);
        if ($curso_id) {
            $cursoSeleccionado = Curso::getById($curso_id);
            if ($cursoSeleccionado) {
                $alumnosCurso = Curso::getAlumnosByCursoId($curso_id, $cursoSeleccionado['ciclo_lectivo']);
            }
        }

        return [
            'cursos' => $cursos,
            'especialidades' => $especialidades,
            'stats' => $stats,
            'filters' => $filters,
            'cursoSeleccionado' => $cursoSeleccionado,
            'alumnosCurso' => $alumnosCurso
        ];
    }

    public static function create() {
        require_role('admin');
        
        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $data = [
            'anio' => input('anio', ''),
            'division' => input('division', ''),
            'turno' => input('turno', ''),
            'especialidad_id' => input('especialidad_id', ''),
            'aula' => input('aula', ''),
            'ciclo_lectivo' => input('ciclo_lectivo', date('Y')),
            'estado' => input('estado', 'activo')
        ];

        // Validaciones
        $errors = [];
        if (empty($data['anio'])) $errors[] = 'El año es obligatorio';
        if (empty($data['division'])) $errors[] = 'La división es obligatoria';
        if (empty($data['turno'])) $errors[] = 'El turno es obligatorio';
        if (empty($data['especialidad_id'])) $errors[] = 'La especialidad es obligatoria';
        if (empty($data['ciclo_lectivo'])) $errors[] = 'El ciclo lectivo es obligatorio';

        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/cursos');
            return;
        }

        try {
            Curso::create($data);
            flash('success', 'Curso creado exitosamente');
        } catch (PDOException $e) {
            flash('errors', ['Error al crear el curso']);
        }

        redirect('index.php?page=admin/cursos');
    }

    public static function update() {
        require_role('admin');

        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $id = input('id', 0);
        if (!$id) {
            flash('errors', ['ID de curso inválido']);
            redirect('index.php?page=admin/cursos');
            return;
        }

        $data = [];
        if (!empty(input('anio'))) $data['anio'] = input('anio');
        if (!empty(input('division'))) $data['division'] = input('division');
        if (!empty(input('turno'))) $data['turno'] = input('turno');
        if (!empty(input('especialidad_id'))) $data['especialidad_id'] = input('especialidad_id');
        if (input('aula') !== '') $data['aula'] = input('aula');
        if (!empty(input('ciclo_lectivo'))) $data['ciclo_lectivo'] = input('ciclo_lectivo');
        if (!empty(input('estado'))) $data['estado'] = input('estado');

        try {
            Curso::update($id, $data);
            flash('success', 'Curso actualizado exitosamente');
        } catch (PDOException $e) {
            flash('errors', ['Error al actualizar el curso']);
        }

        redirect('index.php?page=admin/cursos');
    }

    public static function toggleEstado() {
        require_role('admin');

        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $id = input('id', 0);
        if (!$id) {
            flash('errors', ['ID de curso inválido']);
            redirect('index.php?page=admin/cursos');
            return;
        }

        $curso = Curso::getById($id);
        if (!$curso) {
            flash('errors', ['Curso no encontrado']);
            redirect('index.php?page=admin/cursos');
            return;
        }

        if ($curso['estado'] === 'activo') {
            $nuevoEstado = 'inactivo';
        } else {
            $nuevoEstado = 'activo';
        }
        Curso::update($id, ['estado' => $nuevoEstado]);
        
        if ($nuevoEstado === 'activo') {
            flash('success', 'Curso activado exitosamente');
        } else {
            flash('success', 'Curso desactivado exitosamente');
        }
        
        redirect('index.php?page=admin/cursos');
    }
}
