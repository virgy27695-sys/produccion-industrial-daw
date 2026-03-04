<?php
require_once __DIR__ . '/../config/db.php';

class ClienteModel {

    public static function getAll() {
        global $pdo;
        return $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll();
    }

    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, nombre FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($nombre) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public static function update($id, $nombre) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE clientes SET nombre = ? WHERE id = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}