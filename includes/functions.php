<?php

namespace App\Functions;
use App\Database\DBConnection;

$db = new DBConnection();
$conn = $db->conn;

class BantenFunctions{
    public static function getAllBanten($conn) {
        $sql = "SELECT * FROM banten";
        $result = $conn->query($sql);
        return $result;
    }
    
    public static function addBanten($conn, $name, $price, $description, $quantity, $image) {
        $sql = "INSERT INTO banten (name, price, description, quantity, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsss", $name, $price, $description, $quantity, $image);
        return $stmt->execute();
    }
    
    public static function editBanten($conn, $id, $name, $price, $description, $quantity, $image) {
        $sql = "UPDATE banten SET name=?, price=?, description=?, quantity=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssi", $name, $price, $description, $quantity, $image, $id);
        return $stmt->execute();
    }
    
    public static function deleteBanten($conn, $id) {
        $sql = "DELETE FROM banten WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>