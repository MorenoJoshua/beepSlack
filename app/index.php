<?php
require_once 'SimpleFw.php';

$beep = new SimpleFw();

$beep->view('templates/header');

$vista = isset($_GET['p']) && $_GET['p'] != '' ? $_GET['p'] : 'landing';


if (isset($_SESSION['nombre']) & $_SESSION['nombre'] != '') {
    $beep->view('templates/sidebar');
    $beep->view('templates/topbar');
    $beep->view('templates/uiPrep');
}


$beep->view($vista);
$beep->view('templates/footer');