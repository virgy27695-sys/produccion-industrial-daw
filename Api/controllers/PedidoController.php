<?php
require_once __DIR__ . '/../models/PedidoModel.php';

class PedidoController {

    public static function index() {
        echo json_encode(PedidoModel::getAll());
    }

    public static function show($id) {
        $pedido = PedidoModel::getByIdWithDetails($id);
        if ($pedido) echo json_encode($pedido);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Pedido no encontrado"]);
        }
    }

    // POST /pedidos
    // Body ejemplo:
    // { "programa_id": 1, "items": [ { "pieza_id": 3, "cantidad": 10 }, ... ] }
    public static function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        $programa_id = $data['programa_id'] ?? null;
        $items = $data['items'] ?? [];

        if (!$programa_id || !is_array($items) || count($items) === 0) {
            http_response_code(400);
            echo json_encode(["error" => "programa_id e items son obligatorios"]);
            return;
        }

        $pedidoId = PedidoModel::createPedidoWithItems($programa_id, $items);

        if ($pedidoId) {
            http_response_code(201);
            echo json_encode(["message" => "Pedido creado", "pedido_id" => $pedidoId]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear el pedido"]);
        }
    }
}