<?php
require_once __DIR__ . '/../controllers/PiezaController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        $id ? PiezaController::show($id) : PiezaController::index();
        break;
    case 'POST':
        PiezaController::store();
        break;
    case 'PUT':
        PiezaController::update($id);
        break;
    case 'DELETE':
        PiezaController::destroy($id);
        break;
}