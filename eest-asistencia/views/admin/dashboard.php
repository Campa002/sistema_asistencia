<?php
require_role('admin');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Curso.php';
require_once __DIR__ . '/../../models/Comunicado.php';
require_once __DIR__ . '/../../models/Mensaje.php';
require_once __DIR__ . '/../../models/Reporte.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$usuario_id]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

// ------------------------------------------------------------------
// Todas las estadísticas de este panel salen de la BD real. Ninguna
// está hardcodeada — si no hay datos para un valor, se muestra un
// estado vacío honesto en vez de inventar un número.
// ------------------------------------------------------------------
$total_alumnos = (int) $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'alumno'")->fetchColumn();
$total_preceptores = (int) $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'preceptor'")->fetchColumn();
$cursos_activos = Curso::countByEstado('activo');

// "Hoy" en zona horaria Argentina (no la del servidor).
$hoyArgentina = (new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('Y-m-d');
$stmtHoy = $db->prepare("
    SELECT
        COUNT(DISTINCT CONCAT(reg.fecha, reg.turno, det.alumno_id)) as jornadas_total,
        SUM(CASE WHEN det.estado IN ('ausente', 'ausente_con_presente') THEN 1 ELSE 0 END) as ausentes_total
    FROM detalles_asistencia det
    JOIN registros_asistencia reg ON det.registro_id = reg.id
    WHERE reg.fecha = ? AND reg.estado != 'anulada'
");
$stmtHoy->execute([$hoyArgentina]);
$hoyRow = $stmtHoy->fetch(PDO::FETCH_ASSOC);
$jornadas_hoy = (int) ($hoyRow['jornadas_total'] ?? 0);
$inasistencias_hoy = (int) ($hoyRow['ausentes_total'] ?? 0);
$porcentaje_asistencia_hoy = $jornadas_hoy > 0
    ? round((($jornadas_hoy - $inasistencias_hoy) / $jornadas_hoy) * 100, 1)
    : null; // null = todavía no se tomó asistencia hoy

// Mensajería: mismo modelo real que ya usa el módulo de Mensajes.
$conversacionesAdmin = Mensaje::getAllConversations($usuario_id);
$mensajes_pendientes = array_sum(array_column($conversacionesAdmin, 'no_leidos'));
$ultimaConversacion = $conversacionesAdmin[0] ?? null;

// Comunicado global más reciente (activo).
$comunicadosRecientes = Comunicado::getAll(['estado' => 'activo']);
$ultimoComunicado = $comunicadosRecientes[0] ?? null;

// Tendencia: últimas fechas reales con asistencia tomada (hasta 7). No
// se usan "los últimos 7 días de calendario" porque en un día sin
// ninguna asistencia tomada todavía no hay datos que mostrar — se
// prefiere mostrar las fechas reales más recientes con registros antes
// que una gráfica en cero que parecería 100% de ausentismo.
$fechasConDatos = $db->query("SELECT DISTINCT fecha FROM registros_asistencia WHERE estado != 'anulada' ORDER BY fecha DESC LIMIT 7")->fetchAll(PDO::FETCH_COLUMN);
sort($fechasConDatos);
$tendencia_labels = [];
$tendencia_valores = [];
foreach ($fechasConDatos as $fecha) {
    $stmtDia = $db->prepare("
        SELECT
            COUNT(DISTINCT CONCAT(reg.turno, det.alumno_id)) as jornadas_total,
            SUM(CASE WHEN det.estado IN ('ausente', 'ausente_con_presente') THEN 1 ELSE 0 END) as ausentes_total
        FROM detalles_asistencia det
        JOIN registros_asistencia reg ON det.registro_id = reg.id
        WHERE reg.fecha = ? AND reg.estado != 'anulada'
    ");
    $stmtDia->execute([$fecha]);
    $diaRow = $stmtDia->fetch(PDO::FETCH_ASSOC);
    $jTotal = (int) ($diaRow['jornadas_total'] ?? 0);
    $jAus = (int) ($diaRow['ausentes_total'] ?? 0);
    $tendencia_labels[] = date('d/m', strtotime($fecha));
    $tendencia_valores[] = $jTotal > 0 ? round((($jTotal - $jAus) / $jTotal) * 100, 1) : 0;
}

// Ausentismo por curso: reutiliza el mismo cálculo que ya usa Reportes
// (Reporte::getPresentismoPorDivision), acotado a las fechas reales
// recientes calculadas arriba.
$ausentismoPorCurso = [];
if (!empty($fechasConDatos)) {
    $presentismo = Reporte::getPresentismoPorDivision([
        'fecha_desde' => $fechasConDatos[0],
        'fecha_hasta' => end($fechasConDatos)
    ]);
    usort($presentismo, fn($a, $b) => $a['porcentaje'] <=> $b['porcentaje']);
    $ausentismoPorCurso = array_slice($presentismo, 0, 2);
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
                    <h1>Resumen Institucional</h1>
                    <p>Estado del sistema y asistencia en tiempo real para EEST N°1</p>
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

            <!-- Stats Grid -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Alumnos</div>
                    <div class="kpi-value"><?= number_format($total_alumnos, 0, ',', '.') ?></div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-label">Total Preceptores</div>
                    <div class="kpi-value"><?= $total_preceptores ?></div>
                </div>

                <div class="kpi-card" style="border-left: 3px solid #006397;">
                    <div class="kpi-label" style="color: #006397;">Asistencia Hoy</div>
                    <div class="kpi-value"><?= $porcentaje_asistencia_hoy !== null ? e($porcentaje_asistencia_hoy) . '%' : '—' ?></div>
                </div>

                <div class="kpi-card" style="border-left: 3px solid #DC3545;">
                    <div class="kpi-label" style="color: #DC3545;">Inasistencias Hoy</div>
                    <div class="kpi-value" style="color: #DC3545;"><?= $jornadas_hoy > 0 ? e($inasistencias_hoy) : '—' ?></div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-label">Cursos Activos</div>
                    <div class="kpi-value"><?= $cursos_activos ?></div>
                </div>

                <div class="kpi-card kpi-card-blue">
                    <div class="kpi-label">Mensajes Pendientes</div>
                    <div class="kpi-value"><?= $mensajes_pendientes ?></div>
                </div>
            </div>

            <!-- Dashboard Grid (Charts + Side Cards) -->
            <div class="dash-row" style="margin-top: 32px;">
                <div class="dash-col-main">
                    <!-- Chart Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>Tendencias de Asistencia</h3>
                        </div>
                        <?php if (!empty($tendencia_labels)): ?>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                        <?php else: ?>
                        <p style="color: #6C757D; padding: 24px 0;">Todavía no hay registros de asistencia para graficar una tendencia.</p>
                        <?php endif; ?>

                        <!-- Critical Absence -->
                        <div style="margin-top: 24px;">
                            <h4 style="font-size: 18px; font-weight: 700; color: #071D3A; margin-bottom: 16px;">Ausentismo por Curso (fechas recientes con datos)</h4>
                            <div class="critical-items">
                                <?php if (empty($ausentismoPorCurso)): ?>
                                    <p style="color: #6C757D;">Sin datos suficientes todavía.</p>
                                <?php else: foreach ($ausentismoPorCurso as $curso): ?>
                                    <div class="critical-item">
                                        <span class="badge-curso"><?= e($curso['division']) ?><?= $curso['especialidad'] ? ' ' . e($curso['especialidad']) : '' ?></span>
                                        <span style="font-size: 18px; font-weight: 700; color: #071D3A; margin-left: 12px;"><?= e(round(100 - $curso['porcentaje'], 1)) ?>% de Ausentismo</span>
                                        <a href="index.php?page=admin/reportes" class="card-action-btn" style="margin-left: auto; text-decoration: none;">Ver Detalle</a>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-col-side">
                    <!-- Messages Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>Mensajes</h3>
                            <?php if ($mensajes_pendientes > 0): ?>
                                <span class="badge" style="background: #006397;"><?= e($mensajes_pendientes) ?> Nuevos</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($ultimaConversacion): ?>
                        <div class="message-item">
                            <div class="message-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-title"><?= $ultimaConversacion['otroParticipante'] ? e($ultimaConversacion['otroParticipante']['apellido'] . ', ' . $ultimaConversacion['otroParticipante']['nombre']) : e($ultimaConversacion['titulo'] ?? 'Conversación') ?></span>
                                </div>
                                <p class="message-text"><?= e($ultimaConversacion['ultimo_mensaje'] ?? 'Sin mensajes todavía') ?></p>
                                <span class="message-meta"><?= e(format_date($ultimaConversacion['ultimo_mensaje_fecha'], 'd/m/Y H:i')) ?></span>
                            </div>
                        </div>
                        <?php else: ?>
                        <p style="color: #6C757D; padding: 8px 0;">No hay conversaciones todavía.</p>
                        <?php endif; ?>

                        <a href="index.php?page=admin/mensajes" class="btn-outline" style="width: 100%; margin-top: 8px; text-align: center; display: block; text-decoration: none; box-sizing: border-box;">Abrir Mensajería</a>
                    </div>

                    <!-- Notices Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 style="color: #6C757D; text-transform: uppercase; font-size: 15px; font-weight: 600;">Comunicados Recientes</h3>
                            <a href="index.php?page=admin/comunicados" class="card-action-btn" style="text-decoration: none;">Ver todos</a>
                        </div>

                        <?php if ($ultimoComunicado): ?>
                        <div class="notice-card">
                            <div class="notice-header">
                                <div class="notice-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <path d="M16 2v4M8 2v4M3 10h18"/>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="notice-title"><?= e($ultimoComunicado['titulo']) ?></h4>
                            <p class="notice-text"><?= e(mb_strimwidth($ultimoComunicado['contenido'], 0, 160, '…')) ?></p>
                            <p class="notice-meta"><?= e(format_date($ultimoComunicado['created_at'], 'd/m/Y')) ?></p>
                        </div>
                        <?php else: ?>
                        <p style="color: #6C757D; padding: 8px 0;">No hay comunicados publicados todavía.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php if (!empty($tendencia_labels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($tendencia_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            datasets: [{
                label: '% Asistencia',
                data: <?= json_encode($tendencia_valores, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                borderColor: '#071D3A',
                backgroundColor: 'rgba(7, 29, 58, 0.05)',
                borderWidth: 4,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#071D3A',
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        color: '#6C757D',
                        font: { weight: '700' }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#6C757D',
                        font: { weight: '700', size: 12 }
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
