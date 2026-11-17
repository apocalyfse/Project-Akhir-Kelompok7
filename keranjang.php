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

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : array();
$total     = 0;
include 'header.php';
?>

<div class="container">
    <h2>Keranjang Pesanan</h2>
    <?php include 'alert.php'; ?>

    <?php if (empty($keranjang)) { ?>
        <p class="text-muted">Keranjangmu masih kosong.</p>
        <a href="index.php" class="btn btn-utama">Lihat menu</a>
    <?php } else { ?>
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody>
                <?php
                // $keranjang berisi pasangan id_menu => jumlah
                foreach ($keranjang as $id_menu => $jumlah) {
                    $query    = mysqli_query($connect, "SELECT * FROM menu WHERE id=$id_menu");
                    $m        = mysqli_fetch_assoc($query);
                    $subtotal = $m['harga'] * $jumlah;
                    $total   += $subtotal;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['nama_menu']); ?></td>
                        <td><?php echo rupiah($m['harga']); ?></td>
                        <td><?php echo $jumlah; ?></td>
                        <td><?php echo rupiah($subtotal); ?></td>
                        <td><a href="backend/hapus_keranjang.php?id=<?php echo $id_menu; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i></a></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Total</th><th colspan="2"><?php echo rupiah($total); ?></th></tr>
            </tfoot>
        </table>
        <a href="index.php" class="btn btn-secondary">Tambah menu lain</a>
        <a href="checkout.php" class="btn btn-utama">Lanjut ke Checkout</a>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>
