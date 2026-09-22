<?php
include '../process/cek_login.php';
include '../config/koneksi.php';

$id = $_GET['id'];

$t = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi='$id'");
$transaksi = mysqli_fetch_assoc($t);

$d = mysqli_query($koneksi, "
    SELECT td.*, m.nama_menu
    FROM transaksi_detail td
    JOIN menu m ON td.id_menu = m.id_menu
    WHERE td.id_transaksi='$id'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran</title>
    <script>
    window.onload = function(){document.title = "Struk_<?= $id ?>_<?= date('Ymd_His', strtotime($transaksi['tanggal'])) ?>";}
    </script>
    <style>
        body{
            background:radial-gradient(circle at top, #e9f7ff 0%, #cfeeff 40%, #b6e3ff 100%);
            font-family:'Segoe UI',sans-serif;
            display:flex;
            justify-content:center;
            padding:30px;
        }

        .receipt{
            width:360px;
            background:#fff;
            border-radius:16px;
            box-shadow:0 15px 40px rgba(0,0,0,.15);
            overflow:hidden;
        }

        /* HEADER */
        .receipt-header{
            background:#e8f8f1;
            text-align:center;
            padding:25px 20px;
        }

        .receipt-header .check{
            width:50px;
            height:50px;
            border-radius:50%;
            border:3px solid #2ecc71;
            color:#2ecc71;
            display:flex;
            justify-content:center;
            align-items:center;
            margin:0 auto 10px;
            font-size:26px;
        }

        .receipt-header h4{
            margin:0;
            font-weight:700;
        }

        .receipt-header p{
            margin:3px 0 0;
            font-size:13px;
            color:#666;
        }

        /* BODY */
        .receipt-body{
            padding:20px;
            font-size:14px;
        }

        .info{
            display:flex;
            justify-content:space-between;
            margin-bottom:6px;
            color:#555;
        }

        hr{
            border:none;
            border-top:1px solid #eee;
            margin:15px 0;
        }

        .item{
            display:flex;
            justify-content:space-between;
            margin-bottom:6px;
        }

        .item small{
            color:#888;
        }

        /* TOTAL */
        .total-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:6px;
        }

        .total{
            font-weight:700;
            font-size:16px;
        }

        /* FOOTER BUTTON */
        .receipt-footer{
            padding:15px;
            display:flex;
            gap:10px;
        }

        .btn{
            flex:1;
            border:none;
            padding:10px;
            border-radius:10px;
            font-weight:600;
        }

        .btn-close{
            background:#e0e0e0;
        }

        .btn-print{
            background:#0d6efd;
            color:white;
        }

        @media print{
            body{background:#fff}
            .receipt-footer{display:none}
        }
    </style>
</head>
<div class="receipt">

    <div class="receipt-header">
        <div class="check">✓</div>
        <h4>Transaction Successful!</h4>
        <!-- <p>Invoice: INV-<?= date('Ymd') ?>-<?= $transaksi['id_transaksi'] ?></p> -->
    </div>

    <div class="receipt-body">

        <div class="info">
            <span>Date</span>
            <span><?= date('d M Y, H:i', strtotime($transaksi['tanggal'])) ?></span>
        </div>

        <!-- <div class="info">
            <span>Cashier</span>
            <span>Kasir</span>
        </div> -->

        <div class="info">
            <span>Payment</span>
            <span>Tunai</span>
        </div>

        <hr>

        <?php while ($r = mysqli_fetch_assoc($d)) { ?>
        <div class="item">
            <div>
                <?= $r['nama_menu'] ?><br>
                <small>Rp <?= number_format($r['subtotal'] / $r['qty']) ?> × <?= $r['qty'] ?></small>
            </div>
            <div>Rp <?= number_format($r['subtotal']) ?></div>
        </div>
        <?php } ?>

        <hr>

        <div class="total-row">
            <span>Subtotal</span>
            <span>Rp <?= number_format($transaksi['total']) ?></span>
        </div>

        <!-- <div class="total-row">
            <span>Tax (11%)</span>
            <span>Rp <?= number_format($transaksi['total'] * 0.11) ?></span>
        </div> -->

        <div class="total-row">
            <span>Bayar</span>
            <span>Rp <?= number_format($transaksi['bayar']) ?></span>
        </div>

        <div class="total-row">
            <span>Kembali</span>
            <span>Rp <?= number_format($transaksi['kembali']) ?></span>
        </div>

        <div class="total-row total">
            <span>Total</span>
            <span>Rp <?= number_format($transaksi['total']) ?></span>
        </div>
    </div>

    <div class="receipt-footer">
        <button class="btn btn-close" onclick="window.location='../index.php'">
            Transaksi Baru
        </button>
        <button class="btn btn-print" onclick="cetakStruk()">
            🖨 Cetak
        </button>
        <script>
            function cetakStruk(){window.print();}
        </script>
    </div>

</div>


</body>
</html>
