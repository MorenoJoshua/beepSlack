<?php
$vista = isset($_GET['p']) ? $_GET['p'] : 'index';
require_once 'beepClient.php';
$bc = new BeepClient();

if (!isset($_SESSION['nombre'])) {
    if ($vista == 'crearcuenta') {
        $vistas = [
            'templates/header',
            'templates/sidebar',
            'crearcuenta',
            'templates/footer',
        ];
    } else {

        $vistas = [
            'templates/header',
            'index',
            'templates/footer',
        ];
    }
} else {
    if ($vista == 'index') {
        $vista = 'adentro';
    }
    $vistas = [
        'templates/header',
        'templates/sidebar',
        $vista,
        'templates/footer',
    ];
}

foreach ($vistas as $vista) {
    $bc->vista($vista);
}

/*$bc->vista('templates/header');
$bc->vista('templates/sidebar');
$bc->vista('templates/' . $vista);
$bc->vista('templates/footer');*/