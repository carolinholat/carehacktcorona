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

if($input_typ === 'feedbackInsert') {
    $feedback = $data->text;
    returnBase()->insert("feedback", [
        "text" => $feedback,
    ]);
    echo 'success';
}

if($input_typ === 'frageInsert') {
    $frage = $data->frage;
    $antwort = $data->antwort;
    $abteilungen = $data->abteilungen;
    $kategorien = $data->kategorien;
    $oeffentlich = $data->oeffentlich;
    $themen = $data->themen;
    $freigeben_kategorie = $data->freigeben_kategorie;
    $freigeben_abteilung = $data->freigeben_abteilung;
    returnBase()->insert("fragen", [
        "antwort" => $antwort,
        "frage" => $frage,
        "zeitpunkt_antwort" => date('Y-m-d H:i:s'),
        "freigegeben_fuer_abteilung" => $freigeben_abteilung,
        "freigegeben_fuer_kategorie" => $freigeben_kategorie,
        "freigegeben_fuer_gast" => $oeffentlich,
    ]);
    $frage_id = returnBase()->max('fragen', 'ID');
    if(count($abteilungen) > 0) {
        foreach($abteilungen as $abteilung) {
            returnBase()->insert("frage_von_abteilung", [
                "frage_ID" => $frage_id,
                "abteilung_id" => $abteilung,
            ]);
        }
    }
    if(count($kategorien) > 0) {
        foreach($kategorien as $kategorie) {
            returnBase()->insert("frage_von_kategorie", [
                "frage_ID" => $frage_id,
                "kategorie_id" => $kategorie,
            ]);
        }
    }

    if(count($themen) > 0) {
        foreach($themen as $thema) {
            returnBase()->insert("frage_von_thema", [
                "frage_ID" => $frage_id,
                "thema_id" => $thema,
            ]);
        }
    }
}

if($input_typ === 'frageUpdate') {
    $id = $data->ID;
    $antwort = $data->antwort;
    $freigeben_abteilung = $data->freigeben_abteilung;
    $freigeben_kategorie = $data->freigeben_kategorie;
    $oeffentlich = $data->oeffentlich;
    $themen = $data->themen;
    $abteilungen = $data->abteilungen;
    $kategorien = $data->kategorien;

    returnBase()->update("fragen", [
        "antwort" => $antwort,
        "zeitpunkt_antwort" => date('Y-m-d H:i:s'),
        "freigegeben_fuer_abteilung" => $freigeben_abteilung,
        "freigegeben_fuer_kategorie" => $freigeben_kategorie,
        "freigegeben_fuer_gast" => $oeffentlich,
    ], ['ID' => $id]);

    // frage_von_abteilung
    $frage_von_abteilung_bisher_map = returnBase()->select('frage_von_abteilung', ['abteilung_id'], ['frage_ID' => $id]);
    $frage_von_abteilung_bisher = array_column($frage_von_abteilung_bisher_map, 'abteilung_id');

    $to_delete = array_diff($frage_von_abteilung_bisher, $abteilungen); // abteilung_ids
    if(count($to_delete) > 0) {
        returnBase()->delete('frage_von_abteilung', ['abteilung_id' => $to_delete]);
    }

    $to_insert = array_diff($abteilungen, $frage_von_abteilung_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $abteilung) {
            returnBase()->insert('frage_von_abteilung', ['frage_ID' => $id, 'abteilung_id' => $abteilung]);
        }
    }

    // frage_von_kategorie
    $frage_von_kategorie_bisher_map = returnBase()->select('frage_von_kategorie', ['kategorie_id'], ['frage_ID' => $id]);
    $frage_von_kategorie_bisher = array_column($frage_von_kategorie_bisher_map, 'kategorie_id');

    $to_delete = array_diff($frage_von_kategorie_bisher, $kategorien); // kategorie_ids
    if(count($to_delete) > 0) {
        returnBase()->delete('frage_von_kategorie', ['kategorie_id' => $to_delete]);
    }

    $to_insert = array_diff($kategorien, $frage_von_kategorie_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $kategorie) {
            returnBase()->insert('frage_von_kategorie', ['frage_ID' => $id, 'kategorie_id' => $kategorie]);
        }
    }

    // frage_von_thema
    $frage_von_thema_bisher_map = returnBase()->select('frage_von_thema', ['thema_id'], ['frage_ID' => $id]);
    $frage_von_thema_bisher = array_column($frage_von_thema_bisher_map, 'thema_id');

    $to_delete = array_diff($frage_von_thema_bisher, $themen); // thema_ids
    if(count($to_delete) > 0) {
        returnBase()->delete('frage_von_thema', ['thema_id' => $to_delete]);
    }

    $to_insert = array_diff($themen, $frage_von_thema_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $thema) {
            returnBase()->insert('frage_von_thema', ['frage_ID' => $id, 'thema_id' => $thema]);
        }
    }
} //THEMA
else if($input_typ === 'themaInsert') {
    $thema = $data->thema;
    $abteilungen = $data->abteilungen;
    $thema_id = count(returnBase()->select('themen', ['ID', 'name'])) + 1;
    returnBase()->insert("themen", [
        "name" => $thema,
    ]);
    foreach($abteilungen as $abteilung) {
        returnBase()->insert("thema_von_abteilung", [
            "abteilung_id" => $abteilung,
            "thema_ID" => $thema_id,
        ]);
    }
} else if($input_typ === 'themaDelete') {
    $id = $data->ID;
    returnBase()->delete('frage_von_thema', ['thema_id' => $id]);
    returnBase()->delete('thema_von_abteilung', ['thema_ID' => $id]);
    returnBase()->delete('themen', ['ID' => $id]);

} else if($input_typ === 'themaUpdate') {
    $id = $data->ID;
    $thema = $data->thema;
    returnBase()->update('themen', ['name' => $thema], ['ID' => $id]);
    $abteilungen = $data->abteilungen;

    $themen_von_abteilung_map_bisher = returnBase()->select('thema_von_abteilung', ['abteilung_id'], ['thema_ID' => $id]);
    $themen_von_abteilung_bisher = array_column($themen_von_abteilung_map_bisher, 'abteilung_id');

    $to_delete = array_diff($themen_von_abteilung_bisher, $abteilungen);
    if(count($to_delete) > 0) {
        returnBase()->delete('thema_von_abteilung', ['abteilung_id' => $to_delete]);
    }
    $to_insert = array_diff($abteilungen, $themen_von_abteilung_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $abteilung) {
            returnBase()->insert('thema_von_abteilung', ['thema_ID' => $id, 'abteilung_id' => $abteilung]);
        }
    }
}
// ABTEILUNG
else if($input_typ === 'abteilungInsert') {
    $abteilung = $data->abteilung;
    returnBase()->insert("abteilungen", [
        "name" => $abteilung,
    ]);
}

else if ($input_typ === 'abteilungUpdate') {
    $id = $data->ID;
    $abteilung = $data->abteilung;
    $kategorien = $data->kategorien;
    returnBase()->update('abteilungen', ['name' => $abteilung], ['ID' => $id]);

    $kategorie_hat_abteilung_map_bisher = returnBase()->select('kategorie_hat_abteilung', ['kategorie_id'], ['abteilung_id' => $id]);
    $kategorie_hat_abteilung_bisher = array_column($kategorie_hat_abteilung_map_bisher, 'kategorie_id');

    $to_delete = array_diff($kategorie_hat_abteilung_bisher, $kategorien);
    if(count($to_delete) > 0) {
        returnBase()->delete('kategorie_hat_abteilung', ['kategorie_id' => $to_delete]);
    }
    $to_insert = array_diff($kategorien, $kategorie_hat_abteilung_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $kategorie) {
            returnBase()->insert('kategorie_hat_abteilung', ['kategorie_id' => $kategorie, 'abteilung_id' => $id]);
        }
    }
}

else if ($input_typ === 'abteilungDelete') {
    $id = $data->ID;
    returnBase()->delete('kategorie_hat_abteilung', ['abteilung_id' => $id]);
    returnBase()->delete('frage_von_abteilung', ['abteilung_id' => $id]);
    returnBase()->delete('thema_von_abteilung', ['abteilung_id' => $id]);
    returnBase()->delete('abteilungen', ['ID' => $id]);
}

// KATEGORIE
else if($input_typ === 'kategorieInsert') {
    $kategorie = $data->kategorie;
    $abteilungen = $data->abteilungen;
    returnBase()->insert('kategorie', ['name' => $kategorie]);
    $kategorie_id = returnBase()->max('kategorie', 'ID');
    if(count($abteilungen) > 0) {
        foreach($abteilungen as $abteilung) {
            returnBase()->insert('kategorie_hat_abteilung', ['kategorie_id' => $kategorie_id, 'abteilung_id' => $abteilung]);
        }
    }
}

else if($input_typ === 'kategorieUpdate') {
    $id = $data->ID;
    $kategorie = $data->kategorie;
    $abteilungen = $data->abteilungen;
    returnBase()->update('kategorie', ['name' => $kategorie], ['ID' => $id]);

    $kategorie_hat_abteilung_map_bisher = returnBase()->select('kategorie_hat_abteilung', ['abteilung_id'], ['kategorie_id' => $id]);
    $kategorie_hat_abteilung_bisher = array_column($kategorie_hat_abteilung_map_bisher, 'abteilung_id');

    $to_delete = array_diff($kategorie_hat_abteilung_bisher, $abteilungen);
    if(count($to_delete) > 0) {
        returnBase()->delete('kategorie_hat_abteilung', ['abteilung_id' => $to_delete]);
    }
    $to_insert = array_diff($abteilungen, $kategorie_hat_abteilung_bisher);
    if(count($to_insert) > 0) {
        foreach($to_insert as $abteilung) {
            returnBase()->insert('kategorie_hat_abteilung', ['kategorie_id' => $id, 'abteilung_id' => $abteilung]);
        }
    }
}

else if($input_typ === 'kategorieDelete') {
    $id = $data->ID;
    returnBase()->delete('kategorie_hat_abteilung', ['kategorie_id' => $id]);
    returnBase()->delete('frage_von_kategorie', ['kategorie_id' => $id]);
    returnBase()->delete('kategorie', ['ID' => $id]);
}

// MITARBEITER
else if($input_typ === 'userInsert') {
    $name = $data->name;
    $vorname = $data->vorname;
    $mail = $data->mail;
    $abteilung = $data->abteilung;
    $echo = returnBase()->insert("user", [
        "name" => $name,
        "vorname" => $vorname,
        "mail" => $mail,
        "role" => 'user',
        "abteilung_id" => $abteilung,
    ]);
    echo json_encode($echo);
}
else if($input_typ === 'userUpdate') {
    $name = $data->name;
    $id = $data->ID;
    $vorname = $data->vorname;
    $mail = $data->mail;
    $abteilung = $data->abteilung;
    $echo = returnBase()->update("user", [
        "name" => $name,
        "vorname" => $vorname,
        "mail" => $mail,
        "abteilung_id" => $abteilung,
    ], ['ID' => $id]);

    echo json_encode($echo);
} else if($input_typ === 'userDelete') {
    $id = (int)$data->ID;
    error_log($id);
    $echo = returnBase()->delete("user", ['ID' => $id]);

    echo json_encode($echo);
} // THREAD
else if($input_typ === 'insertThreadBeitrag') {
    if($data->token !== '') {
        $jwt = $data->token;
        try {
            $decoded = JWT::decode($jwt, $publicKey, ['RS256']);

            $decoded_array = (array)$decoded;
            $id = $decoded_array['ID'];
            error_log('thread_ID: ' . $data->thread_ID . ', user:ID: ' . $id . ', text: ' . $data->text);
            returnBase()->insert('forum_beitraege', [
                'thread_ID' => $data->thread_ID,
                'user_ID' => $id,
                'text' => $data->text,
            ]);
        } catch(Exception $e) {
            echo '405';
        }
    } else {
        echo '405';
    }
}






