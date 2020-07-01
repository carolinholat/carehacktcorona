<?php



function returnMessageReceivers($frage_id, $database) {
    $user_array = [];
    $kategorien = array_column($database->select('frage_von_kategorie', ["kategorie_id"], ['frage_ID' => $frage_id]), 'kategorie_id');

    $themen = array_column($database->select('frage_von_thema', ["thema_id"], ['frage_ID' => $frage_id]), 'thema_id');

    $abteilungen_von_kategorie = array_column($database->select('abteilungen',
        ["[><]kategorie_hat_abteilung" => ["ID" => "abteilung_id"]],
        ['abteilungen.ID'],
        ['kategorie_hat_abteilung.kategorie_id' => $kategorien]), 'ID');

    $abteilungen_von_frage = $database->select('frage_von_abteilung', ['abteilung_id'], ['frage_ID' => $frage_id]);

    $users = $database->select('user', ['mail', 'abteilung_id', 'abo_pflicht', 'abo_flex', 'telegram']);

    foreach ($users as $user) {

        $abo_pflicht = explode(",", $user['abo_pflicht']);
        $abo_flex = explode(",", $user['abo_flex']);
        $abo_merge = array_merge($abo_pflicht, $abo_flex);

        if (in_array($user['abteilung_id'], $abteilungen_von_kategorie, FALSE) || in_array($user['abteilung_id'], $abteilungen_von_frage, FALSE) || count(array_intersect($themen, $abo_merge)) > 0) {
            $user_array[] = $user;
        }
    }

    return $user_array;
}
