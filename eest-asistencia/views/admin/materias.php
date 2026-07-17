<?php
require_role('admin');
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$usuario_id = $_SESSION['usuario_id'];
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
            <div class="page-header">
                <div class="page-title">
                    <h1>Gestión de Materias</h1>
                    <p>Configura materias para asistencia por especialidad y división académica.</p>
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
            
            <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Total Materias</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;">68</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Programación</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;">32</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Electrónica</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;">24</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span style="color: #071D3A; font-weight: 800; font-size: 14px; text-transform: uppercase;">Ciclo Básico</span>
                    </div>
                    <div class="stat-value" style="color: #071D3A; font-size: 40px; font-weight: 800;">12</div>
                </div>
            </div>
            
            <div style="margin-top: 32px;">
                <h2 style="font-size: 20px; font-weight: 700; color: #071D3A; margin-bottom: 20px;">Listado de Materias</h2>
                <div class="data-table-container">
                    <div class="table-header">
                        <div class="table-toolbar" style="flex-wrap: wrap; gap: 16px;">
                            <select class="filter-input" style="min-width: 200px;">
                                <option>Especialidad: Todas</option>
                            </select>
                            <select class="filter-input" style="min-width: 200px;">
                                <option>Curso: Todos</option>
                            </select>
                            <button class="btn-outline" style="margin-left: auto;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="14.5 10 4 4 4 20 14.5 14"/>
                                    <path d="M3.5 4L17 14H20a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/>
                                </svg>
                                Limpiar Filtros
                            </button>
                        </div>
                    </div>
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #E8F4FC;">
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Nombre de la Materia</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Especialidad</th>
                                <th style="text-align: left; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Horas Semanales</th>
                                <th style="text-align: right; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #6C757D; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 16px 24px; font-size: 16px; font-weight: 700; color: #071D3A;">Programación 1</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;">Programación</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;">6 hs</td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                        <button class="btn-icon btn-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon btn-icon-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 24px; font-size: 16px; font-weight: 700; color: #071D3A;">Electrónica Analógica</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;">Electrónica</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;">5 hs</td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                        <button class="btn-icon btn-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon btn-icon-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 24px; font-size: 16px; font-weight: 700; color: #071D3A;">Matemática</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #6C757D;">Ciclo Básico</td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #071D3A;">6 hs</td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                        <button class="btn-icon btn-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button class="btn-icon btn-icon-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
