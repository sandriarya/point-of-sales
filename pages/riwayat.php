<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

/* ==========================
   TANGGAL AKTIF (FILTER)
========================== */
$tanggalAktif = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$judul = "Riwayat Transaksi Tanggal " . date('d-m-Y', strtotime($tanggalAktif));

/* ==========================
   PENDAPATAN HARIAN
========================== */
$hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(total) total
    FROM transaksi
    WHERE DATE(tanggal)='$tanggalAktif'
"))['total'];

/* ==========================
   DATA DETAIL TRANSAKSI
========================== */
$data = mysqli_query($koneksi,"
    SELECT 
        t.tanggal,
        m.nama_menu,
        d.harga,
        d.qty,
        d.subtotal
    FROM transaksi_detail d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE DATE(t.tanggal)='$tanggalAktif'
    ORDER BY t.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Transaksi | Cafe Love Story</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
<div class="container-fluid py-4">

<!-- <div class="mb-4">
    <h4 class="fw-bold mb-1">Riwayat Transaksi Harian</h4>
    <small class="text-muted">Data transaksi berdasarkan tanggal</small>
</div> -->

<div class="row g-4 mb-4">

    <!-- KIRI : PENDAPATAN -->
    <div class="col-md-4">
        <div class="card shadow text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6 class="text-muted mb-2">Pendapatan Tanggal</h6>
                <h3 class="fw-bold text-dark mb-1">
                    Rp <?= number_format($hari_ini ?? 0) ?>
                </h3>
                <small class="text-muted">
                    <?= date('d-m-Y', strtotime($tanggalAktif)) ?>
                </small>
            </div>
        </div>
    </div>

    <!-- KANAN : FILTER -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <form method="get" class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="<?= $tanggalAktif ?>"
                               required>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="export_excel.php?tanggal=<?= $tanggalAktif ?>"
                           class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="export_pdf.php?tanggal=<?= $tanggalAktif ?>"
                           target="_blank"
                           class="btn btn-danger w-100">
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>


<!-- TABEL -->
<div class="card shadow">
<div class="card-body table-responsive">

<h5 class="fw-bold mb-3"><?= $judul ?></h5>

<table class="table table-striped table-bordered align-middle table-data">
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Nama Menu</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Subtotal</th>
</tr>
</thead>
<tbody>

<?php
$no = 1;
while($r = mysqli_fetch_assoc($data)){
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
    <td><?= $r['nama_menu'] ?></td>
    <td>Rp <?= number_format($r['harga']) ?></td>
    <td><?= $r['qty'] ?></td>
    <td>Rp <?= number_format($r['subtotal']) ?></td>
</tr>
<?php } ?>

</tbody>
</table>

<div class="alert alert-success fw-bold">
💰 Total Pendapatan: Rp <?= number_format($hari_ini ?? 0) ?>
</div>

</div>
</div>

</div>
</body>
</html>
