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

$json = file_get_contents('php://input');

$data = json_decode($json);

$mail = $data->mail;
$pw = $data->pw;

$auth = returnBase()->get('user', ['ID', 'role', 'abteilung_id', 'abo_pflicht', 'abo_flex'], ["mail" => $mail, 'pw' => $pw]);

if (count($auth) > 0) {
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