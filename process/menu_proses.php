<?php
include '../config/koneksi.php';

/* ================= TAMBAH MENU ================= */
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {

    $nama  = $_POST['nama_menu'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    if ($gambar != '') {
        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $nama_file = time().'.'.$ext;
        move_uploaded_file($tmp, '../assets/img/menu/'.$nama_file);
    } else {
        $nama_file = 'default.jpg';
    }

    mysqli_query($koneksi, "
        INSERT INTO menu (nama_menu, harga, gambar)
        VALUES ('$nama', '$harga', '$nama_file')
    ");

    header("Location: ../pages/menu.php?status=tambah");
    exit;
}

/* ================= UPDATE MENU ================= */
if (isset($_POST['aksi']) && $_POST['aksi'] == 'update') {

    $id    = $_POST['id_menu'];
    $nama  = $_POST['nama_menu'];
    $harga = $_POST['harga'];

    if ($_FILES['gambar']['name'] != '') {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = time().'.'.$ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/img/menu/'.$nama_file);

        mysqli_query($koneksi, "
            UPDATE menu SET
                nama_menu='$nama',
                harga='$harga',
                gambar='$nama_file'
            WHERE id_menu='$id'
        ");
    } else {
        mysqli_query($koneksi, "
            UPDATE menu SET
                nama_menu='$nama',
                harga='$harga'
            WHERE id_menu='$id'
        ");
    }

    header("Location: ../pages/menu.php?status=update");
    exit;
}

/* ================= HAPUS MENU ================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu='$id'");
    header("Location: ../pages/menu.php?status=hapus");
    exit;
}