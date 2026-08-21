<?php
require_once __DIR__ . '/../config/database.php';

class Materia {
    public static function getAll($filters = []) {
        $db = Database::getConnection();
        $sql = "SELECT m.*, e.nombre AS especialidad_nombre
                FROM materias m
                LEFT JOIN especialidades e ON m.especialidad_id = e.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['especialidad_id'])) {
            $sql .= " AND m.especialidad_id = ?";
            $params[] = $filters['especialidad_id'];
        }

        if (!empty($filters['anio_correspondiente'])) {
            $sql .= " AND m.anio_correspondiente = ?";
            $params[] = $filters['anio_correspondiente'];
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= " AND m.activo = ?";
            $params[] = $filters['activo'];
        }

        if (!empty($filters['busqueda'])) {
            $search = "%" . $filters['busqueda'] . "%";
            $sql .= " AND (m.nombre LIKE ? OR m.codigo LIKE ?)";
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY m.nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT m.*, e.nombre AS especialidad_nombre
                              FROM materias m
                              LEFT JOIN especialidades e ON m.especialidad_id = e.id
                              WHERE m.id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO materias (nombre, codigo, especialidad_id, anio_correspondiente, activo)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['codigo'] ?? null,
            $data['especialidad_id'] ?? null,
            $data['anio_correspondiente'] ?? null,
            $data['activo'] ?? 1
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        if (isset($data['nombre'])) { $fields[] = "nombre = ?"; $params[] = $data['nombre']; }
        if (array_key_exists('codigo', $data)) { $fields[] = "codigo = ?"; $params[] = $data['codigo']; }
        if (array_key_exists('especialidad_id', $data)) { $fields[] = "especialidad_id = ?"; $params[] = $data['especialidad_id']; }
        if (array_key_exists('anio_correspondiente', $data)) { $fields[] = "anio_correspondiente = ?"; $params[] = $data['anio_correspondiente']; }
        if (isset($data['activo'])) { $fields[] = "activo = ?"; $params[] = $data['activo']; }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE materias SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function countAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM materias");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countByActivo($activo) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM materias WHERE activo = ?");
        $stmt->execute([$activo]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countByEspecialidad($especialidadId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM materias WHERE especialidad_id = ?");
        $stmt->execute([$especialidadId]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Horario real de un curso (asignaciones_materias), tal como fue
     * cargado desde los Excel oficiales. Solo lectura: no hay edición de
     * horarios desde el Admin, únicamente visualización.
     */
    public static function getAsignaciones($cursoId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT am.*, m.nombre AS materia_nombre,
                                      u.nombre AS preceptor_nombre, u.apellido AS preceptor_apellido
                               FROM asignaciones_materias am
                               JOIN materias m ON m.id = am.materia_id
                               LEFT JOIN usuarios u ON u.id = am.preceptor_id
                               WHERE am.curso_id = ? AND am.activo = 1
                               ORDER BY am.dia_semana ASC, am.hora_inicio ASC");
        $stmt->execute([$cursoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
