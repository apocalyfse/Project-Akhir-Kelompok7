<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
    header("Location: login.php?message=not_logged_in");
    exit();
}
if ($_SESSION['role'] != 'penjual') {
    header("Location: index.php");
    exit();
}
include 'backend/connect.php';

$id      = (int)$_GET['id'];
$id_stan = $_SESSION['id_stan'];

// Menu harus milik stan penjual yang login
$query = mysqli_query($connect, "SELECT * FROM menu WHERE id=$id AND id_stan=$id_stan");
$m     = mysqli_fetch_assoc($query);
if (!$m) {
    header("Location: menu.php");
    exit();
}
include 'header.php';
?>

<div class="container">
    <h2 class="text-center">Edit Your Menu</h2>
    <div class="card card-body mt-3">
        <form method="POST" action="backend/process_edit_menu.php">
            <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
            <label class="form-label">Nama menu</label>
            <input type="text" name="nama_menu" class="form-control mb-3" value="<?php echo htmlspecialchars($m['nama_menu']); ?>" required>
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select mb-3">
                <option <?php if ($m['kategori'] == 'Makanan') echo 'selected'; ?>>Makanan</option>
                <option <?php if ($m['kategori'] == 'Minuman') echo 'selected'; ?>>Minuman</option>
                <option <?php if ($m['kategori'] == 'Camilan') echo 'selected'; ?>>Camilan</option>
            </select>
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="harga" min="0" class="form-control mb-3" value="<?php echo $m['harga']; ?>" required>
            <label class="form-label">Stok</label>
            <input type="number" name="stok" min="0" class="form-control mb-3" value="<?php echo $m['stok']; ?>" required>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="menu.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
