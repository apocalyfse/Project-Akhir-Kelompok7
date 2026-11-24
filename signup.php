<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Daftar - KantinKu</title>
</head>

<body class="halaman-login">
    <div class="container">
        <div class="kartu-form shadow-sm">
            <h2 class="text-center">Create Your Account</h2>
            <?php
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                if ($message == 'used_username') {
                    echo '<div class="alert alert-warning">Username sudah dipakai, gunakan username lain.</div>';
                } elseif ($message == 'failed') {
                    echo '<div class="alert alert-danger">Pembuatan akun gagal, silakan coba lagi.</div>';
                }
            }
            ?>
            <form method="POST" action="backend/process_signup.php">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
                <label class="form-label mt-3">Email</label>
                <input type="email" class="form-control" name="email" required>
                <label class="form-label mt-3">Password</label>
                <input type="password" class="form-control" name="password" required>
                <label class="form-label mt-3">Daftar sebagai</label>
                <select name="role" class="form-select">
                    <option value="pelanggan">Pelanggan (pembeli)</option>
                    <option value="penjual">Penjual (punya kedai)</option>
                </select>
                <label class="form-label mt-3">Nama kedai (isi kalau daftar sebagai penjual)</label>
                <input type="text" class="form-control" name="nama_stan">
                <input type="submit" value="Daftar" class="btn btn-utama w-100 mt-3">
            </form>
            <p class="text-center mt-3"><a href="login.php">Sudah punya akun? Login di sini</a></p>
        </div>
    </div>
</body>

</html>
