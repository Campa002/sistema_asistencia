<?php
require_role('admin');
require_once __DIR__ . '/../../models/Asistencia.php';
require_once __DIR__ . '/../../controllers/AdminAsistenciasController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminAsistenciasController::index();
$registros = $data['registros'];
$cursos = $data['cursos'];
$preceptores = $data['preceptores'];
$materias = $data['materias'];
$filters = $data['filters'];
$registroSeleccionado = $data['registroSeleccionado'];
$detallesSeleccionados = $data['detallesSeleccionados'];
$auditoriaSeleccionada = $data['auditoriaSeleccionada'];
$pagination = $data['pagination'] ?? [];
$editDataMap = $data['editDataMap'];
// Get all registros for stats (not just paginated)
$allRegistrosResult = Asistencia::getAll($filters, 1, 'all');
$allRegistros = $allRegistrosResult['registros'];

// Estados de asistencia
$estados_alumnos = ['presente', 'ausente', 'llegada_tarde', 'ausente_con_presente', 'justificado', 'retirado_anticipado'];
?>
<div class="dashboard-wrapper">
<?php if (isset($_GET['debug_edit'])): ?>
    <pre style="background:#111;color:#0f0;padding:12px;white-space:pre-wrap;position:fixed;top:60px;left:0;z-index:100000;width:100%;max-height:400px;overflow:auto;">
        REGISTROS VISIBLES:
        <?= htmlspecialchars(json_encode(array_column($registros, 'id'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>

        EDIT DATA MAP KEYS:
        <?= htmlspecialchars(json_encode(array_keys($editDataMap ?? []), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>
    </pre>
<?php endif; ?>
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
                    <h1>Historial de Asistencias</h1>
                    <p>Registro detallado de todas las asistencias institucionales.</p>
                </div>
                <div class="date-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <?= e(format_date_long_argentina()) ?>
                </div>
            </div>
            
            <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Total Registros</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 32px; font-weight: 800;"><?= e(count($allRegistros)) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #28A745; font-weight: 800; font-size: 14px; text-transform: uppercase;">Cerrados</span>
                    </div>
                    <div class="stat-value" style="color: #28A745; font-size: 32px; font-weight: 800;"><?= count(array_filter($allRegistros, fn($r) => $r['estado_calculado'] === 'cerrada')) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #FFC107; font-weight: 800; font-size: 14px; text-transform: uppercase;">Abiertos</span>
                    </div>
                    <div class="stat-value" style="color: #FFC107; font-size: 32px; font-weight: 800;"><?= count(array_filter($allRegistros, fn($r) => $r['estado_calculado'] === 'abierta')) ?></div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                    <div class="stat-header">
                        <span style="color: white; font-weight: 800; font-size: 14px; text-transform: uppercase;">Modificados</span>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 32px; font-weight: 800;"><?= count(array_filter($allRegistros, fn($r) => $r['estado_calculado'] === 'modificada')) ?></div>
                </div>
                <div class="stat-card accent">
                    <div class="stat-header">
                        <span style="color: white; font-weight: 800; font-size: 14px; text-transform: uppercase;">Anulados</span>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 32px; font-weight: 800;"><?= count(array_filter($allRegistros, fn($r) => $r['estado_calculado'] === 'anulada')) ?></div>
                </div>
            </div>
            
            <div class="data-table-container" style="margin-top: 32px;">
                <form method="GET" action="index.php" style="display: contents;">
                    <input type="hidden" name="page" value="admin/asistencias">
                    <input type="hidden" name="page_num" value="1">
                    <div class="table-header">
                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Curso:</span>
                                <select class="filter-input" name="curso_id" style="min-width: 180px;">
                                    <option value="">Todos</option>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?= e($curso['id']) ?>" <?= $filters['curso_id'] == $curso['id'] ? 'selected' : '' ?>><?= e($curso['anio'] . '° ' . $curso['division']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Turno:</span>
                                <select class="filter-input" name="turno" style="min-width: 140px;">
                                    <option value="">Todos</option>
                                    <option value="mañana" <?= $filters['turno'] === 'mañana' ? 'selected' : '' ?>>Mañana</option>
                                    <option value="tarde" <?= $filters['turno'] === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                                    <option value="vespertino" <?= $filters['turno'] === 'vespertino' ? 'selected' : '' ?>>Vespertino</option>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Preceptor:</span>
                                <select class="filter-input" name="preceptor_id" style="min-width: 180px;">
                                    <option value="">Todos</option>
                                    <?php foreach ($preceptores as $preceptor) : ?>
                                        <option value="<?= e($preceptor['id']) ?>" <?= $filters['preceptor_id'] == $preceptor['id'] ? 'selected' : '' ?>><?= e($preceptor['apellido'] . ', ' . $preceptor['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Desde:</span>
                                <input type="date" class="filter-input" name="fecha_desde" value="<?= e($filters['fecha_desde']) ?>" style="min-width: 160px;">
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Hasta:</span>
                                <input type="date" class="filter-input" name="fecha_hasta" value="<?= e($filters['fecha_hasta']) ?>" style="min-width: 160px;">
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Estado:</span>
                                <select class="filter-input" name="estado" style="min-width: 140px;">
                                    <option value="">Todos</option>
                                    <option value="abierta" <?= $filters['estado'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>
                                    <option value="cerrada" <?= $filters['estado'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                    <option value="modificada" <?= $filters['estado'] === 'modificada' ? 'selected' : '' ?>>Modificada</option>
                                    <option value="anulada" <?= $filters['estado'] === 'anulada' ? 'selected' : '' ?>>Anulada</option>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Por página:</span>
                                <select class="filter-input" name="per_page" style="min-width: 100px;" onchange="this.form.submit()">
                                    <option value="10" <?= (isset($pagination['per_page']) && $pagination['per_page'] == 10) ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= (isset($pagination['per_page']) && $pagination['per_page'] == 25) ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= (isset($pagination['per_page']) && $pagination['per_page'] == 50) ? 'selected' : '' ?>>50</option>
                                    <option value="all" <?= (isset($pagination['per_page']) && $pagination['per_page'] === 'all') ? 'selected' : '' ?>>Todos</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                                Filtrar
                            </button>
                            <a href="index.php?page=admin/asistencias" class="btn-outline" style="padding: 8px 16px;">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
                <div class="table-meta-bar" style="padding: 14px 24px; color: #6C757D; font-size: 14px; font-weight: 600;">
                    <?php
                    if (isset($pagination['per_page']) && $pagination['per_page'] === 'all') {
                        ?>
                        Mostrando todos los <?= e($pagination['total'] ?? count($allRegistros)) ?> registros
                        <?php
                    } else {
                        $start = ($pagination['page_num'] - 1) * $pagination['per_page'] + 1;
                        $end = min($pagination['page_num'] * $pagination['per_page'], $pagination['total']);
                        ?>
                        Mostrando <?= e($start) ?>–<?= e($end) ?> de <?= e($pagination['total'] ?? count($allRegistros)) ?> registros
                        <?php
                    }
                    ?>
                </div>
                <div class="data-table-scroll" style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr style="background: #E8F4FC;">
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Curso / Div</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Materia</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Día</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Turno</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Bloque</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Horario</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Preceptor</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                            <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registros)) : ?>
                            <tr>
                                <td colspan="9" style="padding: 48px 24px; text-align: center; color: #6C757D;">
                                    No hay registros de asistencia para mostrar.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($registros as $registro) : ?>
                                <?php $bloque_info = Asistencia::getBloqueHorarioInfo($registro['curso_turno'], $registro['bloque_horario'] ?? 'primera_hora'); ?>
                                <tr>
                                    <td style="padding: 16px 24px; font-size: 16px; font-weight: 800; color: #071D3A;"><?= e($registro['anio'] . '° ' . $registro['division']) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e($registro['materia_nombre']) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e(date('d/m/Y', strtotime($registro['fecha']))) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A; font-weight: 600;"><?= e(ucfirst($registro['curso_turno'])) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e($bloque_info['display'] ?? '—') ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;"><?= e($bloque_info ? $bloque_info['inicio'] . ' - ' . $bloque_info['fin'] : '—') ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e($registro['preceptor_apellido'] . ', ' . $registro['preceptor_nombre']) ?></td>
                                    <td style="padding: 16px 24px;">
                                        <?php
                                        $estado_class = match ($registro['estado_calculado']) {
                                            'abierta' => 'background: #FFF3CD; color: #856404;',
                                            'cerrada' => 'background: #D4EDDA; color: #155724;',
                                            'modificada' => 'background: #D1ECF1; color: #0C5460;',
                                            'anulada' => 'background: #F8D7DA; color: #721C24;',
                                            default => 'background: #E9ECEF; color: #383D41;',
                                        };
                                        ?>
                                        <span class="status-badge" style="<?= $estado_class ?> padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                            <?= e(ucfirst($registro['estado_calculado'])) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 24px; text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                            <a href="index.php?page=admin/asistencias&registro_id=<?= e($registro['id']) ?><?= buildQueryString(['page', 'registro_id']) ?>" class="btn-icon" style="color: #6C757D;" title="Ver detalles">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>
                                            <?php if ($registro['estado_calculado'] !== 'anulada') : ?>
                                                <button type="button" onclick="window.openEditModal(<?= (int) $registro['id'] ?>)" class="btn-icon" style="color: #28A745;" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="window.openAnularModal(<?= (int)$registro['id'] ?>)" class="btn-icon" style="color: #DC3545;" title="Anular">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
                <?php if (isset($pagination['total_pages']) && $pagination['total_pages'] > 1) : ?>
                    <div class="pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 1px solid #E9ECEF; flex-wrap: wrap;">
                        <div class="a">
                            <?php if ($pagination['page_num'] > 1) : ?>
                                <button class="b" type="button" onclick="goToAttendancePage(<?= e($pagination['page_num'] - 1) ?>)" class="pagination-btn" style="padding: 8px 16px; border: 1px solid #DEE2E6; border-radius: 8px; text-decoration: none; color: #071D3A; cursor: pointer; background: white;">Anterior</button>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++) : ?>
                                <?php if ($i == $pagination['page_num']) : ?>
                                    <span class="pagination-btn active" style="padding: 8px 16px; border: 1px solid #071D3A; border-radius: 8px; background: #071D3A; color: white;"><?= e($i) ?></span>
                                <?php else : ?>
                                    <button type="button" onclick="goToAttendancePage(<?= e($i) ?>)" class="pagination-btn" style="padding: 8px 16px; border: 1px solid #DEE2E6; border-radius: 8px; text-decoration: none; color: #071D3A; cursor: pointer; background: white;"><?= e($i) ?></button>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="a">
                            <?php if ($pagination['page_num'] < $pagination['total_pages']) : ?>
                                <button class="b" type="button" onclick="goToAttendancePage(<?= e($pagination['page_num'] + 1) ?>)" class="pagination-btn" style="padding: 8px 16px; border: 1px solid #DEE2E6; border-radius: 8px; text-decoration: none; color: #071D3A; cursor: pointer; background: white;">Siguiente</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Ver Detalle -->
    <?php if ($registroSeleccionado) : ?>
        <div id="detalleModal" class="modal" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
            <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 1100px; width: 90%; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="margin: 0; color: #071D3A;">Detalle de Asistencia</h2>
                    <a href="index.php?page=admin/asistencias" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D; text-decoration: none;">&times;</a>
                </div>

                <!-- Información General -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #F8F9FA; padding: 16px; border-radius: 8px;">
                        <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Curso</strong>
                        <div style="font-size: 16px; font-weight: 600; color: #071D3A; margin-top: 4px;"><?= e($registroSeleccionado['anio'] . '° ' . $registroSeleccionado['division']) ?></div>
                    </div>
                    <div style="background: #F8F9FA; padding: 16px; border-radius: 8px;">
                        <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Materia</strong>
                        <div style="font-size: 16px; font-weight: 600; color: #071D3A; margin-top: 4px;"><?= e($registroSeleccionado['materia_nombre']) ?></div>
                    </div>
                    <div style="background: #F8F9FA; padding: 16px; border-radius: 8px;">
                        <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Preceptor</strong>
                        <div style="font-size: 16px; font-weight: 600; color: #071D3A; margin-top: 4px;"><?= e($registroSeleccionado['preceptor_apellido'] . ', ' . $registroSeleccionado['preceptor_nombre']) ?></div>
                    </div>
                    <div style="background: #F8F9FA; padding: 16px; border-radius: 8px;">
                        <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Fecha</strong>
                        <div style="font-size: 16px; font-weight: 600; color: #071D3A; margin-top: 4px;"><?= e(date('d/m/Y', strtotime($registroSeleccionado['fecha']))) ?></div>
                    </div>
                </div>

                <!-- Alumnos -->
                <h3 style="margin: 24px 0 16px 0; color: #071D3A;">Alumnos</h3>
                <?php if (empty($detallesSeleccionados)) : ?>
                    <p style="color: #6C757D; text-align: center; padding: 24px;">No hay detalles de asistencia para este registro.</p>
                <?php else : ?>
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">DNI</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Apellido</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Nombre</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detallesSeleccionados as $detalle) : ?>
                                <?php
                                $estado_color = match ($detalle['estado']) {
                                    'presente' => '#D4EDDA',
                                    'ausente' => '#F8D7DA',
                                    'llegada_tarde' => '#FFF3CD',
                                    'ausente_con_presente' => '#E2E3E5',
                                    'justificado' => '#D1ECF1',
                                    'retiro_anticipado' => '#F5C6CB',
                                    default => '#E9ECEF',
                                };
                                $estado_texto = ucfirst(str_replace('_', ' ', $detalle['estado']));
                                ?>
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($detalle['dni'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A; font-weight: 600;"><?= e($detalle['apellido']) ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($detalle['nombre']) ?></td>
                                    <td style="padding: 12px 16px;">
                                        <span style="background: <?= $estado_color ?>; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                            <?= e($estado_texto) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Auditoría -->
                <h3 style="margin: 24px 0 16px 0; color: #071D3A;">Auditoría</h3>
                <?php if (empty($auditoriaSeleccionada)) : ?>
                    <p style="color: #6C757D; text-align: center; padding: 24px;">No hay registros de auditoría para esta asistencia.</p>
                <?php else : ?>
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Fecha/Hora</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Usuario</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Acción</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Campo</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Valor Anterior</th>
                                <th style="text-align: left; padding: 12px 16px; font-size: 13px; font-weight: 700; color: #6C757D;">Valor Nuevo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditoriaSeleccionada as $audit) : ?>
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e(date('d/m/Y H:i', strtotime($audit['created_at']))) ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($audit['admin_apellido'] . ', ' . $audit['admin_nombre']) ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e(ucfirst($audit['accion'])) ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #6C757D;"><?= e($audit['campo_modificado'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #6C757D;"><?= e($audit['valor_anterior'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($audit['valor_nuevo'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <?php if ($registroSeleccionado['estado_calculado'] !== 'anulada'): ?>
                        <button type="button" onclick="window.openEditFromDetail(<?= (int)$registroSeleccionado['id'] ?>)" class="btn btn-primary" style="padding: 8px 16px;">Editar</button>
                    <?php endif; ?>
                    <a href="index.php?page=admin/asistencias<?= buildQueryString(['page', 'registro_id']) ?>" class="btn-outline" style="padding: 8px 16px;">Cerrar</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal Editar -->
    <div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 1000px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <form method="POST" action="index.php?page=admin/asistencias">
                <input type="hidden" name="action" value="editar_asistencia">
                <input type="hidden" name="registro_id" id="edit_registro_id">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="margin: 0; color: #071D3A;">Editar Asistencia</h2>
                    <button type="button" onclick="window.closeEditModal()" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
                </div>

                <!-- Campos generales -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label for="edit_curso_id" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Curso</label>
                        <select name="curso_id" id="edit_curso_id" class="filter-input" style="width: 100%;">
                            <?php foreach ($cursos as $curso) : ?>
                                <option value="<?= e($curso['id']) ?>"><?= e($curso['anio'] . '° ' . $curso['division']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_materia_id" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Materia</label>
                        <select name="materia_id" id="edit_materia_id" class="filter-input" style="width: 100%;">
                            <?php foreach ($materias as $materia) : ?>
                                <option value="<?= e($materia['id']) ?>"><?= e($materia['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_preceptor_id" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Preceptor</label>
                        <select name="preceptor_id" id="edit_preceptor_id" class="filter-input" style="width: 100%;">
                            <?php foreach ($preceptores as $preceptor) : ?>
                                <option value="<?= e($preceptor['id']) ?>"><?= e($preceptor['apellido'] . ', ' . $preceptor['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_fecha" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Fecha</label>
                        <input type="date" name="fecha" id="edit_fecha" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label for="edit_bloque_horario" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Bloque Horario</label>
                        <select name="bloque_horario" id="edit_bloque_horario" class="filter-input" style="width: 100%;">
                            <option value="primera_hora">1ra Hora</option>
                            <option value="segunda_hora">2da Hora</option>
                        </select>
                    </div>
                    <div>
                                <label for="edit_estado" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Estado</label>
                                <select name="estado" id="edit_estado" class="filter-input" style="width: 100%;">
                                    <option value="abierta">Abierta</option>
                                    <option value="cerrada">Cerrada</option>
                                    <option value="modificada">Modificada</option>
                                </select>
                            </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="edit_observaciones" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Observaciones</label>
                    <textarea name="observaciones" id="edit_observaciones" class="filter-input" style="width: 100%; min-height: 80px;"></textarea>
                </div>

                <!-- Alumnos y sus estados -->
                <h3 style="margin: 0 0 16px 0; color: #071D3A;">Estados de Alumnos</h3>
                <div id="edit_alumnos_container" style="max-height: 300px; overflow-y: auto; margin-bottom: 24px; border: 1px solid #E9ECEF; border-radius: 8px;"></div>

                <div style="margin-bottom: 24px;">
                    <label for="edit_observaciones_auditoria" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Observaciones para Auditoría</label>
                    <textarea name="observaciones_auditoria" id="edit_observaciones_auditoria" class="filter-input" style="width: 100%; min-height: 60px;"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="window.closeEditModal()" class="btn-outline" style="padding: 8px 16px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Anular -->
    <div id="anularModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%;">
            <form method="POST" action="index.php?page=admin/asistencias">
                <input type="hidden" name="action" value="anular_asistencia">
                <input type="hidden" name="registro_id" id="anular_registro_id">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="margin: 0; color: #071D3A;">Anular Asistencia</h2>
                    <button type="button" onclick="window.closeAnularModal()" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
                </div>

                <p style="color: #6C757D; margin-bottom: 24px;">¿Estás seguro que deseas anular esta asistencia? Esta acción no se puede deshacer.</p>

                <div style="margin-bottom: 24px;">
                    <label for="anular_observaciones" style="display: block; font-size: 14px; font-weight: 600; color: #071D3A; margin-bottom: 8px;">Observaciones</label>
                    <textarea name="observaciones" id="anular_observaciones" class="filter-input" style="width: 100%; min-height: 80px;"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="window.closeAnularModal()" class="btn-outline" style="padding: 8px 16px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; background: #DC3545;">Anular</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.editDataMap = <?= json_encode(
            $editDataMap ?? [],
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        ) ?>;
        window.estadosAlumnos = <?= json_encode($estados_alumnos) ?>;
        
        window.goToAttendancePage = function (pageNumber) {
            sessionStorage.setItem(
                'asistenciasScrollY',
                String(window.scrollY)
            );
            const url = new URL(window.location.href);
            url.searchParams.set('page_num', pageNumber);
            window.location.href = url.toString();
        };
        
        document.addEventListener('DOMContentLoaded', function () {
            const savedScroll = sessionStorage.getItem('asistenciasScrollY');
            if (savedScroll !== null) {
                window.scrollTo({
                    top: Number(savedScroll),
                    behavior: 'instant'
                });
                sessionStorage.removeItem('asistenciasScrollY');
            }
        });
        
        window.openEditModal = function (registroId) {
            console.log('registroId solicitado:', registroId);
            const key = String(registroId);
            console.log('key:', key);
            console.log('editDataMap keys:', Object.keys(window.editDataMap || {}));
            const payload = window.editDataMap?.[key];
            console.log('payload:', payload);

            if (!payload || !payload.registro) {
                console.error(
                    'No se encontró información de edición',
                    {
                        registroId,
                        key,
                        clavesDisponibles: Object.keys(window.editDataMap || {}),
                        editDataMap: window.editDataMap
                    }
                );

                alert('No se pudo cargar la asistencia para editar.');
                return;
            }
            
            const registro = payload.registro;
            
            document.getElementById('edit_registro_id').value = registro.id;
            document.getElementById('edit_curso_id').value = registro.curso_id;
            document.getElementById('edit_materia_id').value = registro.materia_id;
            document.getElementById('edit_preceptor_id').value = registro.preceptor_id;
            document.getElementById('edit_fecha').value = registro.fecha;
            document.getElementById('edit_bloque_horario').value = registro.bloque_horario || 'primera_hora';
            document.getElementById('edit_estado').value = registro.estado_calculado || registro.estado;
            document.getElementById('edit_observaciones').value = registro.observaciones || '';
            
            // Cargar alumnos
            const container = document.getElementById('edit_alumnos_container');
            let html = '<table class="data-table" style="width: 100%;"><thead><tr style="background: #E8F4FC;"><th style="padding: 8px 12px; text-align: left;">Alumno</th><th style="padding: 8px 12px; text-align: left;">Estado</th></tr></thead><tbody>';
            
            payload.alumnos.forEach(alumno => {
                html += `<tr>
                    <td style="padding: 8px 12px;">${alumno.apellido}, ${alumno.nombre}</td>
                    <td style="padding: 8px 12px;">
                        <select name="alumnos[${alumno.id}]" class="filter-input" style="width: 100%;">`;
                
                window.estadosAlumnos.forEach(estado => {
                    const selected = alumno.estado === estado ? 'selected' : '';
                    const estadoLabel = estado.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    html += `<option value="${estado}" ${selected}>${estadoLabel}</option>`;
                });
                
                html += `</select>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
            
            document.getElementById('editModal').style.display = 'flex';
        };
        
        window.openEditFromDetail = function (registroId) {
            const detalleModal = document.getElementById('detalleModal');
            if (detalleModal) {
                detalleModal.style.display = 'none';
            }
            window.openEditModal(registroId);
        };
        
        window.closeEditModal = function () {
            document.getElementById('editModal').style.display = 'none';
        };
        
        window.openAnularModal = function (registroId) {
            document.getElementById('anular_registro_id').value = registroId;
            document.getElementById('anularModal').style.display = 'flex';
        };
        
        window.closeAnularModal = function () {
            document.getElementById('anularModal').style.display = 'none';
        };
    </script>
</div>
