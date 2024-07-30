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
        $stmt = $conn->prepare("INSERT INTO banten (name, price, description, quantity, min_grosir, grosir_price, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsiids", $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image);
        if ($stmt->execute()) {
            self::logHistory($conn, $conn->insert_id, 'add', null, json_encode(compact('name', 'price', 'description', 'quantity', 'min_grosir', 'grosir_price', 'image')), 'Banten berhasil ditambahkan');
            return true;
        } else {
            return false;
        }
    }

    public static function editBanten($conn, $id, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image, $edit_reason) {
        $stmt = $conn->prepare("SELECT * FROM banten WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prev_data = $result->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE banten SET name = ?, price = ?, description = ?, quantity = ?, min_grosir = ?, grosir_price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sdsiidsi", $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image, $id);
        if ($stmt->execute()) {
            $new_data = compact('name', 'price', 'description', 'quantity', 'min_grosir', 'grosir_price', 'image');
            $changes = self::getChangedFields($prev_data, $new_data);
            self::logHistory($conn, $id, 'edit', json_encode($prev_data), json_encode($new_data), $edit_reason);
            return true;
        } else {
            return false;
        }
    }

    public static function deleteBanten($conn, $id, $delete_reason) {
        $stmt = $conn->prepare("SELECT * FROM banten WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prev_data = $result->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM banten WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            self::logHistory($conn, $id, 'delete', json_encode($prev_data), null, $delete_reason);
            return true;
        } else {
            return false;
        }
    }

    public static function logHistory($conn, $banten_id, $action, $prev_data, $new_data, $description, $status = 'success') {
        $stmt = $conn->prepare("INSERT INTO history (banten_id, action, description, prev_data, new_data, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $banten_id, $action, $description, $prev_data, $new_data, $status);
        $stmt->execute();
        $stmt->close();
    }

    public static function getChangedFields($prev_data, $new_data) {
        $changes = [];

        if ($prev_data && $new_data) {
            foreach ($new_data as $field => $new_value) {
                if (isset($prev_data[$field]) && $prev_data[$field] !== $new_value) {
                    $changes[$field] = [
                        'prev' => $prev_data[$field],
                        'new' => $new_value
                    ];
                }
            }
        }

        return $changes;
    }
}

?>