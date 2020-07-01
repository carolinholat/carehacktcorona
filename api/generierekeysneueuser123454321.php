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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require './mail_credentials.php';
$credentials = $mail_credentials;

$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgEi+xrdnz3fs0gP5ORWAy4Aw4ZyKAs81/ncLobJb1VS9kym86kbm
qnBWt+xLO6HAFvBvj4ciBLK+PoDewdnt9+AuesobkBQfyOVuinTzvdaAQbS5uYML
77/MaXKwzOmFvzAVIIsyM9BHSi1/lYOl2vlLPrBOcwrCyX9PEkWgQRy5AgMBAAEC
gYAPvQ67MX+Gf7s0VuBN0a61jl3Rg152PEVQtjiGoS23hshnGFNLga5QXcKvIdvF
9AxCerB/2RFRJq3ZLdic8MpXAEp9nbqxNzrv5d/MjToyzUn5/xaSO+UJDajH/Gx0
d0bSoizl7AK7eGfFXFIswUf2DedCnBQasjztm748K4tf3QJBAIkKVOr77DnhOdxC
+gZGDKdDIbufp8PgtdTqhWm+/7RL9HgdIij20AKqrVmAgOCotbI419dlk6jjxTUZ
ffb0VccCQQCH5IQineNvdlZkVcWewNHkLKUi9XO/FvKp2ETNyjWnSWteVdQ6wEBT
AigX73kvF+E8vRx9W+Gw4QR1FFnthPl/AkAv/LsxsdfNiM2/EIEG016472wPjF+t
2rExhpIDLovR8csAiIsetxat6GBdd/8pLEq7xuXmGj6zpFa5Olz+rh6fAkEAgO17
t+QYfg3GFVeTMPU7rcH1wt8hO7En9aBsVtp8YQS1S0EfI8Z2wMqRA3R+gwGi/p9l
QkJENC6orfPrBgBdPQJAecNNtUHGAG8PCd/Bi/Xf3RnA2yfuKwa/bKJKAG9uX4vy
UlwIzwofdLMXuvA5j5eqtzqaDMjc/ryaZ0B9ti0wqg==
-----END RSA PRIVATE KEY-----
EOD;

// wenn es eine pw-nachforderung ist
$mail = file_get_contents('php://input');


function getToken($id, $privateKey) {
    $payload = array(
        "iss" => "example.org",
        "aud" => "example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "ID" => $id
    );
   return JWT::encode($payload, $privateKey, 'RS256');
}

function getLink($key) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . 'scio.projuve.de/#/profil?key=' . $key;
}

function sendKeyRequestMail($credentials, $userArray, $link) {
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = $credentials['host'];                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $credentials['nutzername'];                     // SMTP username
        $mail->Password   = $credentials['passwort'];                                // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = $credentials['port'];                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom($credentials['eigene_adresse'], $credentials['eigener_name']);
        foreach ($userArray as $userMail) {
            $mail->addAddress($userMail['mail']);               // Name is optional
        }
        $mail->addReplyTo($credentials['eigene_adresse'], $credentials['eigener_name']);
        //$mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject ='Neues Passwort Scio';
        $mail->Body    = 'Klicken Sie auf diesen Link oder geben Sie ihn im Browser ein: <a>' . $link . '</a>';
        $mail->AltBody = 'Klicken Sie auf diesen Link oder geben Sie ihn im Browser ein:' . $link ;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($mail) {
    // an den einzelnen user einen neuen link zusenden
    $userArray = $database->select('user', ["ID", 'mail'], ['mail' => $mail]);
    $id = $userArray[0]['ID'];
    $token = getToken($id, $privateKey);
    $link_main = getLink($token);
    $uniqid = uniqid('', false);
    $link = $link_main . '&id=' . $uniqid;
    $database->insert('auth_keys', ["key" => $uniqid]);
    $database->update("user", [
        "pw" => null
    ], ['ID' => $id]);
    sendKeyRequestMail($credentials, $userArray, $link);
}
else {
    // an alle neuen user einen neuen key-link zusenden
   $userArray = $database->select('user', ["ID", 'mail'], ['pw' => null]);
    foreach($userArray as $user) {
        $id = $user['ID'];
        $token = getToken($id, $privateKey);
        $link_main = getLink($token);
        $uniqid = uniqid('', false);
        $link = $link_main . '&id=' . $uniqid;
        $database->insert('auth_keys', ["key" => $uniqid]);
        sendKeyRequestMail($credentials, $userArray, $link);
   }
}




