<?php
require_role('admin');
require_once __DIR__ . '/../../models/ConfiguracionSistema.php';
require_once __DIR__ . '/../../models/LogActividad.php';
require_once __DIR__ . '/../../models/BackupRegistro.php';
require_once __DIR__ . '/../../controllers/AdminGestionTecnicaController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminGestionTecnicaController::index();
$configuraciones = $data['configuraciones'];
$log_filters = $data['log_filters'];
$log_data = $data['log_data'];
$acciones_disponibles = $data['acciones_disponibles'];
$backups = $data['backups'];
$actividad_hoy = $data['actividad_hoy'];

$conf_por_clave = [];
foreach ($configuraciones as $c) {
    $conf_por_clave[$c['clave']] = $c;
}

function formatBytes(?int $bytes): string {
    if ($bytes === null) return '-';
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

$estado_backup_colores = [
    'completado' => ['bg' => '#28A745', 'color' => 'white'],
    'en_proceso' => ['bg' => '#FFF3CD', 'color' => '#856404'],
    'fallido' => ['bg' => '#DC3545', 'color' => 'white']
];

// El envío de emails ahora se hace por API externa (ver models/EmailService.php),
// no por SMTP propio. Estas 4 claves quedan obsoletas para la edición desde acá,
// pero NO se borran de configuracion_sistema (se conservan como histórico).
$claves_smtp_obsoletas = ['email_smtp_host', 'email_smtp_pass', 'email_smtp_port', 'email_smtp_user'];
$configuraciones_editables = array_filter($configuraciones, function ($c) use ($claves_smtp_obsoletas) {
    return !in_array($c['clave'], $claves_smtp_obsoletas, true);
});
?>

<div class="dashboard-wrapper">
    <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div class="dashboard-header-left">
                <h1>Excelencia Administrativa</h1>
            </div>
            <div class="dashboard-header-right">
                <div class="header-actions">
                    <a href="index.php?page=admin/comunicados" class="header-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        Comunicado Global
                    </a>
                    <a href="index.php?page=admin/configuracion" class="header-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </a>
                </div>
                <a href="index.php?page=admin/perfil" class="header-user">
                    <div class="header-user-info">
                        <div class="name"><?= e($admin_user['nombre'] . ' ' . $admin_user['apellido']) ?></div>
                        <div class="role">Administrador del Sistema</div>
                    </div>
                    <div class="header-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                </a>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="page-header">
                <div class="page-title">
                    <h1>Gestión Técnica</h1>
                    <p>Parámetros del sistema, auditoría de actividad y backups</p>
                </div>
            </div>

            <?php if (has_flash('success')): ?>
                <div class="alert alert-success" style="padding: 16px; border-radius: 8px; background: #d4edda; color: #155724; margin: 24px 0;">
                    <?= e(flash('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (has_flash('errors')): ?>
                <div class="alert alert-danger" style="padding: 16px; border-radius: 8px; background: #f8d7da; color: #721c24; margin: 24px 0;">
                    <?php foreach (flash('errors') as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="margin-top: 24px; display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <!-- Parámetros del Sistema -->
                <div class="dashboard-card" style="border: 2px solid #DEE2E6;">
                    <div class="dashboard-card-header">
                        <h3>Parámetros del Sistema</h3>
                    </div>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="admin_actualizar_configuracion">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px 0;">
                            <?php foreach ($configuraciones_editables as $conf): ?>
                                <div>
                                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #071D3A; font-size: 14px;">
                                        <?= e($conf['descripcion'] ?: $conf['clave']) ?>
                                    </label>
                                    <?php if ($conf['tipo'] === 'booleano'): ?>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="checkbox" name="conf_<?= e($conf['clave']) ?>" value="1" <?= $conf['valor'] == '1' ? 'checked' : '' ?>>
                                            <span style="color: #6C757D; font-size: 13px;">Activado</span>
                                        </label>
                                    <?php elseif ($conf['tipo'] === 'numero'): ?>
                                        <input type="number" step="any" name="conf_<?= e($conf['clave']) ?>" value="<?= e($conf['valor']) ?>" class="filter-input" style="width: 100%;">
                                    <?php else: ?>
                                        <input type="<?= str_contains($conf['clave'], 'pass') ? 'password' : 'text' ?>" name="conf_<?= e($conf['clave']) ?>" value="<?= e($conf['valor']) ?>" placeholder="<?= str_contains($conf['clave'], 'pass') && $conf['valor'] !== '' ? '••••••••' : '' ?>" class="filter-input" style="width: 100%;">
                                    <?php endif; ?>
                                    <div style="font-size: 11px; color: #ADB5BD; margin-top: 4px; text-transform: uppercase;"><?= e($conf['clave']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="padding-top: 16px; border-top: 1px solid #E9ECEF; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">Guardar Parámetros</button>
                        </div>
                    </form>
                </div>

                <!-- Resumen del sistema -->
                <div>
                    <div class="dashboard-card" style="border: 2px solid #DEE2E6; background: #071D3A;">
                        <h3 style="color: white; font-size: 18px; margin-bottom: 20px;">Estado del Sistema</h3>
                        <div style="display: grid; gap: 12px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: rgba(255,255,255,0.7); font-weight: 600;">Versión:</span>
                                <span style="color: white; font-weight: 800;"><?= e(ConfiguracionSistema::getValor('version_sistema', '-')) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: rgba(255,255,255,0.7); font-weight: 600;">Estado:</span>
                                <span style="color: #28A745; font-weight: 800; text-transform: capitalize;"><?= e(ConfiguracionSistema::getValor('estado_sistema', '-')) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: rgba(255,255,255,0.7); font-weight: 600;">Ciclo lectivo:</span>
                                <span style="color: white; font-weight: 800;"><?= e(ConfiguracionSistema::getValor('ciclo_lectivo_actual', '-')) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: rgba(255,255,255,0.7); font-weight: 600;">Modo mantenimiento:</span>
                                <span style="color: <?= ConfiguracionSistema::getValor('mantenimiento_modo', '0') == '1' ? '#FFC107' : 'white' ?>; font-weight: 800;">
                                    <?= ConfiguracionSistema::getValor('mantenimiento_modo', '0') == '1' ? 'Activado' : 'Desactivado' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card" style="margin-top: 24px; border: 2px solid #DEE2E6;">
                        <h3 style="font-size: 16px; font-weight: 700; color: #071D3A; margin-bottom: 16px;">Actividad</h3>
                        <div style="display: grid; gap: 12px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6C757D; font-weight: 600;">Eventos registrados hoy:</span>
                                <span style="color: #071D3A; font-weight: 800;"><?= e($actividad_hoy) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6C757D; font-weight: 600;">Total en el log:</span>
                                <span style="color: #071D3A; font-weight: 800;"><?= e($log_data['total']) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6C757D; font-weight: 600;">Backups registrados:</span>
                                <span style="color: #071D3A; font-weight: 800;"><?= e(count($backups)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (ConfiguracionSistema::getValor('mantenimiento_modo', '0') == '1'): ?>
                <div class="alert alert-danger" style="padding: 16px; border-radius: 8px; background: #fff3cd; color: #856404; margin-top: 24px;">
                    ⚠ El modo mantenimiento está <strong>activado</strong>: todos los roles excepto Administrador están bloqueados en todo el sistema y ven una pantalla de mantenimiento. Tu sesión de administrador puede seguir trabajando con normalidad.
                </div>
            <?php endif; ?>

            <!-- Auditoría / Actividad Reciente -->
            <div class="dashboard-card" id="auditoria" style="margin-top: 24px; border: 2px solid #DEE2E6; padding: 0; scroll-margin-top: 24px;">
                <div class="table-header" style="padding: 20px 24px;">
                    <h3 style="margin: 0 0 16px 0; color: #071D3A;">Auditoría — Actividad Reciente</h3>
                    <form method="GET" action="index.php" class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                        <input type="hidden" name="page" value="admin/configuracion">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 13px; text-transform: uppercase;">Acción</label>
                            <select name="log_accion" class="filter-input" style="min-width: 160px;">
                                <option value="">Todas</option>
                                <?php foreach ($acciones_disponibles as $accion): ?>
                                    <option value="<?= e($accion) ?>" <?= $log_filters['accion'] === $accion ? 'selected' : '' ?>><?= e(ucfirst($accion)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 13px; text-transform: uppercase;">Desde</label>
                            <input type="date" name="log_fecha_desde" class="filter-input" value="<?= e($log_filters['fecha_desde']) ?>">
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 13px; text-transform: uppercase;">Hasta</label>
                            <input type="date" name="log_fecha_hasta" class="filter-input" value="<?= e($log_filters['fecha_hasta']) ?>">
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 200px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 13px; text-transform: uppercase;">Buscar</label>
                            <input type="text" name="log_busqueda" class="filter-input" style="width: 100%;" placeholder="Descripción o tabla" value="<?= e($log_filters['busqueda']) ?>">
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="index.php?page=admin/configuracion" class="btn-outline">Limpiar</a>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>

                <div class="table-meta-bar" style="padding: 14px 24px; color: #6C757D; font-size: 14px; font-weight: 600;">
                    <?php
                        $ini = ($log_data['page_num'] - 1) * $log_data['per_page'] + 1;
                        $fin = min($log_data['page_num'] * $log_data['per_page'], $log_data['total']);
                    ?>
                    <?= $log_data['total'] > 0 ? "Mostrando {$ini}–{$fin} de {$log_data['total']} eventos" : 'Sin eventos registrados para los filtros seleccionados' ?>
                </div>

                <div class="data-table-scroll" style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Fecha y Hora</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Usuario</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Acción</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Descripción</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Tabla</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($log_data['registros'])): ?>
                                <tr><td colspan="5" style="padding: 40px 24px; text-align: center; color: #6C757D;">No hay eventos de auditoría para los filtros seleccionados.</td></tr>
                            <?php else: foreach ($log_data['registros'] as $l): ?>
                                <tr>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A; white-space: nowrap;"><?= e(date('d/m/Y H:i', strtotime($l['created_at']))) ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A;"><?= $l['usuario_nombre'] ? e($l['usuario_apellido'] . ', ' . $l['usuario_nombre']) : '<span style="color:#ADB5BD;">Sistema</span>' ?></td>
                                    <td style="padding: 12px 24px;"><span style="background:#E8F4FC;color:#006397; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 800; text-transform: uppercase;"><?= e($l['accion']) ?></span></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A;"><?= e($l['descripcion'] ?? '-') ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #6C757D;"><?= e($l['tabla_afectada'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($log_data['total_pages'] > 1): ?>
                    <div class="pagination-bar" style="display: flex; justify-content: center; gap: 8px; padding: 20px 24px; border-top: 1px solid #E9ECEF; flex-wrap: wrap;">
                        <?php for ($i = 1; $i <= $log_data['total_pages']; $i++):
                            $qs = $_GET; $qs['log_page'] = $i; $qs['page'] = 'admin/configuracion';
                        ?>
                            <?php if ($i == $log_data['page_num']): ?>
                                <span class="pagination-btn active" style="width: auto; min-width: 36px; padding: 6px 12px; border: 1px solid #071D3A; border-radius: 8px; background: #071D3A; color: white;"><?= e($i) ?></span>
                            <?php else: ?>
                                <a href="index.php?<?= http_build_query($qs) ?>#auditoria" class="pagination-btn" style="width: auto; min-width: 36px; padding: 6px 12px; border: 1px solid #DEE2E6; border-radius: 8px; color: #071D3A; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;"><?= e($i) ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Backups -->
            <div class="dashboard-card" style="margin-top: 24px; border: 2px solid #DEE2E6; padding: 0;">
                <div class="table-header" style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <h3 style="margin: 0; color: #071D3A;">Backups del Sistema</h3>
                    <form method="POST" action="index.php" onsubmit="return confirm('¿Ejecutar un backup completo de la base de datos ahora?');">
                        <input type="hidden" name="action" value="admin_ejecutar_backup">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <button type="submit" class="btn-outline">
                            Ejecutar Backup Ahora
                        </button>
                    </form>
                </div>
                <div class="data-table-scroll" style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Archivo</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Tipo</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Tamaño</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Estado</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Creado por</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Fecha</th>
                                <th style="text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 700; color: #6C757D; text-transform: uppercase;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                                <tr><td colspan="7" style="padding: 40px 24px; text-align: center; color: #6C757D;">No hay backups registrados.</td></tr>
                            <?php else: foreach ($backups as $b): ?>
                                <?php $ecol = $estado_backup_colores[$b['estado']] ?? $estado_backup_colores['completado']; ?>
                                <tr>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A; font-weight: 600;"><?= e($b['archivo']) ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A; text-transform: capitalize;"><?= e($b['tipo']) ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A;"><?= e(formatBytes($b['tamano'] !== null ? (int) $b['tamano'] : null)) ?></td>
                                    <td style="padding: 12px 24px;"><span style="background: <?= $ecol['bg'] ?>; color: <?= $ecol['color'] ?>; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 800; text-transform: uppercase;"><?= e(str_replace('_', ' ', $b['estado'])) ?></span></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #071D3A;"><?= $b['creador_nombre'] ? e($b['creador_apellido'] . ', ' . $b['creador_nombre']) : '-' ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; color: #6C757D; white-space: nowrap;"><?= e(date('d/m/Y H:i', strtotime($b['created_at']))) ?></td>
                                    <td style="padding: 12px 24px; font-size: 13px; white-space: nowrap;">
                                        <?php if ($b['estado'] === 'completado'): ?>
                                            <a href="index.php?page=admin/configuracion/descargar_backup&id=<?= e($b['id']) ?>" style="color: #006397; font-weight: 700; text-decoration: none;">Descargar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
