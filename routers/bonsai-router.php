<?php

require_once 'database/database.php';
require_once 'controllers/bonsai-controller.php';


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
    '/bonsai/add' => [
        'controller' => 'BonsaiController',
        'method' => 'addBonsai'
    ],
    '/bonsai/get' => [
        'controller' => 'BonsaiController',
        'method' => 'searchBonsai'
    ],
    '/bonsai/get-one' => [
        'controller' => 'BonsaiController',
        'method' => 'fetchOneBonsai'
    ],
    '/bonsai/update' => [
        'controller' => 'BonsaiController',
        'method' => 'updateBonsai'
    ],
    '/bonsai/delete' => [
        'controller' => 'BonsaiController',
        'method' => 'deleteBonsai'
    ]
];

// Parse the request
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle requests for specific bonsais
$id = null;
$searchTerms = null;

if (strpos($_SERVER['REQUEST_URI'], '/bonsai/get?') === 0) {
    $requestUri = '/bonsai/get';  // Set the base URI
    if (!empty($_GET)) $searchTerms = $_GET; //Copy terms if present
}

$bonsaiIdRegex = '/^\/bonsai\/get-one\/(\d+)$/';
// Regular expression to detect URIs ending with numerical ID
if (preg_match($bonsaiIdRegex, $requestUri, $matches)) {
    $requestUri = '/bonsai/get-one';  // Reset the URI
    $id = $matches[1];                // Capture the numeric ID
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
