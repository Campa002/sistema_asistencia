<?php
require_once __DIR__ . '/../config/database.php';

class Mensaje {
    public static function getAllConversations($userId, $filters = []) {
        $db = Database::getConnection();
        
        // First, get all conversations where user is participant
        $sql = "
            SELECT 
                c.id,
                c.titulo,
                c.created_at,
                c.updated_at
            FROM conversaciones c
            JOIN conversacion_participantes cp ON c.id = cp.conversacion_id
            WHERE cp.usuario_id = ?
        ";
        $params = [$userId];

        if (!empty($filters['rol'])) {
            // Filter by role of the other participant
            $sql .= " AND EXISTS (
                SELECT 1 FROM conversacion_participantes cp2 
                JOIN usuarios u2 ON cp2.usuario_id = u2.id 
                WHERE cp2.conversacion_id = c.id 
                AND cp2.usuario_id != ? 
                AND u2.rol = ?
            )";
            $params[] = $userId;
            $params[] = $filters['rol'];
        }
        
        if (!empty($filters['busqueda'])) {
            $sql .= " AND (
                c.titulo LIKE ? 
                OR EXISTS (
                    SELECT 1 FROM mensajes m 
                    WHERE m.conversacion_id = c.id 
                    AND m.contenido LIKE ?
                )
            )";
            $searchTerm = "%{$filters['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY c.updated_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Now get additional data for each conversation
        $result = [];
        foreach ($conversaciones as $conv) {
            $participantes = self::getParticipantesByConversacionId($conv['id']);
            $otroParticipante = null;
            foreach ($participantes as $p) {
                if ($p['id'] != $userId) {
                    $otroParticipante = $p;
                    break;
                }
            }
            
            // Get last message
            $lastMsg = self::getUltimoMensajeByConversacionId($conv['id']);
            
            // Get unread count
            $unreadCount = self::getUnreadCountByConversacionId($conv['id'], $userId);

            $result[] = [
                'id' => $conv['id'],
                'titulo' => $conv['titulo'],
                'otroParticipante' => $otroParticipante,
                'ultimo_mensaje' => $lastMsg['contenido'] ?? null,
                'ultimo_mensaje_fecha' => $lastMsg['created_at'] ?? $conv['created_at'],
                'no_leidos' => $unreadCount
            ];
        }

        return $result;
    }

    public static function getParticipantesByConversacionId($conversacionId) {
        $db = Database::getConnection();
        $sql = "
            SELECT u.id, u.nombre, u.apellido, u.rol, u.email, u.estado, cp.ultimo_leido
            FROM conversacion_participantes cp
            JOIN usuarios u ON cp.usuario_id = u.id
            WHERE cp.conversacion_id = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMensajesByConversacionId($conversacionId) {
        $db = Database::getConnection();
        $sql = "
            SELECT m.*, u.nombre as remitente_nombre, u.apellido as remitente_apellido, u.rol as remitente_rol
            FROM mensajes m
            JOIN usuarios u ON m.remitente_id = u.id
            WHERE m.conversacion_id = ? AND m.eliminado = 0
            ORDER BY m.created_at ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getUltimoMensajeByConversacionId($conversacionId) {
        $db = Database::getConnection();
        $sql = "
            SELECT m.contenido, m.created_at
            FROM mensajes m
            WHERE m.conversacion_id = ? AND m.eliminado = 0
            ORDER BY m.created_at DESC
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getUnreadCountByConversacionId($conversacionId, $userId) {
        $db = Database::getConnection();
        $sql = "
            SELECT COUNT(*) as count
            FROM mensajes m
            JOIN conversacion_participantes cp ON m.conversacion_id = cp.conversacion_id
            WHERE m.conversacion_id = ? 
            AND m.remitente_id != ? 
            AND m.eliminado = 0 
            AND (cp.ultimo_leido IS NULL OR m.created_at > cp.ultimo_leido)
            AND cp.usuario_id = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId, $userId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    public static function marcarComoLeido($conversacionId, $userId) {
        $db = Database::getConnection();
        $sql = "
            UPDATE conversacion_participantes 
            SET ultimo_leido = NOW() 
            WHERE conversacion_id = ? AND usuario_id = ?
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$conversacionId, $userId]);
    }
    
    public static function enviarMensaje($conversacionId, $remitenteId, $contenido) {
        $db = Database::getConnection();
        
        // Insert message
        $sql = "
            INSERT INTO mensajes (conversacion_id, remitente_id, contenido, tipo, leido, eliminado, created_at) 
            VALUES (?, ?, ?, 'texto', 0, 0, NOW())
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId, $remitenteId, $contenido]);
        
        // Update conversation updated_at
        $sql2 = "
            UPDATE conversaciones 
            SET updated_at = NOW() 
            WHERE id = ?
        ";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([$conversacionId]);
        
        return true;
    }
    
    public static function crearConversacion($participantes, $titulo = null) {
        $db = Database::getConnection();
        
        // First, check if conversation already exists
        $placeholders = str_repeat('?,', count($participantes) - 1) . '?';
        $sqlCheck = "
            SELECT c.id
            FROM conversaciones c
            JOIN conversacion_participantes cp ON c.id = cp.conversacion_id
            WHERE c.tipo = 'directo'
            AND cp.usuario_id IN ($placeholders)
            GROUP BY c.id
            HAVING COUNT(DISTINCT cp.usuario_id) = ?
            LIMIT 1
        ";
        $paramsCheck = array_merge($participantes, [count($participantes)]);
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute($paramsCheck);
        $existingConv = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($existingConv) {
            return $existingConv['id'];
        }
        
        // Create new conversation
        $sql = "
            INSERT INTO conversaciones (titulo, tipo, created_at, updated_at) 
            VALUES (?, 'directo', NOW(), NOW())
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$titulo]);
        $conversacionId = $db->lastInsertId();
        
        // Add participants
        foreach ($participantes as $userId) {
            $sql2 = "
                INSERT INTO conversacion_participantes (conversacion_id, usuario_id, activo, created_at) 
                VALUES (?, ?, 1, NOW())
            ";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([$conversacionId, $userId]);
        }
        
        return $conversacionId;
    }
    
    public static function getConversacionById($conversacionId, $userId) {
        $db = Database::getConnection();
        
        // Check if user is participant
        $sqlCheck = "
            SELECT 1 FROM conversacion_participantes 
            WHERE conversacion_id = ? AND usuario_id = ?
        ";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([$conversacionId, $userId]);
        
        if (!$stmtCheck->fetch()) {
            return null;
        }
        
        $sql = "SELECT * FROM conversaciones WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$conversacionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
