<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $id;
    public $full_name;
    public $passport_number;
    public $phone_number;
    public $email;

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
        $query = "INSERT INTO " . $this->table_name . " (full_name, passport_number, phone_number, email) 
                  VALUES (:full_name, :passport_number, :phone_number, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':passport_number', $this->passport_number);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':email', $this->email);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  full_name = COALESCE(:full_name, full_name),
                  passport_number = COALESCE(:passport_number, passport_number),
                  phone_number = COALESCE(:phone_number, phone_number),
                  email = COALESCE(:email, email)
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':passport_number', $this->passport_number);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':email', $this->email);
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
