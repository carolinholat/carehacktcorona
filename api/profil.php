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

if($input_typ === 'getProfil') {
    if($data->token !== '') {
        $jwt = $data->token;
        try {
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $id = (int)$decoded_array['ID'];
            $user = returnBase()->select('user', [
                'mail', 'name', 'vorname', 'mail_privat', 'handy',
            ], ['ID' => $id])[0];
            $base['user'] = $user;
            echo json_encode($base);

        } catch(Exception $e) {
            echo '405';
        }
    } else {
        echo '405';
    }
}

else if($input_typ === 'abonnieren') {
    if ($data->token !== '') {
        $jwt = $data->token;
        try {
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $id = (int)$decoded_array['ID'];
            $user = returnBase()->select('user', [
                'mail', 'name', 'vorname', 'mail_privat', 'handy',
            ], ['ID' => $id])[0];
            $abo_flex = returnBase()->select('user', ['abo_flex'], ['ID' => $id])[0]['abo_flex'];
            $abo_flex_neu = '';
            if ($abo_flex && $abo_flex !== '') {
                $abo_flex_neu = $abo_flex . ',' . $data->id;
                $abo_flex_array = explode(',', $abo_flex_neu);
            }
            else {
                $abo_flex_neu = $data->id;
                $abo_flex_array = [$data->id];
            }
            returnBase()->update('user', ['abo_flex' => $abo_flex_neu], ['ID' => $id] );

            echo json_encode($abo_flex_array);

        } catch(Exception $e) {
            echo '405';
        }
    }
    else {
        echo '405';
    }
}

else if($input_typ === 'desabonnieren') {
    if ($data->token !== '') {
        $jwt = $data->token;
        try {
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $id = (int)$decoded_array['ID'];
            $user = returnBase()->select('user', [
                'mail', 'name', 'vorname', 'mail_privat', 'handy',
            ], ['ID' => $id])[0];
            $abo_flex = returnBase()->select('user', ['abo_flex'], ['ID' => $id])[0]['abo_flex'];
            $abo_flex_array = array_diff( explode(",", $abo_flex), [$data->id] );
            $abo_flex_neu = implode(",", $abo_flex_array);
            returnBase()->update('user', ['abo_flex' => $abo_flex_neu], ['ID' => $id] );

            echo json_encode($abo_flex_array);

        } catch(Exception $e) {
            echo '405';
        }
    }
    else {
        echo '405';
    }
}


