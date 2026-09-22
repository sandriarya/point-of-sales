<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

if (!isset($_GET['bulan'])) {
    die('Bulan tidak valid');
}

$bulanInput = $_GET['bulan'];
[$tahun, $bulan] = explode('-', $bulanInput);

$tgl_awal  = "$tahun-$bulan-01";
$tgl_akhir = date('Y-m-t', strtotime($tgl_awal));

$data = mysqli_query($koneksi,"
    SELECT
        DATE(tanggal) AS tgl,
        COUNT(id_transaksi) AS jumlah_transaksi,
        SUM(total) AS total_pendapatan
    FROM transaksi
    WHERE DATE(tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    GROUP BY DATE(tanggal)
    ORDER BY tgl ASC
");

$topMenu = mysqli_query($koneksi,"
    SELECT m.nama_menu, SUM(d.qty) total_qty
    FROM transaksi_detail d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE YEAR(t.tanggal)='$tahun'
      AND MONTH(t.tanggal)='$bulan'
    GROUP BY d.id_menu
    ORDER BY total_qty DESC
    LIMIT 10
");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Bulanan_{$tahun}_{$bulan}.xls");
?>

<table border="1" width="100%" cellpadding="6">

    <!-- JUDUL -->
    <tr>
        <th colspan="4" bgcolor="#E6E6E6" align="center">
            <b>LAPORAN BULANAN CAFE LOVE STORY</b><br>
            <?= date('F Y', strtotime($tgl_awal)) ?>
        </th>
    </tr>

    <!-- HEADER -->
    <tr bgcolor="#4CAF50">
        <th>No</th>
        <th>Tanggal</th>
        <th>Jumlah Transaksi</th>
        <th>Total Pendapatan</th>
    </tr>

<?php
$no = 1;
$grand_total = 0;
while ($r = mysqli_fetch_assoc($data)) {
    $grand_total += $r['total_pendapatan'];
?>
    <tr>
        <td align="center"><?= $no++ ?></td>
        <td align="center"><?= date('d-m-Y', strtotime($r['tgl'])) ?></td>
        <td align="center"><?= $r['jumlah_transaksi'] ?></td>
        <td align="right">Rp <?= number_format($r['total_pendapatan']) ?></td>
    </tr>
<?php } ?>

    <!-- TOTAL -->
    <tr bgcolor="#D9EDF7">
        <th colspan="3" align="right">TOTAL BULANAN</th>
        <th align="right">Rp <?= number_format($grand_total) ?></th>
    </tr>

    <!-- SPASI -->
    <tr><td colspan="4"></td></tr>

    <!-- JUDUL TOP MENU -->
    <tr bgcolor="#E6E6E6">
        <th colspan="4" align="center"><b>TOP 10 MENU TERLARIS</b></th>
    </tr>

    <!-- HEADER TOP MENU -->
    <tr bgcolor="#4CAF50">
        <th>No</th>
        <th colspan="2">Nama Menu</th>
        <th>Total Terjual</th>
    </tr>

<?php
$no = 1;
if (mysqli_num_rows($topMenu) > 0) {
    while ($m = mysqli_fetch_assoc($topMenu)) {
?>
    <tr>
        <td align="center"><?= $no++ ?></td>
        <td colspan="2"><?= $m['nama_menu'] ?></td>
        <td align="center"><?= $m['total_qty'] ?></td>
    </tr>
<?php
    }
} else {
?>
    <tr>
        <td colspan="4" align="center">Tidak ada data menu</td>
    </tr>
<?php } ?>

</table>
