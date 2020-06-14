<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

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

$json = file_get_contents('php://input');

$data = json_decode($json);

function getFrage($id) {
    error_log('id' . $id);
    return returnBase()->select('fragen', ['frage', 'antwort', 'zeitpunkt_erstellung', 'freigegeben_fuer_abteilung', 'freigegeben_fuer_kategorie', 'freigegeben_fuer_gast', 'forum_thread', 'zeitpunkt_antwort'], ['forum_thread' => $id]);
}

function getBeitraege($id) {
    $beitraege = returnBase()->select('forum_beitraege', ["[><]user" => ["user_ID" => "ID"], 'forum_beitraege.zeitstempel'], ['forum_beitraege.text', 'user.name', 'user.vorname', 'forum_beitraege.zeitstempel'], ['thread_ID' => $id, "ORDER" => ["zeitstempel" => "ASC"]]);

    return $beitraege;
}

function checkAuth($jwt, $publicKey, $id) {
    if($jwt === '') {
        $freigegeben = getFrage($id)[0]['freigegeben_fuer_gast'];
        if($freigegeben !== null) {
            return true;
        } else {
            return false;
        }
    } else {
        try {
            $auth = true;
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $abteilung = $decoded_array['abteilung_id'];

            if(getFrage($id)['freigegeben_fuer_abteilung'] && getFrage($id)[0]['freigegeben_fuer_abteilung'] !== $abteilung) {
                $auth = false;
            }
            // alle kategorien suchen, denen die abteilung gehört
            $kategorie_array_res = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $abteilung]);
            $kategorien = array_column($kategorie_array_res, 'kategorie_id');

            // prüfen, ob wert vorhanden und wenn ja, in array
            if(getFrage($id)['freigegeben_fuer_kategorie'] && !in_array(getFrage($id)[0]['freigegeben_fuer_kategorie'], $kategorien, false)) {
                $auth = false;
            }
            return $auth;

        } catch(Exception $e) {
            return false;
        }
    }
}


// wenn GET, dann gebe nur öffentlich zugängliche Fragen zurück
if(checkAuth($data->token, $publicKey, $data->id)) {
    $base = [];
    $frage = getFrage($data->id);
    $beitraege = getBeitraege($data->id);
    $base['frage'] = $frage;
    $base['beitraege'] = $beitraege;
    echo json_encode($base);
} // Alles freigegebene für user anzeigen
else {
    echo '405';
}
