<?php
class Pieza {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerTodas() {
        $sql = "
        SELECT
            p.codigo,
            p.denominacion,
            m.nombre AS modelo,
            c.nombre AS cliente
        FROM piezas p
        JOIN modelos m ON p.modelo_id = m.id
        JOIN clientes c ON m.cliente_id = c.id
        ORDER BY c.nombre, m.nombre
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}