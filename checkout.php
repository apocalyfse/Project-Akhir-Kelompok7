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

if (empty($_SESSION['keranjang'])) {
    header("Location: keranjang.php?message=cart_empty");
    exit();
}

$id_stan  = $_SESSION['id_stan_keranjang'];
$hari_ini = date('Y-m-d');
$sekarang = date('H:i:s');

// Tanggal pengambilan (default hari ini, tidak boleh kemarin)
$tanggal = isset($_GET['tanggal']) ? date('Y-m-d', strtotime($_GET['tanggal'])) : $hari_ini;
if ($tanggal < $hari_ini) {
    $tanggal = $hari_ini;
}

// Hitung total keranjang
$total = 0;
foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
    $query  = mysqli_query($connect, "SELECT harga FROM menu WHERE id=$id_menu");
    $m      = mysqli_fetch_assoc($query);
    $total += $m['harga'] * $jumlah;
}

$q_stan = mysqli_query($connect, "SELECT nama_stan FROM stan WHERE id=$id_stan");
$stan   = mysqli_fetch_assoc($q_stan);

// Daftar slot + jumlah pesanan yang sudah memakai slot itu pada tanggal tersebut
$slot = mysqli_query($connect, "SELECT slot.*,
            (SELECT COUNT(*) FROM pesanan WHERE id_slot=slot.id AND id_stan=$id_stan AND tanggal='$tanggal' AND status!='dibatalkan') AS terpakai
            FROM slot ORDER BY jam_mulai");
include 'header.php';
?>

<div class="container">
    <h2>Checkout</h2>
    <p class="text-muted">Pesanan dari <strong><?php echo htmlspecialchars($stan['nama_stan']); ?></strong> &middot; Total <?php echo rupiah($total); ?></p>
    <?php include 'alert.php'; ?>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto"><label class="col-form-label">Tanggal pengambilan</label></div>
        <div class="col-auto"><input type="date" name="tanggal" class="form-control" min="<?php echo $hari_ini; ?>" value="<?php echo $tanggal; ?>"></div>
        <div class="col-auto"><button type="submit" class="btn btn-outline-success">Lihat slot</button></div>
    </form>

    <form method="POST" action="backend/process_checkout.php">
        <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
        <p class="fw-semibold">Pilih jam pengambilan</p>
        <div class="row g-2 mb-3">
            <?php
            while ($s = mysqli_fetch_assoc($slot)) {
                $sisa     = $s['kuota'] - $s['terpakai'];
                $lewat    = ($tanggal == $hari_ini && $s['jam_mulai'] <= $sekarang);
                $nonaktif = ($sisa <= 0 || $lewat);
            ?>
                <div class="col-6 col-md-3">
                    <label class="card card-body <?php if ($nonaktif) echo 'bg-light text-muted'; ?>">
                        <span>
                            <input type="radio" name="id_slot" value="<?php echo $s['id']; ?>" <?php if ($nonaktif) echo 'disabled'; ?> required>
                            <strong><?php echo substr($s['jam_mulai'], 0, 5); ?>-<?php echo substr($s['jam_selesai'], 0, 5); ?></strong>
                        </span>
                        <small>
                            <?php
                            if ($lewat) echo 'Sudah lewat';
                            elseif ($sisa <= 0) echo 'Penuh';
                            else echo 'Sisa ' . $sisa . ' slot';
                            ?>
                        </small>
                    </label>
                </div>
            <?php } ?>
        </div>
        <label class="form-label">Catatan untuk penjual (opsional)</label>
        <input type="text" name="catatan" class="form-control mb-3" maxlength="200" placeholder="Contoh: sambal dipisah">
        <a href="keranjang.php" class="btn btn-secondary">Kembali</a>
        <button type="submit" class="btn btn-utama">Buat Pesanan</button>
    </form>
</div>

<?php include 'footer.php'; ?>
