<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

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

<!DOCTYPE html>
<html>
<head>
<title>Laporan Transaksi Harian</title>
<style>
body{
    font-family:'Segoe UI',sans-serif;
    font-size:12px;
    margin:20px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:3px solid #000;
    padding-bottom:10px;
}

.header-left{
    line-height:1.5;
}

.header-left h2{
    margin:0;
    font-size:18px;
}

.header-right{
    font-size:11px;
    text-align:right;
}

.judul{
    /* background:#000;
    color:#fff; */
    text-align:center;
    padding:6px;
    margin:15px 0;
    font-weight:bold;
    letter-spacing:1px;
}

table{
    width:100%;
    border-collapse:collapse;
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

.total-row td{
    background:#0a8f2a;
    color:#fff;
    font-weight:bold;
}

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

.btn{
    margin-top:20px;
    text-align:center;
}

button{
    padding:8px 20px;
    font-size:12px;
    cursor:pointer;
}

@media print{
    button{ display:none; }
    body{ margin:0; }
    * {-webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;}
}
</style>
</head>
<body>

<!-- KOP LAPORAN -->
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

<!-- JUDUL -->
<div class="judul">
    LAPORAN TRANSAKSI HARIAN<br>
    TANGGAL <?= date('d-m-Y', strtotime($tanggal)) ?>
</div>

<table>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Menu</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>

<?php $no=1; while($r=mysqli_fetch_assoc($data)){ ?>
<tr>
    <td align="center"><?= $no++ ?></td>
    <td><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
    <td><?= $r['nama_menu'] ?></td>
    <td align="right">Rp <?= number_format($r['harga']) ?></td>
    <td align="center"><?= $r['qty'] ?></td>
    <td align="right">Rp <?= number_format($r['subtotal']) ?></td>
</tr>
<?php } ?>

<tr class="total-row">
    <td colspan="5" align="right">TOTAL PENDAPATAN</td>
    <td align="right">Rp <?= number_format($total ?? 0) ?></td>
</tr>
</table>

<!-- TANDA TANGAN -->
<div class="ttd">
    <div></div>
    <div>
        Disetujui Oleh,<br><br><br><br><br><br>
        <u>Muhammad Nur Ramdan</u>
        <br>Manager Cafe</br>
    </div>
</div>

<div class="btn">
    <button onclick="window.print()">🖨 Cetak</button>
</div>

</body>
</html>
