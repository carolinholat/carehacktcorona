<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$bot = new TelegramBot\Api\BotApi('1250940848:AAE6V8nD8PNsd38ngWGrr20kV2XnBDUAAWY');

$bot->sendMessage(190101586, 'TEST');