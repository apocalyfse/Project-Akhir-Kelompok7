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
$tanggal    = date('Y-m-d');
if (isset($_GET['tanggal'])) {
    $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
}

// ringkasan pada tanggal yang dipilih
$q1 = mysqli_query($connect, "SELECT COUNT(*) AS jumlah FROM pesanan WHERE id_penjual=$id_penjual AND tanggal='$tanggal' AND status!='dibatalkan'");
$jumlah_pesanan = mysqli_fetch_assoc($q1)['jumlah'];
$q2 = mysqli_query($connect, "SELECT COALESCE(SUM(total), 0) AS jumlah FROM pesanan WHERE id_penjual=$id_penjual AND tanggal='$tanggal' AND status='selesai'");
$pendapatan = mysqli_fetch_assoc($q2)['jumlah'];

$query = mysqli_query($connect, "SELECT pesanan.*, users.username FROM pesanan JOIN users ON pesanan.id_user = users.id
                                 WHERE pesanan.id_penjual=$id_penjual AND pesanan.tanggal='$tanggal'
                                 ORDER BY pesanan.jam_ambil, pesanan.id");
include 'navbar.php';
?>

<div class="container mt-4">
    <h2>Pesanan Masuk</h2>
    <?php
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if ($message == 'status_updated') {
            echo '<div class="alert alert-success">Status pesanan diperbarui.</div>';
        } elseif ($message == 'lunas_ok') {
            echo '<div class="alert alert-success">Pembayaran dikonfirmasi lunas.</div>';
        } elseif ($message == 'belum_lunas') {
            echo '<div class="alert alert-warning">Pesanan belum lunas, belum bisa diproses.</div>';
        }
    }
    ?>

    <div class="row mb-3">
        <div class="col-md-6"><div class="card card-body"><small class="text-muted">Jumlah pesanan</small><h3><?php echo $jumlah_pesanan; ?></h3></div></div>
        <div class="col-md-6"><div class="card card-body"><small class="text-muted">Pendapatan (pesanan selesai)</small><h3>Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h3></div></div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal; ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-utama w-100">Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <tr><th>Jam ambil</th><th>Kode</th><th>Pelanggan</th><th>Pesanan</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Aksi</th></tr>
            <?php
            if (mysqli_num_rows($query) == 0) {
                echo '<tr><td colspan="8" class="text-center text-muted">Tidak ada pesanan.</td></tr>';
            }
            while ($p = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?php echo $p['jam_ambil']; ?></td>
                    <td class="fw-bold"><?php echo $p['kode']; ?></td>
                    <td><?php echo htmlspecialchars($p['username']); ?></td>
                    <td>
                        <?php
                        $q_detail = mysqli_query($connect, "SELECT * FROM detail_pesanan WHERE id_pesanan=" . $p['id']);
                        while ($d = mysqli_fetch_assoc($q_detail)) {
                            echo $d['jumlah'] . ' x ' . htmlspecialchars($d['nama_menu']) . '<br>';
                        }
                        if ($p['catatan'] != '') {
                            echo '<small class="text-muted">Catatan: ' . htmlspecialchars($p['catatan']) . '</small>';
                        }
                        ?>
                    </td>
                    <td>Rp <?php echo number_format($p['total'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $warna_bayar[$p['bayar']]; ?>"><?php echo strtoupper($p['metode']); ?> - <?php echo $teks_bayar[$p['bayar']]; ?></span>
                        <?php if ($p['bukti'] != '' && file_exists('uploads/' . $p['bukti'])) { ?>
                            <br><a href="uploads/<?php echo $p['bukti']; ?>" target="_blank" class="small">Lihat bukti</a>
                        <?php } ?>
                    </td>
                    <td><span class="badge bg-<?php echo $warna_status[$p['status']]; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                    <td>
                        <?php if ($p['bayar'] == 'dibayar' && $p['status'] != 'dibatalkan') { ?>
                            <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&aksi=lunas" class="btn btn-sm btn-utama">Konfirmasi lunas</a>
                        <?php } ?>

                        <?php if ($p['status'] == 'menunggu' && $p['bayar'] != 'lunas') { ?>
                            <small class="text-muted">Menunggu pembayaran</small>
                        <?php } elseif ($p['status'] == 'menunggu') { ?>
                            <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&aksi=masak" class="btn btn-sm btn-utama">Mulai masak</a>
                        <?php } elseif ($p['status'] == 'dimasak') { ?>
                            <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&aksi=siap" class="btn btn-sm btn-utama">Tandai siap</a>
                        <?php } elseif ($p['status'] == 'siap') { ?>
                            <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&aksi=selesai" class="btn btn-sm btn-utama">Sudah diambil</a>
                        <?php } ?>

                        <?php if ($p['status'] == 'menunggu' || $p['status'] == 'dimasak') { ?>
                            <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&aksi=batal" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Batalkan pesanan ini?')">Batal</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<script>
    // muat ulang tiap 30 detik supaya pesanan baru langsung muncul
    setTimeout(function() { location.reload(); }, 30000);
</script>
</body>

</html>
