<?php
require_role('admin');
require_once __DIR__ . '/../../models/Reporte.php';
require_once __DIR__ . '/../../controllers/AdminReportesController.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminReportesController::index();
$generar = $data['generar'] ?? '';
$modo_reporte = $data['modo_reporte'];
$filters = $data['filters'];
$ordenamiento = $data['ordenamiento'];
$anio_grafico = $data['anio_grafico'] ?? '';
$datos_reporte = $data['datos_reporte'] ?? [];
$stats = $data['stats'];
$report_stats = $data['report_stats'];
$presentismo_por_division = $data['presentismo_por_division'];
$inasistencias_por_curso = $data['inasistencias_por_curso'];
$cursos = $data['cursos'];
$especialidades = $data['especialidades'];
$materias = $data['materias'];
$preceptores = $data['preceptores'];
$anios_disponibles = $data['anios_disponibles'] ?? [];
$alumnos_disponibles = $data['alumnos_disponibles'] ?? [];
$pagination = $data['pagination'] ?? [];
$pdf_disponible = $data['pdf_disponible'] ?? false;

$estado_nombres = [
    'presente' => 'Presente',
    'ausente' => 'Ausente',
    'llegada_tarde' => 'Llegada Tarde',
    'ausente_con_presente' => 'Ausente con Presente',
    'justificado' => 'Justificado',
    'retirado_anticipado' => 'Retirado Anticipado'
];

$bloque_nombres = [
    'primera_hora' => '1ra Hora',
    'segunda_hora' => '2da Hora'
];

// Preparar datos para Chart.js
$chart_labels = [];
$chart_data = [];
$chart_colors = [];
foreach ($presentismo_por_division as $item) {
    $chart_labels[] = $item['division'];
    $chart_data[] = $item['porcentaje'];
    if ($item['porcentaje'] < 70) {
        $chart_colors[] = '#DC3545'; // Rojo
    } elseif ($item['porcentaje'] < 85) {
        $chart_colors[] = '#FD7E14'; // Naranja
    } else {
        $chart_colors[] = '#3498DB'; // Azul
    }
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
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </a>
                </div>
                <a href="index.php?page=admin/perfil" class="header-user">
                    <div class="header-user-info">
                        <div class="name"><?php echo e($admin_user['nombre'] . ' ' . $admin_user['apellido']); ?></div>
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
            <div class="page-header" style="margin-bottom: 24px;">
                <div class="page-title">
                    <h1>Estadísticas Institucionales</h1>
                    <p>Visualización analítica para la toma de decisiones directivas</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <?php if ($generar): ?>
                        <div style="position: relative;">
                            <button class="btn-outline" onclick="toggleExportMenu()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 16v-4V4H6v12h12v-4H6v4h12z"/>
                                </svg>
                                Exportar Reporte
                            </button>
                            <div id="exportMenu" class="export-dropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: white; border: 1px solid #E9ECEF; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); z-index: 100; min-width: 200px;">
                                <form method="POST" action="index.php?page=admin/reportes" style="display: block;">
                                    <input type="hidden" name="action" value="exportar_csv">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="generar" value="1">
                                    <input type="hidden" name="modo_reporte" value="<?php echo e($modo_reporte); ?>">
                                    <input type="hidden" name="ordenamiento" value="<?php echo e($ordenamiento); ?>">
                                    <input type="hidden" name="anio_grafico" value="<?php echo e($anio_grafico); ?>">
                                    <?php foreach ($filters as $key => $value): ?>
                                        <?php if ($value !== ''): ?>
                                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <button type="submit" class="export-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 16v-4V4H6v12h12v-4H6v4h12z"/>
                                        </svg>
                                        Exportar CSV
                                    </button>
                                </form>
                                <?php if ($pdf_disponible): ?>
                                <form method="POST" action="index.php?page=admin/reportes" style="display: block;">
                                    <input type="hidden" name="action" value="exportar_pdf">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="generar" value="1">
                                    <input type="hidden" name="modo_reporte" value="<?php echo e($modo_reporte); ?>">
                                    <input type="hidden" name="ordenamiento" value="<?php echo e($ordenamiento); ?>">
                                    <input type="hidden" name="anio_grafico" value="<?php echo e($anio_grafico); ?>">
                                    <?php foreach ($filters as $key => $value): ?>
                                        <?php if ($value !== ''): ?>
                                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <button type="submit" class="export-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14,2 14,8 20,8"/>
                                        </svg>
                                        Exportar PDF
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button onclick="window.print()" class="export-dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 9V2h12v7"/>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                        <rect x="6" y="14" width="12" height="8"/>
                                    </svg>
                                    Imprimir
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <button class="btn-primary-sm" onclick="document.getElementById('form-filtros').submit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16M4 12h16M4 20h16"/>
                        </svg>
                        <?php echo $generar ? 'Actualizar Datos' : 'Generar Reporte'; ?>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="dashboard-card" style="margin-bottom: 24px;">
                <div class="table-header" style="padding: 20px 24px;">
                    <form method="GET" action="index.php" id="form-filtros">
                        <input type="hidden" name="page" value="admin/reportes">
                        <input type="hidden" name="generar" value="1">
                        <input type="hidden" name="modo_reporte" value="<?php echo e($modo_reporte); ?>">
                        <input type="hidden" name="ordenamiento" value="<?php echo e($ordenamiento); ?>">
                        <input type="hidden" name="anio_grafico" value="<?php echo e($anio_grafico); ?>">
                        <input type="hidden" name="alumno_id" id="alumno_id_hidden" value="<?php echo e($filters['alumno_id']); ?>">

                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Periodo</label>
                                <select name="periodo" class="filter-input" style="min-width: 180px;" onchange="toggleCustomDates(this.value)">
                                    <option value="hoy" <?php echo $filters['periodo'] === 'hoy' ? 'selected' : ''; ?>>Hoy</option>
                                    <option value="semana" <?php echo $filters['periodo'] === 'semana' ? 'selected' : ''; ?>>Esta Semana</option>
                                    <option value="mes" <?php echo $filters['periodo'] === 'mes' ? 'selected' : ''; ?>>Este Mes</option>
                                    <option value="cuatrimestre" <?php echo $filters['periodo'] === 'cuatrimestre' ? 'selected' : ''; ?>>Cuatrimestre</option>
                                    <option value="ciclo" <?php echo $filters['periodo'] === 'ciclo' ? 'selected' : ''; ?>>Ciclo Lectivo</option>
                                    <option value="personalizado" <?php echo $filters['periodo'] === 'personalizado' ? 'selected' : ''; ?>>Personalizado</option>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Curso</label>
                                <select name="curso_id" class="filter-input" id="curso-select" style="min-width: 180px;" onchange="clearAlumnoSelection()">
                                    <option value="">Todos los cursos</option>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?php echo e($curso['id']); ?>" <?php echo $filters['curso_id'] == $curso['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($curso['anio'] . '° ' . $curso['division'] . ' (' . $curso['turno'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Turno</label>
                                <select name="turno" class="filter-input" style="min-width: 140px;">
                                    <option value="">Todos</option>
                                    <option value="mañana" <?php echo $filters['turno'] === 'mañana' ? 'selected' : ''; ?>>Mañana</option>
                                    <option value="tarde" <?php echo $filters['turno'] === 'tarde' ? 'selected' : ''; ?>>Tarde</option>
                                    <option value="vespertino" <?php echo $filters['turno'] === 'vespertino' ? 'selected' : ''; ?>>Vespertino</option>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; position: relative;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Alumno</label>
                                <div style="position: relative; min-width: 250px;">
                                    <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6C757D; width: 18px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <input type="text" id="alumno_search" class="filter-input" style="padding-left: 40px; width: 100%;" placeholder="Buscar por nombre, apellido o DNI" autocomplete="off">
                                    <div id="alumno_autocomplete" class="autocomplete-dropdown" style="display: none;"></div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                                <button type="button" class="btn-outline" onclick="toggleMoreFilters()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 5v14M5 12h14"/>
                                    </svg>
                                    Más filtros
                                </button>
                                <a href="index.php?page=admin/reportes" class="btn-outline">Limpiar</a>
                                <button type="submit" class="btn-generar-reporte">
                                    <?php echo $generar ? 'Actualizar Datos' : 'Generar Reporte'; ?>
                                </button>
                            </div>
                        </div>
                        <div id="customDates" class="table-toolbar" style="margin-top: 16px; display: <?php echo $filters['periodo'] === 'personalizado' ? 'flex' : 'none'; ?>;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Desde</label>
                                <input type="date" name="fecha_desde" class="filter-input" style="min-width: 160px;" value="<?php echo e($filters['fecha_desde']); ?>">
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Hasta</label>
                                <input type="date" name="fecha_hasta" class="filter-input" style="min-width: 160px;" value="<?php echo e($filters['fecha_hasta']); ?>">
                            </div>
                        </div>
                        <div id="moreFilters" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #E9ECEF; display: none;">
                            <div class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Tipo de Registro</label>
                                    <select name="tipo_registro" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todos</option>
                                        <option value="presente" <?php echo $filters['tipo_registro'] === 'presente' ? 'selected' : ''; ?>>Presentes</option>
                                        <option value="ausente" <?php echo $filters['tipo_registro'] === 'ausente' ? 'selected' : ''; ?>>Ausentes</option>
                                        <option value="llegada_tarde" <?php echo $filters['tipo_registro'] === 'llegada_tarde' ? 'selected' : ''; ?>>Llegadas Tarde</option>
                                        <option value="ausente_con_presente" <?php echo $filters['tipo_registro'] === 'ausente_con_presente' ? 'selected' : ''; ?>>Ausentes con Presente</option>
                                        <option value="justificado" <?php echo $filters['tipo_registro'] === 'justificado' ? 'selected' : ''; ?>>Justificados</option>
                                        <option value="retirado_anticipado" <?php echo $filters['tipo_registro'] === 'retirado_anticipado' ? 'selected' : ''; ?>>Retiros Anticipados</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Especialidad</label>
                                    <select name="especialidad_id" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todas</option>
                                        <?php foreach ($especialidades as $esp): ?>
                                            <option value="<?php echo e($esp['id']); ?>" <?php echo $filters['especialidad_id'] == $esp['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($esp['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Materia</label>
                                    <select name="materia_id" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todas</option>
                                        <?php foreach ($materias as $mat): ?>
                                            <option value="<?php echo e($mat['id']); ?>" <?php echo $filters['materia_id'] == $mat['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($mat['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Preceptor</label>
                                    <select name="preceptor_id" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todos</option>
                                        <?php foreach ($preceptores as $pre): ?>
                                            <option value="<?php echo e($pre['id']); ?>" <?php echo $filters['preceptor_id'] == $pre['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($pre['apellido'] . ', ' . $pre['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">División</label>
                                    <select name="division" class="filter-input" style="min-width: 140px;">
                                        <option value="">Todas</option>
                                        <?php foreach (array_unique(array_column($cursos, 'division')) as $div): ?>
                                            <option value="<?php echo e($div); ?>" <?php echo $filters['division'] === $div ? 'selected' : ''; ?>>
                                                <?php echo e($div); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="table-toolbar" style="margin-top: 16px; flex-wrap: wrap; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Rango de Faltas</label>
                                    <select name="rango_faltas" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todos</option>
                                        <option value="sin_faltas" <?php echo $filters['rango_faltas'] === 'sin_faltas' ? 'selected' : ''; ?>>Sin faltas</option>
                                        <option value="1-9" <?php echo $filters['rango_faltas'] === '1-9' ? 'selected' : ''; ?>>1 a 9 faltas</option>
                                        <option value="10-19" <?php echo $filters['rango_faltas'] === '10-19' ? 'selected' : ''; ?>>10 a 19 faltas</option>
                                        <option value="20-27" <?php echo $filters['rango_faltas'] === '20-27' ? 'selected' : ''; ?>>20 a 27 faltas</option>
                                        <option value="28+" <?php echo $filters['rango_faltas'] === '28+' ? 'selected' : ''; ?>>28 o más faltas</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">% Asistencia</label>
                                    <select name="rango_asistencia" class="filter-input" style="min-width: 180px;">
                                        <option value="">Todos</option>
                                        <option value="90+" <?php echo $filters['rango_asistencia'] === '90+' ? 'selected' : ''; ?>>90% o más</option>
                                        <option value="80-89.99" <?php echo $filters['rango_asistencia'] === '80-89.99' ? 'selected' : ''; ?>>80% a 89.99%</option>
                                        <option value="70-79.99" <?php echo $filters['rango_asistencia'] === '70-79.99' ? 'selected' : ''; ?>>70% a 79.99%</option>
                                        <option value="menos70" <?php echo $filters['rango_asistencia'] === 'menos70' ? 'selected' : ''; ?>>Menos de 70%</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Bloque Horario</label>
                                    <select name="bloque_horario" class="filter-input" style="min-width: 140px;">
                                        <option value="">Todos</option>
                                        <option value="primera_hora" <?php echo $filters['bloque_horario'] === 'primera_hora' ? 'selected' : ''; ?>>1ra Hora</option>
                                        <option value="segunda_hora" <?php echo $filters['bloque_horario'] === 'segunda_hora' ? 'selected' : ''; ?>>2da Hora</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <label style="color: #6C757D; font-weight: 700; font-size: 14px; text-transform: uppercase;">Incluir Anuladas</label>
                                    <select name="incluir_anuladas" class="filter-input" style="min-width: 140px;">
                                        <option value="no" <?php echo $filters['incluir_anuladas'] === 'no' ? 'selected' : ''; ?>>No</option>
                                        <option value="si" <?php echo $filters['incluir_anuladas'] === 'si' ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
                <div class="kpi-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div style="width: 48px; height: 48px; background: #E8F4FC; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3498DB" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-label">Asistencia General</div>
                    <div class="kpi-value" style="color: #071D3A;"><?php echo e($report_stats['asistencia_general']); ?>%</div>
                    <div style="font-size: 12px; color: #6C757D; margin-top: 6px;">Acumulado institucional</div>
                </div>

                <div class="kpi-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div style="width: 48px; height: 48px; background: #FFF3CD; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FD7E14" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-label">Llegadas Tarde</div>
                    <div class="kpi-value" style="color: #071D3A;"><?php echo e($report_stats['llegadas_tarde']); ?></div>
                    <div style="font-size: 12px; color: #6C757D; margin-top: 6px;">Total acumulado</div>
                </div>

                <div class="kpi-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div style="width: 48px; height: 48px; background: #E8F4FC; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3498DB" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-label">Justificaciones Cargadas</div>
                    <div class="kpi-value" style="color: #071D3A;"><?php echo e($report_stats['justificaciones_total']); ?></div>
                </div>

                <div class="kpi-card kpi-card-blue">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-label" style="color: rgba(255,255,255,0.85);">Total Faltas</div>
                    <div class="kpi-value" style="color: white;"><?php echo e($report_stats['faltas_total']); ?></div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="dash-row" style="margin-bottom: 24px;">
                <!-- Chart Section -->
                <div class="dash-col-main">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <div>
                                <h3>Presentismo por División</h3>
                                <p style="font-size: 13px; color: #6C757D; margin-top: 4px;">Porcentaje de alumnos presentes - <?php echo $anio_grafico ? $anio_grafico . '° Año' : 'Todos los años'; ?></p>
                            </div>
                            <form method="GET" action="index.php" id="form-anio" style="margin: 0;">
                                <input type="hidden" name="page" value="admin/reportes">
                                <input type="hidden" name="generar" value="<?php echo e($generar); ?>">
                                <input type="hidden" name="modo_reporte" value="<?php echo e($modo_reporte); ?>">
                                <input type="hidden" name="ordenamiento" value="<?php echo e($ordenamiento); ?>">
                                <?php foreach ($filters as $key => $value): ?>
                                    <?php if ($value !== ''): ?>
                                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <select name="anio_grafico" class="filter-input" style="width: auto;" onchange="document.getElementById('form-anio').submit()">
                                    <option value="">Todos los años</option>
                                    <?php foreach ($anios_disponibles as $anio): ?>
                                        <option value="<?php echo e($anio); ?>" <?php echo $anio_grafico == $anio ? 'selected' : ''; ?>>
                                            <?php echo e($anio); ?>° Año
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <div class="chart-container">
                            <canvas id="presentismoChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Side Panel -->
                <div class="dash-col-side">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>Inasistencias por Curso</h3>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <?php foreach ($inasistencias_por_curso['cursos'] as $curso): ?>
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <div>
                                            <span style="font-weight: 700; color: #071D3A; font-size: 14px;"><?php echo e($curso['division']); ?></span>
                                            <?php if ($curso['especialidad']): ?>
                                                <span class="badge-esp" style="margin-left: 8px;"><?php echo e($curso['especialidad']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-weight: 800; color: #DC3545; font-size: 14px;"><?php echo e($curso['porcentaje_ausentismo']); ?>%</span>
                                    </div>
                                    <div style="width: 100%; height: 8px; background: #E9ECEF; border-radius: 4px; overflow: hidden;">
                                        <div style="width: <?php echo e(min($curso['porcentaje_ausentismo'], 100)); ?>%; height: 100%; background: linear-gradient(90deg, #DC3545, #FD7E14); border-radius: 4px;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($inasistencias_por_curso['mayor_ausentismo'] || $inasistencias_por_curso['mejor_asistencia']): ?>
                            <div style="margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <?php if ($inasistencias_por_curso['mayor_ausentismo']): ?>
                                   
                                <?php endif; ?>
                                <?php if ($inasistencias_por_curso['mejor_asistencia']): ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <?php if ($generar): ?>
            <div class="dashboard-card" id="resultados" style="margin-bottom: 24px; padding: 0; scroll-margin-top: 24px;">
                <div class="table-header" style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <h3 style="margin: 0; color: #071D3A; font-size: 16px;">Resumen por Alumno</h3>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="color: #6C757D; font-size: 14px; font-weight: 700;">Por página:</span>
                        <select class="filter-input" style="width: auto;" onchange="cambiarPerPage(this.value)">
                            <?php foreach ([10, 25, 50, 'all'] as $pp): ?>
                                <option value="<?php echo e($pp); ?>" <?php echo (string)($pagination['per_page'] ?? 10) === (string)$pp ? 'selected' : ''; ?>>
                                    <?php echo $pp === 'all' ? 'Todos' : $pp; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if (!empty($pagination['total'])): ?>
                    <div class="table-meta-bar" style="padding: 14px 24px; color: #6C757D; font-size: 14px; font-weight: 600;">
                        <?php if (($pagination['per_page'] ?? 10) === 'all'): ?>
                            Mostrando todos los <?php echo e($pagination['total']); ?> registros
                        <?php else:
                            $start = ($pagination['page_num'] - 1) * $pagination['per_page'] + 1;
                            $end = min($pagination['page_num'] * $pagination['per_page'], $pagination['total']);
                        ?>
                            Mostrando <?php echo e($start); ?>–<?php echo e($end); ?> de <?php echo e($pagination['total']); ?> registros
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="data-table-scroll" style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Alumno</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Curso/División</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Especialidad</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Turno</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Presentes</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Ausentes</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Tarde</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Aus. c/Pres.</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Justif.</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Retiros</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Faltas</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">% Asist.</th>
                                <th style="text-align: center; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($datos_reporte['alumnos'])): ?>
                                <tr>
                                    <td colspan="13" style="padding: 48px 24px; text-align: center; color: #6C757D;">
                                        No hay resultados para los filtros seleccionados.
                                    </td>
                                </tr>
                            <?php else: foreach ($datos_reporte['alumnos'] as $a): ?>
                                <tr>
                                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 700; color: #071D3A;"><?php echo e($a['apellido'] . ', ' . $a['nombre']); ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?php echo e($a['anio'] . '° ' . $a['division']); ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?php echo e($a['especialidad_nombre'] ?? '-'); ?></td>
                                    <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;"><?php echo e(ucfirst($a['turno'] ?? '-')); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['presentes']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['ausentes']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['llegadas_tarde']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['ausentes_con_presente']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['justificados']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; color: #071D3A;"><?php echo e($a['retiros_anticipados']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center; font-size: 14px; font-weight: 700; color: #071D3A;"><?php echo e($a['faltas_total']); ?></td>
                                    <td style="padding: 16px 24px; text-align: center;">
                                        <span class="status-badge" style="<?php echo $a['porcentaje_asistencia'] < 70 ? 'background:#F8D7DA;color:#721C24;' : 'background:#D4EDDA;color:#155724;'; ?> padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 800;">
                                            <?php echo e($a['porcentaje_asistencia']); ?>%
                                        </span>
                                    </td>
                                    <td style="padding: 16px 24px; text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 4px;">
                                            <button type="button" class="btn-icon" title="Ver resumen" onclick="abrirModalResumen(<?php echo (int) $a['alumno_id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn-icon btn-icon-primary" title="Ver asistencia detallada" onclick="abrirModalDetalle(<?php echo (int) $a['alumno_id']; ?>, '<?php echo e(addslashes($a['apellido'] . ', ' . $a['nombre'])); ?>')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
                    <div class="pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 1px solid #E9ECEF; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <?php if ($pagination['page_num'] > 1): ?>
                                <button type="button" onclick="cambiarPagina(<?php echo e($pagination['page_num'] - 1); ?>)" class="pagination-btn" style="width: auto; min-width: 40px; height: auto; padding: 8px 16px; white-space: nowrap; border: 1px solid #DEE2E6; border-radius: 8px; color: #071D3A; cursor: pointer; background: white;">Anterior</button>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <?php if ($i == $pagination['page_num']): ?>
                                    <span class="pagination-btn active" style="width: auto; min-width: 40px; height: auto; padding: 8px 16px; white-space: nowrap; border: 1px solid #071D3A; border-radius: 8px; background: #071D3A; color: white;"><?php echo e($i); ?></span>
                                <?php else: ?>
                                    <button type="button" onclick="cambiarPagina(<?php echo e($i); ?>)" class="pagination-btn" style="width: auto; min-width: 40px; height: auto; padding: 8px 16px; white-space: nowrap; border: 1px solid #DEE2E6; border-radius: 8px; color: #071D3A; cursor: pointer; background: white;"><?php echo e($i); ?></button>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div>
                            <?php if ($pagination['page_num'] < $pagination['total_pages']): ?>
                                <button type="button" onclick="cambiarPagina(<?php echo e($pagination['page_num'] + 1); ?>)" class="pagination-btn" style="width: auto; min-width: 40px; height: auto; padding: 8px 16px; white-space: nowrap; border: 1px solid #DEE2E6; border-radius: 8px; color: #071D3A; cursor: pointer; background: white;">Siguiente</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Modal: Ver resumen del alumno -->
        <div id="modalResumen" class="modal-overlay" onclick="if (event.target === this) cerrarModalResumen();">
            <div class="modal-box" style="max-width: 480px;">
                <div class="modal-box-header">
                    <h2 id="modalResumenTitulo">Resumen del alumno</h2>
                    <button type="button" class="modal-close-btn" onclick="cerrarModalResumen()" aria-label="Cerrar">&times;</button>
                </div>
                <div class="modal-box-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                        <div style="background: #F8F9FA; padding: 14px; border-radius: 8px;">
                            <strong style="color: #6C757D; font-size: 11px; text-transform: uppercase;">Curso</strong>
                            <div id="modalResumenCurso" style="font-size: 15px; font-weight: 700; color: #071D3A; margin-top: 4px;">-</div>
                        </div>
                        <div style="background: #F8F9FA; padding: 14px; border-radius: 8px;">
                            <strong style="color: #6C757D; font-size: 11px; text-transform: uppercase;">Especialidad</strong>
                            <div id="modalResumenEspecialidad" style="font-size: 15px; font-weight: 700; color: #071D3A; margin-top: 4px;">-</div>
                        </div>
                        <div style="background: #F8F9FA; padding: 14px; border-radius: 8px;">
                            <strong style="color: #6C757D; font-size: 11px; text-transform: uppercase;">Turno</strong>
                            <div id="modalResumenTurno" style="font-size: 15px; font-weight: 700; color: #071D3A; margin-top: 4px;">-</div>
                        </div>
                        <div style="background: #E8F4FC; padding: 14px; border-radius: 8px;">
                            <strong style="color: #006397; font-size: 11px; text-transform: uppercase;">% Asistencia</strong>
                            <div id="modalResumenPorcentaje" style="font-size: 15px; font-weight: 700; color: #071D3A; margin-top: 4px;">-</div>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenPresentes" style="font-size: 20px; font-weight: 800; color: #28A745;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Presentes</div>
                        </div>
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenAusentes" style="font-size: 20px; font-weight: 800; color: #DC3545;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Ausentes</div>
                        </div>
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenTarde" style="font-size: 20px; font-weight: 800; color: #FD7E14;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Tarde</div>
                        </div>
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenAusConPres" style="font-size: 20px; font-weight: 800; color: #DC3545;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Aus. c/Pres.</div>
                        </div>
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenJustificados" style="font-size: 20px; font-weight: 800; color: #006397;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Justificados</div>
                        </div>
                        <div style="text-align: center; padding: 12px 4px; border: 1px solid #E9ECEF; border-radius: 8px;">
                            <div id="modalResumenRetiros" style="font-size: 20px; font-weight: 800; color: #FD7E14;">-</div>
                            <div style="font-size: 11px; color: #6C757D; text-transform: uppercase; margin-top: 2px;">Retiros</div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 16px; padding: 14px; background: #071D3A; border-radius: 8px;">
                        <div id="modalResumenFaltas" style="font-size: 24px; font-weight: 800; color: #FFFFFF;">-</div>
                        <div style="font-size: 11px; color: rgba(255,255,255,0.8); text-transform: uppercase; margin-top: 2px;">Total de Faltas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Ver asistencia detallada del alumno -->
        <div id="modalDetalle" class="modal-overlay" onclick="if (event.target === this) cerrarModalDetalle();">
            <div class="modal-box" style="max-width: 1100px;">
                <div class="modal-box-header">
                    <h2 id="modalDetalleTitulo">Asistencia detallada</h2>
                    <button type="button" class="modal-close-btn" onclick="cerrarModalDetalle()" aria-label="Cerrar">&times;</button>
                </div>
                <div class="modal-box-body">
                    <p style="font-size: 13px; color: #6C757D; margin: 0 0 16px 0;">Se muestran los registros del alumno seleccionado dentro de los mismos filtros aplicados en el reporte (período, curso, turno, etc.).</p>
                    <div id="modalDetalleContenido" style="max-height: 55vh; overflow-y: auto; overflow-x: auto;">
                        <p style="text-align: center; color: #6C757D; padding: 24px;">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    @media print {
        .sidebar,
        .dashboard-header,
        .pagination-bar,
        .toggle-group,
        .btn-outline,
        .btn-primary,
        .btn-primary-sm,
        .header-btn,
        .header-icon-btn,
        .header-user,
        .table-header,
        .card-footer {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .dashboard-body {
            padding: 0 !important;
        }
    }

    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #E9ECEF;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 4px;
    }

    .autocomplete-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .autocomplete-item:hover {
        background-color: #F8F9FA;
    }

    .autocomplete-item.active {
        background-color: #E8F4FC;
    }

    .autocomplete-item .alumno-name {
        font-weight: 600;
        color: #071D3A;
    }

    .autocomplete-item .alumno-curso {
        font-size: 13px;
        color: #6C757D;
    }

    .autocomplete-no-results {
        padding: 16px;
        text-align: center;
        color: #6C757D;
    }

    .export-dropdown-item {
        width: 100%;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        color: #071D3A;
        text-align: left;
        transition: background-color 0.2s;
    }

    .export-dropdown-item:hover {
        background-color: #F8F9FA;
    }

    .export-dropdown-item:first-child {
        border-radius: 8px 8px 0 0;
    }

    .export-dropdown-item:last-child {
        border-radius: 0 0 8px 8px;
    }

    .toggle-group .btn-outline.active {
        background: #071D3A;
        color: #FFFFFF;
        border-color: #071D3A;
    }

    /* Botón "Generar Reporte" / "Actualizar Datos" con el mismo tamaño que
       "Más filtros" y "Limpiar" (.btn-outline): mismo padding, borde, radio
       y tipografía, solo cambia el color de fondo. */
    .btn-generar-reporte {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        background: #071D3A;
        color: #FFFFFF;
        border: 2px solid #071D3A;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition, background-color 0.2s);
    }
    .btn-generar-reporte:hover {
        background: #006397;
        border-color: #006397;
    }

    /* Modales "Ver resumen" / "Ver asistencia detallada" */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-overlay.open {
        display: flex;
    }
    .modal-box {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .modal-box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #E9ECEF;
        flex-shrink: 0;
    }
    .modal-box-header h2 {
        margin: 0;
        color: #071D3A;
        font-size: 18px;
    }
    .modal-close-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        color: #6C757D;
        padding: 4px;
    }
    .modal-close-btn:hover {
        color: #071D3A;
    }
    .modal-box-body {
        padding: 24px;
        overflow-y: auto;
    }

    @media (max-width: 768px) {
        .modal-box {
            width: 100%;
            max-height: 100vh;
            border-radius: 0;
        }
        .modal-overlay {
            padding: 0;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = <?php
        echo json_encode([
            'labels' => $chart_labels,
            'data' => $chart_data,
            'colors' => $chart_colors
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>;

    // Datos ya cargados en la tabla de resumen (misma fuente que la BD ya
    // consultó para renderizar la tabla), indexados por alumno_id, para que
    // el modal "Ver resumen" no necesite una consulta nueva al servidor.
    const resumenPorAlumno = <?php
        $resumenMap = [];
        foreach (($datos_reporte['alumnos'] ?? []) as $a) {
            $resumenMap[$a['alumno_id']] = $a;
        }
        echo json_encode($resumenMap, JSON_UNESCAPED_UNICODE);
    ?>;

    <?php
    // Pre-populate selected alumno name if available
    $selectedAlumno = null;
    if (!empty($filters['alumno_id'])) {
        foreach ($alumnos_disponibles as $alumno) {
            if ($alumno['id'] == $filters['alumno_id']) {
                $selectedAlumno = $alumno;
                break;
            }
        }
    }
    if ($selectedAlumno): ?>
        const selectedAlumno = <?php echo json_encode($selectedAlumno, JSON_UNESCAPED_UNICODE); ?>;
    <?php else: ?>
        selectedAlumno = null;
    <?php endif; ?>

    function toggleExportMenu() {
        const menu = document.getElementById('exportMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    function toggleCustomDates(value) {
        const customDates = document.getElementById('customDates');
        customDates.style.display = value === 'personalizado' ? 'flex' : 'none';
    }

    function toggleMoreFilters() {
        const moreFilters = document.getElementById('moreFilters');
        moreFilters.style.display = moreFilters.style.display === 'block' ? 'none' : 'block';
    }

    // Navega manteniendo TODOS los parámetros actuales de la URL (filtros,
    // modo, orden, período, etc.) y aterriza en la sección de resultados en
    // vez de en el comienzo de la página. Sigue siendo navegación normal
    // (GET, sin AJAX) — solo evita que el usuario "pierda" el scroll.
    function irAResultados(cambios) {
        const params = new URLSearchParams(window.location.search);
        for (const clave in cambios) {
            if (cambios[clave] === null) {
                params.delete(clave);
            } else {
                params.set(clave, cambios[clave]);
            }
        }
        window.location.href = window.location.pathname + '?' + params.toString() + '#resultados';
    }

    function cambiarPagina(pagina) {
        irAResultados({ page_num: pagina });
    }

    function cambiarPerPage(perPage) {
        irAResultados({ per_page: perPage, page_num: null });
    }

    // ===== Modal "Ver resumen" =====
    function abrirModalResumen(alumnoId) {
        const a = resumenPorAlumno[alumnoId];
        if (!a) return;

        document.getElementById('modalResumenTitulo').textContent = a.apellido + ', ' + a.nombre;
        document.getElementById('modalResumenCurso').textContent = a.anio + '° ' + a.division;
        document.getElementById('modalResumenEspecialidad').textContent = a.especialidad_nombre || '-';
        document.getElementById('modalResumenTurno').textContent = a.turno ? (a.turno.charAt(0).toUpperCase() + a.turno.slice(1)) : '-';
        document.getElementById('modalResumenPorcentaje').textContent = a.porcentaje_asistencia + '%';
        document.getElementById('modalResumenPresentes').textContent = a.presentes;
        document.getElementById('modalResumenAusentes').textContent = a.ausentes;
        document.getElementById('modalResumenTarde').textContent = a.llegadas_tarde;
        document.getElementById('modalResumenAusConPres').textContent = a.ausentes_con_presente;
        document.getElementById('modalResumenJustificados').textContent = a.justificados;
        document.getElementById('modalResumenRetiros').textContent = a.retiros_anticipados;
        document.getElementById('modalResumenFaltas').textContent = a.faltas_total;

        document.getElementById('modalResumen').classList.add('open');
    }

    function cerrarModalResumen() {
        document.getElementById('modalResumen').classList.remove('open');
    }

    // ===== Modal "Ver asistencia detallada" =====
    function abrirModalDetalle(alumnoId, nombreCompleto) {
        document.getElementById('modalDetalleTitulo').textContent = 'Asistencia detallada — ' + nombreCompleto;
        const contenido = document.getElementById('modalDetalleContenido');
        contenido.innerHTML = '<p style="text-align:center; color:#6C757D; padding:24px;">Cargando...</p>';
        document.getElementById('modalDetalle').classList.add('open');

        // Reutiliza EXACTAMENTE los filtros vigentes en la URL actual
        // (período, curso, turno, materia, etc.) y agrega/fuerza el alumno.
        const params = new URLSearchParams(window.location.search);
        params.set('page', 'admin/reportes/detalle_alumno');
        params.set('alumno_id', alumnoId);

        fetch('index.php?' + params.toString(), {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                contenido.innerHTML = '<p style="text-align:center; color:#DC3545; padding:24px;">' + data.error + '</p>';
                return;
            }
            if (!data.registros || data.registros.length === 0) {
                contenido.innerHTML = '<p style="text-align:center; color:#6C757D; padding:24px;">No hay asistencias registradas para este alumno con los filtros actuales.</p>';
                return;
            }

            let html = '<table class="data-table" style="width:100%;"><thead><tr style="background:#E8F4FC;">';
            ['Fecha', 'Curso/División', 'Especialidad', 'Turno', 'Materia', 'Bloque', 'Horario', 'Preceptor', 'Estado', 'Hora Llegada', 'Hora Retiro', 'Justificación', 'Falta Diaria', 'Observaciones'].forEach(col => {
                html += '<th style="text-align:left; padding:10px 14px; font-size:12px; font-weight:700; color:#6C757D; text-transform:uppercase; white-space:nowrap;">' + col + '</th>';
            });
            html += '</tr></thead><tbody>';

            data.registros.forEach(r => {
                html += '<tr>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.fecha + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.curso + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.especialidad + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.turno + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.materia + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.bloque + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#6C757D; white-space:nowrap;">' + r.horario + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.preceptor + '</td>'
                    + '<td style="padding:10px 14px; white-space:nowrap;"><span class="status-badge" style="background:#E8F4FC;color:#006397; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:800;">' + r.estado + '</span></td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.hora_llegada + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.hora_retiro + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; white-space:nowrap;">' + r.justificacion + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#071D3A; text-align:center; white-space:nowrap;">' + r.falta_diaria + '</td>'
                    + '<td style="padding:10px 14px; font-size:13px; color:#6C757D;">' + r.observaciones + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table>';
            contenido.innerHTML = html;
        })
        .catch(() => {
            contenido.innerHTML = '<p style="text-align:center; color:#DC3545; padding:24px;">Ocurrió un error al cargar la asistencia detallada.</p>';
        });
    }

    function cerrarModalDetalle() {
        document.getElementById('modalDetalle').classList.remove('open');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalResumen();
            cerrarModalDetalle();
        }
    });

    function clearAlumnoSelection() {
        document.getElementById('alumno_search').value = '';
        document.getElementById('alumno_id_hidden').value = '';
        document.getElementById('alumno_autocomplete').style.display = 'none';
    }

    // Autocomplete functionality
    let debounceTimer;
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('alumno_search');
        const autocompleteDiv = document.getElementById('alumno_autocomplete');
        const cursoSelect = document.getElementById('curso-select');
        let selectedIndex = -1;

        // Pre-fill if we have a selected alumno
        if (selectedAlumno) {
            searchInput.value = selectedAlumno.apellido + ', ' + selectedAlumno.nombre;
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            selectedIndex = -1;

            if (query.length < 2) {
                autocompleteDiv.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                const params = new URLSearchParams();
                params.set('page', 'admin/reportes/autocomplete_alumnos');
                params.set('search', query);
                if (cursoSelect.value) {
                    params.set('curso_id', cursoSelect.value);
                }

                fetch('index.php?' + params.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    autocompleteDiv.innerHTML = '';
                    if (data.length === 0) {
                        autocompleteDiv.innerHTML = '<div class="autocomplete-no-results">No se encontraron resultados</div>';
                    } else {
                        data.forEach((alumno, index) => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.dataset.index = index;
                            item.dataset.alumno = JSON.stringify(alumno);
                            item.innerHTML = `
                                <div>
                                    <div class="alumno-name">${alumno.apellido}, ${alumno.nombre}</div>
                                    <div class="alumno-curso">
                                        ${alumno.anio}° ${alumno.division}${alumno.especialidad ? ' - ' + alumno.especialidad : ''}
                                    </div>
                                </div>
                                ${alumno.dni ? `<div style="font-size: 12px; color: #6C757D;">${alumno.dni}</div>` : ''}
                            `;
                            item.addEventListener('click', function() {
                                selectAlumno(alumno);
                            });
                            autocompleteDiv.appendChild(item);
                        });
                    }
                    autocompleteDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching autocomplete data:', error);
                });
            }, 300);
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = autocompleteDiv.querySelectorAll('.autocomplete-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % items.length;
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = selectedIndex <= 0 ? items.length - 1 : selectedIndex - 1;
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    const data = JSON.parse(items[selectedIndex].dataset.alumno || '{}');
                    if (data.id) {
                        selectAlumno(data);
                    }
                }
            } else if (e.key === 'Escape') {
                autocompleteDiv.style.display = 'none';
                selectedIndex = -1;
            }
        });

        function updateActiveItem(items) {
            items.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('active');
                }
            });
        }

        function selectAlumno(alumno) {
            searchInput.value = alumno.apellido + ', ' + alumno.nombre;
            document.getElementById('alumno_id_hidden').value = alumno.id;
            autocompleteDiv.style.display = 'none';
            selectedIndex = -1;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                autocompleteDiv.style.display = 'none';
            }
            const exportMenu = document.getElementById('exportMenu');
            if (exportMenu && !e.target.closest('.dropdown') && !e.target.closest('[onclick*="toggleExportMenu"]')) {
                exportMenu.style.display = 'none';
            }
        });

        // Chart initialization
        const ctx = document.getElementById('presentismoChart');
        if (ctx && chartData.labels.length > 0) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Presentismo (%)',
                        data: chartData.data,
                        backgroundColor: chartData.colors,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + '% de asistencia';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
