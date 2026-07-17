<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    public static function getAll($filters = []) {
        $db = Database::getConnection();
        $sql = "SELECT * FROM usuarios WHERE 1=1";
        $params = [];

        if (!empty($filters['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filters['rol'];
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filters['estado'];
        }

        if (!empty($filters['busqueda'])) {
            $search = "%{$filters['busqueda']}%";
            $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR email LIKE ? OR dni LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO usuarios (
                    nombre, apellido, dni, email, telefono, rol, estado, password_hash
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['dni'],
            $data['email'],
            $data['telefono'] ?? null,
            $data['rol'],
            $data['estado'],
            $passwordHash
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        
        $fields = [];
        $params = [];
        
        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $params[] = $data['nombre'];
        }
        if (isset($data['apellido'])) {
            $fields[] = "apellido = ?";
            $params[] = $data['apellido'];
        }
        if (isset($data['dni'])) {
            $fields[] = "dni = ?";
            $params[] = $data['dni'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        if (isset($data['telefono'])) {
            $fields[] = "telefono = ?";
            $params[] = $data['telefono'];
        }
        if (isset($data['rol'])) {
            $fields[] = "rol = ?";
            $params[] = $data['rol'];
        }
        if (isset($data['estado'])) {
            $fields[] = "estado = ?";
            $params[] = $data['estado'];
        }
        if (isset($data['password'])) {
            $fields[] = "password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function countByRol($rol) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = ?");
        $stmt->execute([$rol]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function countByEstado($estado) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE estado = ?");
        $stmt->execute([$estado]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
