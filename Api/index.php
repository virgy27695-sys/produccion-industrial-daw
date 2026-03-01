<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Capturamos la URL solicitada
$request = $_SERVER['REQUEST_URI'];
$method  = $_SERVER['REQUEST_METHOD'];

// Quitamos parámetros GET
$request = explode('?', $request)[0];


$basePath = '/Proyecto/Api';
$route = str_replace($basePath, '', $request);

// Routing
switch ($route) {

    case '/piezas':
        require_once 'routes/piezas.php';
        break;

    case '/clientes':
        require_once 'routes/clientes.php';
        break;

    case '/pedidos':
        require_once 'routes/pedidos.php';
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Ruta no encontrada'
        ]);
}