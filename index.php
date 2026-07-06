<?php

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

$basePath = __DIR__;

/* API ROOT */
if ($uri === '' || $uri === '/') {
    echo json_encode(["message" => "API running"]);
    exit;
}

/* POST */
if ($uri === "/submitMenuItem") {
    require $basePath . "/api/submit_menu_item.php";
    exit;
}

/* 404 */
http_response_code(404);
echo json_encode(["error" => "Route not found"]);
