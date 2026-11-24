
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

// mengambil menu yang dipilih
$pilihan = array();

if (isset($_GET['pilih'])) {
    $pilihan = $_GET['pilih'];
}

if (count($pilihan) == 0) {
    header("Location: keranjang.php?message=pilih_kosong");
    exit();
}

$total = 0;
$id_penjual = 0;
$daftar = array();

// mengambil data menu
foreach ($pilihan as $id_menu) {
    $id_menu = (int)$id_menu;

    $query = mysqli_query($connect, "SELECT * FROM menu WHERE id='$id_menu'");
    $m = mysqli_fetch_assoc($query);

    if (!isset($_SESSION['keranjang'][$id_menu]) || !$m) {
        header("Location: keranjang.php?message=pilih_kosong");
        exit();
    }

    if ($id_penjual != 0 && $id_penjual != $m['id_penjual']) {
        header("Location: keranjang.php?message=pilih_kosong");
        exit();
    }

    $id_penjual = $m['id_penjual'];
    $m['jumlah'] = $_SESSION['keranjang'][$id_menu];

    $daftar[] = $m;
    $total += $m['harga'] * $m['jumlah'];
}

// mengambil nama kedai
$query = mysqli_query($connect, "SELECT nama_stan FROM users WHERE id='$id_penjual'");
$stan = mysqli_fetch_assoc($query);

// menentukan tanggal
$hari_ini = date('Y-m-d');
$tanggal = $hari_ini;

$jam_terakhir = substr(end($daftar_slot), 0, 5);
$hari_ini_habis = false;

if (date('H:i') >= $jam_terakhir) {
    $hari_ini_habis = true;
    $tanggal = date('Y-m-d', strtotime('+1 day'));
}

if (isset($_GET['tanggal']) && $_GET['tanggal'] >= $hari_ini) {
    $tanggal = $_GET['tanggal'];
}

include 'navbar.php';
?>

<div class="container mt-4">

    <h2>Checkout</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] == 'slot_full') { ?>
        <div class="alert alert-danger">
            Slot penuh atau sudah lewat, pilih slot lain.
        </div>
    <?php } ?>

    <div class="card mb-3">
        <div class="card-body">

            <h5><?php echo htmlspecialchars($stan['nama_stan']); ?></h5>

            <?php foreach ($daftar as $m) { ?>
                <div class="d-flex justify-content-between">
                    <span>
                        <?php echo $m['jumlah']; ?> x
                        <?php echo htmlspecialchars($m['nama_menu']); ?>
                    </span>

                    <span>
                        Rp <?php echo number_format($m['harga'] * $m['jumlah'], 0, ',', '.'); ?>
                    </span>
                </div>
            <?php } ?>

            <hr>

            <div class="d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>

        </div>
    </div>

    <?php if ($hari_ini_habis) { ?>
        <div class="alert alert-info">
            Jam pengambilan hari ini sudah lewat. Pesanan dibuat untuk besok.
        </div>
    <?php } ?>

    <!-- memilih tanggal -->
    <form method="GET" class="row g-2 mb-3">

        <?php foreach ($daftar as $m) { ?>
            <input type="hidden" name="pilih[]" value="<?php echo $m['id']; ?>">
        <?php } ?>

        <div class="col-auto">
            <label class="col-form-label">Tanggal pengambilan</label>
        </div>

        <div class="col-auto">
            <input type="date" name="tanggal" class="form-control"
                min="<?php echo $hari_ini; ?>" value="<?php echo $tanggal; ?>">
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-utama">Lihat Slot</button>
        </div>

    </form>

    <form method="POST" action="backend/process_checkout.php">

        <?php foreach ($daftar as $m) { ?>
            <input type="hidden" name="pilih[]" value="<?php echo $m['id']; ?>">
        <?php } ?>

        <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">

        <p class="fw-semibold">Pilih Jam Pengambilan</p>

        <div class="row g-2 mb-3">

            <?php foreach ($daftar_slot as $slot) {

                $query = mysqli_query($connect, "SELECT COUNT(*) AS jumlah FROM pesanan WHERE id_penjual='$id_penjual' AND tanggal='$tanggal' AND jam_ambil='$slot' AND status!='dibatalkan'");

                $data_slot = mysqli_fetch_assoc($query);
                $sisa = $kuota_slot - $data_slot['jumlah'];

                $nonaktif = false;
                $keterangan = "Sisa " . $sisa . " slot";

                if ($sisa <= 0) {
                    $nonaktif = true;
                    $keterangan = "Penuh";
                }

                if ($tanggal == $hari_ini && substr($slot, 0, 5) <= date('H:i')) {
                    $nonaktif = true;
                    $keterangan = "Sudah lewat";
                }
            ?>

                <div class="col-6 col-md-3">
                    <label class="card card-body">
                        <span>
                            <input type="radio" name="jam_ambil"
                                value="<?php echo $slot; ?>"
                                <?php if ($nonaktif) echo 'disabled'; ?> required>
                            <strong><?php echo $slot; ?></strong>
                        </span>
                        <small><?php echo $keterangan; ?></small>
                    </label>
                </div>

            <?php } ?>

        </div>

        <p class="fw-semibold">Metode Pembayaran</p>

        <div class="row g-2 mb-3">

            <div class="col-md-6">
                <label class="card card-body">
                    <span>
                        <input type="radio" name="metode" value="qris" checked>
                        <strong>QRIS</strong>
                    </span>
                    <small>Scan QR kedai melalui e-wallet atau m-banking.</small>
                </label>
            </div>

            <div class="col-md-6">
                <label class="card card-body">
                    <span>
                        <input type="radio" name="metode" value="transfer">
                        <strong>Transfer</strong>
                    </span>
                    <small>Transfer ke rekening atau e-wallet kedai.</small>
                </label>
            </div>

        </div>

        <label class="form-label">Catatan untuk Penjual</label>
        <input type="text" name="catatan" class="form-control mb-3"
            maxlength="200" placeholder="Contoh: sambal dipisah">

        <a href="keranjang.php" class="btn btn-secondary">Kembali</a>
        <button type="submit" class="btn btn-utama">Buat Pesanan</button>

    </form>

</div>

</body>
</html>
