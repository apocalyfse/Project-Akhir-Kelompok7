<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Daftar - KantinKu</title>
</head>

<body class="login-page">
    <div class="container">
        <div id="signup-card" class="shadow-sm">
            <div class="text-center mb-3">
                <h2>Create Your Account</h2>
            </div>
            <?php include 'alert.php'; ?>
            <form method="POST" action="backend/process_signup.php">
                <label class="form-label"><i class="bi bi-person-fill"></i> Username</label>
                <input type="text" class="form-control" name="username" required>
                <label class="form-label mt-3"><i class="bi bi-envelope-fill"></i> Email</label>
                <input type="email" class="form-control" name="email" required>
                <label class="form-label mt-3"><i class="bi bi-lock-fill"></i> Password</label>
                <input type="password" class="form-control" name="password" required>
                <label class="form-label mt-3">Daftar sebagai</label>
                <select name="role" class="form-select">
                    <option value="pelanggan">Pelanggan (pembeli)</option>
                    <option value="penjual">Penjual (punya stan)</option>
                </select>
                <label class="form-label mt-3">Nama stan (isi kalau daftar sebagai penjual)</label>
                <input type="text" class="form-control" name="nama_stan">
                <input type="submit" value="Daftar" class="btn btn-utama w-100 mt-3">
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Sudah punya akun? Login di sini</a>
            </div>
        </div>
    </div>
</body>

</html>
