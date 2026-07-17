<?php
require_role('admin');
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];

// Stats
$total_alumnos = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'alumno'")->fetchColumn();
$total_preceptores = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'preceptor'")->fetchColumn();
$asistencias_hoy = 1248;
$inasistencias_hoy = 95;
$cursos_activos = 54;
$mensajes_pendientes = 18;
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
                    <button class="header-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        Comunicado Global
                    </button>
                    <button class="header-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </button>
                    <button class="header-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </button>
                </div>
                <div class="header-user">
                    <div class="header-user-info">
                        <div class="name"><?= e($_SESSION['nombre'] ?? 'Admin User') ?></div>
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
                    <?php setlocale(LC_TIME, 'es_ES.UTF-8'); echo strftime('%A, %d de %B de %Y'); ?>
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
                    <div class="kpi-value">92.4%</div>
                </div>
                
                <div class="kpi-card" style="border-left: 3px solid #DC3545;">
                    <div class="kpi-label" style="color: #DC3545;">Inasistencias Hoy</div>
                    <div class="kpi-value" style="color: #DC3545;"><?= $inasistencias_hoy ?></div>
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
                            <h3>Tendencias de Asistencia Semanal</h3>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <div class="legend-dot this-week"></div>
                                    <span>Esta Semana</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot last-week"></div>
                                    <span>Semana Pasada</span>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                        
                        <!-- Critical Absence -->
                        <div style="margin-top: 24px;">
                            <h4 style="font-size: 18px; font-weight: 700; color: #071D3A; margin-bottom: 16px;">Ausentismo Crítico por Curso</h4>
                            <div class="critical-items">
                                <div class="critical-item">
                                    <span class="badge-curso">7° 2da Programación</span>
                                    <span style="font-size: 18px; font-weight: 700; color: #071D3A; margin-left: 12px;">32% de Ausentismo</span>
                                    <button class="card-action-btn" style="margin-left: auto;">Ver Detalle</button>
                                </div>
                                <div class="critical-item">
                                    <span class="badge-curso">4° 2da Electrónica</span>
                                    <span style="font-size: 18px; font-weight: 700; color: #071D3A; margin-left: 12px;">28% de Ausentismo</span>
                                    <button class="card-action-btn" style="margin-left: auto;">Ver Detalle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dash-col-side">
                    <!-- Messages Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>Mensajes de Preceptores</h3>
                            <span class="badge" style="background: #006397;">4 Nuevos</span>
                        </div>
                        
                        <div class="message-item">
                            <div class="message-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-title">Preceptor Rossi (5° 2da)</span>
                                </div>
                                <p class="message-text">El lector de huellas en la sala de talleres no está respondiendo. Necesitamos mantenimiento.</p>
                                <span class="message-meta">Hace 2m</span>
                            </div>
                        </div>
                        
                        <button class="btn-outline" style="width: 100%; margin-top: 8px;">Abrir Mensajería</button>
                    </div>
                    
                    <!-- Notices Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 style="color: #6C757D; text-transform: uppercase; font-size: 15px; font-weight: 600;">Avisos Recientes</h3>
                            <button class="card-action-btn">Ver todos</button>
                        </div>
                        
                        <div class="notice-card">
                            <div class="notice-header">
                                <div class="notice-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <path d="M16 2v4M8 2v4M3 10h18"/>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="notice-title">Inscripción a Pasantías 2026</h4>
                            <p class="notice-text">Recordamos a los alumnos de 7mo año que la inscripción a pasantías vence el 30 de junio.</p>
                            <p class="notice-meta">Ayer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
            datasets: [{
                label: 'Esta Semana',
                data: [92, 94, 90, 95, 92],
                borderColor: '#071D3A',
                backgroundColor: 'rgba(7, 29, 58, 0.05)',
                borderWidth: 4,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#071D3A',
                pointRadius: 0
            }, {
                label: 'Semana Pasada',
                data: [88, 87, 89, 90, 86],
                borderColor: '#DEE2E6',
                backgroundColor: 'transparent',
                borderWidth: 4,
                fill: false,
                tension: 0.3,
                pointBackgroundColor: '#DEE2E6',
                pointRadius: 0
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
                    min: 50,
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
