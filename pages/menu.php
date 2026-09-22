<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

//Search
$search = $_GET['search'] ?? '';
$searchSafe = mysqli_real_escape_string($koneksi, $search);
$searchSQL = $search ? "WHERE nama_menu LIKE '%$searchSafe%'" : '';

//Pagination
$limit   = 10;
$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$offset  = ($halaman - 1) * $limit;

//Total data
$totalData = mysqli_num_rows(
    mysqli_query($koneksi,"SELECT id_menu FROM menu $searchSQL")
);
$totalHalaman = ceil($totalData / $limit);

//Menu
$data = mysqli_query($koneksi,"
    SELECT * FROM menu
    $searchSQL
    ORDER BY nama_menu ASC
    LIMIT $limit OFFSET $offset
");

//notifikasi
$status = $_GET['status'] ?? '';
$alert = '';
if($status=='tambah'){
    $alert = '<div class="alert alert-success">Menu berhasil ditambahkan</div>';
}elseif($status=='update'){
    $alert = '<div class="alert alert-warning">Menu berhasil diperbarui</div>';
}elseif($status=='hapus'){
    $alert = '<div class="alert alert-danger">Menu berhasil dihapus</div>';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Menu | Cafe Love Story</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Menu</h4>
        <small class="text-muted">Kelola daftar menu Cafe Love Story</small>
    </div>
</div>
<?= $alert ?>

<!-- SEARCH -->
<form method="get" class="mb-3">
    <div class="d-flex justify-content-between align-items-center">

        <!-- SEARCH -->
        <div class="input-group" style="width:300px">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari menu..."
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">Cari</button>
        </div>

        <!-- TOMBOL TAMBAH -->
        <button type="button"
            class="btn btn-primary px-4"
            data-bs-toggle="modal"
            data-bs-target="#modalMenu"
            onclick="openTambah()">
            <i class="bi bi-plus-circle me-1"></i> Tambah Menu
        </button>

    </div>
</form>


<!-- ================= LIST MENU ================= -->
<div class="card shadow-sm menu-wrapper">

    <!-- HEADER -->
    <div class="px-4 py-3 menu-header d-flex">
        <div style="width:90px">Gambar</div>
        <div class="flex-grow-1">Nama Menu</div>
        <div style="width:150px">Harga</div>
        <div style="width:120px" class="text-center">Aksi</div>
    </div>

    <!-- BODY -->
    <?php if(mysqli_num_rows($data)>0): ?>
    <?php while($m=mysqli_fetch_assoc($data)): ?>
    <div class="px-4 py-3 d-flex align-items-center border-top menu-row">

        <!-- GAMBAR -->
        <div style="width:90px">
            <img src="../assets/img/menu/<?= $m['gambar'] ?? 'default.jpg' ?>"
                 width="55" height="55"
                 class="rounded object-fit-cover">
        </div>

        <!-- NAMA -->
        <div class="flex-grow-1 fw-medium">
            <?= $m['nama_menu'] ?>
        </div>

        <!-- HARGA -->
        <div style="width:150px" class="fw-bold text-success">
            Rp <?= number_format($m['harga']) ?>
        </div>

        <!-- AKSI -->
        <div style="width:120px" class="text-center">
            <button class="btn btn-sm btn-warning me-1"
                data-bs-toggle="modal"
                data-bs-target="#modalMenu"
                onclick="openEdit(
                    '<?= $m['id_menu'] ?>',
                    '<?= htmlspecialchars($m['nama_menu']) ?>',
                    '<?= $m['harga'] ?>'
                )">
                <i class="bi bi-pencil"></i>
            </button>

            <a href="../process/menu_proses.php?hapus=<?= $m['id_menu'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus menu ini?')">
                <i class="bi bi-trash"></i>
            </a>
        </div>

    </div>
    <?php endwhile; else: ?>
    <div class="p-4 text-center text-muted">
        Data tidak ditemukan
    </div>
    <?php endif; ?>
</div>

<!-- PAGINATION -->
<nav>
<ul class="pagination justify-content-center mt-4">
<li class="page-item <?= $halaman<=1?'disabled':'' ?>">
<a class="page-link"
href="?halaman=<?= $halaman-1 ?>&search=<?= urlencode($search) ?>">&laquo;</a>
</li>

<?php for($i=1;$i<=$totalHalaman;$i++): ?>
<li class="page-item <?= $i==$halaman?'active':'' ?>">
<a class="page-link"
href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>">
<?= $i ?>
</a>
</li>
<?php endfor; ?>

<li class="page-item <?= $halaman>=$totalHalaman?'disabled':'' ?>">
<a class="page-link"
href="?halaman=<?= $halaman+1 ?>&search=<?= urlencode($search) ?>">&raquo;</a>
</li>
</ul>
</nav>

</div>
<!-- CRUD -->
<div class="modal fade" id="modalMenu" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
<h5 class="modal-title" id="modalTitle">Tambah Menu</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form method="post" action="../process/menu_proses.php" enctype="multipart/form-data">
<div class="modal-body">

<input type="hidden" name="id_menu" id="id_menu">
<input type="hidden" name="aksi" id="aksi">

<div class="mb-3">
<label class="form-label">Nama Menu</label>
<input type="text" name="nama_menu" id="nama_menu" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Harga</label>
<input type="number" name="harga" id="harga" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Gambar</label>
<input type="file" name="gambar" class="form-control">
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button type="submit" class="btn btn-primary">Simpan</button>
</div>
</form>

</div>
</div>
</div>

<script>
function openTambah(){
    modalTitle.innerText='Tambah Menu';
    aksi.value='tambah';
    id_menu.value='';
    nama_menu.value='';
    harga.value='';
}
function openEdit(id,nama,hargaMenu){
    modalTitle.innerText='Edit Menu';
    aksi.value='update';
    id_menu.value=id;
    nama_menu.value=nama;
    harga.value=hargaMenu;
}
</script>

</body>
</html>
