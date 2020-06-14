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


//$anzahl_user = $statement->rowCount();

$data = file_get_contents('php://input');


// wenn GET, dann gebe nur öffentlich zugängliche Fragen zurück
if(!$data || $data = '') {
    forumOeffentlich();
    // oeffentlich($db);

} // Alles freigegebene für user anzeigen
else {
    // user id aus postObj
    $jwt = $data;
    try {
        $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

        $decoded_array = (array)$decoded;
        $role = $decoded_array['role'];
        $abteilung = $decoded_array['abteilung_id'];

        forumAnmeldebereich($abteilung);

    } catch(Exception $e) {
        //  oeffentlich($db);
        forumOeffentlich();
    }
}

function forumOeffentlich() {
    $lastmonth = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
    $ein_monat_davor = date('Y-m-d H:i:s', $lastmonth);

    $forum_aktiv = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        [
            'AND' => ['fragen.freigegeben_fuer_gast' => 1, 'forum_threads.zeitstempel[>]' => $ein_monat_davor],
            "ORDER" => ["zeitstempel" => "DESC"],
        ]);

    $forum_alle = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        ['fragen.freigegeben_fuer_gast' => 1, "ORDER" => ["zeitstempel" => "DESC"]]);

    $base = [];

    $base['forum_aktiv'] = $forum_aktiv;
    $base['forum_alle'] = $forum_alle;

    echo json_encode($base);
}

function forumAnmeldebereich($abteilung) {
    $lastmonth = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
    $ein_monat_davor = date('Y-m-d H:i:s', $lastmonth);
    $kategorien_map = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $abteilung]);
    $kategorien = array_column($kategorien_map, 'kategorie_id');

    // abteilung_id + kategorie:null
    $forum_aktiv_1 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        [
            'AND' => [
                'fragen.freigegeben_fuer_abteilung' => $abteilung,
                'fragen.freigegeben_fuer_kategorie' => null,
                'forum_threads.zeitstempel[>]' => $ein_monat_davor
            ],
            "ORDER" => ["zeitstempel" => "DESC"],
        ]);

    //abteilung:null + kategorie:null
    $forum_aktiv_2 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        [
            'AND' => [
                'fragen.freigegeben_fuer_abteilung' => null,
                'fragen.freigegeben_fuer_kategorie' => null,
                'forum_threads.zeitstempel[>]' => $ein_monat_davor
            ],
            "ORDER" => ["zeitstempel" => "DESC"],
        ]);

    //abteilung:id, kategorie:array
    $forum_aktiv_3 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        [
            'AND' => [
                'fragen.freigegeben_fuer_abteilung' => $abteilung,
                'fragen.freigegeben_fuer_kategorie' => $kategorien,
                'forum_threads.zeitstempel[>]' => $ein_monat_davor
            ],
            "ORDER" => ["zeitstempel" => "DESC"],
        ]);

    //abteilung:null, kategorie:array
    $forum_aktiv_4 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        [
            'AND' => [
                'fragen.freigegeben_fuer_abteilung' => null,
                'fragen.freigegeben_fuer_kategorie' => $kategorien,
                'forum_threads.zeitstempel[>]' => $ein_monat_davor
            ],
            "ORDER" => ["zeitstempel" => "DESC"],
        ]);

///////////////////////////////////////////////////////////
///
///  // abteilung_id + kategorie:null
    $forum_alle_1 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        ['AND' => ['fragen.freigegeben_fuer_abteilung' => $abteilung,
                   'fragen.freigegeben_fuer_kategorie' => null],
         "ORDER" => ["zeitstempel" => "DESC"]]);

    //abteilung:null + kategorie:null
    $forum_alle_2 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        ['AND' => ['fragen.freigegeben_fuer_abteilung' => null,
                   'fragen.freigegeben_fuer_kategorie' => null],
         "ORDER" => ["zeitstempel" => "DESC"]]);

    //abteilung:id, kategorie:array
    $forum_alle_3 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        ['AND' => ['fragen.freigegeben_fuer_abteilung' => $abteilung,
                   'fragen.freigegeben_fuer_kategorie' => $kategorien],
         "ORDER" => ["zeitstempel" => "DESC"]]);

    //abteilung:null, kategorie:array
    $forum_alle_4 = returnBase()->select('forum_threads', ["[><]fragen" => ["frage_ID" => "ID"]],
        ['fragen.frage', 'fragen.antwort', 'fragen.ID', 'fragen.zeitpunkt_erstellung', 'fragen.freigegeben_fuer_abteilung', 'fragen.freigegeben_fuer_kategorie', 'fragen.forum_thread', 'fragen.zeitpunkt_antwort'],
        ['AND' => ['fragen.freigegeben_fuer_abteilung' => null,
                   'fragen.freigegeben_fuer_kategorie' => $kategorien],
         "ORDER" => ["zeitstempel" => "DESC"]]);


    $base = [];

    $base['forum_aktiv'] = array_merge($forum_aktiv_1, $forum_aktiv_2, $forum_aktiv_3, $forum_aktiv_4);
    $base['forum_alle'] = array_merge($forum_alle_1, $forum_alle_2, $forum_alle_3, $forum_alle_4);
    echo json_encode($base);
}



/*
$user = 'coffeeJunkie';
$pass = 'coffeeJunkie';
$db = new PDO('mysql:host=127.0.0.1;dbname=carehacktcorona', $user, $pass);
//Fehlermeldungen aktivieren
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/

/*function oeffentlich($db) {
    $base = [];
    $statement_forum = $db->prepare("SELECT * FROM fragen inner join forum_threads ft on fragen.ID = ft.frage_ID WHERE freigegeben_fuer_gast = 1 AND ft.offen = 1 AND ft.zeitstempel > ?   order by ft.zeitstempel desc");

    $lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
    $ein_monat_davor  = date('Y-m-d H:i:s', $lastmonth);

    $statement_forum->execute([$ein_monat_davor]);
    $array_forum = $statement_forum->fetchAll();

    $base['forum_aktiv'] = $array_forum;

    $statement_forum_alle = $db->prepare("SELECT * FROM fragen inner join forum_threads ft on fragen.ID = ft.ID WHERE freigegeben_fuer_gast = ? order by ft.zeitstempel desc");

    $statement_forum_alle->execute([1]);
    $array_forum_alle = $statement_forum_alle->fetchAll();

    $base['forum_alle'] = $array_forum_alle;

    echo json_encode($base);
} */

/* function forumMitgliederbereich($db) {
    $statement = $db->prepare("SELECT * FROM fragen inner join forum_threads ft on fragen.ID = ft.ID
        WHERE (freigegeben_fuer_abteilung = ?
        OR freigegeben_fuer_abteilung IS NULL)
        AND (freigegeben_fuer_kategorie IS NULL
            OR freigegeben_fuer_kategorie NOT IN (
                SELECT kategorie_id FROM kategorie_hat_abteilung WHERE abteilung_id = ?
            ))
        AND ft.offen = 1 AND ft.zeitstempel > ?
        ORDER BY ft.zeitstempel desc");

    $lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
    $ein_monat_davor  = date('Y-m-d H:i:s', $lastmonth);
    $statement->execute([$abteilung, $abteilung, $ein_monat_davor]);
    $forum_aktiv_array = $statement->fetchAll();

    $base = [];
    $base['forum_aktiv'] = $forum_aktiv_array;

    $statement_forum_alle = $db->prepare("SELECT * FROM fragen inner join forum_threads ft on fragen.ID = ft.ID
        WHERE (freigegeben_fuer_abteilung = ?
        OR freigegeben_fuer_abteilung IS NULL)
        AND (freigegeben_fuer_kategorie IS NULL
            OR freigegeben_fuer_kategorie NOT IN (
                SELECT kategorie_id FROM kategorie_hat_abteilung WHERE abteilung_id = ?
            ))
        ORDER BY ft.zeitstempel desc");

    $statement_forum_alle->execute([$abteilung, $abteilung]);
    $forum_alle = $statement_forum_alle->fetchAll();
    $base['forum_alle'] = $forum_alle;

    echo json_encode($base);
} */
