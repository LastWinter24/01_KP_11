<?php
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Customer.php';

$database = new Database();
$db = $database->getConnection();

$customer = new Customer($db);

header("Access-Control-Allow-Origin: *"); // запросы с любого домена
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // разрешенные заголовки
header("Content-Type: application/json");

// метод запроса
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $customer->id = $_GET['id'];
            $data = $customer->readOne();
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Customer not found"]);
            }
        } else {
            $stmt = $customer->read();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($customers);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['full_name']) && !empty($data['passport_number']) && !empty($data['phone_number']) && !empty($data['email'])) {
            $customer->full_name = $data['full_name'];
            $customer->passport_number = $data['passport_number'];
            $customer->phone_number = $data['phone_number'];
            $customer->email = $data['email'];

            if ($customer->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Customer created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to create customer"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['id'])) {
            $customer->id = $data['id'];
            $customer->full_name = $data['full_name'] ?? null;
            $customer->passport_number = $data['passport_number'] ?? null;
            $customer->phone_number = $data['phone_number'] ?? null;
            $customer->email = $data['email'] ?? null;

            if ($customer->update()) {
                echo json_encode(["message" => "Customer updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update customer"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing customer ID"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['id'])) {
            $customer->id = $_GET['id'];
        } elseif (isset($data['id'])) {
            $customer->id = $data['id'];
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing customer ID"]);
            exit;
        }

        if ($customer->delete()) {
            echo json_encode(["message" => "Customer deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete customer"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
?>
