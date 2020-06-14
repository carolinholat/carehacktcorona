<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

// Using Medoo namespace
require './base.php';


use \Firebase\JWT\JWT;

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgEi+xrdnz3fs0gP5ORWAy4Aw4ZyK
As81/ncLobJb1VS9kym86kbmqnBWt+xLO6HAFvBvj4ciBLK+PoDewdnt9+Auesob
kBQfyOVuinTzvdaAQbS5uYML77/MaXKwzOmFvzAVIIsyM9BHSi1/lYOl2vlLPrBO
cwrCyX9PEkWgQRy5AgMBAAE=
-----END PUBLIC KEY-----
EOD;




//$anzahl_user = $statement->rowCount();


function oeffentlich() {
    $fragen = returnBase()->select('fragen',
        ['ID', 'frage', 'antwort', 'zeitpunkt_erstellung', 'zeitpunkt_antwort'],
        ['AND' => ['freigegeben_fuer_gast' => 1,
                   'antwort[!]' => null], "ORDER" => ["zeitpunkt_erstellung" => "DESC"]]);

    $base = [];
    $base['fragen'] = $fragen;
    $base['frage_von_abteilung'] = returnBase()->select('frage_von_abteilung', ['frage_ID', 'abteilung_id']);
    $base['frage_von_thema'] = returnBase()->select('frage_von_thema', ['frage_ID', 'thema_id']);
    $base['frage_von_kategorie'] = returnBase()->select('frage_von_kategorie', ['frage_ID', 'kategorie_id']);

    echo json_encode($base);
}

$jwt = file_get_contents('php://input');

// wenn GET, dann gebe nur öffentlich zugängliche Fragen zurück
if(!$jwt || $jwt === '') {
    oeffentlich();
}

// Alles freigegebene für user anzeigen
else {
    // user id aus postObj

    try {
        $decoded = JWT::decode($jwt, $publicKey, array('RS256'));
        $decoded_array = (array) $decoded;
        $user_abteilung = $decoded_array['abteilung_id'];

        $kategorien_map = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $user_abteilung]);
        $kategorien = array_column($kategorien_map, 'kategorie_id');

        $abgefragt = ['ID', 'frage', 'antwort', 'zeitpunkt_erstellung', 'zeitpunkt_antwort'];

        $fragen_1 = returnBase()->select('fragen', $abgefragt, ['AND' =>
                                                                   ['freigegeben_fuer_abteilung' => null,
                                                                    'freigegeben_fuer_kategorie' => null]]);
        $fragen_2 = returnBase()->select('fragen', $abgefragt, ['AND' =>
                                                                   ['freigegeben_fuer_abteilung' => $user_abteilung,
                                                                    'freigegeben_fuer_kategorie' => null]]);
        $fragen_3 = returnBase()->select('fragen',$abgefragt, ['AND' =>
                                                                   ['freigegeben_fuer_abteilung' => null,
                                                                    'freigegeben_fuer_kategorie' => $kategorien]]);
        $fragen_4 = returnBase()->select('fragen', $abgefragt, ['AND' =>
                                                                   ['freigegeben_fuer_abteilung' => $user_abteilung,
                                                                    'freigegeben_fuer_kategorie' => $kategorien]]);

        $fragen = array_merge($fragen_1, $fragen_2, $fragen_3, $fragen_4);

        $base = [];
        $base['fragen'] = $fragen;
        $base['frage_von_abteilung'] = returnBase()->select('frage_von_abteilung', ['frage_ID', 'abteilung_id']);
        $base['frage_von_kategorie'] = returnBase()->select('frage_von_kategorie', ['frage_ID', 'kategorie_id']);
        $base['frage_von_thema'] = returnBase()->select('frage_von_thema', ['frage_ID', 'thema_id']);

        echo json_encode($base);
    }
    catch(Exception $e) {
        oeffentlich();
    }
}
