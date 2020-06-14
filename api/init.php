<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

require dirname(__DIR__) . '/vendor/autoload.php';

require './base.php';

$chosen = file_get_contents('php://input');

if ($chosen === 'themenundabteilungen') {
    $base = [];
    $base['abteilungen'] = returnBase()->select('abteilungen', ['ID' => ['name']]);
    $base['themen'] = returnBase()->select('themen', ['ID' => ['name']]);
    $base['kategorien'] = returnBase()->select('kategorie', ['ID' => ['name']]);
    $base['kategorie_hat_abteilung'] = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id', 'abteilung_id']);
    $base['thema_von_abteilung'] = returnBase()->select('thema_von_abteilung', ['thema_ID', 'abteilung_id']);

    echo json_encode($base);
}

else if ($chosen === 'themenabteilungenfragen') {
    $base = [];
    $base['abteilungen'] = returnBase()->select('abteilungen', ['ID' => ['name']]);
    $base['themen'] = returnBase()->select('themen', ['ID' => ['name']]);
    $base['fragen'] = returnBase()->select('fragen', ['ID', 'frage', 'antwort']);

    echo json_encode($base);
}

function fragenGefiltert($user_abteilung) {
   /* $abteilungen_not_map = returnBase()->select('abteilungen', ['ID'], ['ID[!]' => $user_abteilung]);
    $abteilungen_not = array_column($abteilungen_not_map, 'ID');

    $kategorien_not_map = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id[!]' => $user_abteilung]);
    $kategorien_not = array_column($kategorien_not_map, 'kategorie_id');

    $fragen = returnBase()->select('fragen',
        ['ID', 'frage', 'antwort', 'zeitpunkt_erstellung', 'zeitpunkt_antwort'],
        ['AND' => [ 'OR' => [
            'freigegeben_fuer_abteilung' => [$user_abteilung, null],
            'freigegeben_fuer_abteilung' => null,
        ]],
         'OR' => [
             'freigegeben_fuer_kategorie[!]' => $kategorien_not,
             'freigegeben_fuer_kategorie' => null
         ]
         , "ORDER" => ["zeitpunkt_erstellung" => "DESC"]]); */

    $kategorien_map = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $user_abteilung]);
    $kategorien = array_column($kategorien_map, 'kategorie_id');

   $fragen_1 = returnBase()->select('fragen', ['frage'], ['AND' =>
                                                             ['freigegeben_fuer_abteilung' => null,
                                                              'freigegeben_fuer_kategorie' => null]]);
    $fragen_2 = returnBase()->select('fragen', ['frage'], ['AND' =>
                                                              ['freigegeben_fuer_abteilung' => $user_abteilung,
                                                               'freigegeben_fuer_kategorie' => null]]);
    $fragen_3 = returnBase()->select('fragen', ['frage'], ['AND' =>
                                                              ['freigegeben_fuer_abteilung' => null,
                                                               'freigegeben_fuer_kategorie' => $kategorien]]);
    $fragen_4 = returnBase()->select('fragen', ['frage'], ['AND' =>
                                                              ['freigegeben_fuer_abteilung' => $user_abteilung,
                                                               'freigegeben_fuer_kategorie' => $kategorien]]);

   $fragen = array_merge($fragen_1, $fragen_2, $fragen_3, $fragen_4);
    return $fragen;
}

function fragenOeffentlich() {
    $fragen = returnBase()->select('fragen',
        ['ID', 'frage', 'antwort', 'zeitpunkt_erstellung', 'zeitpunkt_antwort'],
        ['AND' => ['freigegeben_fuer_gast' => 1,
            'antwort[!]' => null], "ORDER" => ["zeitpunkt_erstellung" => "DESC"]]);
    return $fragen;
}







