<?php
include 'process/cek_login.php';
include 'config/koneksi.php';

$tanggal = date('Y-m-d');

// Statistik
$q_menu = mysqli_query($koneksi,"SELECT COUNT(*) total FROM menu");
$menu = mysqli_fetch_assoc($q_menu);

$q_trx = mysqli_query($koneksi,"
    SELECT COUNT(*) total_trx, SUM(total) omzet
    FROM transaksi
    WHERE DATE(tanggal)='$tanggal'
");
$trx = mysqli_fetch_assoc($q_trx);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kasir Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
<?php include 'pages/navbar.php'; ?>
<div class="container-fluid kasir-container">

<div class="row g-3 kasir-grid">

    <!-- KIRI : KERANJANG -->
<div class="col-md-4">
    <div class="card shadow cart-sticky d-flex flex-column">

        <div class="card-header fw-bold text-center">
            Keranjang
        </div>

        <!-- LIST PESANAN (ATAS) -->
        <div class="cart-items flex-grow-1">
            <table class="table table-sm table-borderless mb-0">
                <thead class="cart-head">
                    <tr>
                        <th>Menu</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="cart"></tbody>
            </table>
        </div>

        <!-- FOOTER (BAWAH, STICKY) -->
        <div class="cart-footer">

            <div class="d-flex justify-content-between mb-1">
                <span>Total</span>
                <strong>Rp <span id="total">0</span></strong>
            </div>

            <select id="pembayaran" class="form-control form-control-sm mb-2"
                onchange="cekPembayaran()">
                <option value="tunai">Tunai</option>
                <option value="qris">QRIS</option>
            </select>

            <div id="tunai-box">
                <input type="number" id="bayar"
                    class="form-control form-control-sm mb-2"
                    placeholder="Uang Bayar"
                    onkeyup="hitungKembali()">

                <input type="text" id="kembali"
                    class="form-control form-control-sm mb-2"
                    placeholder="Kembalian" readonly>
            </div>

            <form method="post" action="process/transaksi_proses.php" onsubmit="return validasiBayar()">
                <input type="hidden" name="cart" id="cart_data">
                <input type="hidden" name="total" id="total_input">
                <input type="hidden" name="bayar" id="bayar_form">
                <input type="hidden" name="pembayaran" id="pembayaran_form">

                <!-- PESAN ERROR -->
                <div id="error-bayar" class="text-danger text-center mb-2" style="display:none;">
                    Uang tidak cukup
                </div>

                <button class="btn btn-bayar w-100 mt-2">
                    BAYAR
                </button>
            </form>

        </div>
    </div>
</div>


<!-- KANAN -->
<div class="col-md-8">
    <div class="right-column">

        <!-- STATISTIK -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card shadow text-center stat-card">
                    <div class="card-body">
                        <div class="fw-semibold">Transaksi Hari Ini</div>
                        <h3><?= $trx['total_trx'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow text-center stat-card">
                    <div class="card-body">
                        <div class="fw-semibold">Omzet Hari Ini</div>
                        <h3>Rp <?= number_format($trx['omzet'] ?? 0) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEARCH -->
        <input type="text" id="search" class="form-control mb-3"
            placeholder="Cari Menu..." onkeyup="filterMenu()">

        <!-- MENU SCROLL (INI KUNCINYA) -->
        <div class="menu-scroll row g-3 flex-grow-1">
            <?php
            $q = mysqli_query($koneksi,"SELECT * FROM menu");
            while($m=mysqli_fetch_assoc($q)){
            ?>
            <div class="col-md-3 menu-item"
                data-nama="<?= strtolower($m['nama_menu']) ?>">
                <div class="card card-menu"
                    onclick="tambahMenu(
                        <?= $m['id_menu'] ?>,
                        '<?= $m['nama_menu'] ?>',
                        <?= $m['harga'] ?>
                    )">
                    <img src="assets/img/menu/<?= $m['gambar'] ?? 'default.jpg' ?>">
                    <div class="card-body text-center p-2">
                        <h6 class="mb-1"><?= $m['nama_menu'] ?></h6>
                        <small>Rp <?= number_format($m['harga']) ?></small>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
</div>


<script>
let cart = [];

function tambahMenu(id,nama,harga){
    let item = cart.find(i => i.id_menu == id);
    if(item){
        item.qty++;
        item.subtotal = item.qty * item.harga;
    }else{
        cart.push({
            id_menu:id,
            nama:nama,
            harga:harga,
            qty:1,
            subtotal:harga
        });
    }
    render();
}

function hapus(i){
    cart.splice(i,1);
    render();
}

function render(){
    let html='', total=0;
    cart.forEach((c,i)=>{
        total+=c.subtotal;
        html+=`
        <tr>
            <td>${c.nama}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-secondary"onclick="kurangQty(${i})">−</button>
                <span class="mx-1">${c.qty}</span>
                <button class="btn btn-sm btn-secondary"onclick="tambahQty(${i})">+</button>
            </td>
            <td>${c.subtotal.toLocaleString()}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="hapus(${i})">x</button>
            </td>
        </tr>`;
    });

    cart_data.value = JSON.stringify(cart);
    total_input.value = total;
    pembayaran_form.value = pembayaran.value;

    document.getElementById('cart').innerHTML = html;
    document.getElementById('total').innerText = total.toLocaleString();
    hitungKembali();
}

function tambahQty(i){
    cart[i].qty++;
    cart[i].subtotal = cart[i].qty * cart[i].harga;
    render();
}

function kurangQty(i){
    if(cart[i].qty > 1){
        cart[i].qty--;
        cart[i].subtotal = cart[i].qty * cart[i].harga;
    }else{
        cart.splice(i,1); // hapus item kalau qty = 1
    }
    render();
}

function hitungKembali(){
    let bayar = document.getElementById('bayar').value || 0;
    kembali.value = bayar - total_input.value;
    bayar_form.value = bayar;

    // Sembunyikan pesan jika sudah cukup
    if(bayar >= total_input.value){
        document.getElementById('error-bayar').style.display = 'none';
    }
}

function validasiBayar(){
    let metode = pembayaran.value;
    let total  = parseInt(total_input.value) || 0;
    let bayar  = parseInt(document.getElementById('bayar').value) || 0;
    let errorBox = document.getElementById('error-bayar');

    // Jika metode tunai, cek uang cukup atau tidak
    if(metode === 'tunai'){
        if(bayar < total){
            errorBox.style.display = 'block';
            return false; // batalkan submit
        }
    }

    // Jika cukup atau QRIS
    errorBox.style.display = 'none';
    return true;
}

function cekPembayaran(){
    let metode = pembayaran.value;
    pembayaran_form.value = metode;
    document.getElementById('tunai-box').style.display =
        metode === 'tunai' ? 'block' : 'none';
}

function filterMenu(){
    let keyword = search.value.toLowerCase();
    document.querySelectorAll('.menu-item').forEach(item=>{
        item.style.display =
            item.getAttribute('data-nama').includes(keyword) ? 'block' : 'none';
    });
}

cekPembayaran();
</script>

</body>
</html>