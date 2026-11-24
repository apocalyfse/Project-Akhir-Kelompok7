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

$id_penjual = $_SESSION['id'];

// isi form: kosong kalau tambah menu, terisi kalau edit (menu.php?edit=id)
$id_edit   = 0;
$nama_menu = "";
$kategori  = "Makanan";
$harga     = "";
$stok      = "";
if (isset($_GET['edit'])) {
    $id    = (int)$_GET['edit'];
    $query = mysqli_query($connect, "SELECT * FROM menu WHERE id=$id AND id_penjual=$id_penjual");
    $data  = mysqli_fetch_assoc($query);
    if ($data) {
        $id_edit   = $data['id'];
        $nama_menu = $data['nama_menu'];
        $kategori  = $data['kategori'];
        $harga     = $data['harga'];
        $stok      = $data['stok'];
    }
}

$query = mysqli_query($connect, "SELECT * FROM menu WHERE id_penjual=$id_penjual ORDER BY kategori, nama_menu");
include 'navbar.php';
?>

<div class="container mt-4">
    <h2>Menu Saya</h2>
    <?php
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if ($message == 'menu_added') {
            echo '<div class="alert alert-success">Menu berhasil ditambahkan.</div>';
        } elseif ($message == 'menu_edited') {
            echo '<div class="alert alert-success">Menu berhasil diubah.</div>';
        } elseif ($message == 'menu_deleted') {
            echo '<div class="alert alert-primary">Menu berhasil dihapus.</div>';
        }
    }
    ?>

    <div class="card card-body mb-3">
        <h5><?php if ($id_edit == 0) { echo 'Tambah Menu'; } else { echo 'Edit Menu'; } ?></h5>
        <form method="POST" action="backend/process_menu.php" enctype="multipart/form-data" class="row g-2">
            <input type="hidden" name="id" value="<?php echo $id_edit; ?>">
            <div class="col-md-3"><input type="text" name="nama_menu" class="form-control" placeholder="Nama menu" value="<?php echo htmlspecialchars($nama_menu); ?>" required></div>
            <div class="col-md-2">
                <select name="kategori" class="form-select">
                    <?php foreach ($daftar_kategori as $k) { ?>
                        <option <?php if ($kategori == $k) echo 'selected'; ?>><?php echo $k; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2"><input type="number" name="harga" min="0" class="form-control" placeholder="Harga" value="<?php echo $harga; ?>" required></div>
            <div class="col-md-1"><input type="number" name="stok" min="0" class="form-control" placeholder="Stok" value="<?php echo $stok; ?>" required></div>
            <div class="col-md-2"><input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-utama w-100">Simpan</button>
                <?php if ($id_edit != 0) { ?><a href="menu.php" class="btn btn-secondary w-100 mt-1">Batal</a><?php } ?>
            </div>
        </form>
        <?php if ($id_edit != 0) { ?><small class="text-muted mt-2">Foto kosongkan kalau tidak diganti.</small><?php } ?>
    </div>

    <table class="table align-middle">
        <tr><th>Foto</th><th>Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        <?php while ($m = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td>
                    <?php if ($m['foto'] != '' && file_exists('uploads/' . $m['foto'])) { ?>
                        <img src="uploads/<?php echo $m['foto']; ?>" width="60" height="45" style="object-fit:cover;border-radius:8px">
                    <?php } else { ?>
                        <i class="bi bi-image text-muted fs-3"></i>
                    <?php } ?>
                </td>
                <td><?php echo htmlspecialchars($m['nama_menu']); ?></td>
                <td><?php echo $m['kategori']; ?></td>
                <td>Rp <?php echo number_format($m['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $m['stok']; ?></td>
                <td>
                    <a href="menu.php?edit=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-utama">Edit</a>
                    <a href="backend/delete_menu.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Hapus menu ini?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>

</html>
