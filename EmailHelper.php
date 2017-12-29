<?php

class EmailHelper
{
    public function sendEmail($to, $from, $subject, $body)
    {
//        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i";
//        if (preg_match($pattern, trim(strip_tags($to)))) {
            $cleanedTo = trim(strip_tags($to));
//        } else {
//            return "El correo que ingresaste es invalido, por favor intenta de nuevo!";
//        }

        $headers = "From: " . $from . "\r\n";
        $headers .= "Reply-To: " . strip_tags($from) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        if (mail($cleanedTo, $subject, $body, $headers)) {
//            echo 'mensaje enviado.';
            return true;
        } else {
//            echo 'prpoblema al enviar el mensaje.';
            return false;
//            die();
        }
    }
}