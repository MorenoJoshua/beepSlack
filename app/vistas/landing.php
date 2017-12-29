<?php
//if (isset($_SESSION['nombre']) && $_SESSION['nombre'] != '') {
/*//    ?>
<!--    <script>-->
<!--        window.location = '?p=adentro'-->
<!--    </script>-->
<!--    --><?//*/
//} else {
//    ?>
    <div class="container vert-center vh-100">
        <div class="col-xs-12">
            <div class="col-xs-4 offset-xs-4 p-b-2">
                <div class="embed-responsive embed-responsive-1by1">
                    <img src="img/beepLogo.png" alt="" class="embed-responsive-item">
                </div>
            </div>
            <div class="col-xs-12">
                <?= isset($_GET['msg']) ? '<div class="alert alert-danger">' . $_GET['msg'] . '</div>' : '' ?>
            </div>
            <div class="col-xs-12">
                <form action="" method="post" id="login">
                    <input type="hidden" name="fn" value="iniciar_sesion">
                    <div class="row">
                        <label for="correo">Correo</label>
                        <input type="text" name="correo" placeholder="Correo" class="form-control">
                    </div>
                    <div class="row">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" placeholder="Contraseña" class="form-control"
                               id="password">
                    </div>
                    <div class="row p-t-1">
                        <input type="submit" value="Iniciar Sesion" class="btn btn-success form-control">
                    </div>
                </form>
            </div>
            <div class="col-xs-12 text-xs-center">
                No tienes cuenta? <a href="?p=crearcuenta">Crear cuenta</a>
            </div>
        </div>
    </div>
    <script>
        $('#login').on('submit', function (e) {
            e.preventDefault();
            datos = $(this).serialize();
            $.post('/', datos, function (data) {
                data = JSON.parse(data);
                if (data.status == 'ok') {
                    window.location = '?p=adentro'
                } else {
                    window.location = '?p=landing&msg=' + data.msg
                }
            })
        })
    </script>
    <?php
//}
