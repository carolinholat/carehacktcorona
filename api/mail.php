<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use TelegramBot\Api\BotApi;

// Load Composer's autoloader
require dirname(__DIR__) . '/vendor/autoload.php';
require './mail_credentials.php';
use Medoo\Medoo;
require 'base.php';
$database = new Medoo($base_array_vars);
$credentials = $mail_credentials;
$bot = new TelegramBot\Api\BotApi('1250940848:AAE6V8nD8PNsd38ngWGrr20kV2XnBDUAAWY');

$question = $database->select('fragen', ['ID', 'frage', 'antwort', 'wichtigkeit'], ["ORDER" => ["zeitpunkt_antwort" => "DESC"]])[0];

$empfaenger = returnMessageReceivers($question['ID'], $database);

function sendMailMessage($userArray, $credentials, $question) {

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
        $mail->Subject = $question['frage'];
        $mail->Body    = '<h2>' . $question['frage'] . '</h2><br>' . $question['antwort'];
        $mail->AltBody = $question['frage'] . ' --> ' . $question['antwort'];

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($question['wichtigkeit'] === 'mail') {
    sendMailMessage($empfaenger, $credentials, $question);
}

if ($question['wichtigkeit'] === 'bot') {
    foreach($empfaenger as $user) {
        $bot->sendMessage($user['telegram'], $question['frage'] . ' --> ' . $question['antwort']);
    }
}

