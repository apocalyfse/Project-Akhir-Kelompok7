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

$id_user = $_SESSION['id'];
$pesanan = mysqli_query($connect, "SELECT pesanan.*, stan.nama_stan, slot.jam_mulai, slot.jam_selesai,
            (SELECT GROUP_CONCAT(CONCAT(jumlah, 'x ', nama_menu) SEPARATOR ', ') FROM detail_pesanan WHERE id_pesanan=pesanan.id) AS isi
            FROM pesanan
            JOIN stan ON pesanan.id_stan = stan.id
            JOIN slot ON pesanan.id_slot = slot.id
            WHERE pesanan.id_user=$id_user
            ORDER BY pesanan.id DESC");
$ada_aktif = false;
include 'header.php';
?>

<div class="container">
    <h2>Riwayat Pesanan</h2>
    <?php include 'alert.php'; ?>

    <?php if (mysqli_num_rows($pesanan) == 0) { ?>
        <p class="text-muted">Belum ada pesanan.</p>
    <?php } ?>

    <?php
    while ($p = mysqli_fetch_assoc($pesanan)) {
        if ($p['status'] == 'menunggu' || $p['status'] == 'dimasak' || $p['status'] == 'siap') {
            $ada_aktif = true;
        }
    ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <div class="kode-pesanan"><?php echo $p['kode']; ?></div>
                    <div><?php echo htmlspecialchars($p['nama_stan']); ?></div>
                    <small class="text-muted">
                        Ambil <?php echo date('d M Y', strtotime($p['tanggal'])); ?>,
                        <?php echo substr($p['jam_mulai'], 0, 5); ?>-<?php echo substr($p['jam_selesai'], 0, 5); ?>
                    </small>
                    <div class="mt-1"><?php echo htmlspecialchars($p['isi']); ?></div>
                    <?php if ($p['catatan'] != '') { ?>
                        <small class="text-muted">Catatan: <?php echo htmlspecialchars($p['catatan']); ?></small>
                    <?php } ?>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?php echo warna_status($p['status']); ?>"><?php echo ucfirst($p['status']); ?></span>
                    <div class="fw-bold"><?php echo rupiah($p['total']); ?></div>
                    <?php if ($p['status'] == 'menunggu') { ?>
                        <a href="backend/batal_pesanan.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger mt-1"
                            onclick="return confirm('Batalkan pesanan ini?')">Batalkan</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php if ($ada_aktif) { ?>
    <script>
        // Ada pesanan yang masih berjalan: muat ulang halaman tiap 15 detik supaya status terbaru
        setTimeout(function() { location.reload(); }, 15000);
    </script>
<?php } ?>
<?php include 'footer.php'; ?>
