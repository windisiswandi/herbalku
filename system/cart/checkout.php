<?php
include '../../config.php';

// $iduser = $_POST['id_user'];
$total_harga = $_POST['total_harga'];
$berat_barang = $_POST['total_berat'];
$time = date('Y-m-d H:i:s');
$tipe_progress = 'Belum Bayar';
$invoice_id = 0;
$kurir = 'jne';
$id_kurir = 0;

// SELECT LOKASI USER
$lokasi_user = $server->query("SELECT * FROM `lokasi_user` WHERE `id_user`='$iduser'");
$lokasi_user_data = mysqli_fetch_assoc($lokasi_user);

if ($lokasi_user_data) {
    $prov_inv = $lokasi_user_data['id_provinsi'] . ',' . $lokasi_user_data['provinsi'];
    $kota_inv = $lokasi_user_data['id_kota'] . ',' . $lokasi_user_data['kota'];
    $alengkap_inv = $lokasi_user_data['alamat_lengkap'];

    $kota_tujuan = $lokasi_user_data['id_kota'];
    // JNE
    $curl_jne = curl_init();
    curl_setopt_array($curl_jne, array(
        CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "origin=$kota_id_toko&destination=$kota_tujuan&weight=$berat_barang&courier=$kurir",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: $rajaongkir_key"
        ),
    ));
    $response_cost_jne = curl_exec($curl_jne);
    $err_cost_jne = curl_error($curl_jne);
    curl_close($curl_jne);
    if ($err_cost_jne) {
        echo "cURL Error #:" . $err_cost_jne;
    } else {
        $data_cost_jne = json_decode($response_cost_jne, true);
        $data_cost_jne_arr = $data_cost_jne['rajaongkir']['results'][$id_kurir];
        $kurir_ongkir = $data_cost_jne_arr['code'];
        $kurir_layanan_ongkir = $data_cost_jne_arr['costs'][$id_kurir]['service'];
        $etd_ongkir = $data_cost_jne_arr['costs'][$id_kurir]['cost'][$id_kurir]['etd'];
        $harga_ongkir =  $data_cost_jne_arr['costs'][$id_kurir]['cost'][$id_kurir]['value'];
    }
    $insert_checkout = $server->query("INSERT INTO `invoice` (`id_user`, `total_harga`, `kurir`, `id_kurir`, `layanan_kurir`, `etd`, `harga_ongkir`, `provinsi`, `kota`, `alamat_lengkap`, `waktu`, `tipe_progress`) VALUES ($iduser, $total_harga, '$kurir_ongkir', $id_kurir, '$kurir_layanan_ongkir', '$etd_ongkir', $harga_ongkir, '$prov_inv', '$kota_inv', '$alengkap_inv', '$time', '$tipe_progress')");
    if (!$insert_checkout) {
        echo "Error: " . $server->error;
        die;
    }
} else {
    $insert_checkout = $server->query("INSERT INTO `invoice`(`id_user`, `total_harga`, `kurir`, `id_kurir`, `waktu`, `tipe_progress`) VALUES ($iduser, $total_harga, '$kurir', $id_kurir, '$time', '$tipe_progress')");
    if (!$insert_checkout) {
        echo "Error: " . $server->error;
        die;
    }
    
}
$invoice_id = $server->insert_id;

// SELECT CART
$select_cart = $server->query("SELECT * FROM `keranjang`, `iklan` where keranjang.id_user=$iduser and keranjang.id_iklan=iklan.id");

// input ke invoice_item
while ($cart_data = mysqli_fetch_assoc($select_cart)) {
    $stock_produk_update = $cart_data['stok'] - $cart_data['jumlah'];
    $invoice_item = $server->query("INSERT INTO invoice_item (idinvoice, id_iklan, qty) VALUES ($invoice_id, {$cart_data['id_iklan']}, {$cart_data['jumlah']})");
    $stock_item = $server->query("UPDATE iklan SET stok=$stock_produk_update where id={$cart_data['id_iklan']}");
}

$delete_cart_ck = $server->query("DELETE FROM `keranjang` WHERE `id_user`='$iduser'");

?>

<script>
    window.location.href = "<?php echo $url; ?>checkout/detail/<?php echo $invoice_id; ?>";
</script>