<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Mensaje.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/LogActividad.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminMensajesController {
    public static function index() {
        require_role('admin');
        $userId = $_SESSION['usuario_id'];
        
        $filters = [
            'rol' => input('rol', ''),
            'busqueda' => input('busqueda', '')
        ];

        $conversaciones = Mensaje::getAllConversations($userId, $filters);
        
        // Get allowed recipients for new conversation
        $allowedRecipients = self::getAllowedRecipients();

        $conversacionSeleccionada = null;
        $mensajesSeleccionados = [];
        $participantesSeleccionados = [];
        $otroParticipante = null;
        
        if (isset($_GET['conversacion_id'])) {
            $conversacionId = intval($_GET['conversacion_id']);
            
            // Check if user is participant
            $conversacion = Mensaje::getConversacionById($conversacionId, $userId);
            if ($conversacion) {
                $participantesSeleccionados = Mensaje::getParticipantesByConversacionId($conversacionId);
                $mensajesSeleccionados = Mensaje::getMensajesByConversacionId($conversacionId);
                $conversacionSeleccionada = $conversacion;
                
                // Mark as read
                Mensaje::marcarComoLeido($conversacionId, $userId);
                
                // Get other participant
                foreach ($participantesSeleccionados as $p) {
                    if ($p['id'] != $userId) {
                        $otroParticipante = $p;
                        break;
                    }
                }
            }
        }

        return [
            'conversaciones' => $conversaciones,
            'allowedRecipients' => $allowedRecipients,
            'filters' => $filters,
            'conversacionSeleccionada' => $conversacionSeleccionada,
            'mensajesSeleccionados' => $mensajesSeleccionados,
            'participantesSeleccionados' => $participantesSeleccionados,
            'otroParticipante' => $otroParticipante
        ];
    }
    
    public static function handlePost() {
        require_role('admin');
        $userId = $_SESSION['usuario_id'];
        $action = input('action', '');

        if (!verify_csrf_token(input('csrf_token', ''))) {
            flash('errors', ['Token de seguridad inválido. Recargue la página e intente nuevamente.']);
            $queryParams = ['page' => 'admin/mensajes'];
            if (isset($_GET['conversacion_id'])) {
                $queryParams['conversacion_id'] = $_GET['conversacion_id'];
            }
            redirect('index.php?' . http_build_query($queryParams));
            return;
        }

        switch ($action) {
            case 'enviar_mensaje':
                self::enviarMensaje($userId);
                break;
            case 'nueva_conversacion':
                self::nuevaConversacion($userId);
                break;
        }
    }
    
    private static function enviarMensaje($userId) {
        $conversacionId = intval(input('conversacion_id', 0));
        $contenido = trim(input('contenido', ''));
        
        if ($conversacionId && $contenido) {
            // Verify user is participant
            $conversacion = Mensaje::getConversacionById($conversacionId, $userId);
            if ($conversacion) {
                Mensaje::enviarMensaje($conversacionId, $userId, $contenido);
                // No se guarda el contenido del mensaje en la auditoría (es
                // comunicación institucional privada) — solo metadata.
                LogActividad::registrar(
                    $userId,
                    'ENVIAR_MENSAJE',
                    "Envió un mensaje en la conversación #$conversacionId",
                    'mensajes',
                    $conversacionId,
                    null,
                    null
                );
            }
        }
        
        // Redirect back to conversation
        $queryParams = ['page' => 'admin/mensajes'];
        if (isset($_GET['conversacion_id'])) {
            $queryParams['conversacion_id'] = $_GET['conversacion_id'];
        }
        
        redirect('index.php?' . http_build_query($queryParams));
    }
    
    private static function nuevaConversacion($userId) {
        $destinatarioId = intval(input('destinatario_id', 0));
        $contenido = trim(input('contenido', ''));
        
        if ($destinatarioId && $contenido) {
            // Verify recipient is allowed
            $allowed = self::getAllowedRecipients();
            $isAllowed = false;
            foreach ($allowed as $rol => $users) {
                foreach ($users as $user) {
                    if ($user['id'] == $destinatarioId) {
                        $isAllowed = true;
                        break 2;
                    }
                }
            }
            
            if ($isAllowed) {
                $conversacionId = Mensaje::crearConversacion([$userId, $destinatarioId]);
                Mensaje::enviarMensaje($conversacionId, $userId, $contenido);
                LogActividad::registrar(
                    $userId,
                    'CREAR_CONVERSACION',
                    "Inició una conversación con el usuario #$destinatarioId",
                    'conversaciones',
                    (int) $conversacionId,
                    null,
                    null
                );

                redirect('index.php?page=admin/mensajes&conversacion_id=' . $conversacionId);
            }
        }
        
        redirect('index.php?page=admin/mensajes');
    }

    private static function getAllowedRecipients() {
        $db = Database::getConnection();
        $roles = ['admin', 'directivo', 'preceptor'];
        
        $result = [];
        foreach ($roles as $rol) {
            $stmt = $db->prepare("SELECT id, nombre, apellido, rol, email FROM usuarios WHERE rol = ? AND estado = 'activo' ORDER BY apellido, nombre");
            $stmt->execute([$rol]);
            $result[$rol] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $result;
    }
}
