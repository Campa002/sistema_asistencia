<?php
require_role('admin');
require_once __DIR__ . '/../../models/Curso.php';
require_once __DIR__ . '/../../controllers/AdminCursosController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get data
$data = AdminCursosController::index();
$cursos = $data['cursos'];
$especialidades = $data['especialidades'];
$stats = $data['stats'];
$filters = $data['filters'];
$cursoSeleccionado = $data['cursoSeleccionado'];
$alumnosCurso = $data['alumnosCurso'];
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
                    <h1>Gestión de Cursos</h1>
                    <p>Configura divisiones académicas, turnos y especialidades institucionales.</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Agregar Curso
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
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Total Cursos</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 36px; font-weight: 800;"><?= e($stats['total']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Activos</span>
                    </div>
                    <div class="stat-value" style="color: #28A745; font-size: 36px; font-weight: 800;"><?= e($stats['activos']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 13px; text-transform: uppercase;">Inactivos</span>
                    </div>
                    <div class="stat-value" style="color: #DC3545; font-size: 36px; font-weight: 800;"><?= e($stats['inactivos']) ?></div>
                </div>
            </div>

            <!-- Filters & Table -->
            <div class="data-table-container" style="margin-top: 32px;">
                <form method="GET" action="index.php" style="display: contents;">
                    <input type="hidden" name="page" value="admin/cursos">
                    <div class="table-header">
                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px; background: #F1F5F9; padding: 8px 12px; border-radius: 8px;">
                                <?php 
                                $allBg = empty($filters['anio']) ? 'white' : 'transparent';
                                $allColor = empty($filters['anio']) ? '#071D3A' : '#6C757D';
                                ?>
                                <button type="submit" name="anio" value="" style="background: <?php echo $allBg; ?>; color: <?php echo $allColor; ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Todos</button>
                                <?php for($a=1; $a<=7; $a++): 
                                    $bg = ($filters['anio'] == $a) ? 'white' : 'transparent';
                                    $color = ($filters['anio'] == $a) ? '#071D3A' : '#6C757D';
                                ?>
                                    <button type="submit" name="anio" value="<?= e($a) ?>" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;"><?= e($a) ?>°</button>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="table-header" style="border-top: none; display: flex; align-items: center; gap: 16px;">
                        <input type="text" class="filter-input" placeholder="Buscar por aula, curso o especialidad..." name="busqueda" value="<?= e($filters['busqueda']) ?>" style="min-width: 240px;">
                        <select class="filter-input" name="turno" style="min-width: 160px;">
                            <option value="">Turno: Todos</option>
                            <option value="mañana" <?= $filters['turno'] === 'mañana' ? 'selected' : '' ?>>Mañana</option>
                            <option value="tarde" <?= $filters['turno'] === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                            <option value="noche" <?= $filters['turno'] === 'noche' ? 'selected' : '' ?>>Noche</option>
                        </select>
                        <select class="filter-input" name="especialidad_id" style="min-width: 180px;">
                            <option value="">Especialidad: Todas</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= e($esp['id']) ?>" <?= $filters['especialidad_id'] == $esp['id'] ? 'selected' : '' ?>><?= e($esp['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="filter-input" name="estado" style="min-width: 160px;">
                            <option value="">Estado: Todos</option>
                            <option value="activo" <?= $filters['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $filters['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            <option value="archivado" <?= $filters['estado'] === 'archivado' ? 'selected' : '' ?>>Archivado</option>
                        </select>
                        <a href="index.php?page=admin/cursos" class="btn-outline" style="margin-left: auto;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="14.5 10 4 4 4 20 14.5 14"/>
                                <path d="M3.5 4L17 14H20a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/>
                            </svg>
                            Limpiar Filtros
                        </a>
                    </div>
                </form>

                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr style="background: #E8F4FC;">
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Curso</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Especialidad</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Turno</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Aula</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                            <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cursos)): ?>
                            <tr>
                                <td colspan="6" style="padding: 48px 24px; text-align: center; color: #6C757D;">
                                    No hay cursos para mostrar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td style="padding: 16px 24px;">
                                        <div style="font-size: 18px; font-weight: 800; color: #071D3A;"><?= e($curso['anio']) ?>° <?= e($curso['division']) ?></div>
                                        <div style="font-size: 13px; color: #6C757D;">Ciclo lectivo: <?= e($curso['ciclo_lectivo']) ?></div>
                                    </td>
                                    <td style="padding: 16px 24px;">
                                        <span class="badge-esp"><?= e($curso['especialidad_nombre']) ?></span>
                                    </td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A; font-weight: 600;"><?= e(ucfirst($curso['turno'])) ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e($curso['aula'] ?? '—') ?></td>
                                    <td style="padding: 16px 24px;">
                                        <span class="status-badge <?= $curso['estado'] === 'activo' ? 'active' : 'inactive' ?>">
                                            <?= e(ucfirst($curso['estado'])) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 24px; text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                            <a href="index.php?page=admin/cursos&curso_id=<?= e($curso['id']) ?>" class="btn-icon" style="color: #6C757D;" title="Ver alumnos">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                </svg>
                                            </a>
                                            <button class="btn-icon" style="color: #6C757D;" onclick="openViewModal(<?= htmlspecialchars(json_encode($curso), ENT_QUOTES, 'UTF-8') ?>)" title="Ver detalles">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="btn-icon" style="color: #3498DB;" onclick="openEditModal(<?= htmlspecialchars(json_encode($curso), ENT_QUOTES, 'UTF-8') ?>)" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <form method="POST" action="index.php" style="display: inline;">
                                                <input type="hidden" name="action" value="admin_toggle_estado_curso">
                                                <input type="hidden" name="id" value="<?= e($curso['id']) ?>">
                                                <label class="toggle-switch" style="display: inline-flex; cursor: pointer;">
                                                    <input type="checkbox" onchange="this.form.submit()" <?= $curso['estado'] === 'activo' ? 'checked' : '' ?>>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid #E9ECEF;">
                    <div style="color: #6C757D; font-size: 14px; font-weight: 600;">Mostrando <?= e(count($cursos)) ?> curso(s)</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Agregar Curso</h2>
                <button onclick="closeModal('createModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_create_curso">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Año</label>
                        <select name="anio" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar año</option>
                            <?php for($a=1; $a<=7; $a++): ?>
                                <option value="<?= e($a) ?>"><?= e($a) ?>°</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">División</label>
                        <select name="division" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar división</option>
                            <?php for($d=1; $d<=5; $d++): ?>
                                <option value="<?= e($d) ?>"><?= e($d) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Turno</label>
                        <select name="turno" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar turno</option>
                            <option value="mañana">Mañana</option>
                            <option value="tarde">Tarde</option>
                            <option value="noche">Noche</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Especialidad</label>
                        <select name="especialidad_id" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= e($esp['id']) ?>"><?= e($esp['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Aula</label>
                        <input type="text" name="aula" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Ciclo Lectivo</label>
                        <input type="number" name="ciclo_lectivo" required class="filter-input" style="width: 100%;" value="<?= date('Y') ?>">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Estado</label>
                        <select name="estado" class="filter-input" style="width: 100%;">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="archivado">Archivado</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Curso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Detalle del Curso</h2>
                <button onclick="closeModal('viewModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <div id="viewModalContent"></div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-outline" onclick="closeModal('viewModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Editar Curso</h2>
                <button onclick="closeModal('editModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form id="editForm" method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_update_curso">
                <input type="hidden" name="id" id="edit_id">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Año</label>
                        <select name="anio" id="edit_anio" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar año</option>
                            <?php for($a=1; $a<=7; $a++): ?>
                                <option value="<?= e($a) ?>"><?= e($a) ?>°</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">División</label>
                        <select name="division" id="edit_division" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar división</option>
                            <?php for($d=1; $d<=5; $d++): ?>
                                <option value="<?= e($d) ?>"><?= e($d) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Turno</label>
                        <select name="turno" id="edit_turno" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar turno</option>
                            <option value="mañana">Mañana</option>
                            <option value="tarde">Tarde</option>
                            <option value="noche">Noche</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Especialidad</label>
                        <select name="especialidad_id" id="edit_especialidad_id" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= e($esp['id']) ?>"><?= e($esp['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Aula</label>
                        <input type="text" name="aula" id="edit_aula" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Ciclo Lectivo</label>
                        <input type="number" name="ciclo_lectivo" id="edit_ciclo_lectivo" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Estado</label>
                        <select name="estado" id="edit_estado" class="filter-input" style="width: 100%;">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="archivado">Archivado</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alumnos Modal -->
    <div id="alumnosModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Alumnos del Curso</h2>
                <a href="index.php?page=admin/cursos" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D; text-decoration: none;">&times;</a>
            </div>
            <?php if ($cursoSeleccionado): ?>
                <p style="margin-bottom: 24px; color: #6C757D;">Curso: <?= e($cursoSeleccionado['anio']) ?>° <?= e($cursoSeleccionado['division']) ?> - <?= e($cursoSeleccionado['especialidad_nombre']) ?></p>
                <?php if (empty($alumnosCurso)): ?>
                    <p style="color: #6C757D; text-align: center; padding: 40px 0;">No hay alumnos asignados a este curso.</p>
                <?php else: ?>
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
                            <?php foreach ($alumnosCurso as $alumno): ?>
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($alumno['dni'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A; font-weight: 600;"><?= e($alumno['apellido'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #071D3A;"><?= e($alumno['nombre'] ?? '—') ?></td>
                                    <td style="padding: 12px 16px;">
                                        <span class="status-badge <?= $alumno['matricula_estado'] === 'activo' ? 'active' : 'inactive' ?>">
                                            <?= e(ucfirst($alumno['matricula_estado'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="index.php?page=admin/cursos" class="btn-outline">Cerrar</a>
            </div>
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

function openViewModal(curso) {
    const content = document.getElementById('viewModalContent');
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <strong style="color: #071D3A;">Curso:</strong>
                    <div>${eHTML(curso.anio)}° ${eHTML(curso.division)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Especialidad:</strong>
                    <div>${eHTML(curso.especialidad_nombre)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Turno:</strong>
                    <div>${eHTML(curso.turno.charAt(0).toUpperCase() + curso.turno.slice(1))}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Aula:</strong>
                    <div>${eHTML(curso.aula || '—')}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Ciclo Lectivo:</strong>
                    <div>${eHTML(curso.ciclo_lectivo)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Estado:</strong>
                    <div>${getStatusBadgeHTML(curso.estado)}</div>
                </div>
            </div>
        </div>
    `;
    openModal('viewModal');
}

function openEditModal(curso) {
    document.getElementById('edit_id').value = curso.id;
    document.getElementById('edit_anio').value = curso.anio;
    document.getElementById('edit_division').value = curso.division;
    document.getElementById('edit_turno').value = curso.turno;
    document.getElementById('edit_especialidad_id').value = curso.especialidad_id;
    document.getElementById('edit_aula').value = curso.aula || '';
    document.getElementById('edit_ciclo_lectivo').value = curso.ciclo_lectivo;
    document.getElementById('edit_estado').value = curso.estado;
    openModal('editModal');
}

function eHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getStatusBadgeHTML(estado) {
    const span = document.createElement('span');
    span.className = 'status-badge ' + (estado === 'activo' ? 'active' : 'inactive');
    span.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
    return span.outerHTML;
}

window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
};

<?php if ($cursoSeleccionado): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal('alumnosModal');
    });
<?php endif; ?>
</script>
