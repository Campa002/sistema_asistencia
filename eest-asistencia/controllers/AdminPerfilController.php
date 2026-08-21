<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Perfil del propio usuario autenticado (Admin). Solo permite modificar
 * los datos del usuario en sesión — nunca de otro usuario, y nunca su rol
 * (el rol ni siquiera se lee del POST acá, a propósito).
 */
class AdminPerfilController {
    public static function actualizarPerfil() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/perfil');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/perfil');
            return;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        $anterior = Usuario::getById($usuarioId);
        if (!$anterior) {
            flash('errors', ['Usuario no encontrado']);
            redirect('index.php?page=admin/perfil');
            return;
        }

        $nombre = trim((string) input('nombre', ''));
        $apellido = trim((string) input('apellido', ''));
        $email = trim((string) input('email', ''));
        $telefono = trim((string) input('telefono', ''));

        $errors = [];
        if ($nombre === '') {
            $errors[] = 'El nombre es obligatorio';
        }
        if ($apellido === '') {
            $errors[] = 'El apellido es obligatorio';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/perfil');
            return;
        }

        // Whitelist explícito: rol y estado nunca se leen del POST de este
        // formulario, así que no pueden cambiar desde acá bajo ninguna
        // circunstancia, sin importar qué se envíe en la petición.
        $data = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $telefono,
        ];

        try {
            Usuario::update($usuarioId, $data);

            // Refrescar los datos de sesión que se muestran en los headers.
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellido'] = $apellido;
            $_SESSION['email'] = $email;

            LogActividad::registrar(
                $usuarioId,
                'ACTUALIZAR_PERFIL',
                'Actualizó su propio perfil',
                'usuarios',
                $usuarioId,
                [
                    'nombre' => $anterior['nombre'],
                    'apellido' => $anterior['apellido'],
                    'email' => $anterior['email'],
                    'telefono' => $anterior['telefono'],
                ],
                $data
            );
            flash('success', 'Perfil actualizado correctamente');
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                flash('errors', ['El email ya está registrado por otro usuario']);
            } else {
                flash('errors', ['Error al actualizar el perfil']);
            }
        }

        redirect('index.php?page=admin/perfil');
    }

    public static function cambiarPassword() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/perfil');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/perfil');
            return;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        $actual = Usuario::getById($usuarioId);
        if (!$actual) {
            flash('errors', ['Usuario no encontrado']);
            redirect('index.php?page=admin/perfil');
            return;
        }

        $passwordActual = (string) input('password_actual', '');
        $passwordNueva = (string) input('password_nueva', '');
        $passwordConfirmacion = (string) input('password_confirmacion', '');

        $errors = [];
        if ($passwordActual === '' || !password_verify($passwordActual, $actual['password_hash'])) {
            $errors[] = 'La contraseña actual no es correcta';
        }
        if (strlen($passwordNueva) < 8) {
            $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres';
        }
        if ($passwordNueva !== $passwordConfirmacion) {
            $errors[] = 'La confirmación no coincide con la nueva contraseña';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            redirect('index.php?page=admin/perfil');
            return;
        }

        // Usuario::update() ya hashea con password_hash() internamente —
        // reutiliza exactamente el mismo mecanismo que ya usa el resto del
        // sistema (creación/edición de usuarios desde Gestión de Usuarios).
        Usuario::update($usuarioId, ['password' => $passwordNueva]);

        // Nunca se guarda la contraseña ni el hash en el log.
        LogActividad::registrar(
            $usuarioId,
            'CAMBIAR_PASSWORD',
            'Cambió su propia contraseña',
            'usuarios',
            $usuarioId,
            null,
            null
        );

        // Comportamiento actual del sistema: no hay invalidación de otras
        // sesiones en ningún flujo existente (tampoco en primer_acceso) —
        // se mantiene la sesión activa, sin inventar un mecanismo nuevo.
        flash('success', 'Contraseña actualizada correctamente');
        redirect('index.php?page=admin/perfil');
    }
}
