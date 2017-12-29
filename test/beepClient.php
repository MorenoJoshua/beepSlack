<?php

class BeepClient
{
    public function __construct()
    {
        session_start();

    }

    public function vista($vista, $data = [])
    {
        if ($vista == 'index') {
            session_destroy();
            unset($_SESSION);
        }
        foreach ($data as $k => $v) {
            $$k = $v;
        }
        $vistaACargar = 'vistas/' . $vista . '.php';
        if (file_exists($vistaACargar)) {
            require $vistaACargar;
        } else {
            require 'vistas/404.php';
        }
    }
}