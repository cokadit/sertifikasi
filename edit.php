<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;

// Check if the ID is set and fetch the Banten data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM banten WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $banten = $result->fetch_assoc();
    $stmt->close();

    if (!$banten) {
        $_SESSION['message'] = "Banten tidak ditemukan";
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['message'] = "ID Banten tidak diset";
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $min_grosir = isset($_POST['min_grosir']) ? $_POST['min_grosir'] : 0;
    $grosir_price = null;
    $image = $_FILES['image']['name'];
    $edit_reason = $_POST['edit_reason'];

    // Calculate grosir price
    if (isset($_POST['grosir']) && $_POST['grosir'] == 'yes') {
        $grosir_price = $price * 0.95; // 5% discount
    } else {
        $grosir_price = 0;
        $min_grosir = 0;
    }

    // Move the uploaded file to the directory if a new image is uploaded
    if ($image) {
        $image = time() . '_' . basename($image);
        $target_dir = "images/";
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $image = $_POST['prev_image']; // Keep the previous image if no new image is uploaded
    }

    // Update the Banten data
    if (BantenFunctions::editBanten($conn, $id, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image, $edit_reason)) {
        $_SESSION['message'] = "Banten berhasil diperbarui";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['message'] = "Gagal memperbarui banten atau tidak ada perubahan yang dibuat.";
    }
}

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
}
?>

<h2 class="mb-4">Edit Banten</h2>
<form action="edit.php?id=<?= $banten['id'] ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $banten['id'] ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= $banten['name'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" class="form-control" id="price" name="price" value="<?= $banten['price'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description" name="description"><?= $banten['description'] ?></textarea>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= $banten['quantity'] ?>"
            required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="grosir" name="grosir" value="yes"
            <?= $banten['grosir_price'] ? 'checked' : '' ?> onclick="toggleMinGrosir()">
        <label class="form-check-label" for="grosir">Hitung Harga Grosir (Diskon 5%)</label>
    </div>
    <div class="mb-3" id="minGrosirGroup" style="<?= $banten['grosir_price'] ? 'display: block;' : 'display: none;' ?>">
        <label for="min_grosir" class="form-label">Jumlah Minimal Grosir</label>
        <input type="number" class="form-control" id="min_grosir" name="min_grosir"
            value="<?= $banten['min_grosir'] ?>">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar</label>
        <input type="file" class="form-control" id="image" name="image">
        <input type="hidden" name="prev_image" value="<?= $banten['image'] ?>">
    </div>
    <div class="mb-3">
        <label for="edit_reason" class="form-label">Alasan Perubahan</label>
        <textarea class="form-control" id="edit_reason" name="edit_reason" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
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