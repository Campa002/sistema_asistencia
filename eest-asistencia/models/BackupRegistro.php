<?php
require_once __DIR__ . '/../config/database.php';

class BackupRegistro {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT b.*, u.nombre as creador_nombre, u.apellido as creador_apellido
                             FROM backups_registro b
                             LEFT JOIN usuarios u ON b.creado_por = u.id
                             ORDER BY b.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUltimo() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM backups_registro ORDER BY created_at DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM backups_registro WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(string $archivo, ?int $tamano, string $tipo, ?int $creadoPor, string $estado, ?string $notas = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO backups_registro (archivo, tamano, tipo, creado_por, estado, notas)
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$archivo, $tamano, $tipo, $creadoPor, $estado, $notas]);
        return $db->lastInsertId();
    }
}
