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

$user = 'coffeeJunkie';
$pass = 'coffeeJunkie';
$db = new PDO('mysql:host=127.0.0.1;dbname=carehacktcorona', $user, $pass);
//Fehlermeldungen aktivieren
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//$anzahl_user = $statement->rowCount();

$data = file_get_contents('php://input');

function oeffentlich($db) {
    $statement_frage = $db->prepare("SELECT * FROM fragen WHERE freigegeben_fuer_gast = ? AND antwort IS NOT NULL order by zeitpunkt_erstellung desc");
    $statement_frage->execute([1]);
    $array_frage = $statement_frage->fetchAll();

    $statement_frage_von_abteilung = $db->query("SELECT * FROM frage_von_abteilung");
    $array_frage_von_abteilung = $statement_frage_von_abteilung->fetchAll();

    $statement_frage_von_thema = $db->query("SELECT * FROM frage_von_thema");
    $array_frage_von_thema = $statement_frage->fetchAll();

    $statement_frage_von_kategorie = $db->query("SELECT * FROM frage_von_kategorie");
    $array_frage_von_kategorie = $statement_frage_von_kategorie->fetchAll();

    $base = [];
    $base['fragen'] = $array_frage;
    $base['frage_von_abteilung'] = $array_frage_von_abteilung;
    $base['frage_von_thema'] = $array_frage_von_thema;
    $base['frage_von_kategorie'] = $array_frage_von_kategorie;

    echo json_encode($base);
}

// wenn GET, dann gebe nur öffentlich zugängliche Fragen zurück
if(!$data || $data = '') {
    oeffentlich($db);
}

// Alles freigegebene für user anzeigen
else {
    // user id aus postObj
    $jwt = $data;
    try {
        $decoded = JWT::decode($jwt, $publicKey, array('RS256'));

        $decoded_array = (array) $decoded;
        $role = $decoded_array['role'];
        $abteilung = $decoded_array['abteilung_id'];

        $statement = $db->prepare("SELECT * FROM fragen
        WHERE (freigegeben_fuer_abteilung = ? 
        OR freigegeben_fuer_abteilung IS NULL)
        AND (freigegeben_fuer_kategorie IS NULL
            OR freigegeben_fuer_kategorie NOT IN (
                SELECT kategorie_id FROM kategorie_hat_abteilung WHERE abteilung_id = ?
            ))
        AND antwort IS NOT NULL
        order by zeitpunkt_erstellung desc");
        $statement->execute([$abteilung, $abteilung]);
        $array = $statement->fetchAll();

        $base = [];
        $base['fragen'] = $array;
        $base['frage_von_abteilung'] = returnBase()->select('frage_von_abteilung', ['frage_ID', 'abteilung_id']);
        $base['frage_von_kategorie'] = returnBase()->select('frage_von_kategorie', ['frage_ID', 'kategorie_id']);
        $base['frage_von_thema'] = returnBase()->select('frage_von_thema', ['frage_ID', 'thema_id']);

        echo json_encode($base);
    }
    catch(Exception $e) {
        oeffentlich($db);
    }
}
