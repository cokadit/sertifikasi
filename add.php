<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];

    // Melakukan generate nama unik ke gambar
    $image = time() . '_' . basename($image);

    $target_dir = "images/";
    $target_file = $target_dir . $image;

    // Memindahkan file yang diupload ke directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Call the function to add Banten to the database
        if (BantenFunctions::addBanten($conn, $name, $price, $description, $quantity, $image)) {
            $_SESSION['message'] = "Banten berhasil ditambahkan";
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['message'] = "Gagal menambahkan banten";
        }
    } else {
        $_SESSION['message'] = "Gagal mengunggah gambar";
    }
}
?>

<h2 class="mb-4">Tambah Banten</h2>
<form action="add.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" class="form-control" id="price" name="price" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description" name="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="quantity" name="quantity" required>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar</label>
        <input type="file" class="form-control" id="image" name="image" required>
    </div>
    <button type="submit" class="btn btn-primary">Tambah</button>
</form>

<?php include 'includes/footer.php'; ?>