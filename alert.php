<?php
// Alert Bootstrap berdasarkan parameter ?message=... di URL
// Format: 'nama_message' => array('warna alert', 'teks')
$daftar = array(
    'not_logged_in'   => array('warning', 'Kamu belum login, silakan login dulu.'),
    'account_created' => array('success', 'Akun berhasil dibuat, silakan login.'),
    'false'           => array('danger',  'Username atau password salah.'),
    'logout'          => array('primary', 'Kamu sudah logout.'),
    'account_deleted' => array('primary', 'Akun berhasil dihapus.'),
    'used_username'   => array('warning', 'Username sudah dipakai, gunakan username lain.'),
    'failed'          => array('danger',  'Proses gagal, silakan coba lagi.'),
    'edit_successful' => array('success', 'Data akun berhasil diubah.'),
    'cart_added'      => array('success', 'Menu masuk ke keranjang.'),
    'cart_empty'      => array('warning', 'Keranjang masih kosong.'),
    'stok_habis'      => array('danger',  'Stok menu tidak mencukupi.'),
    'beda_stan'       => array('warning', 'Satu pesanan hanya untuk satu stan. Selesaikan atau kosongkan keranjang dulu.'),
    'slot_full'       => array('danger',  'Slot penuh atau sudah lewat, pilih slot lain.'),
    'order_success'   => array('success', 'Pesanan berhasil dibuat! Tunjukkan kode pengambilan ke penjual.'),
    'order_cancelled' => array('primary', 'Pesanan dibatalkan.'),
    'status_updated'  => array('success', 'Status pesanan diperbarui.'),
    'menu_added'      => array('success', 'Menu berhasil ditambahkan.'),
    'menu_edited'     => array('success', 'Menu berhasil diubah.'),
    'menu_deleted'    => array('primary', 'Menu berhasil dihapus.')
);

if (isset($_GET['message']) && isset($daftar[$_GET['message']])) {
    $alert = $daftar[$_GET['message']];
    echo '<div class="alert alert-' . $alert[0] . '" role="alert">' . $alert[1] . '</div>';
}
?>
