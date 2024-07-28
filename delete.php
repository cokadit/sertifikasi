<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;

$id = $_GET['id'];

$sql = "SELECT image FROM banten WHERE id=$id";
$result = $conn->query($sql);
$banten = $result->fetch_assoc();
$old_image = $banten['image'];

if (BantenFunctions::deleteBanten($conn, $id)) {
    if (file_exists("images/$old_image")) {
        unlink("images/$old_image");
    }
    $_SESSION['message'] = "Banten berhasil dihapus";
} else {
    $_SESSION['message'] = "Gagal menghapus banten";
}

header('Location: index.php');
exit();
?>
