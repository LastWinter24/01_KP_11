<?php
header("Access-Control-Allow-Origin: *"); // запросы с любого домена
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // разрешенные заголовки
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(["message" => "Данные успешно добавлены"]);
    exit();
}
header("Content-Type: application/json");

// запрашиваемый URI
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
$endpoint = trim($request_uri[0], '/'); 

// HTTP-метод
$method = $_SERVER['REQUEST_METHOD'];

// подключение контроллеров
$base_path = dirname(__DIR__) . '/controllers/';

switch ($endpoint) {
    case 'api/customers':
        include $base_path . 'CustomerController.php';
        break;
    case 'api/cars':
        include $base_path . 'CarController.php';
        break;
    case 'api/agreements':
        include $base_path . 'AgreementController.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        exit;
}
?>