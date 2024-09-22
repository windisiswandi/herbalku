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
    $sql = "SELECT invoice.*, invoice_item.*, akun.*, akun.id as akun_id, iklan.id as iklan_id, iklan.*
            FROM invoice
            JOIN invoice_item ON invoice_item.idinvoice = invoice.idinvoice
            JOIN akun ON akun.id = invoice.id_user
            JOIN iklan ON iklan.id = invoice_item.id_iklan
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
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
    <link rel="stylesheet" href="../../assets/css/admin/index.css">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

</head>

<style>
    /* Styling untuk card */
.card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Styling untuk header card */
.card-header {
    background-color: #007bff;
    color: #fff;
    padding: 15px;
    font-size: 16px;
    font-weight: bold;
}

/* Styling untuk card-body */
.card-body {
    padding: 20px;
    background-color: #ffffff;
    border-top: 1px solid #e0e0e0;
    box-sizing: border-box;
}

/* Styling untuk tombol di card-body */
.card-body .btn {
    margin-top: 10px;
}

/* Styling untuk form */
form.col-md-7 {
    padding: 0; /* Hilangkan padding di sekitar form */
}

/* Styling untuk row dalam form */
.row.align-items-center {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    box-sizing: border-box;
    margin-bottom: 15px; /* Memberi jarak antar elemen */
}

/* Styling untuk label */
.col-form-label {
    font-weight: 500; /* Tebalkan teks label */
    font-size: 16px; /* Sesuaikan ukuran teks */
    color: #333; /* Warna teks label */
}

/* Styling untuk input type date */
.form-control {
    width: 100%;
    padding: 8px 12px; /* Beri padding yang nyaman */
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
}

/* Styling untuk tombol submit */
button.btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
    width: 130px; /* Lebar tombol sesuai dengan gambar */
    display: inline-block;
}

button.btn:hover {
    background-color: #0056b3;
}

/* Styling untuk tombol di form */
button[type="submit"] {
    margin-top: 15px; /* Jarak tombol dari form */
}

.row > * {
    width: 100%;
    box-sizing: border-box;
}

/* Responsive styling untuk kolom */
@media (min-width: 576px) {
    .col-sm-4 {
        width: 33.33333333%;
        flex: 0 0 auto;
    }

    .col-md-7 {
        flex: 0 0 auto;
        width: 58.33333333%;
    }
    
    .col-sm-8 {
        width: 66.6666667%;
        flex: 0 0 auto;
    }

    button[type="submit"] {
        width: 100%; /* Tombol lebar penuh pada layar kecil */
    }
}


/* Styling untuk tabel */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #007bff;
    color: white;
}

table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

table td {
    font-size: 14px;
    color: #333;
}

table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Text styling */
.text-uppercase {
    text-transform: uppercase;
}

.text-capitalize {
    text-transform: capitalize;
}

</style>

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
                        <table id="basic-datatables" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No Order</th>
                                    <th>Nama Penerima</th>
                                    <th>Tanggal Order</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
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
                                            <?= $order['judul']; ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo "Rp ".number_format($order['harga'], 0, ".", "."); ?>
                                        </td>
                                        <td><?= $order['qty']; ?></td>
                                        <td><?= "Rp ".number_format($order['harga']*$order['qty'], 0, ".", ".");; ?></td>
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
    <!-- <script src="../../assets/vendor/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/vendor/datatables/datatables.min.js"></script> -->
    <script>
        new DataTable('#basic-datatables', {
            layout: {
                topStart: {
                    buttons: ['excel', 'pdf', 'print']
                }
            }
        });
    </script>
</body>
</html>