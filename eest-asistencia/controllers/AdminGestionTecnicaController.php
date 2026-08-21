<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ConfiguracionSistema.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../models/BackupRegistro.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminGestionTecnicaController {
    public static function index() {
        require_role('admin');

        $configuraciones = ConfiguracionSistema::getAll();

        $log_filters = [
            'accion' => input('log_accion', ''),
            'usuario_id' => input('log_usuario_id', ''),
            'fecha_desde' => input('log_fecha_desde', ''),
            'fecha_hasta' => input('log_fecha_hasta', ''),
            'busqueda' => input('log_busqueda', '')
        ];
        $log_page = max(1, intval(input('log_page', 1)));
        $log_data = LogActividad::getAll($log_filters, $log_page, 15);
        $acciones_disponibles = LogActividad::getAcciones();

        $backups = BackupRegistro::getAll();

        return [
            'configuraciones' => $configuraciones,
            'log_filters' => $log_filters,
            'log_data' => $log_data,
            'acciones_disponibles' => $acciones_disponibles,
            'backups' => $backups,
            'actividad_hoy' => LogActividad::countHoy()
        ];
    }

    public static function actualizarConfiguracion() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/configuracion');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/configuracion');
            return;
        }

        $configuraciones = ConfiguracionSistema::getAll();
        $errors = [];
        $actualizados = 0;

        foreach ($configuraciones as $conf) {
            $clave = $conf['clave'];
            $tipo = $conf['tipo'];

            if ($tipo === 'booleano') {
                // Un checkbox no marcado no viaja en el POST: su ausencia significa "0".
                $valor = isset($_POST["conf_$clave"]) ? '1' : '0';
            } else {
                if (!array_key_exists("conf_$clave", $_POST)) {
                    continue; // este campo no se envió en este submit
                }
                $valor = trim((string) input("conf_$clave", ''));

                if ($tipo === 'numero' && $valor !== '' && !is_numeric($valor)) {
                    $errors[] = "El valor de \"$clave\" debe ser numérico";
                    continue;
                }
            }

            if ($valor !== (string) $conf['valor']) {
                ConfiguracionSistema::actualizarValor($clave, $valor);
                LogActividad::registrar(
                    $_SESSION['usuario_id'],
                    'MODIFICAR_CONFIGURACION',
                    "Modificó el parámetro \"$clave\"" . (!empty($conf['descripcion']) ? " ({$conf['descripcion']})" : ''),
                    'configuracion_sistema',
                    isset($conf['id']) ? (int) $conf['id'] : null,
                    ['clave' => $clave, 'valor' => $conf['valor']],
                    ['clave' => $clave, 'valor' => $valor]
                );
                $actualizados++;
            }
        }

        if (!empty($errors)) {
            flash('errors', $errors);
        } elseif ($actualizados > 0) {
            flash('success', "Configuración actualizada ($actualizados parámetro" . ($actualizados > 1 ? 's' : '') . ")");
        } else {
            flash('success', 'No hubo cambios para guardar');
        }

        redirect('index.php?page=admin/configuracion');
    }

    /**
     * Ejecuta un backup real de la base de datos con mysqldump. Solo admin
     * (require_role ya lo garantiza) y solo vía POST + CSRF.
     *
     * Diseño de seguridad:
     * - El nombre del archivo lo genera el servidor (timestamp), nunca viene
     *   del usuario.
     * - Las credenciales salen únicamente de config/database.php (constantes
     *   ya existentes), nunca de $_GET/$_POST.
     * - El comando se arma como ARRAY para proc_open (no como string), por lo
     *   que no hay intérprete de shell de por medio: no hay forma de inyectar
     *   comandos aunque alguna de estas piezas cambiara en el futuro.
     * - El password (si existiera) se pasa por variable de entorno MYSQL_PWD,
     *   no como argumento de línea de comandos, para no exponerlo en un
     *   listado de procesos del sistema operativo.
     */
    public static function ejecutarBackup() {
        require_role('admin');
        if (!is_post()) {
            redirect('index.php?page=admin/configuracion');
            return;
        }
        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            redirect('index.php?page=admin/configuracion');
            return;
        }

        $resultado = self::generarBackupReal();

        if ($resultado['exito']) {
            LogActividad::registrar(
                $_SESSION['usuario_id'],
                'EJECUTAR_BACKUP',
                "Ejecutó un backup manual de la base de datos: {$resultado['archivo']} (" . self::formatearTamano($resultado['tamano']) . ")",
                'backups_registro',
                $resultado['registro_id'],
                null,
                ['archivo' => $resultado['archivo'], 'tamano' => $resultado['tamano'], 'estado' => 'completado']
            );
            flash('success', "Backup generado exitosamente: {$resultado['archivo']} (" . self::formatearTamano($resultado['tamano']) . ")");
        } else {
            LogActividad::registrar(
                $_SESSION['usuario_id'],
                'EJECUTAR_BACKUP',
                "Intentó ejecutar un backup manual y falló: {$resultado['error']}",
                'backups_registro',
                $resultado['registro_id'] ?? null,
                null,
                ['estado' => 'fallido', 'error' => $resultado['error']]
            );
            flash('errors', ['No se pudo generar el backup: ' . $resultado['error']]);
        }

        redirect('index.php?page=admin/configuracion');
    }

    /**
     * Descarga un backup ya generado, validando que:
     * - el usuario es admin,
     * - el id corresponde a un registro real en backups_registro con
     *   estado 'completado' (no se acepta ninguna ruta/nombre arbitrario
     *   del usuario, solo un id numérico que se resuelve contra la BD),
     * - el archivo resuelto sigue estando físicamente dentro de backups/
     *   (defensa en profundidad contra path traversal).
     */
    public static function descargarBackup() {
        require_role('admin');

        $id = intval(input('id', 0));
        $registro = $id ? BackupRegistro::getById($id) : null;
        if (!$registro || $registro['estado'] !== 'completado') {
            http_response_code(404);
            echo 'Backup no encontrado.';
            return;
        }

        $backupsDir = realpath(__DIR__ . '/../backups');
        $rutaArchivo = $backupsDir !== false
            ? realpath($backupsDir . DIRECTORY_SEPARATOR . $registro['archivo'])
            : false;

        if ($backupsDir === false || $rutaArchivo === false || strpos($rutaArchivo, $backupsDir) !== 0 || !is_file($rutaArchivo)) {
            http_response_code(404);
            echo 'El archivo de backup ya no está disponible en el servidor.';
            return;
        }

        LogActividad::registrar(
            $_SESSION['usuario_id'],
            'DESCARGAR_BACKUP',
            "Descargó el backup \"{$registro['archivo']}\"",
            'backups_registro',
            $id,
            null,
            null
        );

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        header('X-Content-Type-Options: nosniff');
        readfile($rutaArchivo);
    }

    private static function generarBackupReal(): array {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        $backupsDir = __DIR__ . '/../backups';

        if (!is_dir($backupsDir)) {
            if (!@mkdir($backupsDir, 0755, true) && !is_dir($backupsDir)) {
                $idFallido = BackupRegistro::create('(sin generar)', null, 'manual', $usuarioId, 'fallido', 'No se pudo crear la carpeta backups/');
                return ['exito' => false, 'error' => 'No se pudo crear la carpeta backups/ en el servidor.', 'registro_id' => $idFallido];
            }
        }

        $mysqldumpPath = self::localizarMysqldump();
        if ($mysqldumpPath === null) {
            $idFallido = BackupRegistro::create('(sin generar)', null, 'manual', $usuarioId, 'fallido', 'mysqldump no encontrado en el servidor');
            return ['exito' => false, 'error' => 'mysqldump no está disponible en este servidor. Verifique la instalación de MySQL/MariaDB.', 'registro_id' => $idFallido];
        }

        $nombreArchivo = 'backup_' . date('Y-m-d_His') . '.sql';
        $rutaCompleta = $backupsDir . DIRECTORY_SEPARATOR . $nombreArchivo;

        $comando = [
            $mysqldumpPath,
            '--host=' . DB_HOST,
            '--port=' . DB_PORT,
            '--user=' . DB_USER,
            '--single-transaction',
            '--routines',
            '--triggers',
            DB_NAME
        ];

        $env = getenv();
        if (DB_PASS !== '') {
            $env['MYSQL_PWD'] = DB_PASS;
        }

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['file', $rutaCompleta, 'w'],
            2 => ['pipe', 'w']
        ];

        $process = @proc_open($comando, $descriptorspec, $pipes, $backupsDir, $env ?: null);
        if (!is_resource($process)) {
            $idFallido = BackupRegistro::create($nombreArchivo, null, 'manual', $usuarioId, 'fallido', 'No se pudo iniciar el proceso mysqldump');
            return ['exito' => false, 'error' => 'No se pudo iniciar el proceso mysqldump.', 'registro_id' => $idFallido];
        }

        fclose($pipes[0]);
        $stderrOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $tamano = file_exists($rutaCompleta) ? filesize($rutaCompleta) : 0;

        if ($exitCode !== 0 || $tamano <= 0) {
            // No dejar un archivo parcial/corrupto en el listado de backups.
            if (file_exists($rutaCompleta)) {
                @unlink($rutaCompleta);
            }
            $errorMsg = trim($stderrOutput) !== '' ? trim($stderrOutput) : "mysqldump finalizó con código de salida $exitCode";
            $idFallido = BackupRegistro::create($nombreArchivo, null, 'manual', $usuarioId, 'fallido', mb_substr($errorMsg, 0, 500));
            return ['exito' => false, 'error' => $errorMsg, 'registro_id' => $idFallido];
        }

        $idExito = BackupRegistro::create($nombreArchivo, $tamano, 'manual', $usuarioId, 'completado', null);
        return ['exito' => true, 'archivo' => $nombreArchivo, 'tamano' => $tamano, 'registro_id' => $idExito];
    }

    /**
     * Ubica el binario mysqldump en instalaciones típicas de XAMPP. Si no lo
     * encuentra, se reporta claramente en vez de intentar un workaround.
     */
    private static function localizarMysqldump(): ?string {
        $candidatos = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            dirname(PHP_BINARY) . '\\..\\mysql\\bin\\mysqldump.exe'
        ];
        foreach ($candidatos as $candidato) {
            $real = realpath($candidato);
            if ($real !== false && is_file($real)) {
                return $real;
            }
        }
        return null;
    }

    private static function formatearTamano(int $bytes): string {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}
