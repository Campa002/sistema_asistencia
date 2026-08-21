<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Comunicados Globales del Administrador.
 *
 * No existe una tabla "comunicados" separada en la BD real (sql/eest_asistencia.sql).
 * El diseño real usa la tabla "notificaciones", con la columna `tipo` ENUM que
 * incluye 'comunicado' entre sus valores. Este modelo trabaja exclusivamente
 * sobre las filas con tipo = 'comunicado', dejando 'aviso'/'alerta'/'recordatorio'
 * (generados por otras partes del sistema) fuera de este CRUD.
 */
class Comunicado {
    public static $prioridades_validas = ['normal', 'alta', 'urgente'];
    public static $roles_validos = ['admin', 'directivo', 'preceptor', 'alumno', 'padre_tutor'];

    public static function getAll($filters = []) {
        $db = Database::getConnection();
        $sql = "SELECT n.*, u.nombre as creador_nombre, u.apellido as creador_apellido
                FROM notificaciones n
                JOIN usuarios u ON n.creado_por = u.id
                WHERE n.tipo = 'comunicado'";
        $params = [];

        if (($filters['estado'] ?? '') === 'activo') {
            $sql .= " AND n.activo = 1";
        } elseif (($filters['estado'] ?? '') === 'inactivo') {
            $sql .= " AND n.activo = 0";
        }

        if (!empty($filters['prioridad'])) {
            $sql .= " AND n.prioridad = ?";
            $params[] = $filters['prioridad'];
        }

        if (!empty($filters['busqueda'])) {
            $search = '%' . $filters['busqueda'] . '%';
            $sql .= " AND (n.titulo LIKE ? OR n.contenido LIKE ?)";
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY n.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::getConnection();
        // Scopeado a tipo = 'comunicado' a propósito: evita que este CRUD
        // pueda leer/editar una notificación de otro tipo (alerta, aviso,
        // recordatorio) aunque alguien manipule el id por URL/POST.
        $stmt = $db->prepare("SELECT * FROM notificaciones WHERE id = ? AND tipo = 'comunicado' LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO notificaciones
                (institucion_id, creado_por, titulo, contenido, tipo, prioridad, rol_destino, activo, fecha_expiracion)
                VALUES (1, ?, ?, ?, 'comunicado', ?, ?, 1, ?)");
        $stmt->execute([
            $data['creado_por'],
            $data['titulo'],
            $data['contenido'],
            $data['prioridad'],
            $data['rol_destino'],
            $data['fecha_expiracion'] !== '' ? $data['fecha_expiracion'] : null
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['titulo', 'contenido', 'prioridad', 'rol_destino'] as $campo) {
            if (isset($data[$campo])) {
                $fields[] = "$campo = ?";
                $params[] = $data[$campo];
            }
        }
        if (array_key_exists('fecha_expiracion', $data)) {
            $fields[] = "fecha_expiracion = ?";
            $params[] = $data['fecha_expiracion'] !== '' ? $data['fecha_expiracion'] : null;
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE notificaciones SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ? AND tipo = 'comunicado'";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Baja lógica / reactivación. No hay una columna de "estado" con más
     * granularidad (eliminado/archivado) que el booleano `activo` ya
     * existente, así que "Eliminar" y "Desactivar" son la misma operación:
     * activo pasa a 0. Reactivar vuelve a ponerlo en 1.
     */
    public static function toggleActivo($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notificaciones SET activo = NOT activo, updated_at = NOW() WHERE id = ? AND tipo = 'comunicado'");
        return $stmt->execute([$id]);
    }

    public static function countByEstado($activo) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE tipo = 'comunicado' AND activo = ?");
        $stmt->execute([$activo]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM notificaciones WHERE tipo = 'comunicado'");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
