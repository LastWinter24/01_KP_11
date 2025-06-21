<?php
class Car {
    private $conn;
    private $table_name = "cars";

    public $id;
    public $brand;
    public $model;
    public $license_plate;
    public $vin_number;
    public $owner_full_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existsByVin($vin_number) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE vin_number = :vin_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vin_number', $vin_number);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (brand, model, license_plate, vin_number, owner_full_name) 
                  VALUES (:brand, :model, :license_plate, :vin_number, :owner_full_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':brand', $this->brand);
        $stmt->bindParam(':model', $this->model);
        $stmt->bindParam(':license_plate', $this->license_plate);
        $stmt->bindParam(':vin_number', $this->vin_number);
        $stmt->bindParam(':owner_full_name', $this->owner_full_name);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  brand = COALESCE(:brand, brand),
                  model = COALESCE(:model, model),
                  license_plate = COALESCE(:license_plate, license_plate),
                  vin_number = COALESCE(:vin_number, vin_number),
                  owner_full_name = COALESCE(:owner_full_name, owner_full_name)
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':brand', $this->brand);
        $stmt->bindParam(':model', $this->model);
        $stmt->bindParam(':license_plate', $this->license_plate);
        $stmt->bindParam(':vin_number', $this->vin_number);
        $stmt->bindParam(':owner_full_name', $this->owner_full_name);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
