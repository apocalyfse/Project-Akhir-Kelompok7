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

$id_stan = $_SESSION['id_stan'];
$menu    = mysqli_query($connect, "SELECT * FROM menu WHERE id_stan=$id_stan ORDER BY kategori, nama_menu");
include 'header.php';
?>

<div class="container">
    <h2>Menu Saya</h2>
    <?php include 'alert.php'; ?>

    <div class="card card-body mb-3">
        <h5>Tambah menu</h5>
        <form method="POST" action="backend/process_add_menu.php" class="row g-2">
            <div class="col-md-4"><input type="text" name="nama_menu" class="form-control" placeholder="Nama menu" required></div>
            <div class="col-md-2">
                <select name="kategori" class="form-select">
                    <option>Makanan</option>
                    <option>Minuman</option>
                    <option>Camilan</option>
                </select>
            </div>
            <div class="col-md-2"><input type="number" name="harga" min="0" class="form-control" placeholder="Harga" required></div>
            <div class="col-md-2"><input type="number" name="stok" min="0" class="form-control" placeholder="Stok" required></div>
            <div class="col-md-2"><button type="submit" class="btn btn-utama w-100">Simpan</button></div>
        </form>
    </div>

    <table class="table align-middle">
        <thead class="table-light">
            <tr><th>Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while ($m = mysqli_fetch_assoc($menu)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['nama_menu']); ?></td>
                    <td><?php echo $m['kategori']; ?></td>
                    <td><?php echo rupiah($m['harga']); ?></td>
                    <td><?php echo $m['stok']; ?></td>
                    <td>
                        <a href="edit_menu.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil-square"></i></a>
                        <a href="backend/delete_menu.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Hapus menu ini?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
