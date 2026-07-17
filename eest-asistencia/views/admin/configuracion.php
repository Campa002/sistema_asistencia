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
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
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
                    <h1>Gestión Técnica</h1>
                    <p>Configuración avanzada del sistema y herramientas de administración</p>
                </div>
            </div>
            
            <div style="margin-top: 32px; display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
                <div>
                    <div class="dashboard-card" style="border: 2px solid #DEE2E6;">
                        <div class="dashboard-card-header">
                            <h3>Perfil de Administrador</h3>
                            <button class="card-action-btn">Cerrar Sesión</button>
                        </div>
                        <div style="display: flex; gap: 24px; padding: 24px 0;">
                            <div style="width: 120px; height: 120px; background: #DEE2E6; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="1.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-size: 20px; font-weight: 800; color: #071D3A; margin-bottom: 10px;">Carlos Eduardo Rodriguez</h4>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px;">Administrador de Sistema</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <span style="color: #6C757D; font-size: 15px;">Desde Mayo 2024</span>
                                </div>
                                <button class="btn-icon" style="margin-top: 12px; background: #071D3A; color: white; border-radius: 50%; width: 36px; height: 36px; margin-left: 85px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card" style="margin-top: 24px; border: 2px solid #DEE2E6;">
                        <div class="dashboard-card-header">
                            <h3>Actividad Reciente</h3>
                            <button class="card-action-btn">Ver todo el historial</button>
                        </div>
                        <div style="padding: 8px 0;">
                            <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #F1F5F9;">
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <div style="font-size: 14px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Fecha y Hora</div>
                                        <div style="font-size: 14px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Acción Realizada</div>
                                        <div style="font-size: 14px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Usuario</div>
                                        <div style="font-size: 14px; font-weight: 700; color: #6C757D; text-transform: uppercase;">Estado</div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #F1F5F9;">
                                <div style="width: 120px; color: #071D3A; font-weight: 600;">24 May, 19:12pm</div>
                                <div style="flex: 1; color: #071D3A;">Creación de curso 6to 1ra</div>
                                <div style="color: #071D3A; font-weight: 700;">Carlos Rodriguez (Admin)</div>
                                <div style="background: #E8F8F0; color: #28A745; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">Completado</div>
                            </div>
                            <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #F1F5F9;">
                                <div style="width: 120px; color: #071D3A; font-weight: 600;">23 May, 09:12am</div>
                                <div style="flex: 1; color: #071D3A;">Modificación de asistencia</div>
                                <div style="color: #071D3A; font-weight: 700;">Marta Silva (Preceptor)</div>
                                <div style="background: #E8F8F0; color: #28A745; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">Completado</div>
                            </div>
                            <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #F1F5F9;">
                                <div style="width: 120px; color: #071D3A; font-weight: 600;">20 May, 18:20pm</div>
                                <div style="flex: 1; color: #071D3A;">Gestión de reemplazo docente</div>
                                <div style="color: #071D3A; font-weight: 700;">Luciano Fuentes (Admin)</div>
                                <div style="background: #E8F8F0; color: #28A745; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">Completado</div>
                            </div>
                            <div style="display: flex; gap: 16px; padding: 16px 0;">
                                <div style="width: 120px; color: #071D3A; font-weight: 600;">22 May, 10:30am</div>
                                <div style="flex: 1; color: #071D3A;">Generación de reporte mensual</div>
                                <div style="color: #071D3A; font-weight: 700;">Carlos Rodriguez (Admin)</div>
                                <div style="background: #E8F8F0; color: #28A745; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">Completado</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card" style="margin-top: 24px; border: 2px solid #DEE2E6; background: #F5F9FF;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #DEE2E6;">
                            <h3 style="font-size: 18px; font-weight: 700; color: #071D3A; margin: 0;">Seguridad de la Cuenta</h3>
                            <span style="background: #76C7FF; color: #006397; padding: 5px 14px; border-radius: 16px; font-size: 12px; font-weight: 800; text-transform: uppercase;">Nivel de Seguridad: Alto</span>
                        </div>
                        <div style="margin-top: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; background: white; border: 1px solid #DEE2E6; border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    <div>
                                        <div style="font-size: 16px; font-weight: 700; color: #071D3A;">Cambiar Contraseña</div>
                                        <div style="font-size: 13px; color: #6C757D;">Última actualización hace 45 días.</div>
                                    </div>
                                </div>
                                <button class="btn-outline" style="padding: 8px 16px; font-size: 14px;">Gestionar</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="dashboard-card" style="border: 2px solid #DEE2E6; background: #071D3A;">
                        <h3 style="color: white; font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                            </svg>
                            Resumen Académico
                        </h3>
                        <div style="display: grid; gap: 16px;">
                            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 16px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Asistencias Registradas</div>
                                <div style="color: white; font-size: 40px; font-weight: 800;">2,482</div>
                                <svg style="float: right; margin-top: -36px; opacity: 0.8;" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </div>
                            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 16px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Faltas Justificadas</div>
                                <div style="color: white; font-size: 40px; font-weight: 800;">124</div>
                                <svg style="float: right; margin-top: -36px; opacity: 0.8;" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 16px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Cursos Administrados</div>
                                <div style="color: white; font-size: 40px; font-weight: 800;">32</div>
                                <svg style="float: right; margin-top: -36px; opacity: 0.8;" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5L12 2z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                </svg>
                            </div>
                            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 16px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Reportes Generados</div>
                                <div style="color: white; font-size: 40px; font-weight: 800;">18</div>
                                <svg style="float: right; margin-top: -36px; opacity: 0.8;" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                    <line x1="9" y1="16" x2="9" y2="19"/>
                                    <line x1="15" y1="16" x2="15" y2="19"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card" style="margin-top: 24px; border: 2px solid #DEE2E6; background: #DEE2E6;">
                        <h3 style="font-size: 16px; font-weight: 700; color: #6C757D; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            Información del Sistema
                        </h3>
                        <div style="display: grid; gap: 12px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6C757D; font-weight: 600;">Versión:</span>
                                <span style="color: #071D3A; font-weight: 800; background: white; padding: 4px 10px; border-radius: 6px;">v2.4.0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6C757D; font-weight: 600;">Última Actualización:</span>
                                <span style="color: #071D3A; font-weight: 700;">15/10/2026</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #6C757D; font-weight: 600;">Estado del Sistema:</span>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 10px; height: 10px; background: #28A745; border-radius: 50%;"></div>
                                    <span style="color: #28A745; font-weight: 800; font-size: 13px;">Operativo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
