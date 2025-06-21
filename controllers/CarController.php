<?php
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Car.php';

$database = new Database();
$db = $database->getConnection();

$car = new Car($db);

header("Access-Control-Allow-Origin: *"); // запросы с любого домена
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // разрешенные заголовки
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
header("Content-Type: application/json");

// метод запроса
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $car->id = $_GET['id'];
            $data = $car->readOne();
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Car not found"]);
            }
        } else {
            $stmt = $car->read();
            $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($cars);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['brand']) && !empty($data['model']) && !empty($data['license_plate']) 
            && !empty($data['vin_number']) && !empty($data['owner_full_name'])) {
            
            // Проверяем, существует ли VIN
            if ($car->existsByVin($data['vin_number'])) {
                http_response_code(409);
                echo json_encode(["error" => "Car with this VIN already exists"]);
                exit;
            }

            $car->brand = $data['brand'];
            $car->model = $data['model'];
            $car->license_plate = $data['license_plate'];
            $car->vin_number = $data['vin_number'];
            $car->owner_full_name = $data['owner_full_name'];

            try {
                if ($car->create()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Car created successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to create car"]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['id'])) {
            $car->id = $data['id'];
            $car->brand = $data['brand'] ?? null;
            $car->model = $data['model'] ?? null;
            $car->license_plate = $data['license_plate'] ?? null;
            $car->vin_number = $data['vin_number'] ?? null;
            $car->owner_full_name = $data['owner_full_name'] ?? null;

            try {
                if ($car->update()) {
                    echo json_encode(["message" => "Car updated successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to update car"]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing car ID"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['id'])) {
            $car->id = $_GET['id'];
        } elseif (isset($data['id'])) {
            $car->id = $data['id'];
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing car ID"]);
            exit;
        }

        try {
            if ($car->delete()) {
                echo json_encode(["message" => "Car deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to delete car"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
?>
