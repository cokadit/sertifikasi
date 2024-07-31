<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$banten = BantenFunctions::getBantenById($conn, $id);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $delete_reason = $_POST['delete_reason'];

    if (BantenFunctions::deleteBanten($conn, $id, $delete_reason)) {
        $_SESSION['message'] = "Banten berhasil dihapus";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['message'] = "Gagal menghapus banten";
    }
}


if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
}
?>

<h2 class="mb-4">Hapus Banten</h2>
<form action="delete.php" method="post">
    <input type="hidden" name="id" value="<?= $banten['id'] ?>">
    <div class="mb-3">
        <label for="delete_reason" class="form-label">Alasan Penghapusan</label>
        <textarea class="form-control" id="delete_reason" name="delete_reason" required></textarea>
    </div>
    <button type="submit" class="btn btn-danger">Hapus</button>
</form>
<?php include 'includes/footer.php'; ?>