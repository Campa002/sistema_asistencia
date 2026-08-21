<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Comunicado.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminComunicadosController {
    public static function index() {
        require_role('admin');

        $filters = [
            'estado' => input('estado', ''),
            'prioridad' => input('prioridad', ''),
            'busqueda' => input('busqueda', '')
        ];

        $comunicados = Comunicado::getAll($filters);
        $stats = [
            'total' => Comunicado::countAll(),
            'activos' => Comunicado::countByEstado(1),
            'inactivos' => Comunicado::countByEstado(0)
        ];

        return [
            'comunicados' => $comunicados,
            'stats' => $stats,
            'filters' => $filters
        ];
    }

    public static function create() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/comunicados');
            return;
        }
        if (!self::validarCsrf()) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        [$data, $errors] = self::leerYValidar();
        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        $data['creado_por'] = $_SESSION['usuario_id'];
        $nuevoId = Comunicado::create($data);
        LogActividad::registrar(
            $_SESSION['usuario_id'],
            'CREAR_COMUNICADO',
            "Creó el comunicado \"{$data['titulo']}\"",
            'notificaciones',
            (int) $nuevoId,
            null,
            $data
        );
        flash('success', 'Comunicado creado exitosamente');
        redirect('index.php?page=admin/comunicados');
    }

    public static function update() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/comunicados');
            return;
        }
        if (!self::validarCsrf()) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        $id = intval(input('id', 0));
        $anterior = $id ? Comunicado::getById($id) : null;
        if (!$id || !$anterior) {
            flash('errors', ['Comunicado no encontrado']);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        [$data, $errors] = self::leerYValidar();
        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        Comunicado::update($id, $data);
        LogActividad::registrar(
            $_SESSION['usuario_id'],
            'EDITAR_COMUNICADO',
            "Editó el comunicado \"{$data['titulo']}\" (ID $id)",
            'notificaciones',
            $id,
            [
                'titulo' => $anterior['titulo'],
                'contenido' => $anterior['contenido'],
                'prioridad' => $anterior['prioridad'],
                'rol_destino' => $anterior['rol_destino'],
                'fecha_expiracion' => $anterior['fecha_expiracion']
            ],
            $data
        );
        flash('success', 'Comunicado actualizado exitosamente');
        redirect('index.php?page=admin/comunicados');
    }

    public static function toggleActivo() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/comunicados');
            return;
        }
        if (!self::validarCsrf()) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        $id = intval(input('id', 0));
        $comunicado = $id ? Comunicado::getById($id) : null;
        if (!$comunicado) {
            flash('errors', ['Comunicado no encontrado']);
            redirect('index.php?page=admin/comunicados');
            return;
        }

        Comunicado::toggleActivo($id);
        $mensaje = $comunicado['activo'] == 1 ? 'desactivado' : 'activado';
        LogActividad::registrar(
            $_SESSION['usuario_id'],
            $comunicado['activo'] == 1 ? 'DESACTIVAR_COMUNICADO' : 'ACTIVAR_COMUNICADO',
            "Comunicado \"{$comunicado['titulo']}\" (ID $id) $mensaje",
            'notificaciones',
            $id,
            ['activo' => (int) $comunicado['activo']],
            ['activo' => $comunicado['activo'] == 1 ? 0 : 1]
        );
        flash('success', "Comunicado $mensaje exitosamente");
        redirect('index.php?page=admin/comunicados');
    }

    private static function validarCsrf(): bool {
        return verify_csrf_token(input('csrf_token', ''));
    }

    /**
     * Lee título/mensaje/prioridad/destinatarios/vencimiento del POST y los
     * valida. Devuelve [datos_listos_para_el_modelo, errores].
     */
    private static function leerYValidar(): array {
        $titulo = trim((string) input('titulo', ''));
        $contenido = trim((string) input('contenido', ''));
        $prioridad = input('prioridad', 'normal');
        $fechaExpiracion = trim((string) input('fecha_expiracion', ''));
        $rolDestino = self::resolverRolDestino();

        $errors = [];
        if ($titulo === '') {
            $errors[] = 'El título es obligatorio';
        } elseif (mb_strlen($titulo) > 255) {
            $errors[] = 'El título no puede superar los 255 caracteres';
        }
        if ($contenido === '') {
            $errors[] = 'El mensaje es obligatorio';
        }
        if (!in_array($prioridad, Comunicado::$prioridades_validas, true)) {
            $errors[] = 'La prioridad seleccionada no es válida';
        }
        if ($rolDestino === '') {
            $errors[] = 'Debe seleccionar al menos un destinatario';
        }
        if ($fechaExpiracion !== '' && !self::esFechaValida($fechaExpiracion)) {
            $errors[] = 'La fecha de vencimiento no es válida';
        }

        return [[
            'titulo' => $titulo,
            'contenido' => $contenido,
            'prioridad' => $prioridad,
            'rol_destino' => $rolDestino,
            'fecha_expiracion' => $fechaExpiracion
        ], $errors];
    }

    /**
     * Arma el valor de rol_destino (columna SET) a partir de los checkboxes
     * "destinatarios[]" del formulario. Si se marcó "todos", eso prevalece
     * sobre cualquier otra selección individual.
     */
    private static function resolverRolDestino(): string {
        $destinatarios = $_POST['destinatarios'] ?? [];
        if (!is_array($destinatarios)) {
            $destinatarios = [];
        }
        if (in_array('todos', $destinatarios, true)) {
            return 'todos';
        }
        $validos = array_values(array_intersect($destinatarios, Comunicado::$roles_validos));
        return implode(',', $validos);
    }

    private static function esFechaValida(string $fecha): bool {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d !== false && $d->format('Y-m-d') === $fecha;
    }
}
