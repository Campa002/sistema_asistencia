<?php
require_role('admin');
require_once __DIR__ . '/../../models/Comunicado.php';
require_once __DIR__ . '/../../controllers/AdminComunicadosController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminComunicadosController::index();
$comunicados = $data['comunicados'];
$stats = $data['stats'];
$filters = $data['filters'];

$roles_nombres = [
    'admin' => 'Administrador',
    'directivo' => 'Directivo',
    'preceptor' => 'Preceptor',
    'alumno' => 'Alumno',
    'padre_tutor' => 'Padre/Tutor',
    'todos' => 'Toda la comunidad'
];

$prioridad_colores = [
    'normal' => ['bg' => '#E8F4FC', 'color' => '#006397'],
    'alta' => ['bg' => '#FFF3CD', 'color' => '#856404'],
    'urgente' => ['bg' => '#F8D7DA', 'color' => '#721C24']
];

function formatearDestinatarios(string $rolDestino, array $nombres): string {
    if ($rolDestino === '' ) return '-';
    if ($rolDestino === 'todos') return $nombres['todos'];
    $partes = array_filter(array_map('trim', explode(',', $rolDestino)));
    $etiquetas = array_map(fn($r) => $nombres[$r] ?? $r, $partes);
    return implode(', ', $etiquetas);
}
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
                    <h1>Comunicados Globales</h1>
                    <p>Avisos institucionales para padres, alumnos y preceptores</p>
                </div>
                <button type="button" class="btn btn-primary" onclick="openModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    Crear Comunicado
                </button>
            </div>

            <!-- Mensajes flash -->
            <?php if (has_flash('success')): ?>
                <div class="alert alert-success" style="padding: 16px; border-radius: 8px; background: #d4edda; color: #155724; margin-bottom: 24px;">
                    <?= e(flash('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (has_flash('errors')): ?>
                <div class="alert alert-danger" style="padding: 16px; border-radius: 8px; background: #f8d7da; color: #721c24; margin-bottom: 24px;">
                    <?php foreach (flash('errors') as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
                <div class="stat-card">
                    <div class="stat-label">Total de Comunicados</div>
                    <div class="stat-value"><?= e($stats['total']) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Activos</div>
                    <div class="stat-value" style="color: #28A745;"><?= e($stats['activos']) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Inactivos</div>
                    <div class="stat-value" style="color: #6C757D;"><?= e($stats['inactivos']) ?></div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="dashboard-card" style="margin-bottom: 24px;">
                <div class="table-header" style="padding: 20px 24px;">
                    <form method="GET" action="index.php" class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                        <input type="hidden" name="page" value="admin/comunicados">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Estado</label>
                            <select name="estado" class="filter-input" style="min-width: 140px;">
                                <option value="">Todos</option>
                                <option value="activo" <?= $filters['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $filters['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Prioridad</label>
                            <select name="prioridad" class="filter-input" style="min-width: 140px;">
                                <option value="">Todas</option>
                                <option value="normal" <?= $filters['prioridad'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="alta" <?= $filters['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                                <option value="urgente" <?= $filters['prioridad'] === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 220px;">
                            <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Buscar</label>
                            <input type="text" name="busqueda" class="filter-input" style="width: 100%;" placeholder="Título o mensaje" value="<?= e($filters['busqueda']) ?>">
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="index.php?page=admin/comunicados" class="btn-outline">Limpiar</a>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listado -->
            <div class="data-table-container">
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr style="background: #E8F4FC;">
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Título</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Destinatarios</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Prioridad</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Publicación</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Vencimiento</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                            <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($comunicados)): ?>
                            <tr>
                                <td colspan="7" style="padding: 48px 24px; text-align: center; color: #6C757D;">
                                    No hay comunicados para los filtros seleccionados.
                                </td>
                            </tr>
                        <?php else: foreach ($comunicados as $c): ?>
                            <?php $prio = $prioridad_colores[$c['prioridad']] ?? $prioridad_colores['normal']; ?>
                            <tr>
                                <td style="padding: 16px 24px; font-size: 16px; font-weight: 700; color: #071D3A;"><?= e($c['titulo']) ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;"><?= e(formatearDestinatarios($c['rol_destino'], $roles_nombres)) ?></td>
                                <td style="padding: 16px 24px;">
                                    <span style="background: <?= $prio['bg'] ?>; color: <?= $prio['color'] ?>; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?= e(ucfirst($c['prioridad'])) ?></span>
                                </td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e(date('d/m/Y', strtotime($c['created_at']))) ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= $c['fecha_expiracion'] ? e(date('d/m/Y', strtotime($c['fecha_expiracion']))) : '-' ?></td>
                                <td style="padding: 16px 24px;">
                                    <?php if ($c['activo'] == 1): ?>
                                        <span style="background: #28A745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase;">Activo</span>
                                    <?php else: ?>
                                        <span style="background: #6C757D; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase;">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                        <button type="button" class="btn-icon" style="color: var(--support-gray);" onclick="openViewModal(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" title="Ver comunicado">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-icon" style="color: var(--accent);" onclick="openEditModal(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <form method="POST" action="index.php" style="display: inline;" onsubmit="return confirm('<?= $c['activo'] == 1 ? '¿Desactivar este comunicado? Dejará de mostrarse a los destinatarios.' : '¿Reactivar este comunicado?' ?>');">
                                            <input type="hidden" name="action" value="admin_toggle_comunicado">
                                            <input type="hidden" name="id" value="<?= e($c['id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <?php if ($c['activo'] == 1): ?>
                                                <button type="submit" class="btn-icon" style="color: var(--danger);" title="Eliminar (baja lógica)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn-icon" style="color: var(--accent);" title="Reactivar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="1 4 1 10 7 10"/>
                                                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Crear Comunicado -->
    <div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Crear Comunicado</h2>
                <button type="button" onclick="closeModal('createModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_create_comunicado">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Título</label>
                    <input type="text" name="titulo" required maxlength="255" class="filter-input" style="width: 100%;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Mensaje</label>
                    <textarea name="contenido" required rows="4" class="filter-input" style="width: 100%; resize: vertical;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Prioridad</label>
                        <select name="prioridad" class="filter-input" style="width: 100%;">
                            <option value="normal">Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_expiracion" class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="margin-bottom: 8px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Destinatarios</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 16px;">
                        <?php foreach (Comunicado::$roles_validos as $rol): ?>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: #071D3A; cursor: pointer;">
                                <input type="checkbox" name="destinatarios[]" value="<?= e($rol) ?>"> <?= e($roles_nombres[$rol]) ?>
                            </label>
                        <?php endforeach; ?>
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: #071D3A; font-weight: 700; cursor: pointer;">
                            <input type="checkbox" name="destinatarios[]" value="todos"> Toda la comunidad
                        </label>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" class="btn-outline" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Comunicado</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Comunicado -->
    <div id="viewModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Detalle del Comunicado</h2>
                <button type="button" onclick="closeModal('viewModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <div id="viewModalContent"></div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-outline" onclick="closeModal('viewModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Comunicado -->
    <div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Editar Comunicado</h2>
                <button type="button" onclick="closeModal('editModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form id="editForm" method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_update_comunicado">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" id="edit_id">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Título</label>
                    <input type="text" name="titulo" id="edit_titulo" required maxlength="255" class="filter-input" style="width: 100%;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Mensaje</label>
                    <textarea name="contenido" id="edit_contenido" required rows="4" class="filter-input" style="width: 100%; resize: vertical;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Prioridad</label>
                        <select name="prioridad" id="edit_prioridad" class="filter-input" style="width: 100%;">
                            <option value="normal">Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_expiracion" id="edit_fecha_expiracion" class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="margin-bottom: 8px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Destinatarios</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 16px;">
                        <?php foreach (Comunicado::$roles_validos as $rol): ?>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: #071D3A; cursor: pointer;">
                                <input type="checkbox" name="destinatarios[]" value="<?= e($rol) ?>"> <?= e($roles_nombres[$rol]) ?>
                            </label>
                        <?php endforeach; ?>
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: #071D3A; font-weight: 700; cursor: pointer;">
                            <input type="checkbox" name="destinatarios[]" value="todos"> Toda la comunidad
                        </label>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" class="btn-outline" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function eHTML(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

const rolesNombres = <?= json_encode($roles_nombres, JSON_UNESCAPED_UNICODE) ?>;

function formatearDestinatariosJS(rolDestino) {
    if (!rolDestino) return '-';
    if (rolDestino === 'todos') return rolesNombres['todos'];
    return rolDestino.split(',').map(r => rolesNombres[r.trim()] || r.trim()).join(', ');
}

function getPrioridadBadgeHTML(prioridad) {
    const colores = { normal: ['#E8F4FC', '#006397'], alta: ['#FFF3CD', '#856404'], urgente: ['#F8D7DA', '#721C24'] };
    const [bg, color] = colores[prioridad] || colores.normal;
    const span = document.createElement('span');
    span.style.cssText = `background:${bg};color:${color};padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;text-transform:uppercase;`;
    span.textContent = prioridad.charAt(0).toUpperCase() + prioridad.slice(1);
    return span.outerHTML;
}

function openViewModal(c) {
    const content = document.getElementById('viewModalContent');
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Título</strong>
                <div style="font-size: 18px; font-weight: 700; color: #071D3A; margin-top: 4px;">${eHTML(c.titulo)}</div>
            </div>
            <div>
                <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Mensaje</strong>
                <div style="font-size: 14px; color: #071D3A; margin-top: 4px; white-space: pre-wrap;">${eHTML(c.contenido)}</div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Destinatarios</strong>
                    <div style="margin-top: 4px;">${eHTML(formatearDestinatariosJS(c.rol_destino))}</div>
                </div>
                <div>
                    <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Prioridad</strong>
                    <div style="margin-top: 4px;">${getPrioridadBadgeHTML(c.prioridad)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Publicado</strong>
                    <div style="margin-top: 4px;">${eHTML(c.created_at)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A; font-size: 12px; text-transform: uppercase;">Vencimiento</strong>
                    <div style="margin-top: 4px;">${eHTML(c.fecha_expiracion || 'Sin vencimiento')}</div>
                </div>
            </div>
        </div>
    `;
    openModal('viewModal');
}

function openEditModal(c) {
    document.getElementById('edit_id').value = c.id;
    document.getElementById('edit_titulo').value = c.titulo;
    document.getElementById('edit_contenido').value = c.contenido;
    document.getElementById('edit_prioridad').value = c.prioridad;
    document.getElementById('edit_fecha_expiracion').value = c.fecha_expiracion || '';

    const destinatarios = c.rol_destino ? c.rol_destino.split(',').map(r => r.trim()) : [];
    document.querySelectorAll('#editForm input[name="destinatarios[]"]').forEach(cb => {
        cb.checked = destinatarios.includes(cb.value);
    });

    openModal('editModal');
}
</script>
