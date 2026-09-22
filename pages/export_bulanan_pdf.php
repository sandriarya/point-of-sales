<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

/* ================= VALIDASI BULAN ================= */
if (!isset($_GET['bulan'])) {
    die('Bulan tidak valid');
}

// Format: YYYY-MM
$bulanInput = $_GET['bulan'];
[$tahun, $bulan] = explode('-', $bulanInput);

$tgl_awal  = "$tahun-$bulan-01";
$tgl_akhir = date('Y-m-t', strtotime($tgl_awal));

/* ================= DATA LAPORAN HARIAN ================= */
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

/* ================= DATA TOP 10 MENU ================= */
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
?>

<!DOCTYPE html>
<html>
<head>
<title>Laporan Bulanan</title>
<style>
body{
    font-family:'Segoe UI',sans-serif;
    font-size:12px;
    margin:20px;
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:3px solid #000;
    padding-bottom:10px;
}
.header-left h2{
    margin:0;
    font-size:18px;
}
.header-left{
    line-height:1.5;
}
.header-right{
    font-size:11px;
    text-align:right;
}

/* ===== JUDUL ===== */
.judul{
    text-align:center;
    margin:15px 0;
    font-weight:bold;
    letter-spacing:1px;
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
th, td{
    border:1px solid #000;
    padding:6px;
}
th{
    background:#0a8f2a;
    color:#fff;
    text-align:center;
}
td{
    vertical-align:top;
}
.text-center{ text-align:center; }
.text-right{ text-align:right; }

.total-row td{
    background:#0a8f2a;
    color:#fff;
    font-weight:bold;
}

/* ===== TTD ===== */
.ttd{
    width:100%;
    margin-top:40px;
    display:flex;
    justify-content:space-between;
}
.ttd div{
    width:30%;
    text-align:center;
}

/* ===== BUTTON ===== */
.btn{
    margin-top:20px;
    text-align:center;
}
button{
    padding:8px 20px;
    font-size:12px;
    cursor:pointer;
}

/* ===== PRINT ===== */
@media print{
    button{ display:none; }
    body{ margin:0; }
    *{
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
</head>
<body>

<!-- ===== KOP ===== -->
<div class="header">
    <div class="header-left">
        <h2>CAFE LOVE STORY</h2>
        Alamat: Jl. Gatot Subroto No. 108, Bandung<br>
        Telp: 0821-1782-1369
    </div>
    <div class="header-right">
        Tanggal Cetak: <?= date('d-m-Y') ?>
    </div>
</div>

<!-- ===== JUDUL ===== -->
<div class="judul">
    LAPORAN BULANAN<br>
    BULAN <?= date('F Y', strtotime($tgl_awal)) ?>
</div>

<!-- ===== LAPORAN HARIAN ===== -->
<table>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Jumlah Transaksi</th>
    <th>Total Pendapatan</th>
</tr>

<?php
$no = 1;
$grand_total = 0;

if ($data && mysqli_num_rows($data) > 0) {
    while ($r = mysqli_fetch_assoc($data)) {
        $grand_total += $r['total_pendapatan'];
?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td class="text-center"><?= date('d-m-Y', strtotime($r['tgl'])) ?></td>
    <td class="text-center"><?= $r['jumlah_transaksi'] ?></td>
    <td class="text-right">Rp <?= number_format($r['total_pendapatan']) ?></td>
</tr>
<?php
    }
} else {
?>
<tr>
    <td colspan="4" class="text-center">Tidak ada data transaksi</td>
</tr>
<?php } ?>

<tr class="total-row">
    <td colspan="3" class="text-right">TOTAL BULANAN</td>
    <td class="text-right">Rp <?= number_format($grand_total) ?></td>
</tr>
</table>

<!-- ===== TOP MENU ===== -->
<div class="judul" style="margin-top:25px;">
    TOP 10 MENU TERLARIS
</div>

<table>
<tr>
    <th>No</th>
    <th>Nama Menu</th>
    <th>Total Terjual</th>
</tr>

<?php
$no = 1;
if ($topMenu && mysqli_num_rows($topMenu) > 0) {
    while ($m = mysqli_fetch_assoc($topMenu)) {
?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $m['nama_menu'] ?></td>
    <td class="text-center"><?= $m['total_qty'] ?></td>
</tr>
<?php
    }
} else {
?>
<tr>
    <td colspan="3" class="text-center">Tidak ada data</td>
</tr>
<?php } ?>
</table>

<!-- ===== TTD ===== -->
<div class="ttd">
    <div></div>
    <div>
        Disetujui Oleh,<br><br><br><br>
        <u>Muhammad Nur Ramdan</u><br>
        Manager Cafe
    </div>
</div>

<div class="btn">
    <button onclick="window.print()">🖨 Cetak</button>
</div>

</body>
</html>
