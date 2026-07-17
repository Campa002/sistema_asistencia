<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AuthController {

    public static function login(): void {
        $email    = trim(input('email', ''));
        $password = input('password', '');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Completá todos los campos.';
            redirect('index.php?page=login');
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = 'Email o contraseña incorrectos.';
            redirect('index.php?page=login');
        }

        if (!password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Email o contraseña incorrectos.';
            redirect('index.php?page=login');
        }

        if ($user['estado'] === 'pendiente') {
            $_SESSION['error'] = 'Tu cuenta está pendiente de aprobación.';
            redirect('index.php?page=login');
        }

        if ($user['estado'] === 'inactivo' || $user['estado'] === 'rechazado') {
            $_SESSION['error'] = 'Tu cuenta no está activa. Contactá al administrador.';
            redirect('index.php?page=login');
        }

        // Login exitoso — guardar sesión
        login_user($user);

        // Si debe cambiar contraseña en primer acceso
        if ((int)$user['primer_acceso'] === 1) {
            redirect('index.php?page=primer_acceso');
        }

        // Redirigir según rol
        redirect_by_role();
    }

    public static function logout(): void {
        logout_user();
        redirect('index.php?page=login');
    }

    public static function registerAlumno(): void {
        $nombre          = trim(input('nombre', ''));
        $email           = strtolower(trim(input('email', '')));
        $password        = input('password', '');
        $password_confirm= input('password_confirm', '');
        $curso_id        = (int)input('curso_id', 0);
        $nombre_padre    = trim(input('nombre_padre', ''));

        if (!$nombre || !$email || !$password || !$curso_id) {
            $_SESSION['error'] = 'Completá todos los campos obligatorios.';
            redirect('index.php?page=register_alumno');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El email no es válido.';
            redirect('index.php?page=register_alumno');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            redirect('index.php?page=register_alumno');
        }

        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            redirect('index.php?page=register_alumno');
        }

        $db = Database::getConnection();

        // Verificar email único
        $check = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['error'] = 'Ese email ya está registrado.';
            redirect('index.php?page=register_alumno');
        }

        try {
            $db->beginTransaction();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, rol, estado, primer_acceso) VALUES (?, '', ?, ?, 'alumno', 'pendiente', 0)")
               ->execute([$nombre, $email, $hash]);
            $uid = (int)$db->lastInsertId();

            // Solicitud de acceso
            $db->prepare("INSERT INTO solicitudes_acceso (usuario_id, tipo, estado, datos_extra) VALUES (?, 'alumno', 'pendiente', ?)")
               ->execute([$uid, json_encode(['curso_id' => $curso_id, 'nombre_padre' => $nombre_padre])]);

            $db->commit();

            $_SESSION['success'] = 'Solicitud enviada. El directivo revisará tu cuenta.';
            redirect('index.php?page=login');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Error al registrar. Intentá de nuevo.';
            redirect('index.php?page=register_alumno');
        }
    }

    public static function primerAcceso(): void {
        $email          = strtolower(trim(input('email', '')));
        $password       = input('password', '');
        $password_confirm = input('password_confirm', '');

        if (!$email || !$password || !$password_confirm) {
            $_SESSION['error'] = 'Completá todos los campos.';
            redirect('index.php?page=primer_acceso');
        }

        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            redirect('index.php?page=primer_acceso');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            redirect('index.php?page=primer_acceso');
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? AND primer_acceso = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = 'No se encontró el usuario o ya completó este paso.';
            redirect('index.php?page=primer_acceso');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db->prepare("UPDATE usuarios SET password_hash = ?, primer_acceso = 0, estado = 'activo' WHERE id = ?")
           ->execute([$hash, $user['id']]);

        $_SESSION['success'] = 'Contraseña configurada. Ya podés iniciar sesión.';
        redirect('index.php?page=login');
    }
}