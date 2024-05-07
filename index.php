<?php

require_once 'database.php';
require_once 'bonsai-controller.php';

$database = new Database();
$dbConnection = $database->getConnection();

$routes = [
    '/add-bonsai' => [
        'controller' => 'BonsaiController',
        'method' => 'addBonsai'
    ],
    '/get-bonsai' => [
        'controller' => 'BonsaiController',
        'method' => 'fetchAllBonsai'
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
if (array_key_exists($requestUri, $routes)) {
    $route = $routes[$requestUri];

    $controller = new $route['controller']($dbConnection);
    $method = $route['method'];

    if (method_exists($controller, $method)) {
        $result = $controller->$method();
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(405);
        echo json_encode(["error" => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => 'Not Found']);
}
