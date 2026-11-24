<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Login - KantinKu</title>
</head>

<body class="halaman-login">
    <div class="container">
        <div class="kartu-form shadow-sm">
            <h2 class="text-center">Welcome to KantinKu</h2>
            <p class="text-center text-muted">Pesan dulu, ambil tanpa antre di Kantin Melati</p>
            <?php
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                if ($message == 'not_logged_in') {
                    echo '<div class="alert alert-warning">Kamu belum login, silakan login dulu.</div>';
                } elseif ($message == 'account_created') {
                    echo '<div class="alert alert-success">Akun berhasil dibuat, silakan login.</div>';
                } elseif ($message == 'false') {
                    echo '<div class="alert alert-danger">Username atau password salah.</div>';
                } elseif ($message == 'logout') {
                    echo '<div class="alert alert-primary">Kamu sudah logout.</div>';
                } elseif ($message == 'account_deleted') {
                    echo '<div class="alert alert-primary">Akun berhasil dihapus.</div>';
                }
            }
            ?>
            <form method="POST" action="backend/process_login.php">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
                <label class="form-label mt-3">Password</label>
                <input type="password" class="form-control" name="password" required>
                <input type="submit" value="Login" class="btn btn-utama w-100 mt-3">
            </form>
            <p class="text-center mt-3"><a href="signup.php">Belum punya akun? Daftar di sini</a></p>
        </div>
    </div>
</body>

</html>
