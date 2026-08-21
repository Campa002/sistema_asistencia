<?php
require_role('admin');
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../controllers/AdminUsuariosController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener datos para la vista
$data = AdminUsuariosController::index();
$usuarios = $data['usuarios'];
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
            <div class="page-header">
                <div class="page-title">
                    <h1>Gestión de Usuarios</h1>
                    <p>Administración centralizada de roles institucionales y perfiles académicos.</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Agregar Usuario
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

            <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Total Usuarios</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;"><?= e($stats['total']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Padres/Tutores</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;"><?= e($stats['padres_tutores']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Alumnos</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;"><?= e($stats['alumnos']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Preceptores</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;"><?= e($stats['preceptores']) ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Activos</span>
                    </div>
                    <div class="stat-value" style="color: #28A745; font-size: 40px; font-weight: 800;"><?= e($stats['activos']) ?></div>
                </div>
            </div>

            <div class="data-table-container" style="margin-top: 32px;">
                <form method="GET" action="index.php" style="display: contents;">
                    <input type="hidden" name="page" value="admin/usuarios">
                    <div class="table-header">
                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px; background: #F1F5F9; padding: 8px 12px; border-radius: 8px;">
                                <button type="submit" name="rol" value="" style="background: <?= empty($filters['rol']) ? 'white' : 'transparent' ?>; color: <?= empty($filters['rol']) ? '#071D3A' : '#6C757D' ?>; border: 2px solid white; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Todos</button>
                                <button type="submit" name="rol" value="admin" style="background: <?= $filters['rol'] === 'admin' ? 'white' : 'transparent' ?>; color: <?= $filters['rol'] === 'admin' ? '#071D3A' : '#6C757D' ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Admin</button>
                                <button type="submit" name="rol" value="preceptor" style="background: <?= $filters['rol'] === 'preceptor' ? 'white' : 'transparent' ?>; color: <?= $filters['rol'] === 'preceptor' ? '#071D3A' : '#6C757D' ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Preceptores</button>
                                <button type="submit" name="rol" value="alumno" style="background: <?= $filters['rol'] === 'alumno' ? 'white' : 'transparent' ?>; color: <?= $filters['rol'] === 'alumno' ? '#071D3A' : '#6C757D' ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Alumnos</button>
                                <button type="submit" name="rol" value="padre_tutor" style="background: <?= $filters['rol'] === 'padre_tutor' ? 'white' : 'transparent' ?>; color: <?= $filters['rol'] === 'padre_tutor' ? '#071D3A' : '#6C757D' ?>; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; cursor: pointer;">Padres/Tutores</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-header" style="border-top: none; display: flex; align-items: center; gap: 16px;">
                        <input type="text" class="filter-input" placeholder="Buscar por nombre, apellido, email o DNI..." name="busqueda" value="<?= e($filters['busqueda']) ?>" style="min-width: 240px;">
                        <select class="filter-input" name="estado" style="min-width: 200px;">
                            <option value="">Estado: Todos</option>
                            <option value="activo" <?= $filters['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $filters['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            <option value="pendiente" <?= $filters['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                        <a href="index.php?page=admin/usuarios" class="btn-outline" style="margin-left: auto;">
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
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Información de Usuario</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">DNI</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Rol</th>
                            <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                            <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="5" style="padding: 48px 24px; text-align: center; color: #6C757D;">
                                    No hay usuarios para mostrar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td style="padding: 16px 24px;">
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <div style="width: 48px; height: 48px; background: #DEE2E6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div style="font-size: 18px; font-weight: 800; color: #071D3A;"><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></div>
                                                <div style="font-size: 13px; color: #6C757D;"><?= e($usuario['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?= e($usuario['dni'] ?? 'N/A') ?></td>
                                    <td style="padding: 16px 24px;">
                                        <span class="role-badge"><?= e(get_role_name($usuario['rol'])) ?></span>
                                    </td>
                                    <td style="padding: 16px 24px;">
                                        <span class="status-badge <?= $usuario['estado'] === 'activo' ? 'active' : 'inactive' ?>">
                                            <?= e(ucfirst($usuario['estado'])) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 24px; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                            <button class="btn-icon" style="color: var(--support-gray);" onclick="openViewModal(<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES, 'UTF-8') ?>)" title="Ver detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="btn-icon" style="color: var(--accent);" onclick="openEditModal(<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES, 'UTF-8') ?>)" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <form method="POST" action="index.php" style="display: inline;">
                                <input type="hidden" name="action" value="admin_toggle_estado_usuario">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= e($usuario['id']) ?>">
                                <label class="toggle-switch" style="display: inline-flex; cursor: pointer;">
                                    <input type="checkbox" onchange="this.form.submit()" <?= $usuario['estado'] === 'activo' ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                            <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                <form method="POST" action="index.php" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    <input type="hidden" name="action" value="admin_delete_usuario">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= e($usuario['id']) ?>">
                                    <button type="submit" class="btn-icon" style="color: var(--danger);" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid #E9ECEF;">
                    <div style="color: #6C757D; font-size: 14px; font-weight: 600;">Mostrando <?= e(count($usuarios)) ?> usuario(s)</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Crear Usuario -->
    <div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Agregar Usuario</h2>
                <button onclick="closeModal('createModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_create_usuario">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nombre</label>
                        <input type="text" name="nombre" required class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Apellido</label>
                        <input type="text" name="apellido" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">DNI</label>
                        <input type="text" name="dni" required class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Email</label>
                        <input type="email" name="email" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Teléfono</label>
                        <input type="text" name="telefono" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Contraseña</label>
                        <input type="password" name="password" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Rol</label>
                        <select name="rol" required class="filter-input" style="width: 100%;">
                            <option value="">Seleccionar rol</option>
                            <option value="admin">Administrador</option>
                            <option value="directivo">Directivo</option>
                            <option value="preceptor">Preceptor</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Estado</label>
                        <select name="estado" class="filter-input" style="width: 100%;">
                            <option value="activo" selected>Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Usuario -->
    <div id="viewModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Detalle de Usuario</h2>
                <button onclick="closeModal('viewModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <div id="viewModalContent"></div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-outline" onclick="closeModal('viewModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Editar Usuario</h2>
                <button onclick="closeModal('editModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form id="editForm" method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_update_usuario">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" id="edit_id">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" required class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Apellido</label>
                        <input type="text" name="apellido" id="edit_apellido" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">DNI</label>
                        <input type="text" name="dni" id="edit_dni" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Email</label>
                        <input type="email" name="email" id="edit_email" required class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Teléfono</label>
                        <input type="text" name="telefono" id="edit_telefono" class="filter-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nueva Contraseña (opcional)</label>
                        <input type="password" name="password" id="edit_password" class="filter-input" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Rol</label>
                        <select name="rol" id="edit_rol" required class="filter-input" style="width: 100%;">
                            <option value="admin">Administrador</option>
                            <option value="directivo">Directivo</option>
                            <option value="preceptor">Preceptor</option>
                            <option value="alumno">Alumno</option>
                            <option value="padre_tutor">Padre/Tutor</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Estado</label>
                        <select name="estado" id="edit_estado" class="filter-input" style="width: 100%;">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="pendiente">Pendiente</option>
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
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function openViewModal(usuario) {
    const content = document.getElementById('viewModalContent');
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <strong style="color: #071D3A;">Nombre:</strong>
                    <div>${eHTML(usuario.nombre)} ${eHTML(usuario.apellido)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">DNI:</strong>
                    <div>${eHTML(usuario.dni || 'N/A')}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Email:</strong>
                    <div>${eHTML(usuario.email)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Teléfono:</strong>
                    <div>${eHTML(usuario.telefono || 'N/A')}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Rol:</strong>
                    <div>${getRoleNameHTML(usuario.rol)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Estado:</strong>
                    <div>${getStatusBadgeHTML(usuario.estado)}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Último Acceso:</strong>
                    <div>${eHTML(usuario.ultimo_acceso || 'Nunca')}</div>
                </div>
                <div>
                    <strong style="color: #071D3A;">Fecha de Creación:</strong>
                    <div>${eHTML(usuario.created_at)}</div>
                </div>
            </div>
        </div>
    `;
    openModal('viewModal');
}

function openEditModal(usuario) {
    document.getElementById('edit_id').value = usuario.id;
    document.getElementById('edit_nombre').value = usuario.nombre;
    document.getElementById('edit_apellido').value = usuario.apellido;
    document.getElementById('edit_dni').value = usuario.dni || '';
    document.getElementById('edit_email').value = usuario.email;
    document.getElementById('edit_telefono').value = usuario.telefono || '';
    document.getElementById('edit_rol').value = usuario.rol;
    document.getElementById('edit_estado').value = usuario.estado;
    openModal('editModal');
}

function eHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getRoleNameHTML(role) {
    const roles = {
        'admin': 'Administrador',
        'directivo': 'Directivo',
        'preceptor': 'Preceptor',
        'alumno': 'Alumno',
        'padre_tutor': 'Padre/Tutor'
    };
    const span = document.createElement('span');
    span.className = 'role-badge';
    span.textContent = roles[role] || role;
    return span.outerHTML;
}

function getStatusBadgeHTML(status) {
    const span = document.createElement('span');
    span.className = 'status-badge ' + (status === 'activo' ? 'active' : 'inactive');
    span.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    return span.outerHTML;
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}
</script>
