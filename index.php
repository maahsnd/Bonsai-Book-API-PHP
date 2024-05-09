<?php

$requestUri = $_SERVER['REQUEST_URI'];
$BONSAIROUTE = '/bonsai';
$USERROUTE = '/user';

if (strpos($requestUri, $BONSAIROUTE) === 0) {
    require 'routers/bonsai-router.php';
} elseif (strpos($requestUri, $USERROUTE) === 0) {
    require 'routers/user-router.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
}
