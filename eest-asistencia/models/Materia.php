<?php
require_once __DIR__ . '/../config/database.php';

class Materia {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM materias ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM materias WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
