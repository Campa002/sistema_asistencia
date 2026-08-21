<?php
require_once __DIR__ . '/../config/database.php';

class ConfiguracionSistema {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM configuracion_sistema ORDER BY clave");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getValor(string $clave, $default = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT valor FROM configuracion_sistema WHERE clave = ? LIMIT 1");
        $stmt->execute([$clave]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $default;
    }

    public static function actualizarValor(string $clave, string $valor): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE configuracion_sistema SET valor = ?, updated_at = NOW() WHERE clave = ?");
        return $stmt->execute([$valor, $clave]);
    }
}
