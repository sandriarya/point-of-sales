<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Cafe Love Story</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI',sans-serif;

    /* BACKGROUND GRADIENT */
    background:radial-gradient(circle at top, #e9f7ff 0%, #cfeeff 40%, #b6e3ff 100%);
}

/* CARD LOGIN */
.login-card{
    width:360px;
    background:#fff;
    border-radius:20px;
    padding:45px 30px 30px;
    position:relative;
    box-shadow:0 20px 45px rgba(0,0,0,.18); /* FLOATING */
}

/* ICON USER */
.avatar{
    width:80px;
    height:80px;
    background:#0d6efd; /* WARNA INDEX */
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    font-size:36px;
    position:absolute;
    top:-40px;
    left:50%;
    transform:translateX(-50%);
    box-shadow:0 10px 25px rgba(13,110,253,.4);
}

/* JUDUL */
.title{
    font-weight:700;
    margin-top:35px;
}

.subtitle{
    font-size:14px;
    color:#888;
    margin-bottom:25px;
}

/* INPUT */
.form-control{
    border-radius:30px;
    padding:12px 45px;
    background:#f1f3f6;
    border:1px solid #e0e0e0;
}

.form-control:focus{
    box-shadow:none;
    border-color:#0d6efd;
}

.input-group{
    position:relative;
}

.input-group-text{
    background:transparent;
    border:none;
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#999;
    z-index:5;
}

/* BUTTON */
.btn-login{
    background:#0d6efd;
    border:none;
    color:white;
    font-weight:600;
    border-radius:30px;
    padding:12px;
}

.btn-login:hover{
    background:#0b5ed7;
}

/* FOOTER */
.footer-text{
    font-size:13px;
    color:#aaa;
}
</style>
</head>

<body>

<div class="login-card">

    <div class="avatar">
        <i class="bi bi-person"></i>
    </div>

    <div class="text-center">
        <h4 class="title">Cafe Love Story</h4>
        <p class="subtitle">Login Kasir</p>
    </div>

    <?php if (isset($_SESSION['error'])) : ?>
    <div class="alert alert-danger text-center py-2">
        <?= $_SESSION['error']; ?>
    </div>
<?php unset($_SESSION['error']); endif; ?>

<?php if (isset($_SESSION['success'])) : ?>
    <div class="alert alert-success text-center py-2">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <form method="post" action="../process/auth_proses.php">

        <div class="mb-3 input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
            <input type="text" name="username"
                   class="form-control"
                   placeholder="Username"
                   required>
        </div>

        <div class="mb-4 input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" name="password"
                   class="form-control"
                   placeholder="Password"
                   required>
        </div>

        <button type="submit" class="btn btn-login w-100">
            LOGIN
        </button>

        <div class="text-center mt-3">
            <a href="lupa_password.php" class="text-decoration-none" style="font-size:14px;">
                Lupa password?
            </a>
        </div>

    </form>

    <div class="text-center mt-4 footer-text">
        © <?= date('Y'); ?> Cafe Love Story<br>
        Sistem Kasir Berbasis Web
    </div>
</div>

</body>
</html>
