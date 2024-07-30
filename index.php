<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';
use App\Database\DBConnection;
use App\Functions\BantenFunctions;

$db = new DBConnection();
$conn = $db->conn;
$banten = BantenFunctions::getAllBanten($conn);

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
}
?>

<h2 class="mb-4">Daftar Produk</h2>
<table id="bantenTable" class="table table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Jumlah</th>
            <th>Minimal Grosir</th>
            <th>Harga Grosir</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $banten->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td>Rp.<?php echo $row['price']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['min_grosir']; ?></td>
            <td>Rp.<?php echo $row['grosir_price']; ?></td>
            <td> <?php if (!empty($row['image']) && file_exists('images/' . $row['image'])) { ?>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="100">
                <?php } else { ?>
                <img src="images/default.png" alt="Default Image" width="100">
                <?php } ?>
            </td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                <a href="#" class="btn btn-danger" onclick="showDeleteModal(<?php echo $row['id']; ?>)">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="confirmDeleteButton" href="#" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function showDeleteModal(id) {
    var deleteButton = document.getElementById('confirmDeleteButton');
    deleteButton.href = 'delete.php?id=' + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
$(document).ready(function() {
    $('#bantenTable').DataTable();

    var toastEl = document.getElementById('toast');
    if (toastEl) {
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>