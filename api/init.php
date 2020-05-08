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

$chosen = file_get_contents('php://input');

if ($chosen === 'themenundabteilungen') {
    $base = [];
    $base['abteilungen'] = returnBase()->select('abteilungen', ['ID' => ['name']]);
    $base['themen'] = returnBase()->select('themen', ['ID' => ['name']]);
    $base['kategorien'] = returnBase()->select('kategorie', ['ID' => ['name']]);
    $base['kategorie_hat_abteilung'] = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id', 'abteilung_id']);

    echo json_encode($base);
}

else if ($chosen === 'themenabteilungenfragen') {
    $base = [];
    $base['abteilungen'] = returnBase()->select('abteilungen', ['ID' => ['name']]);
    $base['themen'] = returnBase()->select('themen', ['ID' => ['name']]);
    $base['fragen'] = returnBase()->select('fragen', ['ID', 'frage', 'antwort']);

    echo json_encode($base);
}




