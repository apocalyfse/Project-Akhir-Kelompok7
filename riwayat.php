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
$query   = mysqli_query($connect, "SELECT pesanan.*, users.nama_stan, users.rekening, users.qris
                                   FROM pesanan JOIN users ON pesanan.id_penjual = users.id
                                   WHERE pesanan.id_user=$id_user ORDER BY pesanan.id DESC");
include 'navbar.php';
?>

<div class="container mt-4">
    <h2>Riwayat Pesanan</h2>
    <?php
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if ($message == 'order_success') {
            echo '<div class="alert alert-success">Pesanan berhasil dibuat! Klik "Bayar sekarang" untuk melakukan pembayaran.</div>';
        } elseif ($message == 'order_cancelled') {
            echo '<div class="alert alert-primary">Pesanan dibatalkan.</div>';
        } elseif ($message == 'bukti_terkirim') {
            echo '<div class="alert alert-success">Bukti pembayaran terkirim. Tunggu konfirmasi dari penjual.</div>';
        } elseif ($message == 'bayar_gagal') {
            echo '<div class="alert alert-danger">Upload bukti gagal. Gunakan gambar JPG/PNG/WEBP maksimal 2 MB.</div>';
        } elseif ($message == 'failed') {
            echo '<div class="alert alert-danger">Proses gagal, silakan coba lagi.</div>';
        }
    }

    if (mysqli_num_rows($query) == 0) {
        echo '<p class="text-muted">Belum ada pesanan.</p>';
    }

    while ($p = mysqli_fetch_assoc($query)) {
    ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <div class="kode-pesanan"><?php echo $p['kode']; ?></div>
                    <div><?php echo htmlspecialchars($p['nama_stan']); ?></div>
                    <small class="text-muted">Ambil <?php echo date('d M Y', strtotime($p['tanggal'])); ?>, <?php echo $p['jam_ambil']; ?></small>
                    <?php
                    // isi pesanan
                    $q_detail = mysqli_query($connect, "SELECT * FROM detail_pesanan WHERE id_pesanan=" . $p['id']);
                    while ($d = mysqli_fetch_assoc($q_detail)) {
                        echo '<div>' . $d['jumlah'] . ' x ' . htmlspecialchars($d['nama_menu']) . '</div>';
                    }
                    if ($p['catatan'] != '') {
                        echo '<small class="text-muted">Catatan: ' . htmlspecialchars($p['catatan']) . '</small>';
                    }
                    ?>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?php echo $warna_status[$p['status']]; ?>"><?php echo ucfirst($p['status']); ?></span>
                    <?php if ($p['status'] != 'dibatalkan') { ?>
                        <span class="badge bg-<?php echo $warna_bayar[$p['bayar']]; ?>"><?php echo strtoupper($p['metode']); ?> - <?php echo $teks_bayar[$p['bayar']]; ?></span>
                    <?php } ?>
                    <div class="fw-bold">Rp <?php echo number_format($p['total'], 0, ',', '.'); ?></div>

                    <?php if ($p['status'] != 'dibatalkan' && $p['bayar'] != 'lunas') { ?>
                        <button class="btn btn-sm btn-utama mt-1" data-bs-toggle="collapse" data-bs-target="#bayar<?php echo $p['id']; ?>">
                            <?php
                            if ($p['bayar'] == 'belum') {
                                echo 'Bayar sekarang';
                            } else {
                                echo 'Lihat pembayaran';
                            }
                            ?>
                        </button>
                    <?php } ?>
                    <?php if ($p['status'] == 'menunggu') { ?>
                        <a href="backend/batal_pesanan.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger mt-1"
                            onclick="return confirm('Batalkan pesanan ini?')">Batalkan</a>
                    <?php } ?>
                </div>
            </div>

            <?php if ($p['status'] != 'dibatalkan' && $p['bayar'] != 'lunas') { ?>
                <div class="collapse" id="bayar<?php echo $p['id']; ?>">
                    <div class="card-body border-top">
                        <p class="fw-semibold mb-1">Total bayar: <span class="harga">Rp <?php echo number_format($p['total'], 0, ',', '.'); ?></span></p>
                        <?php if ($p['metode'] == 'qris') { ?>
                            <p class="small">Buka aplikasi e-wallet atau m-banking, scan QRIS di bawah, lalu bayar sesuai total.</p>
                            <?php if ($p['qris'] != '' && file_exists('uploads/' . $p['qris'])) { ?>
                                <img src="uploads/<?php echo $p['qris']; ?>" style="max-width:240px" class="img-fluid border rounded mb-3">
                            <?php } else { ?>
                                <div class="alert alert-warning">Penjual belum mengunggah QRIS.</div>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="small">Transfer sesuai total lewat aplikasi m-banking atau e-wallet ke:</p>
                            <div class="alert alert-light border fw-semibold"><?php echo htmlspecialchars($p['rekening']); ?></div>
                        <?php } ?>

                        <form method="POST" action="backend/process_bayar.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                            <label class="form-label">Upload bukti pembayaran (screenshot)</label>
                            <input type="file" name="bukti" class="form-control mb-2" accept=".jpg,.jpeg,.png,.webp" required>
                            <button type="submit" class="btn btn-utama">Kirim bukti pembayaran</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<script>
    // muat ulang tiap 15 detik supaya status terbaru (kecuali kalau panel pembayaran sedang dibuka)
    setTimeout(function() {
        if (!document.querySelector('.collapse.show')) {
            location.reload();
        }
    }, 15000);
</script>
</body>

</html>
