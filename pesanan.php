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

$id_stan = $_SESSION['id_stan'];
$tanggal = isset($_GET['tanggal']) ? date('Y-m-d', strtotime($_GET['tanggal'])) : date('Y-m-d');
$cari    = isset($_GET['cari']) ? mysqli_real_escape_string($connect, $_GET['cari']) : '';

// Ringkasan pada tanggal yang dipilih
$q1 = mysqli_query($connect, "SELECT COUNT(*) AS jumlah FROM pesanan WHERE id_stan=$id_stan AND tanggal='$tanggal' AND status!='dibatalkan'");
$jumlah_pesanan = mysqli_fetch_assoc($q1)['jumlah'];
$q2 = mysqli_query($connect, "SELECT COALESCE(SUM(total),0) AS jumlah FROM pesanan WHERE id_stan=$id_stan AND tanggal='$tanggal' AND status='selesai'");
$pendapatan = mysqli_fetch_assoc($q2)['jumlah'];

$pesanan = mysqli_query($connect, "SELECT pesanan.*, users.username, slot.jam_mulai, slot.jam_selesai,
            (SELECT GROUP_CONCAT(CONCAT(jumlah, 'x ', nama_menu) SEPARATOR ', ') FROM detail_pesanan WHERE id_pesanan=pesanan.id) AS isi
            FROM pesanan
            JOIN users ON pesanan.id_user = users.id
            JOIN slot ON pesanan.id_slot = slot.id
            WHERE pesanan.id_stan=$id_stan AND pesanan.tanggal='$tanggal' AND pesanan.kode LIKE '%$cari%'
            ORDER BY slot.jam_mulai, pesanan.id");

// Tombol status berikutnya: status sekarang => array(status baru, teks tombol)
$aksi = array(
    'menunggu' => array('dimasak', 'Mulai masak'),
    'dimasak'  => array('siap', 'Tandai siap'),
    'siap'     => array('selesai', 'Sudah diambil')
);
include 'header.php';
?>

<div class="container">
    <h2>Pesanan Masuk</h2>
    <?php include 'alert.php'; ?>

    <div class="row mb-3">
        <div class="col-md-6"><div class="card card-body"><small class="text-muted">Pesanan aktif/selesai</small><h3><?php echo $jumlah_pesanan; ?></h3></div></div>
        <div class="col-md-6"><div class="card card-body"><small class="text-muted">Pendapatan (pesanan selesai)</small><h3><?php echo rupiah($pendapatan); ?></h3></div></div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal; ?>"></div>
        <div class="col-md-6"><input type="text" name="cari" class="form-control" placeholder="Cari kode pengambilan (KTN-...)" value="<?php echo htmlspecialchars($cari); ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-utama w-100">Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>Jam ambil</th><th>Kode</th><th>Pelanggan</th><th>Pesanan</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($pesanan) == 0) {
                    echo '<tr><td colspan="7" class="text-center text-muted">Tidak ada pesanan.</td></tr>';
                }
                while ($p = mysqli_fetch_assoc($pesanan)) { ?>
                    <tr>
                        <td><?php echo substr($p['jam_mulai'], 0, 5); ?>-<?php echo substr($p['jam_selesai'], 0, 5); ?></td>
                        <td class="fw-bold"><?php echo $p['kode']; ?></td>
                        <td><?php echo htmlspecialchars($p['username']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($p['isi']); ?>
                            <?php if ($p['catatan'] != '') { ?><br><small class="text-muted">Catatan: <?php echo htmlspecialchars($p['catatan']); ?></small><?php } ?>
                        </td>
                        <td><?php echo rupiah($p['total']); ?></td>
                        <td><span class="badge bg-<?php echo warna_status($p['status']); ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td>
                            <?php if (isset($aksi[$p['status']])) { ?>
                                <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&status=<?php echo $aksi[$p['status']][0]; ?>" class="btn btn-sm btn-success"><?php echo $aksi[$p['status']][1]; ?></a>
                            <?php } ?>
                            <?php if ($p['status'] == 'menunggu' || $p['status'] == 'dimasak') { ?>
                                <a href="backend/update_status.php?id=<?php echo $p['id']; ?>&status=dibatalkan" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Batalkan pesanan ini?')">Batal</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Muat ulang tiap 30 detik supaya pesanan baru langsung muncul
    setTimeout(function() { location.reload(); }, 30000);
</script>
<?php include 'footer.php'; ?>
