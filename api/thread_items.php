<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

function returnBase() {
    $database = new Medoo([
        'database_type' => 'mysql',
        'database_name' => 'carehacktcorona',
        'server' => '127.0.0.1',
        'username' => 'coffeeJunkie',
        'password' => 'coffeeJunkie',
    ]);
    return $database;
}

use \Firebase\JWT\JWT;

$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
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
