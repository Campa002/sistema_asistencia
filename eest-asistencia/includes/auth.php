<?php
/**
 * Gestión de sesiones y autenticación
 */
require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function is_logged_in(): bool {
    return !empty($_SESSION['usuario_id']) && isset($_SESSION['rol']);
}

function get_auth_user(): ?array {
    if (!is_logged_in()) return null;
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function has_role(string $role): bool {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === $role;
}

function has_any_role(array $roles): bool {
    return isset($_SESSION['rol']) && in_array($_SESSION['rol'], $roles, true);
}

function require_auth(): void {
    if (!is_logged_in()) {
        redirect('index.php?page=login');
    }
}

function require_role(string $role): void {
    require_auth();
    if (!has_role($role)) {
        redirect('index.php?page=unauthorized');
    }
}

function require_any_role(array $roles): void {
    require_auth();
    if (!has_any_role($roles)) {
        redirect('index.php?page=unauthorized');
    }
}

function login_user(array $user): void {
    // NO usar session_regenerate_id en XAMPP — puede destruir la sesión
    $_SESSION['usuario_id'] = (int)$user['id'];
    $_SESSION['nombre']     = $user['nombre'];
    $_SESSION['apellido']   = $user['apellido'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['rol']        = $user['rol'];
    $_SESSION['logged_in']  = true;

    try {
        $db = Database::getConnection();
        $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?")
           ->execute([$user['id']]);
    } catch (Exception $e) { }
}

function logout_user(): void {
    session_unset();
    session_destroy();
}

function redirect_by_role(): void {
    if (!is_logged_in()) {
        redirect('index.php?page=login');
        return;
    }
    $routes = [
        'admin'      => 'index.php?page=admin/dashboard',
        'directivo'  => 'index.php?page=directivo/directivo',
        'preceptor'  => 'index.php?page=preceptor/preceptor',
        'alumno'     => 'index.php?page=alumno/alumnos',
        'padre_tutor'=> 'index.php?page=padre_tutor/padre-tutor',
    ];
    $rol = $_SESSION['rol'] ?? '';
    redirect($routes[$rol] ?? 'index.php?page=login');
}