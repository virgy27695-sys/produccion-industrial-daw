<?php
require_once __DIR__ . '/../models/ClienteModel.php';

class ClienteController {

    public static function index() {
        $clientes = ClienteModel::getAll();
        echo json_encode($clientes);
    }

    public static function show($id) {
        $cliente = ClienteModel::getById($id);
        if ($cliente) echo json_encode($cliente);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Cliente no encontrado"]);
        }
    }

    public static function store() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nombre']) || trim($data['nombre']) === '') {
            http_response_code(400);
            echo json_encode(["error" => "El nombre es obligatorio"]);
            return;
        }

        $ok = ClienteModel::create($data['nombre']);
        if ($ok) {
            http_response_code(201);
            echo json_encode(["message" => "Cliente creado"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear cliente"]);
        }
    }

    public static function update($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Falta id"]);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nombre']) || trim($data['nombre']) === '') {
            http_response_code(400);
            echo json_encode(["error" => "El nombre es obligatorio"]);
            return;
        }

        $ok = ClienteModel::update($id, $data['nombre']);
        if ($ok) echo json_encode(["message" => "Cliente actualizado"]);
        else {
            http_response_code(400);
            echo json_encode(["error" => "Error al actualizar cliente"]);
        }
    }

    public static function destroy($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Falta id"]);
            return;
        }

        $ok = ClienteModel::delete($id);
        if ($ok) echo json_encode(["message" => "Cliente eliminado"]);
        else {
            http_response_code(400);
            echo json_encode(["error" => "Error al eliminar cliente"]);
        }
    }
}