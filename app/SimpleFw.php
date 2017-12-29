<?php

class SimpleFw
{
    function __construct()
    {
        session_start();
    }

    function view($vista, $data = [])
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }
        $vistaACargar = 'vistas/' . $vista . '.php';
        if (file_exists($vistaACargar)) {
            require_once $vistaACargar;
        } else {
            require_once 'vistas/templates/404.php';
        }
    }
}