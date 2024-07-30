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
    $min_grosir = isset($_POST['min_grosir']) ? $_POST['min_grosir'] : 0;
    $grosir_price = null;
    $image = $_FILES['image']['name'];

    // Check if the name already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM banten WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['message'] = "Nama banten sudah ada, silakan gunakan nama lain.";
    } else {
        // Generate unique name for the image
        $image = time() . '_' . basename($image);

        $target_dir = "images/";
        $target_file = $target_dir . $image;

        // Calculate grosir price
        if (isset($_POST['grosir']) && $_POST['grosir'] == 'yes') {
            $grosir_price = $price * 0.95; // 5% discount
        } else {
            $grosir_price = 0;
            $min_grosir = 0;
        }

        // Move the uploaded file to the directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Call the function to add Banten to the database
            if (BantenFunctions::addBanten($conn, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image)) {
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
}

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
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
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="grosir" name="grosir" value="yes"
            onclick="toggleMinGrosir()">
        <label class="form-check-label" for="grosir">Hitung Harga Grosir (Diskon 5%)</label>
    </div>
    <div class="mb-3" id="minGrosirGroup" style="display: none;">
        <label for="quantity" class="form-label">Jumlah Minimal Grosir</label>
        <input type="number" class="form-control" id="min_grosir" name="min_grosir">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar</label>
        <input type="file" class="form-control" id="image" name="image" required>
    </div>
    <button type="submit" class="btn btn-primary">Tambah</button>
</form>

<script>
function toggleMinGrosir() {
    var grosirCheckbox = document.getElementById('grosir');
    var minGrosirGroup = document.getElementById('minGrosirGroup');
    if (grosirCheckbox.checked) {
        minGrosirGroup.style.display = 'block';
    } else {
        minGrosirGroup.style.display = 'none';
    }
}

// Initial call to set the correct visibility
toggleMinGrosir();
</script>
<?php include 'includes/footer.php'; ?>