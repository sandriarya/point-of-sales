<?php
include '../config/koneksi.php';

// Ambil data dari form
$cart       = json_decode($_POST['cart'], true);
$pembayaran = $_POST['pembayaran'];
$total      = $_POST['total'];
$bayar      = $_POST['bayar'] ?? 0;
$kembali    = ($pembayaran == 'tunai') ? ($bayar - $total) : 0;
$tanggal    = date('Y-m-d H:i:s');

// Validasi keranjang
if (!$cart || count($cart) == 0) {
    die('Keranjang kosong');
}

// Simpan transaksi utama
mysqli_query($koneksi, "
    INSERT INTO transaksi (tanggal, total, bayar, kembali)
    VALUES ('$tanggal', '$total', '$bayar', '$kembali')
");

$id_transaksi = mysqli_insert_id($koneksi);
// Simpan detail transaksi
foreach ($cart as $item) {

    $id_menu = $item['id_menu'];
    $qty     = $item['qty'];

    // Ambil harga menu
    $q = mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu='$id_menu'");
    $m = mysqli_fetch_assoc($q);

    $harga    = $m['harga'];
    $subtotal = $harga * $qty;

    mysqli_query($koneksi, "
        INSERT INTO transaksi_detail 
        (id_transaksi, id_menu, qty, harga, subtotal)
        VALUES 
        ('$id_transaksi', '$id_menu', '$qty', '$harga', '$subtotal')
    ");
}

// Redirect ke struk
header("Location: ../pages/struk.php?id=$id_transaksi");
exit;