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
            <div class="card shadow-sm p-4">
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle text-secondary" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold">My Profile</h3>
                </div>
                <?php include 'alert.php'; ?>
                <form method="POST" action="backend/process_edit_account.php">
                    <label class="form-label text-muted fw-semibold">Username</label>
                    <input type="text" class="form-control mb-3" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                    <label class="form-label text-muted fw-semibold">Email</label>
                    <input type="email" class="form-control mb-3" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                    <label class="form-label text-muted fw-semibold">Password</label>
                    <input type="password" class="form-control mb-3" name="password" placeholder="Kosongkan kalau tidak ingin mengganti password...">
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Save Change</button>
                        <a href="account.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
