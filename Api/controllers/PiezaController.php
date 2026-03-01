<?php
require_once __DIR__ . '/../models/PiezaModel.php';

class PiezaController {

    public static function index() {
        echo json_encode(PiezaModel::getAll());
    }

    public static function show($id) {
        $pieza = PiezaModel::getById($id);
        if ($pieza) {
            echo json_encode($pieza);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Pieza no encontrada']);
        }
    }

    public static function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        PiezaModel::create($data);
        http_response_code(201);
        echo json_encode(['mensaje' => 'Pieza creada']);
    }

    public static function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        PiezaModel::update($id, $data);
        echo json_encode(['mensaje' => 'Pieza actualizada']);
    }

    public static function destroy($id) {
        PiezaModel::delete($id);
        echo json_encode(['mensaje' => 'Pieza eliminada']);
    }
}