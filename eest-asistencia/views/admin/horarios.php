<?php
require_role('admin');
require_once __DIR__ . '/../../models/Materia.php';
require_once __DIR__ . '/../../models/Curso.php';
require_once __DIR__ . '/../../controllers/AdminMateriasController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminMateriasController::horarios();
$cursos = $data['cursos'];
$curso_id = $data['curso_id'];
$curso_actual = $data['curso_actual'];
$dias = $data['dias'];
$por_dia = $data['por_dia'];
$total = $data['total'];
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
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title">
                    <h1>Horarios por Curso</h1>
                    <p>Horario real cargado desde los Excel oficiales (ciclo básico y ciclo superior). Solo lectura: cada fila representa una toma de asistencia, sin importar cuántas horas/módulos abarque la materia.</p>
                </div>
                <a href="index.php?page=admin/materias" class="btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Ir a Materias
                </a>
            </div>

            <!-- Selector de curso -->
            <div class="data-table-container" style="margin-bottom: 24px;">
                <form method="GET" action="index.php" style="display: flex; align-items: center; gap: 16px; padding: 20px 24px; flex-wrap: wrap;">
                    <input type="hidden" name="page" value="admin/horarios">
                    <label style="font-weight: 700; color: #071D3A;">Curso:</label>
                    <select name="curso_id" class="filter-input" style="min-width: 220px;" onchange="this.form.submit()">
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?= e($c['id']) ?>" <?= (int) $curso_id === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= e($c['anio']) ?>°<?= e($c['division']) ?>° — <?= e($c['especialidad_nombre']) ?> (<?= e(ucfirst($c['turno'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span style="color: #6C757D; font-size: 14px; font-weight: 600;">
                        <?= e($total) ?> materia(s)/toma(s) de asistencia distintas en la semana
                    </span>
                </form>
            </div>

            <?php if (!$curso_actual): ?>
                <div class="data-table-container" style="padding: 40px 24px; text-align: center; color: #6C757D;">
                    No hay cursos activos para mostrar.
                </div>
            <?php elseif ($total === 0): ?>
                <div class="data-table-container" style="padding: 40px 24px; text-align: center; color: #6C757D;">
                    Este curso no tiene horario cargado todavía.
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                    <?php foreach ($dias as $diaNum => $diaNombre): ?>
                        <div class="data-table-container" style="padding: 0;">
                            <div style="padding: 16px 20px; border-bottom: 1px solid #E9ECEF; background: #E8F4FC;">
                                <h3 style="margin: 0; color: #071D3A; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;"><?= e($diaNombre) ?></h3>
                            </div>
                            <div style="padding: 12px 16px;">
                                <?php if (empty($por_dia[$diaNum])): ?>
                                    <div style="padding: 16px 4px; color: #ADB5BD; font-size: 14px;">Sin materias asignadas.</div>
                                <?php else: foreach ($por_dia[$diaNum] as $a): ?>
                                    <div style="padding: 12px; margin-bottom: 8px; border: 1px solid #E9ECEF; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                                            <span style="font-weight: 700; color: #071D3A; font-size: 14px;"><?= e($a['materia_nombre']) ?></span>
                                            <span class="status-badge active" style="font-size: 11px;"><?= e($a['modulo_horario']) ?></span>
                                        </div>
                                        <div style="font-size: 13px; color: #6C757D; margin-top: 4px;">
                                            <?= e(substr($a['hora_inicio'], 0, 5)) ?> a <?= e(substr($a['hora_fin'], 0, 5)) ?>
                                        </div>
                                        <?php if ($a['preceptor_apellido']): ?>
                                            <div style="font-size: 12px; color: #ADB5BD; margin-top: 2px;">
                                                <?= e($a['preceptor_apellido']) ?>, <?= e($a['preceptor_nombre']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
