<?php
// konfigurasi database
$host     = "localhost";
$user     = "root";
$password = "";
$database = "kasir_cafe_love_story";

date_default_timezone_set('Asia/Jakarta');

// membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
