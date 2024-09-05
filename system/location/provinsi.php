<?php
// RAJA ONGKIR PROVINSI
$curl = curl_init();
// curl_setopt_array($curl, array(
//     CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => "",
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 30,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => "GET",
//     CURLOPT_HTTPHEADER => array(
//         "key: $rajaongkir_key"
//     ),
// ));

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province=22",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "key: $rajaongkir_key"
    ),
));
$res_city = curl_exec($curl);
$err_city = curl_error($curl);
curl_close($curl);
if ($err_city) {
    echo "cURL Error #:" . $err_city;
} else {
    $city_data = json_decode($res_city, true);
    $city_isi_data = $city_data['rajaongkir']['results'];
}
