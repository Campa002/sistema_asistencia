<?php
require_role('admin');
require_once __DIR__ . '/../../models/Mensaje.php';
require_once __DIR__ . '/../../controllers/AdminMensajesController.php';

$db = Database::getConnection();
$userId = $_SESSION['usuario_id'];
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

$data = AdminMensajesController::index();
$conversaciones = $data['conversaciones'];
$allowedRecipients = $data['allowedRecipients'];
$filters = $data['filters'];
$conversacionSeleccionada = $data['conversacionSeleccionada'];
$mensajesSeleccionados = $data['mensajesSeleccionados'];
$otroParticipante = $data['otroParticipante'];

$rolNombres = [
    'admin' => 'Administrador',
    'directivo' => 'Directivo',
    'preceptor' => 'Preceptor'
];
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
        
        <div class="chat-container">
            <!-- Columna 1: Lista de conversaciones -->
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h2 class="chat-title">Bandeja de entrada</h2>
                    <?php 
                    $totalNoLeidos = 0;
                    foreach ($conversaciones as $c) {
                        $totalNoLeidos += $c['no_leidos'];
                    }
                    if ($totalNoLeidos > 0): 
                    ?>
                        <span class="unread-badge"><?= e($totalNoLeidos) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="chat-sidebar-search">
                    <form method="GET" action="index.php" class="search-form">
                        <input type="hidden" name="page" value="admin/mensajes">
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscar conversación..." value="<?= e($filters['busqueda']) ?>">
                    </form>
                </div>
                
                <div class="chat-sidebar-filters">
                    <form method="GET" action="index.php" class="filter-form">
                        <input type="hidden" name="page" value="admin/mensajes">
                        <button type="submit" name="rol" value="" class="filter-btn <?= empty($filters['rol']) ? 'active' : '' ?>">Todos</button>
                        <button type="submit" name="rol" value="directivo" class="filter-btn <?= $filters['rol'] === 'directivo' ? 'active' : '' ?>">Directivos</button>
                        <button type="submit" name="rol" value="preceptor" class="filter-btn <?= $filters['rol'] === 'preceptor' ? 'active' : '' ?>">Preceptores</button>
                        <button type="submit" name="rol" value="admin" class="filter-btn <?= $filters['rol'] === 'admin' ? 'active' : '' ?>">Administradores</button>
                    </form>
                </div>
                
                <div class="chat-sidebar-new">
                    <button type="button" class="btn-new-conversation" onclick="openNewConversationModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Nueva conversación
                    </button>
                </div>
                
                <div class="chat-list">
                    <?php if (empty($conversaciones)): ?>
                        <div class="empty-state">
                            <p>Todavía no tenés conversaciones institucionales.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversaciones as $conv): ?>
                            <a href="index.php?page=admin/mensajes&conversacion_id=<?= e($conv['id']) ?>" 
                               class="chat-item <?= $conversacionSeleccionada && $conversacionSeleccionada['id'] === $conv['id'] ? 'active' : '' ?>">
                                <div class="chat-item-avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="chat-item-info">
                                    <div class="chat-item-header">
                                        <span class="chat-item-name">
                                            <?php if ($conv['otroParticipante']): ?>
                                                <?= e($conv['otroParticipante']['nombre'] . ' ' . $conv['otroParticipante']['apellido']) ?>
                                            <?php else: ?>
                                                Conversación #<?= e($conv['id']) ?>
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($conv['ultimo_mensaje_fecha']): ?>
                                            <span class="chat-item-time">
                                                <?php 
                                                $date = new DateTime($conv['ultimo_mensaje_fecha']);
                                                $now = new DateTime();
                                                $diff = $now->diff($date);
                                                
                                                if ($diff->days === 0): 
                                                    echo $date->format('H:i');
                                                elseif ($diff->days === 1): 
                                                    echo 'Ayer';
                                                elseif ($diff->days < 7): 
                                                    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                                    echo $dias[$date->format('w')];
                                                else: 
                                                    echo $date->format('d/m/Y');
                                                endif;
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chat-item-preview">
                                        <?php if ($conv['ultimo_mensaje']): ?>
                                            <?= e(mb_substr($conv['ultimo_mensaje'], 0, 40)) ?>
                                            <?php if (mb_strlen($conv['ultimo_mensaje']) > 40): ?>
                                                ...
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <em>Nuevo chat</em>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($conv['no_leidos'] > 0): ?>
                                    <span class="chat-item-unread"><?= e($conv['no_leidos']) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Columna 2: Chat -->
            <div class="chat-main">
                <?php if ($conversacionSeleccionada && $otroParticipante): ?>
                    <div class="chat-main-header">
                        <div class="chat-main-header-info">
                            <div class="chat-main-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="chat-main-header-text">
                                <h3><?= e($otroParticipante['nombre'] . ' ' . $otroParticipante['apellido']) ?></h3>
                                <span class="chat-main-status">
                                    <?= e($rolNombres[$otroParticipante['rol']]) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <?php if (empty($mensajesSeleccionados)): ?>
                            <div class="empty-chat">
                                <p>No hay mensajes aún</p>
                                <p>Envía un mensaje para iniciar la conversación</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($mensajesSeleccionados as $msg): ?>
                                <div class="chat-message <?= $msg['remitente_id'] === $userId ? 'sent' : 'received' ?>">
                                    <div class="chat-message-bubble">
                                        <div class="chat-message-text">
                                            <?= e($msg['contenido']) ?>
                                        </div>
                                        <div class="chat-message-time">
                                            <?php $msgDate = new DateTime($msg['created_at']);
                                            echo $msgDate->format('d/m/Y H:i'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-input-area">
                        <form method="POST" action="index.php?page=admin/mensajes&conversacion_id=<?= e($conversacionSeleccionada['id']) ?>" class="chat-input-form">
                            <input type="hidden" name="action" value="enviar_mensaje">
                            <input type="hidden" name="conversacion_id" value="<?= e($conversacionSeleccionada['id']) ?>">
                            <div class="chat-input-wrapper">
                                <input type="text" name="contenido" class="chat-input" placeholder="Escribe un mensaje..." required>
                                <button type="submit" class="chat-send-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4L22 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="empty-chat-main">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <h2>Seleccioná una conversación para ver los mensajes.</h2>
                        <p>O iniciá una nueva conversación</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Columna 3: Información de contacto -->
            <div class="chat-info">
                <?php if ($conversacionSeleccionada && $otroParticipante): ?>
                    <div class="chat-info-header">
                        <div class="chat-info-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3><?= e($otroParticipante['nombre'] . ' ' . $otroParticipante['apellido']) ?></h3>
                        <span class="chat-info-role"><?= e($rolNombres[$otroParticipante['rol']]) ?></span>
                    </div>
                    
                    <div class="chat-info-section">
                        <h4 class="chat-info-section-title">Información de contacto</h4>
                        <div class="chat-info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <path d="M22 6l-10 7L2 6"/>
                            </svg>
                            <span><?= e($otroParticipante['email']) ?></span>
                        </div>
                        <div class="chat-info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>Activo desde <?= e(date('d/m/Y', strtotime($otroParticipante['created_at'] ?? 'N/A'))) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="chat-info-empty">
                        <p>Selecciona una conversación<br>para ver la información</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Modal: Nueva Conversación -->
    <div class="modal" id="newConversationModal">
        <div class="modal-content modal-medium">
            <div class="modal-header">
                <h2>Nueva conversación</h2>
                <button class="modal-close-btn" onclick="closeNewConversationModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="index.php?page=admin/mensajes" class="new-conversation-form">
                <input type="hidden" name="action" value="nueva_conversacion">
                
                <div class="form-group">
                    <label for="destinatario">Seleccionar destinatario</label>
                    <select id="destinatario" name="destinatario_id" class="filter-input" required>
                        <option value="">Selecciona un usuario</option>
                        <?php foreach ($allowedRecipients as $rol => $users): ?>
                            <optgroup label="<?= e($rolNombres[$rol]) ?>">
                                <?php foreach ($users as $user): ?>
                                    <?php if ($user['id'] !== $userId): ?>
                                        <option value="<?= e($user['id']) ?>">
                                            <?= e($user['apellido'] . ', ' . $user['nombre']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="newMessage">Primer mensaje</label>
                    <textarea id="newMessage" name="contenido" class="filter-input" rows="4" placeholder="Escribe tu mensaje..." required></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-outline" onclick="closeNewConversationModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Iniciar conversación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Chat Styles */
.chat-container {
    display: flex;
    height: calc(100vh - 80px);
    overflow: hidden;
}

/* Chat Sidebar */
.chat-sidebar {
    width: 320px;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    background: white;
}

.chat-sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-title {
    font-size: 20px;
    font-weight: 700;
    color: #071D3A;
    margin: 0;
}

.unread-badge {
    background: #007BFF;
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 12px;
}

.chat-sidebar-search {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
}

.search-input {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
}

.chat-sidebar-filters {
    padding: 12px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-form {
    width: 100%;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 6px 16px;
    border: none;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    background: #e9ecef;
    color: #6C757D;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background: #dee2e6;
}

.filter-btn.active {
    background: #007BFF;
    color: white;
}

.chat-sidebar-new {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
}

.btn-new-conversation {
    width: 100%;
    padding: 10px 16px;
    background: #007BFF;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s ease;
}

.btn-new-conversation:hover {
    background: #0056b3;
}

.chat-list {
    flex: 1;
    overflow-y: auto;
}

.chat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f3f5;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s ease;
}

.chat-item:hover {
    background: #f8f9fa;
}

.chat-item.active {
    background: #e6f2ff;
}

.chat-item-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6C757D;
    flex-shrink: 0;
}

.chat-item-info {
    flex: 1;
    min-width: 0;
}

.chat-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.chat-item-name {
    font-weight: 700;
    color: #071D3A;
    font-size: 15px;
}

.chat-item-time {
    font-size: 12px;
    color: #6C757D;
    flex-shrink: 0;
    margin-left: 8px;
}

.chat-item-preview {
    font-size: 13px;
    color: #6C757D;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-item-unread {
    background: #28A745;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}

/* Chat Main */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

.chat-main-header {
    padding: 16px 24px;
    border-bottom: 1px solid #e9ecef;
    background: white;
    display: flex;
    align-items: center;
}

.chat-main-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-main-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6C757D;
}

.chat-main-header-text h3 {
    font-size: 16px;
    font-weight: 700;
    color: #071D3A;
    margin: 0;
}

.chat-main-status {
    font-size: 13px;
    color: #6C757D;
    display: flex;
    align-items: center;
    gap: 6px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28A745;
}

.chat-messages {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.chat-message {
    display: flex;
}

.chat-message.sent {
    justify-content: flex-end;
}

.chat-message.received {
    justify-content: flex-start;
}

.chat-message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 16px;
}

.chat-message.sent .chat-message-bubble {
    background: #071D3A;
    color: white;
    border-bottom-right-radius: 4px;
}

.chat-message.received .chat-message-bubble {
    background: white;
    color: #071D3A;
    border-bottom-left-radius: 4px;
}

.chat-message-text {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 4px;
    word-wrap: break-word;
}

.chat-message-time {
    font-size: 11px;
    opacity: 0.7;
    text-align: right;
}

.chat-input-area {
    padding: 16px 24px;
    border-top: 1px solid #e9ecef;
    background: white;
}

.chat-input-form {
    width: 100%;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 24px;
}

.chat-input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 14px;
    padding: 8px 0;
    outline: none;
}

.chat-send-btn {
    background: #007BFF;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease;
}

.chat-send-btn:hover {
    background: #0056b3;
}

.empty-chat,
.empty-chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6C757D;
    text-align: center;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #6C757D;
}

/* Chat Info */
.chat-info {
    width: 300px;
    border-left: 1px solid #e9ecef;
    background: white;
    padding: 24px;
    display: flex;
    flex-direction: column;
}

.chat-info-header {
    text-align: center;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid #e9ecef;
}

.chat-info-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6C757D;
    margin: 0 auto 16px;
}

.chat-info-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #071D3A;
    margin: 0 0 8px;
}

.chat-info-role {
    font-size: 14px;
    color: #007BFF;
    font-weight: 600;
}

.chat-info-section {
    margin-bottom: 24px;
}

.chat-info-section-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #6C757D;
    margin: 0 0 16px;
    letter-spacing: 0.5px;
}

.chat-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    color: #071D3A;
    font-size: 14px;
}

.chat-info-empty {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #6C757D;
}

/* Modal Medium */
.modal-content.modal-medium {
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.modal-close-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    color: #6C757D;
}

.new-conversation-form {
    margin-top: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #071D3A;
    margin-bottom: 8px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}
</style>

<script>
function openNewConversationModal() {
    document.getElementById('newConversationModal').style.display = 'flex';
}

function closeNewConversationModal() {
    document.getElementById('newConversationModal').style.display = 'none';
}

// Scroll to bottom of chat on load
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
