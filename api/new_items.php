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

$json = file_get_contents('php://input');

$data = json_decode($json);

$input_typ = $data->typ;

if ($input_typ === 'frage') {
    $frage = $data->frage;
    $antwort = $data->antwort;
    returnBase()->insert("fragen", [
        "antwort" => $antwort,
        "frage" => $frage
    ]);
}

else if ($input_typ === 'thema') {
    $thema = $data->thema;
    returnBase()->insert("themen", [
        "name" => $thema,
    ]);
}

else if ($input_typ === 'abteilung') {
    $abteilung = $data->abteilung;
    returnBase()->insert("abteilungen", [
        "name" => $abteilung,
    ]);
}

else if ($input_typ === 'person') {
    $name = $data->name;
    $mail = $data->mail;
    $abteilung = $data->abteilung;
    returnBase()->insert("user", [
        "name" => $name,
        "mail" => $mail,
        "role" => 'user',
        "abteilung_id" => $abteilung
    ]);
}

echo 'test';


