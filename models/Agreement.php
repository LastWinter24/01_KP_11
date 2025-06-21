<?php
class Agreement {
    private $conn;
    private $table_name = "agreements";

    public $id;
    public $pts;
    public $insurance_type;
    public $duration;
    public $people_count;
    public $people_names;
    public $customer_id;
    public $car_id;

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

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
            (pts, insurance_type, duration, people_count, people_names, customer_id, car_id) 
            VALUES (:pts, :insurance_type, :duration, :people_count, :people_names, :customer_id, :car_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pts', $this->pts);
        $stmt->bindParam(':insurance_type', $this->insurance_type);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':people_count', $this->people_count);
        $stmt->bindParam(':people_names', $this->people_names);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':car_id', $this->car_id);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  pts = COALESCE(:pts, pts),
                  insurance_type = COALESCE(:insurance_type, insurance_type),
                  duration = COALESCE(:duration, duration),
                  people_count = COALESCE(:people_count, people_count),
                  people_names = COALESCE(:people_names, people_names),
                  customer_id = COALESCE(:customer_id, customer_id),
                  car_id = COALESCE(:car_id, car_id)
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':pts', $this->pts);
        $stmt->bindParam(':insurance_type', $this->insurance_type);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':people_count', $this->people_count);
        $stmt->bindParam(':people_names', $this->people_names);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':car_id', $this->car_id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}