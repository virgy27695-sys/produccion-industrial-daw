<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Quita automáticamente el directorio donde está index.php (sin hardcode)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

$route = $uriPath;

// Si la URL empieza por /loquesea/Api lo quitamos
if ($scriptDir !== '' && str_starts_with($route, $scriptDir)) {
    $route = substr($route, strlen($scriptDir));
}

// Quitar /index.php si viene en la ruta
$route = preg_replace('#^/index\.php#', '', $route);

// Normalizar
$route = '/' . ltrim($route, '/');

switch ($route) {
    case '/piezas':
        require_once __DIR__ . '/routes/piezas.php';
        break;

    case '/clientes':
        require_once __DIR__ . '/routes/clientes.php';
        break;

    case '/pedidos':
        require_once __DIR__ . '/routes/pedidos.php';
        break;

    case '/auth':
    case '/login':
        require_once __DIR__ . '/routes/auth.php';
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Ruta no encontrada",
            "ruta" => $route
        ]);
        break;
}