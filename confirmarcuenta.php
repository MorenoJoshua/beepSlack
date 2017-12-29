<?php
require_once 'coneccion.php';
require_once 'Beep.php';
$b = new Beep($sql);
if ($b->db
    ->where('activo', 0)
    ->where('correo', $_REQUEST['e'])
    ->where('token', $_REQUEST['t'])
    ->update('usuarios', ['activo' => 1])
) {
    echo $b->templates->usuario_activado();
} else {
    echo 'Hubo un error al activar tu cuenta';
};