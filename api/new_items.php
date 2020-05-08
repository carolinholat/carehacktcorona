<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

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

$json = file_get_contents('php://input');

$data = json_decode($json);

$input_typ = $data->action;

if($input_typ === 'feedbackInsert') {
    $feedback = $data->text;
    returnBase()->insert("feedback", [
        "text" => $feedback,
    ]);
    echo 'success';
}

if($input_typ === 'frageInsert') {
    $frage = $data->frage;
    $antwort = $data->antwort;
    $abteilungen = $data->abteilungen;
    $kategorien = $data->kategorien;
    $oeffentlich = $data->oeffentlich;
    $themen = $data->themen;
    $freigeben_kategorie = $data->freigeben_kategorie;
    $freigeben_abteilung = $data->freigeben_abteilung;
    returnBase()->insert("fragen", [
        "antwort" => $antwort,
        "frage" => $frage,
        "zeitpunkt_antwort" => date('Y-m-d H:i:s'),
        "freigegeben_fuer_abteilung" => $freigeben_abteilung,
        "freigegeben_fuer_kategorie" => $freigeben_kategorie,
        "freigegeben_fuer_gast" => $oeffentlich,
    ]);
}

if($input_typ === 'frageUpdate') {
    $id = $data->ID;
    $antwort = $data->antwort;
    $freigeben_abteilung = $data->freigeben_abteilung;
    $freigeben_kategorie = $data->freigeben_kategorie;
    $oeffentlich = $data->oeffentlich;
    $themen = $data->themen;

    returnBase()->update("fragen", [
        "antwort" => $antwort,
        "zeitpunkt_antwort" => date('Y-m-d H:i:s'),
        "freigegeben_fuer_abteilung" => $freigeben_abteilung,
        "freigegeben_fuer_kategorie" => $freigeben_kategorie,
        "freigegeben_fuer_gast" => $oeffentlich,
    ], ['ID' => $id]);
    // TODO: THEMEN UND KATEGORIEN UPDATEN
} else if($input_typ === 'themaInsert') {
    $thema = $data->thema;
    $abteilungen = $data->abteilungen;
    $thema_id = count(returnBase()->select('themen', ['ID', 'name'])) + 1;
    error_log($thema_id);
    error_log($thema);
    returnBase()->insert("themen", [
        "name" => $thema,
    ]);
    foreach($abteilungen as $abteilung) {
        returnBase()->insert("thema_von_abteilung", [
            "abteilung_id" => $abteilung,
            "thema_ID" => $thema_id,
        ]);
    }
} else if($input_typ === 'abteilung') {
    $abteilung = $data->abteilung;
    returnBase()->insert("abteilungen", [
        "name" => $abteilung,
    ]);
} // MITARBEITER
else if($input_typ === 'userInsert') {
    $name = $data->name;
    $vorname = $data->vorname;
    $mail = $data->mail;
    $abteilung = $data->abteilung;
    $echo = returnBase()->insert("user", [
        "name" => $name,
        "vorname" => $vorname,
        "mail" => $mail,
        "role" => 'user',
        "abteilung_id" => $abteilung,
    ]);
    echo json_encode($echo);
} else if($input_typ === 'userUpdate') {
    $name = $data->name;
    $id = $data->ID;
    $vorname = $data->vorname;
    $mail = $data->mail;
    $abteilung = $data->abteilung;
    $echo = returnBase()->update("user", [
        "name" => $name,
        "vorname" => $vorname,
        "mail" => $mail,
        "role" => 'user',
        "abteilung_id" => $abteilung,
    ], ['ID' => $id]);

    echo json_encode($echo);
} else if($input_typ === 'userDelete') {
    $id = (int)$data->ID;
    error_log($id);
    $echo = returnBase()->delete("user", ['ID' => $id]);

    echo json_encode($echo);
} else if($input_typ === 'insertThreadBeitrag') {
    if($data->token !== '') {
        $jwt = $data->token;
        try {
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $id = $decoded_array['ID'];
            error_log('thread_ID: ' . $data->thread_ID . ', user:ID: ' . $id . ', text: ' . $data->text);
            returnBase()->insert('forum_beitraege', [
                'thread_ID' => $data->thread_ID,
                'user_ID' => $id,
                'text' => $data->text,
            ]);
        } catch(Exception $e) {
            echo '405';
        }
    } else {
        echo '405';
    }
}

echo 'test';


