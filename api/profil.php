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


