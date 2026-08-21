<?php
require_role('admin');
require_once __DIR__ . '/../../models/Materia.php';
require_once __DIR__ . '/../../controllers/AdminMateriasController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminMateriasController::index();
$materias = $data['materias'];
$especialidades = $data['especialidades'];
$stats = $data['stats'];
$filters = $data['filters'];
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
                    <button class="header-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </button>
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
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title">
                    <h1>Gestión de Materias</h1>
                    <p>Materias disponibles para armar los horarios de cada curso y determinar la toma de asistencia.</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Agregar Materia
                </button>
            </div>

            <!-- Flash Messages -->
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

            <!-- Stats Grid -->
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Total Materias</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 36px; font-weight: 800;"><?= e($stats['total']) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Activas</span>
                    </div>
                    <div class="stat-value" style="color: #28A745; font-size: 36px; font-weight: 800;"><?= e($stats['activas']) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Inactivas</span>
                    </div>
                    <div class="stat-value" style="color: #DC3545; font-size: 36px; font-weight: 800;"><?= e($stats['inactivas']) ?></div>
                </div>
            </div>

            <!-- Filters & Table -->
            <div class="data-table-container" style="margin-top: 32px;">
                <form method="GET" action="index.php" style="display: contents;">
                    <input type="hidden" name="page" value="admin/materias">
                    <div class="table-header">
                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                            <select name="especialidad_id" class="filter-input" style="min-width: 200px;" onchange="this.form.submit()">
                                <option value="">Especialidad: Todas</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?= e($esp['id']) ?>" <?= (string) $filters['especialidad_id'] === (string) $esp['id'] ? 'selected' : '' ?>><?= e($esp['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="activo" class="filter-input" style="min-width: 160px;" onchange="this.form.submit()">
                                <option value="">Estado: Todos</option>
                                <option value="1" <?= $filters['activo'] === '1' ? 'selected' : '' ?>>Activas</option>
                                <option value="0" <?= $filters['activo'] === '0' ? 'selected' : '' ?>>Inactivas</option>
                            </select>
                            <input type="text" name="busqueda" class="filter-input" placeholder="Buscar por nombre o código" value="<?= e($filters['busqueda']) ?>" style="min-width: 220px;">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="index.php?page=admin/materias" class="btn-outline" style="margin-left: auto;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="14.5 10 4 4 4 20 14.5 14"/>
                                    <path d="M3.5 4L17 14H20a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/>
                                </svg>
                                Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </form>
                <div class="data-table-scroll" style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Nombre de la Materia</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Código</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Especialidad</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Año</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                                <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($materias)): ?>
                                <tr><td colspan="6" style="padding: 40px 24px; text-align: center; color: #6C757D;">No hay materias para los filtros seleccionados.</td></tr>
                            <?php else: foreach ($materias as $materia): ?>
                                <tr>
                                    <td style="padding: 16px 24px; font-size: 16px; font-weight: 700; color: #071D3A;"><?= e($materia['nombre']) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;"><?= e($materia['codigo'] ?? '—') ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;"><?= e($materia['especialidad_nombre'] ?? 'General') ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= $materia['anio_correspondiente'] ? e($materia['anio_correspondiente']) . '°' : '—' ?></td>
                                    <td style="padding: 16px 24px;">
                                        <span class="status-badge <?= $materia['activo'] == 1 ? 'active' : 'inactive' ?>">
                                            <?= $materia['activo'] == 1 ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 24px; text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 10px; align-items: center;">
                                            <button class="btn-icon btn-icon-primary" onclick="openEditModal(<?= htmlspecialchars(json_encode($materia), ENT_QUOTES, 'UTF-8') ?>)" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <form method="POST" action="index.php" style="display: inline;">
                                                <input type="hidden" name="action" value="admin_toggle_estado_materia">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="id" value="<?= e($materia['id']) ?>">
                                                <label class="toggle-switch" style="display: inline-flex; cursor: pointer;">
                                                    <input type="checkbox" onchange="this.form.submit()" <?= $materia['activo'] == 1 ? 'checked' : '' ?>>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid #E9ECEF;">
                    <div style="color: #6C757D; font-size: 14px; font-weight: 600;">Mostrando <?= e(count($materias)) ?> materia(s)</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Agregar Materia</h2>
                <button onclick="closeModal('createModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_create_materia">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nombre</label>
                    <input type="text" name="nombre" required class="filter-input" style="width: 100%;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Código</label>
                        <input type="text" name="codigo" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Año correspondiente</label>
                        <select name="anio_correspondiente" class="filter-input" style="width: 100%;">
                            <option value="">Sin especificar</option>
                            <?php for ($a = 1; $a <= 7; $a++): ?>
                                <option value="<?= e($a) ?>"><?= e($a) ?>°</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Especialidad</label>
                    <select name="especialidad_id" class="filter-input" style="width: 100%;">
                        <option value="">General (todas las especialidades)</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?= e($esp['id']) ?>"><?= e($esp['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Editar Materia</h2>
                <button onclick="closeModal('editModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form id="editForm" method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_update_materia">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" id="edit_id">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" required class="filter-input" style="width: 100%;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Código</label>
                        <input type="text" name="codigo" id="edit_codigo" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Año correspondiente</label>
                        <select name="anio_correspondiente" id="edit_anio_correspondiente" class="filter-input" style="width: 100%;">
                            <option value="">Sin especificar</option>
                            <?php for ($a = 1; $a <= 7; $a++): ?>
                                <option value="<?= e($a) ?>"><?= e($a) ?>°</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Especialidad</label>
                    <select name="especialidad_id" id="edit_especialidad_id" class="filter-input" style="width: 100%;">
                        <option value="">General (todas las especialidades)</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?= e($esp['id']) ?>"><?= e($esp['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
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

function openEditModal(materia) {
    document.getElementById('edit_id').value = materia.id;
    document.getElementById('edit_nombre').value = materia.nombre;
    document.getElementById('edit_codigo').value = materia.codigo || '';
    document.getElementById('edit_anio_correspondiente').value = materia.anio_correspondiente || '';
    document.getElementById('edit_especialidad_id').value = materia.especialidad_id || '';
    openModal('editModal');
}
</script>
