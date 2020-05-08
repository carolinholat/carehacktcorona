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

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;


$jwt = file_get_contents('php://input');
$decoded = JWT::decode($jwt, $publicKey, array('RS256'));

$decoded_array = (array) $decoded;
$role = $decoded_array['role'];
if ($role === 'admin') {
    $base = [];
    $user = returnBase()->select("user", ['ID', 'mail', 'name', 'vorname', 'abteilung_id']);

    $beitraege = returnBase()->select("fragen", ['ID', 'frage', 'antwort',
                                                 'zeitpunkt_erstellung', 'freigegeben_fuer_abteilung', 'freigegeben_fuer_kategorie', 'freigegeben_fuer_gast', 'forum_thread']);

    $fragen_zu_beantworten = returnBase()->select("fragen", ['ID', 'frage', 'antwort',
                                                             'zeitpunkt_erstellung', 'freigegeben_fuer_abteilung', 'freigegeben_fuer_kategorie', 'freigegeben_fuer_gast', 'forum_thread'], ["antwort" => null]);

    $base['user'] = $user;
    $base['beitraege'] = $beitraege;
    $base['fragen_zu_beantworten'] = $fragen_zu_beantworten;
    echo json_encode($base);
}
else {
    echo '405';
}