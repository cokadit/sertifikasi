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
    $min_grosir = isset($_POST['min_grosir']) ? $_POST['min_grosir'] : 0;
    $grosir_price = null;
    $image = $_FILES['image']['name'];

    // Check if the name already exists, excluding the current record
    $stmt = $conn->prepare("SELECT COUNT(*) FROM banten WHERE name = ? AND id != ?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['message'] = "Nama banten sudah ada, silakan gunakan nama lain.";
    } else {
        // Generate unique name for the image
        if ($image) {
            $image = time() . '_' . basename($image);
            $target_dir = "images/";
            $target_file = $target_dir . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        } else {
            $image = $banten['image'];
        }

        // Calculate grosir price
        if (isset($_POST['grosir']) && $_POST['grosir'] == 'yes') {
            $grosir_price = $price * 0.95; // 5% discount
        } else {
            $grosir_price = 0;
            $min_grosir = 0;
        }

        if (BantenFunctions::editBanten($conn, $id, $name, $price, $description, $quantity, $min_grosir, $grosir_price, $image)) {
            $_SESSION['message'] = "Banten berhasil diupdate";
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['message'] = "Gagal mengupdate banten";
        }
    }
}

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
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
        <input type="number" class="form-control" id="price" name="price" step="0.01"
            value="<?php echo $banten['price']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description"
            name="description"><?php echo $banten['description']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="quantity" name="quantity"
            value="<?php echo $banten['quantity']; ?>" required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="grosir" name="grosir" value="yes">
        <label class="form-check-label" for="grosir">Hitung Harga Grosir (Diskon 5%)</label>
    </div>
    <div class="mb-3" id="minGrosirGroup">
        <label for="quantity" class="form-label">Jumlah Minimal Grosir</label>
        <input type="number" class="form-control" id="min_grosir" name="min_grosir"
            value="<?php echo $banten['quantity']; ?>">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Gambar</label>
        <input type="file" class="form-control" id="image" name="image">
        <img src="images/<?php echo $banten['image']; ?>" alt="<?php echo $banten['name']; ?>" width="100">
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

// Check if grosir_price is above 0 and set the checkbox and show the input field
window.onload = function() {
    var grosirPrice = <?php echo $banten['grosir_price']; ?>;
    var grosirCheckbox = document.getElementById('grosir');
    var minGrosirGroup = document.getElementById('minGrosirGroup');

    if (grosirPrice > 0) {
        grosirCheckbox.checked = true;
        minGrosirGroup.style.display = 'block';
    } else {
        grosirCheckbox.checked = false;
        minGrosirGroup.style.display = 'none';
    }

    grosirCheckbox.addEventListener('change', toggleMinGrosir);
};

toggleMinGrosir();
</script>
<?php include 'includes/footer.php'; ?>