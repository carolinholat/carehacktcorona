<?php

use Medoo\Medoo;
error_log('Test successful');

  /*  $database = new Medoo([
        'database_type' => 'mysql',
        'database_name' => 'carehacktcorona',
        'server' => 'localhost',
        'username' => 'coffeeJunkie',
        'password' => 'coffeeJunkie',
         'charset' => 'utf8mb4',
         'collation' => 'utf8mb4_general_ci',
    ]); */

 // use Database;
  $array = [
      'database_type' => 'mysql',
      'database_name' => 'carehacktcorona',
      'server' => 'localhost',
      'username' => 'coffeeJunkie',
      'password' => 'coffeeJunkie',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
  ];
 // $dbObj = new Database\Database($array);
 // $db = $dbObj->database;

//var_dump($db);