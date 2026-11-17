<?php
// Header + navbar, dipakai semua halaman setelah login
$role = $_SESSION['role'];
$jumlah_keranjang = isset($_SESSION['keranjang']) ? array_sum($_SESSION['keranjang']) : 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <title>KantinKu</title>
</head>

<body>
    <nav class="navbar navbar-dark navbar-expand-lg" id="main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">KantinKu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuNavbar">
                <ul class="navbar-nav me-auto">
                    <?php if ($role == 'pelanggan') { ?>
                        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="keranjang.php">Keranjang (<?php echo $jumlah_keranjang; ?>)</a></li>
                        <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="pesanan.php">Pesanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="menu.php">Menu Saya</a></li>
                    <?php } ?>
                </ul>
                <a href="account.php" class="btn btn-light me-2">My Account</a>
                <a href="backend/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
