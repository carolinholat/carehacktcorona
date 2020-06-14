<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

function returnBase() {
    $database = new Medoo([
        'database_type' => 'mariadb',
        'database_name' => 'carehacktcorona',
        'server' => '127.0.0.1',
        'username' => 'coffeeJunkie',
        'password' => 'projuventa',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
    ]);
    return $database;
}
