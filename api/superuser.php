<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

use Medoo\Medoo;
require 'base.php';
$database = new Medoo($base_array_vars);

use \Firebase\JWT\JWT;

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgEi+xrdnz3fs0gP5ORWAy4Aw4ZyK
As81/ncLobJb1VS9kym86kbmqnBWt+xLO6HAFvBvj4ciBLK+PoDewdnt9+Auesob
kBQfyOVuinTzvdaAQbS5uYML77/MaXKwzOmFvzAVIIsyM9BHSi1/lYOl2vlLPrBO
cwrCyX9PEkWgQRy5AgMBAAE=
-----END PUBLIC KEY-----
EOD;


$jwt = file_get_contents('php://input');
$decoded = JWT::decode($jwt, $publicKey, ['RS256']);

$decoded_array = (array)$decoded;
$role = $decoded_array['role'];
if($role === 'admin') {
    $base = [];
    $user = $database->select("user", ['ID', 'mail', 'name', 'vorname', 'abteilung_id']);

    $beitraege = $database->select("fragen", [
        'ID', 'frage', 'antwort',
        'zeitpunkt_erstellung', 'freigegeben_fuer_abteilung', 'freigegeben_fuer_kategorie', 'freigegeben_fuer_gast', 'forum_thread', 'wichtigkeit',
    ]);

    $fragen_zu_beantworten = $database->select("fragen", [
        'ID', 'frage', 'antwort',
        'zeitpunkt_erstellung', 'freigegeben_fuer_abteilung', 'freigegeben_fuer_kategorie', 'freigegeben_fuer_gast', 'forum_thread',
    ], ["antwort" => null]);

    $frage_von_abteilung = $database->select("frage_von_abteilung", ['frage_ID', 'abteilung_id']);
    $frage_von_kategorie = $database->select("frage_von_kategorie", ['frage_ID', 'kategorie_id']);
    $frage_von_thema = $database->select("frage_von_thema", ['frage_ID', 'thema_id']);

    $base['user'] = $user;
    $base['beitraege'] = $beitraege;
    $base['fragen_zu_beantworten'] = $fragen_zu_beantworten;
    $base['frage_von_abteilung'] = $frage_von_abteilung;
    $base['frage_von_kategorie'] = $frage_von_kategorie;
    $base['$frage_von_thema'] = $frage_von_thema;
    echo json_encode($base);
} else {
    http_response_code(405);
    echo '405';
}