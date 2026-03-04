<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../controllers/ClienteController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        $id ? ClienteController::show($id) : ClienteController::index();
        break;
    case 'POST':
        ClienteController::store();
        break;
    case 'PUT':
        ClienteController::update($id);
        break;
    case 'DELETE':
        ClienteController::destroy($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}