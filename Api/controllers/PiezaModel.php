<?php
require_once __DIR__ . '/../config/database.php';

class PiezaModel {

    public static function getAll() {
        $db = Database::connect();
        $sql = "SELECT p.*, f.nombre AS familia
                FROM piezas p
                LEFT JOIN familias f ON p.familia_id = f.id";
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
        $stmt = $db->prepare(
            "INSERT INTO piezas (familia_id, codigo, denominacion)
             VALUES (?, ?, ?)"
        );
        return $stmt->execute([
            $data['familia_id'],
            $data['codigo'],
            $data['denominacion']
        ]);
    }

    public static function update($id, $data) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE piezas SET familia_id=?, codigo=?, denominacion=? WHERE id=?"
        );
        return $stmt->execute([
            $data['familia_id'],
            $data['codigo'],
            $data['denominacion'],
            $id
        ]);
    }

    public static function delete($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM piezas WHERE id=?");
        return $stmt->execute([$id]);
    }
}