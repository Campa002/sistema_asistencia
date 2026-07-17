<?php
require_once __DIR__ . '/../config/database.php';

class Curso {
    public static function getAll($filters = []) {
        $db = Database::getConnection();
        $sql = "SELECT c.*, e.nombre AS especialidad_nombre 
                FROM cursos c 
                JOIN especialidades e ON c.especialidad_id = e.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['anio'])) {
            $sql .= " AND c.anio = ?";
            $params[] = $filters['anio'];
        }

        if (!empty($filters['turno'])) {
            $sql .= " AND c.turno = ?";
            $params[] = $filters['turno'];
        }

        if (!empty($filters['especialidad_id'])) {
            $sql .= " AND c.especialidad_id = ?";
            $params[] = $filters['especialidad_id'];
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND c.estado = ?";
            $params[] = $filters['estado'];
        }

        if (!empty($filters['busqueda'])) {
            $search = "%" . $filters['busqueda'] . "%";
            $sql .= " AND (c.aula LIKE ? OR e.nombre LIKE ? OR CONCAT(c.anio, '° ', c.division) LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY c.anio ASC, c.division ASC, c.turno ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.*, e.nombre AS especialidad_nombre 
                              FROM cursos c 
                              JOIN especialidades e ON c.especialidad_id = e.id 
                              WHERE c.id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getEspecialidades() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM especialidades WHERE activo = 1 ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO cursos (anio, division, turno, especialidad_id, aula, ciclo_lectivo, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['anio'],
            $data['division'],
            $data['turno'],
            $data['especialidad_id'],
            $data['aula'] ?? null,
            $data['ciclo_lectivo'],
            $data['estado'] ?? 'activo'
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $fields = [];
        $params = [];
        
        if (isset($data['anio'])) { $fields[] = "anio = ?"; $params[] = $data['anio']; }
        if (isset($data['division'])) { $fields[] = "division = ?"; $params[] = $data['division']; }
        if (isset($data['turno'])) { $fields[] = "turno = ?"; $params[] = $data['turno']; }
        if (isset($data['especialidad_id'])) { $fields[] = "especialidad_id = ?"; $params[] = $data['especialidad_id']; }
        if (isset($data['aula'])) { $fields[] = "aula = ?"; $params[] = $data['aula']; }
        if (isset($data['ciclo_lectivo'])) { $fields[] = "ciclo_lectivo = ?"; $params[] = $data['ciclo_lectivo']; }
        if (isset($data['estado'])) { $fields[] = "estado = ?"; $params[] = $data['estado']; }
        
        if (empty($fields)) return false;
        
        $params[] = $id;
        $sql = "UPDATE cursos SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function countAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM cursos");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countByEstado($estado) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM cursos WHERE estado = ?");
        $stmt->execute([$estado]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function getAlumnosByCursoId($curso_id, $ciclo_lectivo = null) {
        $db = Database::getConnection();
        if ($ciclo_lectivo === null) {
            $ciclo_lectivo = date('Y');
        }
        $sql = "SELECT u.*, ac.estado AS matricula_estado, ac.fecha_matricula
                FROM alumno_cursos ac
                JOIN usuarios u ON ac.alumno_id = u.id
                WHERE ac.curso_id = ? AND ac.ciclo_lectivo = ?
                ORDER BY u.apellido, u.nombre";
        $stmt = $db->prepare($sql);
        $stmt->execute([$curso_id, $ciclo_lectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
