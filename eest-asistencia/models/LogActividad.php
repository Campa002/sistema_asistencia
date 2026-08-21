<?php
require_once __DIR__ . '/../config/database.php';

class LogActividad {
    public static function getAll($filters = [], $page_num = 1, $per_page = 20) {
        $db = Database::getConnection();
        $sql = "SELECT l.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.rol as usuario_rol
                FROM log_actividad l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                WHERE 1=1";
        $total_sql = "SELECT COUNT(*) as total FROM log_actividad l WHERE 1=1";
        $params = [];
        $total_params = [];

        if (!empty($filters['accion'])) {
            $sql .= " AND l.accion = ?";
            $total_sql .= " AND l.accion = ?";
            $params[] = $filters['accion'];
            $total_params[] = $filters['accion'];
        }
        if (!empty($filters['usuario_id'])) {
            $sql .= " AND l.usuario_id = ?";
            $total_sql .= " AND l.usuario_id = ?";
            $params[] = $filters['usuario_id'];
            $total_params[] = $filters['usuario_id'];
        }
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND l.created_at >= ?";
            $total_sql .= " AND l.created_at >= ?";
            $params[] = $filters['fecha_desde'] . ' 00:00:00';
            $total_params[] = $filters['fecha_desde'] . ' 00:00:00';
        }
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND l.created_at <= ?";
            $total_sql .= " AND l.created_at <= ?";
            $params[] = $filters['fecha_hasta'] . ' 23:59:59';
            $total_params[] = $filters['fecha_hasta'] . ' 23:59:59';
        }
        if (!empty($filters['busqueda'])) {
            $search = '%' . $filters['busqueda'] . '%';
            $sql .= " AND (l.descripcion LIKE ? OR l.tabla_afectada LIKE ?)";
            $total_sql .= " AND (l.descripcion LIKE ? OR l.tabla_afectada LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $total_params[] = $search;
            $total_params[] = $search;
        }

        $total_stmt = $db->prepare($total_sql);
        $total_stmt->execute($total_params);
        $total = (int) $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $sql .= " ORDER BY l.created_at DESC";
        $per_page = max(1, (int) $per_page);
        $page_num = max(1, (int) $page_num);
        $offset = ($page_num - 1) * $per_page;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return [
            'registros' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page_num' => $page_num,
            'per_page' => $per_page,
            'total_pages' => max(1, (int) ceil($total / $per_page))
        ];
    }

    public static function getAcciones() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT DISTINCT accion FROM log_actividad ORDER BY accion");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function countHoy() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM log_actividad WHERE DATE(created_at) = CURDATE()");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Registra un evento de auditoría real. Usa exclusivamente las columnas
     * ya existentes en log_actividad (no requiere ningún cambio de esquema).
     *
     * @param int|null $usuarioId Quién hizo la acción (NULL si es del propio sistema).
     * @param string $accion Código corto en mayúsculas, ej. 'CREAR_COMUNICADO'.
     * @param string|null $descripcion Texto legible de lo que pasó.
     * @param string|null $tablaAfectada Nombre real de la tabla afectada.
     * @param int|null $registroId Id del registro afectado en esa tabla.
     * @param mixed $datosAnteriores Se guarda como JSON si no es null.
     * @param mixed $datosNuevos Se guarda como JSON si no es null.
     */
    public static function registrar(
        ?int $usuarioId,
        string $accion,
        ?string $descripcion = null,
        ?string $tablaAfectada = null,
        ?int $registroId = null,
        $datosAnteriores = null,
        $datosNuevos = null
    ): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO log_actividad
                (usuario_id, accion, descripcion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $usuarioId,
            $accion,
            $descripcion,
            $tablaAfectada,
            $registroId,
            $datosAnteriores !== null ? json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE) : null,
            $datosNuevos !== null ? json_encode($datosNuevos, JSON_UNESCAPED_UNICODE) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : null
        ]);
    }
}
