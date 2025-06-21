<?php
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Agreement.php';

$database = new Database();
$db = $database->getConnection();

$agreement = new Agreement($db);

header("Access-Control-Allow-Origin: *"); // запросы с любого домена
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // разрешенные заголовки
header("Content-Type: application/json");

// метод запроса
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $agreement->id = $_GET['id'];
            $data = $agreement->readOne();
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Agreement not found"]);
            }
        } else {
            $stmt = $agreement->read();
            $agreements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($agreements);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['pts']) && !empty($data['insurance_type']) && !empty($data['duration']) 
         && isset($data['people_count']) && !empty($data['people_names']) 
         && !empty($data['customer_id']) && !empty($data['car_id'])) {
            $agreement->customer_id = $data['customer_id'];
            $agreement->car_id = $data['car_id'];
            $agreement->pts = $data['pts'];
            $agreement->insurance_type = $data['insurance_type'];
            $agreement->duration = $data['duration'];
            $agreement->people_count = $data['people_count'];
            $agreement->people_names = $data['people_names'];

            if ($agreement->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Agreement created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to create agreement"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['id'])) {
            $agreement->customer_id = $data['customer_id'] ?? null;
            $agreement->car_id = $data['car_id'] ?? null;
            $agreement->id = $data['id'];
            $agreement->pts = $data['pts'] ?? null;
            $agreement->insurance_type = $data['insurance_type'] ?? null;
            $agreement->duration = $data['duration'] ?? null;
            $agreement->people_count = $data['people_count'] ?? null;
            $agreement->people_names = $data['people_names'] ?? null;

            if ($agreement->update()) {
                echo json_encode(["message" => "Agreement updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update agreement"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing agreement ID"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['id'])) {
            $agreement->id = $_GET['id'];
        } elseif (isset($data['id'])) {
            $agreement->id = $data['id'];
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing agreement ID"]);
            exit;
        }

        if ($agreement->delete()) {
            echo json_encode(["message" => "Agreement deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete agreement"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
?>
