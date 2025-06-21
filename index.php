<?php
header("Access-Control-Allow-Origin: *"); // запросы с любого домена
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // разрешенные заголовки
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
header("Content-Type: application/json");

// URI запрос
$requestUri = trim($_SERVER['REQUEST_URI'], '/');

// доступные маршруты API
switch ($requestUri) {
    case 'api/customers':
        require_once 'controllers/CustomerController.php';
        break;
    case 'api/cars':
        require_once 'controllers/CarController.php';
        break;
    case 'api/agreements':
        require_once 'controllers/AgreementController.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        exit;
}
