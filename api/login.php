<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

require './base.php';

use \Firebase\JWT\JWT;
$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgEi+xrdnz3fs0gP5ORWAy4Aw4ZyKAs81/ncLobJb1VS9kym86kbm
qnBWt+xLO6HAFvBvj4ciBLK+PoDewdnt9+AuesobkBQfyOVuinTzvdaAQbS5uYML
77/MaXKwzOmFvzAVIIsyM9BHSi1/lYOl2vlLPrBOcwrCyX9PEkWgQRy5AgMBAAEC
gYAPvQ67MX+Gf7s0VuBN0a61jl3Rg152PEVQtjiGoS23hshnGFNLga5QXcKvIdvF
9AxCerB/2RFRJq3ZLdic8MpXAEp9nbqxNzrv5d/MjToyzUn5/xaSO+UJDajH/Gx0
d0bSoizl7AK7eGfFXFIswUf2DedCnBQasjztm748K4tf3QJBAIkKVOr77DnhOdxC
+gZGDKdDIbufp8PgtdTqhWm+/7RL9HgdIij20AKqrVmAgOCotbI419dlk6jjxTUZ
ffb0VccCQQCH5IQineNvdlZkVcWewNHkLKUi9XO/FvKp2ETNyjWnSWteVdQ6wEBT
AigX73kvF+E8vRx9W+Gw4QR1FFnthPl/AkAv/LsxsdfNiM2/EIEG016472wPjF+t
2rExhpIDLovR8csAiIsetxat6GBdd/8pLEq7xuXmGj6zpFa5Olz+rh6fAkEAgO17
t+QYfg3GFVeTMPU7rcH1wt8hO7En9aBsVtp8YQS1S0EfI8Z2wMqRA3R+gwGi/p9l
QkJENC6orfPrBgBdPQJAecNNtUHGAG8PCd/Bi/Xf3RnA2yfuKwa/bKJKAG9uX4vy
UlwIzwofdLMXuvA5j5eqtzqaDMjc/ryaZ0B9ti0wqg==
-----END RSA PRIVATE KEY-----
EOD;

$json = file_get_contents('php://input');

$data = json_decode($json);

$mail = $data->mail;
$pw = $data->pw;

$auth = returnBase()->get('user', ['ID', 'role', 'abteilung_id', 'abo_pflicht', 'abo_flex', 'pw'], ["mail" => $mail]);

if (count($auth) > 0 && password_verify($pw, $auth['pw'])) {
    $role = $auth['role'];
    $abteilung = $auth['abteilung_id'];
    $payload = array(
        "iss" => "example.org",
        "aud" => "example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "abteilung_id" => $abteilung,
        "role" => $role,
        "ID" => $auth['ID']
    );

    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    $base = [];
    $base['token'] = $jwt;
    $base['role'] = $role;
    $base['abteilung'] = $abteilung;
    $kategorien_array = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $abteilung]);
    $kategorien_as_string = array_column($kategorien_array, 'kategorie_id');
    $kategorien =[];
    foreach($kategorien_as_string as $string) {
        $kategorien[] = (int)$string;
    }
    $base['kategorien'] = $kategorien;
    // abo-string to abo-array
    $abo_pflicht = $auth['abo_pflicht'] ? explode(",", $auth['abo_pflicht']) : [];
    $base['abo_pflicht'] = $abo_pflicht;
    $abo_flex = $auth['abo_flex'] ? explode(",", $auth['abo_flex']) : [];
    $base['abo_flex'] = $abo_flex;
    $abo_string = array_unique(array_merge($abo_flex, $abo_pflicht));
    $abo = [];
    foreach($abo_string as $string) {
        $abo[] = (int)$string;
    }
    $base['abo'] = $abo;
    //
    echo json_encode($base);
}
else {
    $base['token'] = '405';
    $base['role'] = 'none';
    echo json_encode($base);
}