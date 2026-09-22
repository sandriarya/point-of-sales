<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

/* =========================
   FILTER BULAN AKTIF
========================= */
$bulanAktif = $_GET['bulan'] ?? date('Y-m');
[$tahun, $bulan] = explode('-', $bulanAktif);

/* =========================
   RINGKASAN
========================= */
$hariIni = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(total) total
    FROM transaksi
    WHERE DATE(tanggal)=CURDATE()
"))['total'];

$mingguIni = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(total) total
    FROM transaksi
    WHERE YEARWEEK(tanggal,1)=YEARWEEK(CURDATE(),1)
"))['total'];

$bulanIni = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(total) total
    FROM transaksi
    WHERE YEAR(tanggal)='$tahun'
      AND MONTH(tanggal)='$bulan'
"))['total'];

/* =========================
   DATA GRAFIK (HARIAN / BULAN)
========================= */
$grafik = mysqli_query($koneksi,"
    SELECT DATE(tanggal) tgl, SUM(total) total
    FROM transaksi
    WHERE YEAR(tanggal)='$tahun'
      AND MONTH(tanggal)='$bulan'
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal)
");

/* =========================
   TOP 10 MENU TERLARIS
========================= */
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
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Bulanan | Cafe Love Story</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="container-fluid py-4">

<!-- ================= HEADER ================= -->
<!-- <h4 class="fw-bold">Laporan Pendapatan</h4>
<p class="text-muted mb-4">Rekap pendapatan Cafe Love Story</p> -->

<!-- ================= RINGKASAN ================= -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <small>Pendapatan</small>
                <h6 class="fw-bold">Hari Ini</h6>
                <h3>Rp <?= number_format($hariIni ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <small>Pendapatan</small>
                <h6 class="fw-bold">Minggu Ini</h6>
                <h3>Rp <?= number_format($mingguIni ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <small>Pendapatan</small>
                <h6 class="fw-bold">Bulan Ini</h6>
                <h3>Rp <?= number_format($bulanIni ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- ================= FILTER + EXPORT ================= -->
<form method="get" class="mb-4">
<div class="row g-2 align-items-end">
    <div class="col-md-3">
        <label class="fw-bold">Pilih Bulan</label>
        <input type="month" name="bulan" class="form-control" value="<?= $bulanAktif ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">Tampilkan</button>
    </div>
    <div class="col-md-2">
        <a href="export_bulanan.php?bulan=<?= $bulanAktif ?>"
           class="btn btn-success w-100">
           <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
    </div>
    <div class="col-md-2">
    <a href="export_bulanan_pdf.php?bulan=<?= $bulanAktif ?>"
       class="btn btn-danger w-100">
       <i class="bi bi-file-earmark-pdf me-1"></i>PDF
    </a>
</div>
</div>
</form>

<!-- ================= GRAFIK & TOP MENU ================= -->
<div class="row g-3">

<!-- GRAFIK -->
<div class="col-md-8">
    <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
            <h6 class="fw-bold mb-3">Grafik Pendapatan Harian (Bulanan)</h6>

            <!-- wrapper canvas fleksibel -->
            <div class="flex-grow-1">
                <canvas id="grafikBulanan"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TOP MENU -->
<div class="col-md-4">
<div class="card shadow-sm h-100">
<div class="card-body p-0">
<h6 class="fw-bold px-3 pt-3 mb-2">Top 10 Menu Terlaris Bulan Ini</h6>

<table class="table table-sm table-bordered mb-0">
<thead class="table-light text-center">
<tr>
    <th>No</th>
    <th>Menu</th>
    <th>Qty</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($m = mysqli_fetch_assoc($topMenu)){
?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $m['nama_menu'] ?></td>
    <td class="text-center fw-bold"><?= $m['total_qty'] ?></td>
</tr>
<?php } ?>

<?php if(mysqli_num_rows($topMenu) == 0){ ?>
<tr>
    <td colspan="3" class="text-center text-muted">
        Tidak ada data
    </td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>
</div>

</div>
</div>

<!-- ================= SCRIPT GRAFIK ================= -->
<script>
const ctx = document.getElementById('grafikBulanan');

new Chart(ctx,{
    type:'line',
    data:{
        labels:[
            <?php
            while($g=mysqli_fetch_assoc($grafik)){
                echo "'".date('d',strtotime($g['tgl']))."',";
            }
            ?>
        ],
        datasets:[{
            data:[
                <?php
                mysqli_data_seek($grafik,0);
                while($g=mysqli_fetch_assoc($grafik)){
                    echo $g['total'].",";
                }
                ?>
            ],
            borderWidth:2,
            tension:0.35,
            pointRadius:4,
            fill:false
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{ display:false }
        },
        scales:{
            y:{ beginAtZero:true }
        }
    }
});
</script>

</body>
</html>
