<?php

if (isset($_REQUEST['fn'])) {
//    $sql = [
//        'host' => 'localhost',
//        'user' => 'root',
//        'pwd' => 'CHANGEME',
//        'db' => 'beep',
//    ];
    require_once 'coneccion.php';
    require_once 'Beep.php';
    $beep = new Beep($sql);
    $fn = isset($_REQUEST['fn']) ? $_REQUEST['fn'] : 'nada';
    unset($_REQUEST['fn']);
    $beep->$fn();
} else {
//    var_dump($_REQUEST);
//    var_dump($_GET);
//    var_dump($_POST);
//    die(phpinfo());
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Beeprofiles.com</title>
    </head>
    <body>
    Temporal
    </body>
    </html>
    <?php
}
