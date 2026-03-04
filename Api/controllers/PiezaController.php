<?php
require_once __DIR__ . '/../models/PiezaModel.php';

class PiezaController {

    public static function index() {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(PiezaModel::getAll());
    }

    public static function show($id) {
        header("Content-Type: application/json; charset=UTF-8");

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el id"]);
            return;
        }

        $pieza = PiezaModel::getById($id);
        if ($pieza) echo json_encode($pieza);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Pieza no encontrada"]);
        }
    }

    public static function store() {
        header("Content-Type: application/json; charset=UTF-8");

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            return;
        }

        if (PiezaModel::create($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Pieza creada"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear la pieza"]);
        }
    }

    public static function update($id) {
        header("Content-Type: application/json; charset=UTF-8");

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el id"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            return;
        }

        if (PiezaModel::update($id, $data)) echo json_encode(["message" => "Pieza actualizada"]);
        else {
            http_response_code(400);
            echo json_encode(["error" => "Error al actualizar la pieza"]);
        }
    }

    public static function destroy($id) {
        header("Content-Type: application/json; charset=UTF-8");

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el id"]);
            return;
        }

        if (PiezaModel::delete($id)) echo json_encode(["message" => "Pieza eliminada"]);
        else {
            http_response_code(400);
            echo json_encode(["error" => "Error al eliminar la pieza"]);
        }
    }
}