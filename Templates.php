<?php

class Templates
{
    private $dominio;

    public function __construct()
    {
        $this->dominio = 'https://beeprofiles.com';
    }

    public function create_email_template($email, $token)
    {
        return <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
<h3>Hola!</h3>
<p>Para activar tu cuenta en beeprofiles da click en el siguiente url:</p>
<a href="$this->dominio/confirmarcuenta.php?e=$email&t=$token">$this->dominio/confirmarcuenta.php?e=$email&t=$token</a>
</body>
</html>
HTML;

    }

    public function usuario_activado()
    {
        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                         <meta http-equiv="X-UA-Compatible" content="ie=edge">
             <title>Gracias por activar tu usuario</title>
</head>
<body>
  <h3>Tu usuario ha sido activado, puedes iniciar sesion!</h3>
</body>
</html>
HTML;

    }
}