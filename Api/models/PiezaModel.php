<?php
require_once __DIR__ . '/../config/db.php';

class PiezaModel {
    public static function getAll() {
        $db = Database::connect();

        $sql = "SELECT p.*, m.nombre AS modelo, c.nombre AS cliente
                FROM piezas p
                INNER JOIN modelos m ON p.modelo_id = m.id
                INNER JOIN clientes c ON m.cliente_id = c.id";

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM piezas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO piezas (codigo, denominacion, modelo_id) VALUES (?, ?, ?)");
        return $stmt->execute([$data['codigo'], $data['denominacion'], $data['modelo_id']]);
    }

    public static function update($id, $data) {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE piezas SET codigo = ?, denominacion = ?, modelo_id = ? WHERE id = ?");
        return $stmt->execute([$data['codigo'], $data['denominacion'], $data['modelo_id'], $id]);
    }

    public static function delete($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM piezas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}