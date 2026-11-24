
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

$per_kedai = array();

if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
        $id_menu = (int)$id_menu;

        $query = mysqli_query($connect, "SELECT menu.*, users.nama_stan 
            FROM menu JOIN users ON menu.id_penjual = users.id 
            WHERE menu.id = $id_menu");

        $m = mysqli_fetch_assoc($query);

        if ($m) {
            $m['jumlah'] = $jumlah;
            $per_kedai[$m['id_penjual']][] = $m;
        }
    }
}

include 'navbar.php';
?>

<div class="container mt-4">
    <h2>Keranjang Pesanan</h2>
    <p class="text-muted">Pilih menu yang ingin dipesan sekarang.</p>

    <?php
    if (isset($_GET['message'])) {
        if ($_GET['message'] == 'pilih_kosong') {
            echo '<div class="alert alert-warning">Pilih minimal satu menu untuk checkout.</div>';
        } elseif ($_GET['message'] == 'stok_habis') {
            echo '<div class="alert alert-danger">Stok menu tidak mencukupi.</div>';
        } elseif ($_GET['message'] == 'cart_removed') {
            echo '<div class="alert alert-primary">Menu berhasil dihapus dari keranjang.</div>';
        }
    }
    ?>

    <?php if (count($per_kedai) == 0) { ?>
        <p class="text-muted">Keranjangmu masih kosong.</p>
        <a href="index.php" class="btn btn-utama">Lihat Menu</a>
    <?php } ?>

    <?php foreach ($per_kedai as $menu_kedai) { ?>
        <form method="GET" action="checkout.php" class="card shadow-sm mb-3">

            <div class="card-header fw-bold">
                <?php echo htmlspecialchars($menu_kedai[0]['nama_stan']); ?>
            </div>

            <table class="table align-middle mb-0">
                <?php foreach ($menu_kedai as $m) { ?>
                    <tr>
                        <td style="width:40px">
                            <input type="checkbox" name="pilih[]"
                                value="<?php echo $m['id']; ?>"
                                class="form-check-input" checked>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($m['nama_menu']); ?>
                        </td>

                        <td>
                            <?php echo $m['jumlah']; ?> x
                            Rp <?php echo number_format($m['harga'], 0, ',', '.'); ?>
                        </td>

                        <td class="fw-bold">
                            Rp <?php echo number_format($m['harga'] * $m['jumlah'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <a href="backend/hapus_keranjang.php?id=<?php echo $m['id']; ?>"
                                class="btn btn-sm btn-outline-danger">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-utama">
                    Checkout yang Dicentang
                </button>
            </div>

        </form>
    <?php } ?>
</div>

</body>
</html>
