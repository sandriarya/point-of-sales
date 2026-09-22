<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['reset_password'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($koneksi, "SELECT * FROM kasir WHERE username='$username'");
    $user = mysqli_fetch_assoc($cek);

    if ($user) {
        mysqli_query($koneksi, "
            UPDATE kasir 
            SET password='$password' 
            WHERE username='$username'
        ");

        $_SESSION['success'] = "Password berhasil di reset.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password | Cafe Love Story</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI',sans-serif;
    background:radial-gradient(circle at top, #e9f7ff, #b6e3ff);
}
.card{
    width:380px;
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.18);
}
</style>
</head>
<body>

<div class="card p-4">

    <h4 class="fw-bold text-center mb-3">Reset Password</h4>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger text-center py-2">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" required minlength="3">
        </div>

        <button type="submit" name="reset_password" class="btn btn-success w-100">
            Reset Password
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none">Kembali ke Login</a>
    </div>

</div>

</body>
</html>
