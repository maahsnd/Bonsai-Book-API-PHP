<?php

require_once 'database/database.php';
require_once 'controllers/user-controller.php';

// Parse the request
$requestUri = $_SERVER['REQUEST_URI'];
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

$database = new Database();
$dbConnection = $database->getConnection();

$routes = [
    '/user/add' => [
        'controller' => 'UserController',
        'method' => 'addUser'
    ],
    '/user/get' => [
        'controller' => 'UserController',
        'method' => 'getUser'
    ]
];

// Handle requests for specific user
$id = null;

$userIdRegex = '/^(\/user\/[A-Za-z]+)(\/\d+)$/';
// Regular expression to detect URIs ending with numerical ID
if (preg_match($userIdRegex, $requestUri, $matches)) {
    $requestUri = $matches[1];  // Reset the URI to whatever preceded numerical ID
    $id = substr($matches[2], 1); // Trim the leading slash, capture the numeric ID
}



// Route the request
if (array_key_exists($requestUri, $routes)) {

    $route = $routes[$requestUri];

    $controller = new $route['controller']($dbConnection);
    $method = $route['method'];

    if (method_exists($controller, $method)) {
        if ($id) {
            $controller->$method($id);
        } else {
            $controller->$method();
        }
    } else {
        http_response_code(405);
    }
} else {
    http_response_code(404);
}
