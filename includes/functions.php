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
    
    public static function addBanten($conn, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image) {

        $sql = "INSERT INTO banten (name, price, description, quantity, min_grosir, grosir_price, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssiis", $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image);
        return $stmt->execute();
    }
    
    public static function editBanten($conn, $id, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image) {
        $sql = "UPDATE banten SET name=?, price=?, description=?, quantity=?, min_grosir=?, grosir_price=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssiisi", $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image, $id);
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