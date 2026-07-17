<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminUsuariosController {
    public static function index() {
        require_role('admin');
        $filters = [
            'rol' => input('rol', ''),
            'estado' => input('estado', ''),
            'busqueda' => input('busqueda', '')
        ];
        
        $usuarios = Usuario::getAll($filters);
        
        $stats = [
            'total' => Usuario::countAll(),
            'alumnos' => Usuario::countByRol('alumno'),
            'preceptores' => Usuario::countByRol('preceptor'),
            'padres_tutores' => Usuario::countByRol('padre_tutor'),
            'activos' => Usuario::countByEstado('activo')
        ];

        return [
            'usuarios' => $usuarios,
            'stats' => $stats,
            'filters' => $filters
        ];
    }

    public static function create() {
        require_role('admin');
        
        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $data = [
            'nombre' => input('nombre', ''),
            'apellido' => input('apellido', ''),
            'dni' => input('dni', ''),
            'email' => input('email', ''),
            'telefono' => input('telefono', ''),
            'rol' => input('rol', ''),
            'estado' => input('estado', 'activo'),
            'password' => input('password', '')
        ];

        // Validaciones
        $errors = [];

        if (empty($data['nombre'])) $errors[] = 'El nombre es obligatorio';
        if (empty($data['apellido'])) $errors[] = 'El apellido es obligatorio';
        if (empty($data['email'])) $errors[] = 'El email es obligatorio';
        if (empty($data['password'])) $errors[] = 'La contraseña es obligatoria';
        if (empty($data['rol'])) $errors[] = 'El rol es obligatorio';
        if (!in_array($data['rol'], ['admin', 'directivo', 'preceptor'])) {
            $errors[] = 'Rol inválido';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        try {
            Usuario::create($data);
            flash('success', 'Usuario creado exitosamente');
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                flash('errors', ['El email ya está registrado']);
            } else {
                flash('errors', ['Error al crear el usuario']);
            }
        }

        redirect('index.php?page=admin/usuarios');
    }

    public static function update() {
        require_role('admin');

        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $id = input('id', 0);
        if (!$id) {
            flash('errors', ['ID de usuario inválido']);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        $data = [];
        if (!empty(input('nombre'))) $data['nombre'] = input('nombre');
        if (!empty(input('apellido'))) $data['apellido'] = input('apellido');
        if (!empty(input('dni'))) $data['dni'] = input('dni');
        if (!empty(input('email'))) $data['email'] = input('email');
        if (input('telefono') !== '') $data['telefono'] = input('telefono');
        if (!empty(input('rol'))) $data['rol'] = input('rol');
        if (!empty(input('estado'))) $data['estado'] = input('estado');
        if (!empty(input('password'))) $data['password'] = input('password');

        try {
            Usuario::update($id, $data);
            flash('success', 'Usuario actualizado exitosamente');
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                flash('errors', ['El email ya está registrado']);
            } else {
                flash('errors', ['Error al actualizar el usuario']);
            }
        }

        redirect('index.php?page=admin/usuarios');
    }

    public static function toggleEstado() {
        require_role('admin');

        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $id = input('id', 0);
        if (!$id) {
            flash('errors', ['ID de usuario inválido']);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        $usuario = Usuario::getById($id);
        if (!$usuario) {
            flash('errors', ['Usuario no encontrado']);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        $nuevoEstado = $usuario['estado'] === 'activo' ? 'inactivo' : 'activo';
        Usuario::update($id, ['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'activo' ? 'activado' : 'desactivado';
        flash('success', "Usuario $mensaje exitosamente");
        redirect('index.php?page=admin/usuarios');
    }

    public static function delete() {
        require_role('admin');

        if (!is_post()) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $id = input('id', 0);
        if (!$id) {
            flash('errors', ['ID de usuario inválido']);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        if ($id == $_SESSION['usuario_id']) {
            flash('errors', ['No puedes eliminar tu propio usuario']);
            redirect('index.php?page=admin/usuarios');
            return;
        }

        try {
            Usuario::delete($id);
            flash('success', 'Usuario eliminado exitosamente');
        } catch (PDOException $e) {
            flash('errors', ['Error al eliminar el usuario']);
        }

        redirect('index.php?page=admin/usuarios');
    }
}
