
<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
    header("Location: login.php?message=not_logged_in");
    exit();
}

include 'backend/connect.php';

$id = $_SESSION['id'];

$query = mysqli_query($connect, "SELECT * FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: backend/logout.php");
    exit();
}

include 'navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="card-body">

                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle text-secondary"
                            style="font-size: 4rem;"></i>

                        <h3 class="fw-bold mt-2">My Profile</h3>
                        <p class="text-muted">Informasi akun KantinKu</p>
                    </div>

                    <hr class="mb-4">

                    <?php
                    if (isset($_GET['message'])) {
                        if ($_GET['message'] == 'used_username') {
                            echo '<div class="alert alert-warning">Username sudah dipakai.</div>';
                        } elseif ($_GET['message'] == 'edit_successful') {
                            echo '<div class="alert alert-success">Data akun berhasil disimpan.</div>';
                        } elseif ($_GET['message'] == 'failed') {
                            echo '<div class="alert alert-danger">Data akun gagal disimpan.</div>';
                        }
                    }
                    ?>

                    <form method="POST" action="backend/process_edit_account.php"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control"
                                value="<?php echo htmlspecialchars($data['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?php echo htmlspecialchars($data['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengganti password">
                        </div>

                        <?php if ($data['role'] == 'penjual') { ?>

                            <hr class="my-4">

                            <h5 class="fw-bold mb-3">Informasi Penjual</h5>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold">Nama Kedai</label>
                                <input type="text" name="nama_stan" class="form-control"
                                    value="<?php echo htmlspecialchars($data['nama_stan']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold">Rekening / E-wallet</label>
                                <input type="text" name="rekening" class="form-control"
                                    value="<?php echo htmlspecialchars($data['rekening']); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold">Gambar QRIS</label>

                                <?php if ($data['qris'] != '' && file_exists('uploads/' . $data['qris'])) { ?>
                                    <div class="mb-2">
                                        <img src="uploads/<?php echo htmlspecialchars($data['qris']); ?>"
                                            height="100" class="img-thumbnail">
                                    </div>
                                <?php } ?>

                                <input type="file" name="qris" class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">
                            </div>

                        <?php } ?>

                        <div class="d-flex justify-content-between align-items-center mt-4">

                            <button type="submit" class="btn btn-warning px-4 text-white fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i>
                                Save Change
                            </button>

                            <a href="backend/delete_account.php?id=<?php echo $id; ?>"
                                class="btn btn-outline-danger px-4"
                                onclick="return confirm('Yakin ingin menghapus akun?')">

                                <i class="bi bi-trash-fill me-1"></i>
                                Delete Account
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
