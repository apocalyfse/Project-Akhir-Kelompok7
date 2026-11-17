<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
    header("Location: login.php?message=not_logged_in");
    exit();
}
if ($_SESSION['role'] != 'pelanggan') {
    header("Location: pesanan.php");
    exit();
}
include 'backend/connect.php';

// Cari menu berdasarkan nama menu / nama stan, dan filter kategori
$cari     = isset($_GET['cari']) ? mysqli_real_escape_string($connect, $_GET['cari']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($connect, $_GET['kategori']) : '';

$sql = "SELECT menu.*, stan.nama_stan FROM menu JOIN stan ON menu.id_stan = stan.id
        WHERE (menu.nama_menu LIKE '%$cari%' OR stan.nama_stan LIKE '%$cari%')";
if ($kategori != '') {
    $sql .= " AND menu.kategori='$kategori'";
}
$sql  .= " ORDER BY stan.nama_stan, menu.nama_menu";
$menu  = mysqli_query($connect, $sql);

include 'header.php';
?>

<div class="container">
    <div class="text-center mb-4">
        <h2>Welcome Back <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <p class="text-muted">Mau makan apa hari ini? Pesan dulu, ambil sesuai jam yang kamu pilih.</p>
    </div>
    <?php include 'alert.php'; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="cari" class="form-control" placeholder="Cari menu atau stan..." value="<?php echo htmlspecialchars($cari); ?>">
        </div>
        <div class="col-md-4">
            <select name="kategori" class="form-select">
                <option value="">Semua kategori</option>
                <option value="Makanan" <?php if ($kategori == 'Makanan') echo 'selected'; ?>>Makanan</option>
                <option value="Minuman" <?php if ($kategori == 'Minuman') echo 'selected'; ?>>Minuman</option>
                <option value="Camilan" <?php if ($kategori == 'Camilan') echo 'selected'; ?>>Camilan</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-utama w-100"><i class="bi bi-search"></i> Cari</button>
        </div>
    </form>

    <div class="row">
        <?php while ($m = mysqli_fetch_assoc($menu)) { ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($m['nama_menu']); ?></h5>
                        <p class="text-muted small mb-1"><?php echo htmlspecialchars($m['nama_stan']); ?> &middot; <?php echo $m['kategori']; ?></p>
                        <p class="fw-bold mb-1"><?php echo rupiah($m['harga']); ?></p>
                        <p class="small mb-2">Stok: <?php echo $m['stok']; ?></p>
                        <?php if ($m['stok'] > 0) { ?>
                            <form method="POST" action="backend/tambah_keranjang.php">
                                <input type="hidden" name="id_menu" value="<?php echo $m['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-utama"><i class="bi bi-cart-plus"></i> Tambah ke keranjang</button>
                            </form>
                        <?php } else { ?>
                            <span class="badge bg-secondary">Habis</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
