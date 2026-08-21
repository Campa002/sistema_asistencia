<?php
require_role('admin');
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];

// Get current user info
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <div class="header-user">
                    <div class="header-user-info">
                        <div class="name"><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></div>
                        <div class="role">Administrador del Sistema</div>
                    </div>
                    <div class="header-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="page-header">
                <div class="page-title">
                    <h1>Mi Perfil</h1>
                    <p>Administra tus datos de usuario y preferencias del sistema.</p>
                </div>
            </div>

            <?php if (has_flash('success')): ?>
                <div class="alert alert-success" style="padding: 16px; border-radius: 8px; background: #d4edda; color: #155724; margin-top: 24px;">
                    <?= e(flash('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (has_flash('errors')): ?>
                <div class="alert alert-danger" style="padding: 16px; border-radius: 8px; background: #f8d7da; color: #721c24; margin-top: 24px;">
                    <?php foreach (flash('errors') as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="margin-top: 32px; display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
                <div>
                    <div class="dashboard-card" style="border: 2px solid #DEE2E6;">
                        <div class="dashboard-card-header">
                            <h3>Información Personal</h3>
                        </div>
                        <div style="display: flex; gap: 24px; padding: 24px 0;">
                            <div style="width: 120px; height: 120px; background: #DEE2E6; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="1.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-size: 20px; font-weight: 800; color: #071D3A; margin-bottom: 10px;"><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h4>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px; text-transform: capitalize;"><?= e($usuario['rol']) ?></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px;"><?= e($usuario['email']) ?></span>
                                </div>
                                <?php if ($usuario['telefono']): ?>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px;"><?= e($usuario['telefono']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($usuario['ultimo_acceso']): ?>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px;">Último acceso: <?= e(date('d/m/Y H:i', strtotime($usuario['ultimo_acceso']))) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="dashboard-card" style="border: 2px solid #DEE2E6;">
                        <div class="dashboard-card-header">
                            <h3>Acciones</h3>
                        </div>
                        <div style="padding: 24px 0;">
                            <button type="button" class="btn-outline" style="width: 100%; margin-bottom: 16px;" onclick="openModal('editProfileModal')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar Perfil
                            </button>
                            <button type="button" class="btn-outline" style="width: 100%; margin-bottom: 16px;" onclick="openModal('changePasswordModal')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="10" x2="15" y2="10"/>
                                    <line x1="9" y1="14" x2="15" y2="14"/>
                                </svg>
                                Cambiar Contraseña
                            </button>
                            <form method="POST" action="<?= url('index.php') ?>" style="margin: 0;">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="btn-outline" style="width: 100%; border-color: #DC3545; color: #DC3545;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Editar Perfil Modal -->
    <div id="editProfileModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Editar Perfil</h2>
                <button type="button" onclick="closeModal('editProfileModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_actualizar_perfil">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nombre</label>
                        <input type="text" name="nombre" required class="filter-input" style="width: 100%;" value="<?= e($usuario['nombre']) ?>">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Apellido</label>
                        <input type="text" name="apellido" required class="filter-input" style="width: 100%;" value="<?= e($usuario['apellido']) ?>">
                    </div>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Email</label>
                    <input type="email" name="email" required class="filter-input" style="width: 100%;" value="<?= e($usuario['email']) ?>">
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Teléfono</label>
                    <input type="text" name="telefono" class="filter-input" style="width: 100%;" value="<?= e($usuario['telefono'] ?? '') ?>">
                </div>
                <p style="color: #6C757D; font-size: 13px; margin-bottom: 24px;">El rol (<?= e($usuario['rol']) ?>) no se puede modificar desde acá.</p>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('editProfileModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cambiar Contraseña Modal -->
    <div id="changePasswordModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 32px; max-width: 460px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; color: #071D3A;">Cambiar Contraseña</h2>
                <button type="button" onclick="closeModal('changePasswordModal')" style="background: transparent; border: none; cursor: pointer; font-size: 24px; color: #6C757D;">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="admin_cambiar_password">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Contraseña actual</label>
                    <input type="password" name="password_actual" required class="filter-input" style="width: 100%;" autocomplete="current-password">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Nueva contraseña</label>
                    <input type="password" name="password_nueva" required minlength="8" class="filter-input" style="width: 100%;" autocomplete="new-password">
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #071D3A;">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmacion" required minlength="8" class="filter-input" style="width: 100%;" autocomplete="new-password">
                </div>
                <p style="color: #6C757D; font-size: 13px; margin-bottom: 24px;">Mínimo 8 caracteres.</p>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-outline" onclick="closeModal('changePasswordModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
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
</script>
