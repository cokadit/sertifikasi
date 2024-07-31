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



// Fetch recent history
$stmt = $conn->prepare("SELECT history.id, banten.name, history.banten_id, history.action, history.description, history.prev_data, history.new_data, history.action_date, history.status 
        FROM history LEFT JOIN banten ON history.banten_id = banten.id ORDER BY action_date DESC");
$stmt->execute();
$history = $stmt->get_result();
$stmt->close();


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
        <?php $i = 1; ?>
        <?php while ($row = $banten->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $i++; ?></td>
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

<h2 class="mb-4 mt-5">History</h2>
<table id="historyTable" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Banten Id</th>
            <th>Action</th>
            <th>Description</th>
            <th>Changed Fields</th>
            <th>Action Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $j = 1; ?>
        <?php while ($row = $history->fetch_assoc()): ?>
        <tr>
            <td><?= $j++ ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['banten_id'] ?></td>
            <td><?= $row['action'] ?></td>
            <td><?= $row['description'] ?></td>
            <td>
                <?php
                    $prev_data = json_decode($row['prev_data'], true);
                    $new_data = json_decode($row['new_data'], true);

                    // print_r($prev_data);
                    // echo "<br>";
                    // print_r($new_data);

                    $changes = BantenFunctions::getChangedFields($prev_data, $new_data);

                    if (!empty($changes)) {
                        foreach ($changes as $field => $change) {
                            echo "<strong>$field:</strong> " . $change['prev'] . " -> " . $change['new'] . "<br>";
                        }
                    } elseif ($row['action'] == 'delete') {
                        echo "Deleted record";
                    } elseif ($row['action'] == 'add') {
                        echo "Added record";
                    }else {
                        echo "No changes made.";
                    }
                ?>
            </td>
            <td><?= $row['action_date'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
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