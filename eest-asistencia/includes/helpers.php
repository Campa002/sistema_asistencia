<?php
require_once __DIR__ . '/../config/constants.php';

function url(string $path = ''): string {
    return BASE_URL . ltrim($path, '/');
}

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    // Si ya es URL completa, usarla directo; si no, construirla
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . url($path));
    }
    exit;
}

function input(string $key, $default = null): mixed {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function is_post(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function format_date(string $date, string $format = 'd/m/Y'): string {
    return (new DateTime($date))->format($format);
}

function format_date_long_argentina(?string $date = 'now'): string {
    $timezone = new DateTimeZone('America/Argentina/Buenos_Aires');
    $dateTime = $date instanceof DateTimeInterface
        ? DateTimeImmutable::createFromInterface($date)->setTimezone($timezone)
        : new DateTimeImmutable($date ?: 'now', $timezone);

    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'es_AR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'America/Argentina/Buenos_Aires',
            IntlDateFormatter::GREGORIAN,
            "EEEE, d 'de' MMMM 'de' y"
        );

        $formatted = $formatter->format($dateTime);
        if ($formatted !== false) {
            return mb_convert_case($formatted, MB_CASE_TITLE, 'UTF-8');
        }
    }

    $dias = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];
    $meses = ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'];

    $dia = $dias[$dateTime->format('l')] ?? $dateTime->format('l');
    $mes = $meses[$dateTime->format('F')] ?? strtolower($dateTime->format('F'));

    return sprintf('%s, %d de %s de %d', $dia, (int) $dateTime->format('d'), $mes, (int) $dateTime->format('Y'));
}

/**
 * Fecha corta en español, ej. "24 may, 2026" — para tarjetas/listas de los
 * portales de rol donde no entra la fecha larga. Mismo criterio que
 * format_date_long_argentina(): IntlDateFormatter si está disponible, con
 * mapa de respaldo manual, siempre en español y sin depender del locale del
 * sistema operativo.
 */
function format_date_short_argentina(?string $date = 'now'): string {
    $timezone = new DateTimeZone('America/Argentina/Buenos_Aires');
    $dateTime = $date instanceof DateTimeInterface
        ? DateTimeImmutable::createFromInterface($date)->setTimezone($timezone)
        : new DateTimeImmutable($date ?: 'now', $timezone);

    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'es_AR',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            'America/Argentina/Buenos_Aires',
            IntlDateFormatter::GREGORIAN,
            "d MMM, y"
        );
        $formatted = $formatter->format($dateTime);
        if ($formatted !== false) {
            return rtrim(str_replace('.', '', $formatted));
        }
    }

    $meses = ['January' => 'Ene', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Abr', 'May' => 'May', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Ago', 'September' => 'Sep', 'October' => 'Oct', 'November' => 'Nov', 'December' => 'Dic'];
    $mes = $meses[$dateTime->format('F')] ?? $dateTime->format('M');

    return sprintf('%d %s, %d', (int) $dateTime->format('d'), $mes, (int) $dateTime->format('Y'));
}

function get_role_name(string $role): string {
    return [
        'admin'      => 'Administrador',
        'directivo'  => 'Directivo',
        'preceptor'  => 'Preceptor',
        'alumno'     => 'Alumno',
        'padre_tutor'=> 'Padre/Tutor',
    ][$role] ?? $role;
}

function dashboard_url_by_role(?string $role = null): string {
    $rol = $role ?? ($_SESSION['rol'] ?? null);
    if (!$rol) return url('index.php?page=login');
    return url([
        'admin'      => 'index.php?page=admin/dashboard',
        'directivo'  => 'index.php?page=directivo/directivo',
        'preceptor'  => 'index.php?page=preceptor/preceptor',
        'alumno'     => 'index.php?page=alumno/alumnos',
        'padre_tutor'=> 'index.php?page=padre_tutor/padre-tutor',
    ][$rol] ?? 'index.php?page=login');
}

function flash(string $key, mixed $value = null): mixed {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function has_flash(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

function buildQueryString(array $exclude = []): string {
    $params = $_GET;
    foreach ($exclude as $key) {
        unset($params[$key]);
    }
    $query = http_build_query($params);
    return $query ? '&' . $query : '';
}
