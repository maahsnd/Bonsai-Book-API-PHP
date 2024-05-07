<?php

$routes = [
    '/add-bonsai' => [
        'controller' => 'BonsaiController',
        'method' => 'addBonsai'
    ]
];

// Parse the request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle CORS preflight requests
if ($requestMethod == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header('Content-Type: application/json');
    http_response_code(200);
    exit();
}

// Route the request
if (array_key_exists($requestUri, $routes) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $route = $routes[$requestUri];

    require $route['controller'] . '.php';
    $controller = new $route['controller']();
    $method = $route['method'];

    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        http_response_code(405);
        echo json_encode(["error" => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => 'Not Found']);
}
