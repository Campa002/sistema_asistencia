<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminMateriasController {
    public static function index() {
        require_role('admin');
        $filters = [
            'especialidad_id' => input('especialidad_id', ''),
            'activo' => input('activo', ''),
            'busqueda' => input('busqueda', '')
        ];

        $materias = Materia::getAll($filters);
        $especialidades = Curso::getEspecialidades();

        $stats = [
            'total' => Materia::countAll(),
            'activas' => Materia::countByActivo(1),
            'inactivas' => Materia::countByActivo(0)
        ];

        return [
            'materias' => $materias,
            'especialidades' => $especialidades,
            'stats' => $stats,
            'filters' => $filters
        ];
    }

    public static function create() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/materias');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $data = [
            'nombre' => trim((string) input('nombre', '')),
            'codigo' => trim((string) input('codigo', '')) ?: null,
            'especialidad_id' => input('especialidad_id', '') !== '' ? (int) input('especialidad_id') : null,
            'anio_correspondiente' => input('anio_correspondiente', '') !== '' ? (int) input('anio_correspondiente') : null,
            'activo' => 1
        ];

        $errors = [];
        if ($data['nombre'] === '') {
            $errors[] = 'El nombre de la materia es obligatorio';
        }
        if ($data['anio_correspondiente'] !== null && ($data['anio_correspondiente'] < 1 || $data['anio_correspondiente'] > 7)) {
            $errors[] = 'El año correspondiente debe estar entre 1 y 7';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/materias');
            return;
        }

        try {
            $nuevoId = Materia::create($data);
            LogActividad::registrar(
                $_SESSION['usuario_id'],
                'CREAR_MATERIA',
                "Creó la materia \"{$data['nombre']}\"",
                'materias',
                (int) $nuevoId,
                null,
                $data
            );
            flash('success', 'Materia creada exitosamente');
        } catch (PDOException $e) {
            flash('errors', ['Error al crear la materia']);
        }

        redirect('index.php?page=admin/materias');
    }

    public static function update() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/materias');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $id = (int) input('id', 0);
        if (!$id) {
            flash('errors', ['ID de materia inválido']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $anterior = Materia::getById($id);
        if (!$anterior) {
            flash('errors', ['Materia no encontrada']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $nombre = trim((string) input('nombre', ''));
        if ($nombre === '') {
            flash('errors', ['El nombre de la materia es obligatorio']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $anio = input('anio_correspondiente', '');
        if ($anio !== '' && ((int) $anio < 1 || (int) $anio > 7)) {
            flash('errors', ['El año correspondiente debe estar entre 1 y 7']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $data = [
            'nombre' => $nombre,
            'codigo' => trim((string) input('codigo', '')) ?: null,
            'especialidad_id' => input('especialidad_id', '') !== '' ? (int) input('especialidad_id') : null,
            'anio_correspondiente' => $anio !== '' ? (int) $anio : null,
        ];

        try {
            Materia::update($id, $data);
            LogActividad::registrar(
                $_SESSION['usuario_id'],
                'ACTUALIZAR_MATERIA',
                "Actualizó la materia \"{$anterior['nombre']}\" (ID $id)",
                'materias',
                $id,
                [
                    'nombre' => $anterior['nombre'],
                    'codigo' => $anterior['codigo'],
                    'especialidad_id' => $anterior['especialidad_id'],
                    'anio_correspondiente' => $anterior['anio_correspondiente'],
                ],
                $data
            );
            flash('success', 'Materia actualizada exitosamente');
        } catch (PDOException $e) {
            flash('errors', ['Error al actualizar la materia']);
        }

        redirect('index.php?page=admin/materias');
    }

    public static function toggleEstado() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/materias');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $id = (int) input('id', 0);
        $materia = $id ? Materia::getById($id) : null;
        if (!$materia) {
            flash('errors', ['Materia no encontrada']);
            redirect('index.php?page=admin/materias');
            return;
        }

        $nuevoActivo = $materia['activo'] == 1 ? 0 : 1;
        Materia::update($id, ['activo' => $nuevoActivo]);
        $mensaje = $nuevoActivo == 1 ? 'activada' : 'desactivada';
        LogActividad::registrar(
            $_SESSION['usuario_id'],
            $nuevoActivo == 1 ? 'ACTIVAR_MATERIA' : 'DESACTIVAR_MATERIA',
            "Materia \"{$materia['nombre']}\" (ID $id) $mensaje",
            'materias',
            $id,
            ['activo' => (int) $materia['activo']],
            ['activo' => $nuevoActivo]
        );
        flash('success', "Materia $mensaje exitosamente");
        redirect('index.php?page=admin/materias');
    }

    /**
     * Horario real de un curso, solo lectura, tal como fue cargado desde
     * los Excel oficiales (Horarios ciclo básico / ciclo superior) a
     * asignaciones_materias. No permite crear/editar/eliminar horarios.
     */
    public static function horarios() {
        require_role('admin');

        $cursos = Curso::getAll(['estado' => 'activo']);

        $cursoId = input('curso_id', '');
        $cursoId = $cursoId !== '' ? (int) $cursoId : (int) ($cursos[0]['id'] ?? 0);

        $cursoActual = null;
        foreach ($cursos as $c) {
            if ((int) $c['id'] === $cursoId) { $cursoActual = $c; break; }
        }

        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];
        $porDia = [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $asignaciones = $cursoId ? Materia::getAsignaciones($cursoId) : [];
        foreach ($asignaciones as $a) {
            $porDia[(int) $a['dia_semana']][] = $a;
        }

        return [
            'cursos' => $cursos,
            'curso_id' => $cursoId,
            'curso_actual' => $cursoActual,
            'dias' => $dias,
            'por_dia' => $porDia,
            'total' => count($asignaciones),
        ];
    }
}
