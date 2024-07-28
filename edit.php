<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;

$id = $_GET['id'];
$sql = "SELECT * FROM banten WHERE id=$id";
$result = $conn->query($sql);
$banten = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $image = $banten['image'];
    }

    if (BantenFunctions::editBanten($conn, $id, $name, $price, $description, $quantity, $image)) {
        $_SESSION['message'] = "Banten berhasil diupdate";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['message'] = "Gagal mengupdate banten";
    }
}
?>

<h2 class="mb-4">Edit Banten</h2>
<form action="edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $banten['name']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $banten['price']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description" name="description"><?php echo $banten['description']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $banten['quantity']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar</label>
        <input type="file" class="form-control" id="image" name="image">
        <img src="images/<?php echo $banten['image']; ?>" alt="<?php echo $banten['name']; ?>" width="100">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>

<?php include 'includes/footer.php'; ?>
