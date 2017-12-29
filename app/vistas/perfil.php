<?php
require_once '/var/www/html/coneccion.php';
require_once '/var/www/html/Beep.php';
$beep = new Beep($sql);

$wall = $beep->_wallposts($_REQUEST['id']);

//var_dump($wall);
foreach ($wall as $postEnWall) {

    $imagen = $postEnWall['imagen'] != null ? "<img src=\"/imagenes/{$postEnWall['imagen']}\" alt=\"\" class='w-100'>" : '';
    $texto = $postEnWall['contenido'] != null ? "<div class=\"col-xs-12\">{$postEnWall['contenido']}</div>" : '';
    echo <<<HTML
<div class="p-b-1">
    <div class="">$imagen</div>
    <div class="">$texto</div>
</div>
<hr>

HTML;

}