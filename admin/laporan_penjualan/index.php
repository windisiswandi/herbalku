<?php
include '../../config.php';

$page_admin = 'laporan_penjualan';

if (isset($_COOKIE['login_admin'])) {
    if ($akun_adm == 'false') {
        header("location: " . $url . "system/admin/logout");
    }
} else {
    header("location: " . $url . "admin/login/");
}

 if (isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])) {
    $sql = "SELECT invoice.*, akun.*, akun.id as akun_id, iklan.id as iklan_id, iklan.*
            FROM invoice
            JOIN akun ON akun.id = invoice.id_user
            JOIN iklan ON iklan.id = invoice.id_iklan
            WHERE invoice.waktu >= '$_GET[tgl_awal]'
            AND invoice.waktu <= '$_GET[tgl_akhir]'";
    $orders = [];
    $exec = $server->query($sql);
    while ($order = mysqli_fetch_assoc($exec)) {
        $orders[] = $order;
    }

    // var_dump($orders); die;
 }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="../../assets/icons/<?php echo $logo; ?>" type="image/svg">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/plugins.min.css">
    <link rel="stylesheet" href="../../assets/css/admin/index.css">
    <!-- script -->
    <script src="../../assets/vendor/jquery.min.js"></script>
</head>

<body>
    <div class="admin">
        <?php include '../partials/menu.php'; ?>
        <div class="content_admin">
            <div class="card">
                <div class="card-header">
                    <h4>Laporan Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-5">
                        <form class="col-md-7">
                            <div class="row align-items-center mb-2">
                                <label class="col-sm-4 col-form-label">Dari Tanggal</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control " name="tgl_awal" value="<?= @$_GET['tgl_awal']; ?>" required>
                                </div>
                            </div>
                            <div class="row align-items-center mb-2">
                                <label class="col-sm-4 col-form-label">Sampai Tanggal</label>
                                <div class="col-sm-8">
                                    <input type="date" class=" form-control" name="tgl_akhir" value="<?= @$_GET['tgl_akhir']; ?>" required>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3">Terapkan</button>
                        </form>
                    </div>
                    <?php if(isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])): ?>
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No Order</th>
                                    <th>Nama Penerima</th>
                                    <th>Tanggal Order</th>
                                    <th>Produk</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($orders)) : ?>
                                    <?php 
                                    $totals = 0;
                                        foreach($orders as $order) : ?>
                                    <tr>
                                        <td>#<?= $order['idinvoice']; ?></td>
                                        <td class="text-uppercase"><?= $order['nama_lengkap']; ?></td>
                                        <td>
                                            <?= $order['waktu']; ?>
                                        </td>
                                        <td>
                                            <div class="box_judul_ic">
                                                <h1><?= $order['judul']; ?></h1>
                                                <p class="mt-1">Harga <span><?= "Rp". number_format($order['harga'], 0, ".", "."); ?></span></p>
                                                <p>Total Produk <span><?= $order['jumlah']; ?></span></p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            echo "Rp ".number_format($order['jumlah']*$order['harga'], 0, ".", "."); ?>
                                        </td>
                                        <td class="text-capitalize">
                                            <?= $order['tipe_progress']; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                            
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </div>
    <link rel="stylesheet" href="../../assets/js/bootstrap.min.js">
    <script src="../../assets/vendor/datatables/datatables.min.js"></script>
    <script>
        $("#basic-datatables").DataTable({});
    </script>
</body>
</html>