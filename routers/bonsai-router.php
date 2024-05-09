<?php

require_once 'database/database.php';
require_once 'controllers/bonsai-controller.php';

$database = new Database();
$dbConnection = $database->getConnection();

$routes = [
    '/add' => [
        'controller' => 'BonsaiController',
        'method' => 'addBonsai'
    ],
    '/get' => [
        'controller' => 'BonsaiController',
        'method' => 'searchBonsai'
    ],
    '/get-one' => [
        'controller' => 'BonsaiController',
        'method' => 'fetchOneBonsai'
    ],
    '/update' => [
        'controller' => 'BonsaiController',
        'method' => 'updateBonsai'
    ],
    '/delete' => [
        'controller' => 'BonsaiController',
        'method' => 'deleteBonsai'
    ]
];

// Parse the request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle requests for specific bonsais
$id = null;
$searchTerms = null;

if (strpos($_SERVER['REQUEST_URI'], '/get') === 0) {
    $requestUri = '/get';  // Set the base URI
    if (!empty($_GET)) $searchTerms = $_GET; //Copy terms if present
}

// Regular expression to detect URIs ending with numerical ID
if (preg_match('/^(\/[a-z\-]+)\/(\d+)$/', $requestUri, $matches)) {
    $requestUri = $matches[1]; // Get the base part of the URI without the ID
    $id = $matches[2]; // Capture the numeric ID
}

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
        if ($id || $searchTerms) {
            $id ? $controller->$method($id) : $controller->$method($searchTerms);
        } else {
            $controller->$method;
        }
    } else {
        http_response_code(405);
    }
} else {
    http_response_code(404);
}
