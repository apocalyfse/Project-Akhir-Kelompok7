<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != true) {
    header("Location: login.php?message=not_logged_in");
    exit();
}
include 'backend/connect.php';

$id    = $_SESSION['id'];
$query = mysqli_query($connect, "SELECT * FROM users WHERE id=$id");
$data  = mysqli_fetch_assoc($query);
if (!$data) {
    header("Location: backend/logout.php");
    exit();
}
include 'header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php include 'alert.php'; ?>
            <div class="card shadow-sm p-4">
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle text-secondary" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold">My Profile</h3>
                    <p class="text-muted">Informasi akun KantinKu kamu</p>
                </div>
                <label class="form-label text-muted fw-semibold">Username</label>
                <div class="form-control bg-light mb-3"><?php echo htmlspecialchars($data['username']); ?></div>
                <label class="form-label text-muted fw-semibold">Email</label>
                <div class="form-control bg-light mb-3"><?php echo htmlspecialchars($data['email']); ?></div>
                <label class="form-label text-muted fw-semibold">Peran</label>
                <div class="form-control bg-light mb-3"><?php echo ucfirst($data['role']); ?></div>
                <div class="d-flex justify-content-between mt-3">
                    <a href="edit_account.php" class="btn btn-warning text-white"><i class="bi bi-pencil-square"></i> Edit Account</a>
                    <a href="backend/delete_account.php?id=<?php echo $id; ?>" class="btn btn-outline-danger"
                        onclick="return confirm('Yakin ingin menghapus akun?')"><i class="bi bi-trash-fill"></i> Delete Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
