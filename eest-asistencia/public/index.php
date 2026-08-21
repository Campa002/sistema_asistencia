<?php
/**
 * Punto de entrada principal
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

$page = input('page', 'home');
$GLOBALS['page'] = $page;

// DEBUG TEMPORAL - borrar después
if (isset($_GET['debug'])) {
    echo '<pre>';
    echo 'SESSION: ';
    print_r($_SESSION);
    echo 'PAGE: ' . $page . "\n";
    echo 'IS_LOGGED_IN: ' . (is_logged_in() ? 'SI' : 'NO') . "\n";
    echo 'ROL: ' . ($_SESSION['rol'] ?? 'NO HAY ROL') . "\n";
    echo 'USUARIO_ID: ' . ($_SESSION['usuario_id'] ?? 'NO HAY ID') . "\n";
    echo 'BASE_URL: ' . BASE_URL . "\n";
    echo '</pre>';
    die();
}

// Rutas públicas: NO requieren sesión
$public_routes = ['home', 'login', 'register_alumno', 'primer_acceso', 'unauthorized'];

// ========================================
// 1. Manejar acciones de formulario primero
// ========================================
// Handle AJAX requests
if ($page === 'admin/reportes/autocomplete_alumnos') {
    require_once __DIR__ . '/../controllers/AdminReportesController.php';
    AdminReportesController::autocompleteAlumnos();
    exit;
}

if ($page === 'admin/reportes/detalle_alumno') {
    require_once __DIR__ . '/../controllers/AdminReportesController.php';
    AdminReportesController::detalleAlumno();
    exit;
}

if ($page === 'admin/configuracion/descargar_backup') {
    require_once __DIR__ . '/../controllers/AdminGestionTecnicaController.php';
    AdminGestionTecnicaController::descargarBackup();
    exit;
}

if ($page === 'preceptor/enviar_mensaje_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/PreceptorController.php';
    PreceptorController::enviarMensajeAjax();
    exit;
}

if ($page === 'preceptor/guardar_asistencia_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/PreceptorController.php';
    PreceptorController::guardarAsistenciaAjax();
    exit;
}

if ($page === 'preceptor/detalle_asistencia_ajax') {
    require_once __DIR__ . '/../controllers/PreceptorController.php';
    PreceptorController::detalleAsistenciaAjax();
    exit;
}

if ($page === 'preceptor/editar_asistencia_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/PreceptorController.php';
    PreceptorController::editarAsistenciaAjax();
    exit;
}

if ($page === 'preceptor/indicar_ausencia_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/PreceptorController.php';
    PreceptorController::indicarAusenciaAjax();
    exit;
}

if ($page === 'padre_tutor/enviar_mensaje_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/PadreTutorController.php';
    PadreTutorController::enviarMensajeAjax();
    exit;
}

if ($page === 'alumno/enviar_mensaje_ajax' && is_post()) {
    require_once __DIR__ . '/../controllers/AlumnoController.php';
    AlumnoController::enviarMensajeAjax();
    exit;
}

if (is_post()) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    require_once __DIR__ . '/../controllers/AdminAsistenciasController.php';
    require_once __DIR__ . '/../controllers/AdminUsuariosController.php';
    require_once __DIR__ . '/../controllers/AdminCursosController.php';
    require_once __DIR__ . '/../controllers/AdminMensajesController.php';
    require_once __DIR__ . '/../controllers/AdminReportesController.php';
    require_once __DIR__ . '/../controllers/AdminComunicadosController.php';
    require_once __DIR__ . '/../controllers/AdminGestionTecnicaController.php';
    require_once __DIR__ . '/../controllers/AdminPerfilController.php';
    require_once __DIR__ . '/../controllers/AdminMateriasController.php';
    $action = input('action', '');

    switch ($action) {
        case 'login':
            AuthController::login();
            break;
        case 'logout':
            AuthController::logout();
            break;
        case 'register_alumno':
            AuthController::registerAlumno();
            break;
        case 'primer_acceso':
            AuthController::primerAcceso();
            break;
        case 'admin_create_usuario':
            AdminUsuariosController::create();
            break;
        case 'admin_update_usuario':
            AdminUsuariosController::update();
            break;
        case 'admin_toggle_estado_usuario':
            AdminUsuariosController::toggleEstado();
            break;
        case 'admin_delete_usuario':
            AdminUsuariosController::delete();
            break;
        case 'admin_create_curso':
            AdminCursosController::create();
            break;
        case 'admin_update_curso':
            AdminCursosController::update();
            break;
        case 'admin_toggle_estado_curso':
            AdminCursosController::toggleEstado();
            break;
        case 'editar_asistencia':
        case 'anular_asistencia':
            AdminAsistenciasController::handlePost();
            break;
        case 'enviar_mensaje':
        case 'nueva_conversacion':
            AdminMensajesController::handlePost();
            break;
        case 'exportar_csv':
        case 'exportar_pdf':
            AdminReportesController::handlePost();
            break;
        case 'admin_create_comunicado':
            AdminComunicadosController::create();
            break;
        case 'admin_update_comunicado':
            AdminComunicadosController::update();
            break;
        case 'admin_toggle_comunicado':
            AdminComunicadosController::toggleActivo();
            break;
        case 'admin_actualizar_configuracion':
            AdminGestionTecnicaController::actualizarConfiguracion();
            break;
        case 'admin_ejecutar_backup':
            AdminGestionTecnicaController::ejecutarBackup();
            break;
        case 'admin_actualizar_perfil':
            AdminPerfilController::actualizarPerfil();
            break;
        case 'admin_cambiar_password':
            AdminPerfilController::cambiarPassword();
            break;
        case 'admin_create_materia':
            AdminMateriasController::create();
            break;
        case 'admin_update_materia':
            AdminMateriasController::update();
            break;
        case 'admin_toggle_estado_materia':
            AdminMateriasController::toggleEstado();
            break;
    }
}

// ========================================
// 2. Flujo inicial (home o raíz)
// ========================================
if ($page === 'home' || $page === '' || empty($page)) {
    if (is_logged_in()) {
        redirect_by_role();
    } else {
        $page = 'login';
    }
}

// ========================================
// 3. Si usuario está logueado y accede a login/register, redirigir
// ========================================
if (is_logged_in() && in_array($page, ['login', 'register_alumno', 'primer_acceso'])) {
    redirect_by_role();
}

// ========================================
// 4. Rutas protegidas y permisos
// ========================================
$protected_routes = [
    'admin/dashboard'       => ['admin'],
    'admin/usuarios'        => ['admin'],
    'admin/cursos'          => ['admin'],
    'admin/materias'        => ['admin'],
    'admin/horarios'        => ['admin'],
    'admin/asistencias'     => ['admin'],
    'admin/mensajes'        => ['admin'],
    'admin/reportes'        => ['admin'],
    'admin/comunicados'     => ['admin'],
    'admin/configuracion'   => ['admin'],

    'directivo/dashboard'   => ['directivo'],
    'directivo/directivo'   => ['directivo'],
    'directivo/solicitudes' => ['directivo'],
    'directivo/vinculaciones'=> ['directivo'],
    'directivo/reemplazos'  => ['directivo'],
    'directivo/asistencias' => ['directivo'],

    'preceptor/dashboard'        => ['preceptor'],
    'preceptor/preceptor'        => ['preceptor'],
    'preceptor/tomar_asistencia' => ['preceptor'],
    'preceptor/historial'        => ['preceptor'],
    'preceptor/justificaciones'  => ['preceptor'],
    'preceptor/retiros'          => ['preceptor'],
    'preceptor/mensajes'         => ['preceptor'],

    'alumno/dashboard'     => ['alumno'],
    'alumno/alumnos'  => ['alumno'],
    'alumno/historial'     => ['alumno'],
    'alumno/asignar_tutor' => ['alumno'],

    'padre_tutor/dashboard'      => ['padre_tutor'],
    'padre_tutor/padre-tutor'    => ['padre_tutor'],
    'padre_tutor/historial'      => ['padre_tutor'],
    'padre_tutor/justificaciones'=> ['padre_tutor'],
    'padre_tutor/mensajes'       => ['padre_tutor'],
];

if (isset($protected_routes[$page])) {
    if (!is_logged_in()) {
        redirect('index.php?page=login');
    }
    $required_roles = $protected_routes[$page];
    if (!has_any_role($required_roles)) {
        redirect('index.php?page=unauthorized');
    }
}

// ========================================
// 4b. Modo mantenimiento (chequeo centralizado)
// ========================================
// Bloquea a TODOS los roles no-admin en TODO el sistema mientras
// `mantenimiento_modo` esté activo. El admin siempre puede seguir
// trabajando. No es un redirect (evita loops): se renderiza acá mismo
// la página de mantenimiento y se corta la ejecución.
require_once __DIR__ . '/../models/ConfiguracionSistema.php';
$rutas_permitidas_en_mantenimiento = ['login', 'unauthorized'];
$mantenimiento_activo = ConfiguracionSistema::getValor('mantenimiento_modo', '0') === '1';
$es_admin_logueado = is_logged_in() && has_role('admin');

if ($mantenimiento_activo && !$es_admin_logueado && !in_array($page, $rutas_permitidas_en_mantenimiento, true)) {
    http_response_code(503);
    require __DIR__ . '/../views/errors/mantenimiento.php';
    exit;
}

// ========================================
// 5. Cargar la vista correspondiente
// ========================================
ob_start();
$view_found = false;

if (in_array($page, $public_routes)) {
    switch ($page) {
        case 'home':
        case 'login':
            require __DIR__ . '/../views/auth/login.php';
            $view_found = true;
            break;
        case 'register_alumno':
            require __DIR__ . '/../views/auth/register_alumno.php';
            $view_found = true;
            break;
        case 'primer_acceso':
            require __DIR__ . '/../views/auth/primer_acceso.php';
            $view_found = true;
            break;
        case 'unauthorized':
            if (!is_logged_in()) {
                redirect('index.php?page=login');
            }
            require __DIR__ . '/../views/errors/unauthorized.php';
            $view_found = true;
            break;
    }
} else {
    $parts = explode('/', $page);
    $role  = $parts[0] ?? '';
    $view  = $parts[1] ?? 'dashboard';

    $view_path = __DIR__ . "/../views/{$role}/{$view}.php";
    if (file_exists($view_path)) {
        require $view_path;
        $view_found = true;
    }
}

if (!$view_found) {
    require __DIR__ . '/../views/errors/404.php';
}

$content = ob_get_clean();

$is_auth_error_view = in_array($page, ['login', 'register_alumno', 'primer_acceso', 'unauthorized']) || !$view_found;
if (!$is_auth_error_view) {
    require __DIR__ . '/../views/layouts/main.php';
} else {
    echo $content;
}