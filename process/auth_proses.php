<?php
session_start();
include '../config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM kasir WHERE username='$username'");
$user  = mysqli_fetch_assoc($query);

if ($user) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];

        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: ../auth/login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../auth/login.php");
    exit;
}
