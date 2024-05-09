<?php

$requestUri = $_SERVER['REQUEST_URI'];
echo $requestUri;
return;

if (strpos($requestUri, '/bonsai') === 0) {
    $requestUri = substr($requestUri, 7);
    require 'bonsai-router.php';
} elseif (strpos($requestUri, '/user') === 0) {
    $requestUri = substr($requestUri, 5);
    require 'user-router.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
}
