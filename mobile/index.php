<?php
//require 'clases/beepmobile.php';
require 'templates/header.php';
require 'templates/topbar.php';
require 'templates/sidebar.php';

//$beep = new BeepMobile();

?>
    <div class="container">
        <?php

        $archivo = 'vistas/' . array_keys($_REQUEST)[0] . '.php';
        if (file_exists($archivo)) {
            require $archivo;
        } else {
            require 'templates/404.php';
        }

        ?>
    </div>
<div id="test"></div>
<?php
require 'templates/footer.php';