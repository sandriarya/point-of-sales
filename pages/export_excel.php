<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Harian_$tanggal.xls");

$data = mysqli_query($koneksi, "
    SELECT 
        t.tanggal,
        m.nama_menu,
        d.harga,
        d.qty,
        d.subtotal
    FROM transaksi_detail d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE DATE(t.tanggal)='$tanggal'
    ORDER BY t.tanggal ASC
");

$total = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(total) total 
    FROM transaksi 
    WHERE DATE(tanggal)='$tanggal'
"))['total'];
?>

<table border="1" width="100%" cellpadding="6">

    <!-- JUDUL -->
    <tr>
        <th colspan="6" bgcolor="#E6E6E6" align="center">
            <b>LAPORAN TRANSAKSI HARIAN</b><br>
            <b>CAFE LOVE STORY</b><br>
            <span>
                Tanggal <?= date('d-m-Y', strtotime($tanggal)) ?>
            </span>
        </th>
    </tr>

    <!-- HEADER -->
    <tr bgcolor="#4CAF50" align="center">
        <th>No</th>
        <th>Tanggal</th>
        <th>Menu</th>
        <th>Harga</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>

<?php 
$no = 1; 
while ($r = mysqli_fetch_assoc($data)) { 
?>
    <tr>
        <td align="center"><?= $no++ ?></td>
        <td align="center"><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
        <td><?= $r['nama_menu'] ?></td>
        <td align="right">Rp <?= number_format($r['harga']) ?></td>
        <td align="center"><?= $r['qty'] ?></td>
        <td align="right">Rp <?= number_format($r['subtotal']) ?></td>
    </tr>
<?php } ?>

    <!-- TOTAL -->
    <tr bgcolor="#D9EDF7" style="font-weight:bold;">
        <td colspan="5" align="center">TOTAL PENDAPATAN</td>
        <td align="right">Rp <?= number_format($total ?? 0) ?></td>
    </tr>

</table>