
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

$cari = "";
$kategori = "";

if (isset($_GET['cari'])) {
    $cari = mysqli_real_escape_string($connect, $_GET['cari']);
}

if (isset($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($connect, $_GET['kategori']);
}

$sql = "SELECT menu.*, users.nama_stan FROM menu
        JOIN users ON menu.id_penjual = users.id
        WHERE (menu.nama_menu LIKE '%$cari%' OR users.nama_stan LIKE '%$cari%')";

if ($kategori != "") {
    $sql .= " AND menu.kategori = '$kategori'";
}

$sql .= " ORDER BY users.nama_stan, menu.nama_menu";

$query = mysqli_query($connect, $sql);

include 'navbar.php';
?>

<div class="container mt-4">

    <div class="hero mb-4">
        <p class="mb-1">
            Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </p>

        <h2 class="fw-bold">Pesan dulu,<br>ambil tanpa antre.</h2>

        <p class="mb-0">Kantin Melati UPN Kampus II</p>
    </div>

    <?php
    if (isset($_GET['message'])) {
        if ($_GET['message'] == 'cart_added') {
            echo '<div class="alert alert-success">Menu berhasil ditambahkan ke keranjang.</div>';
        } elseif ($_GET['message'] == 'stok_habis') {
            echo '<div class="alert alert-danger">Stok menu tidak mencukupi.</div>';
        }
    }
    ?>

    <form method="GET" class="mb-3">
        <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>">

        <input type="text" name="cari" class="form-control cari-pill"
            placeholder="Mau makan apa hari ini?"
            value="<?php echo htmlspecialchars($cari); ?>">
    </form>

    <div class="mb-3">

        <a href="index.php?cari=<?php echo urlencode($cari); ?>"
            class="chip <?php if ($kategori == '') echo 'aktif'; ?>">
            Semua
        </a>

        <?php foreach ($daftar_kategori as $k) { ?>
            <a href="index.php?kategori=<?php echo urlencode($k); ?>&cari=<?php echo urlencode($cari); ?>"
                class="chip <?php if ($kategori == $k) echo 'aktif'; ?>">
                <?php echo htmlspecialchars($k); ?>
            </a>
        <?php } ?>

    </div>

    <h5 class="fw-bold mb-3">Menu Populer</h5>

    <div class="row">

        <?php while ($m = mysqli_fetch_assoc($query)) { ?>

            <div class="col-6 col-md-3 mb-3">
                <div class="kartu-menu">

                    <?php if ($m['foto'] != "" && file_exists('uploads/' . $m['foto'])) { ?>

                        <img src="uploads/<?php echo htmlspecialchars($m['foto']); ?>"
                            class="foto-menu"
                            alt="<?php echo htmlspecialchars($m['nama_menu']); ?>">

                    <?php } else { ?>

                        <div class="foto-menu foto-kosong">
                            <i class="bi bi-image"></i>
                        </div>

                    <?php } ?>

                    <?php if ($m['stok'] > 0) { ?>

                        <form method="POST" action="backend/tambah_keranjang.php">
                            <input type="hidden" name="id_menu" value="<?php echo $m['id']; ?>">
                            <button type="submit" class="btn-plus">+</button>
                        </form>

                    <?php } ?>

                    <div class="p-3">

                        <div class="fw-semibold">
                            <?php echo htmlspecialchars($m['nama_menu']); ?>
                        </div>

                        <small class="text-muted">
                            <?php echo htmlspecialchars($m['nama_stan']); ?>
                        </small>

                        <div class="d-flex justify-content-between align-items-center mt-1">

                            <span class="harga">
                                Rp <?php echo number_format($m['harga'], 0, ',', '.'); ?>
                            </span>

                            <?php if ($m['stok'] > 0) { ?>
                                <small>Stok <?php echo $m['stok']; ?></small>
                            <?php } else { ?>
                                <span class="badge bg-secondary">Habis</span>
                            <?php } ?>

                        </div>
                    </div>

                </div>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>
