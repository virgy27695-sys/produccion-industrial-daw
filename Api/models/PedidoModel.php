<?php
require_once __DIR__ . '/../config/db.php';

class PedidoModel {

    public static function getAll() {
        global $pdo;
        $sql = "
            SELECT p.id, p.programa_id, p.fecha_pedido, p.estado
            FROM pedidos p
            ORDER BY p.id DESC
        ";
        return $pdo->query($sql)->fetchAll();
    }

    public static function getByIdWithDetails($id) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT id, programa_id, fecha_pedido, estado FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch();

        if (!$pedido) return null;

        $stmt2 = $pdo->prepare("
            SELECT dp.id, dp.pieza_id, dp.cantidad,
                   pi.codigo, pi.denominacion
            FROM detalle_pedidos dp
            JOIN piezas pi ON pi.id = dp.pieza_id
            WHERE dp.pedido_id = ?
        ");
        $stmt2->execute([$id]);
        $pedido['items'] = $stmt2->fetchAll();

        return $pedido;
    }

    public static function createPedidoWithItems($programa_id, $items) {
        global $pdo;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO pedidos (programa_id) VALUES (?)");
            $stmt->execute([$programa_id]);
            $pedidoId = (int)$pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, pieza_id, cantidad) VALUES (?,?,?)");

            foreach ($items as $it) {
                $pieza_id = $it['pieza_id'] ?? null;
                $cantidad = $it['cantidad'] ?? null;

                if (!$pieza_id || !$cantidad || (int)$cantidad <= 0) {
                    $pdo->rollBack();
                    return false;
                }

                $stmtItem->execute([$pedidoId, $pieza_id, $cantidad]);
            }

            $pdo->commit();
            return $pedidoId;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
}