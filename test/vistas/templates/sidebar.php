<?php
if (isset($_SESSION['nombre'])) {
    ?>
    <div id="topbar" class="deep-orange lighten-5 white-text">
        <div data-activates="slide-out" class="button-collapse material-icons black-text">menu</div>
        <a href="./?p=adentro">Beeprofiles</a>
        <div class="">
            <a class="material-icons" id="topnav" href="#modal1">info</a>
            <a class="material-icons" id="topnav" onclick="showBusqueda()">search</a>

        </div>
    </div>
    <ul id="slide-top" class="side-nav">
        <li><a href="#!">First Sidebar Link</a></li>
        <li><a href="#!">Second Sidebar Link</a></li>
    </ul>


    <ul id="slide-out" class="side-nav red">
        <li>
            <div class="userView">
                <!--            <div class="background">-->
                <!--                <img src="/imagenes/5851afc46b729.jpg">-->
                <!--            </div>-->
                <a href="./?p=miperfil"><img class="circle"
                                             src="/imagenes/<?= $_SESSION['imagen'] != 'null' ? $_SESSION['imagen'] : 'default.png' ?>"></a>
                <a href="./?p=miperfil"><span
                            class="white-xtext name"><?= $_SESSION['nombre'] ?> <?= $_SESSION['apellido'] ?></span></a>
                <a href="./?p=miperfil"><span class="white-xtext email"><?= $_SESSION['correo'] ?></span></a>
            </div>
        </li>
        <li>
            <a class="subheader darken-4">Perfiles</a>
        </li>
        <li>
            <a href="" class="dark-text"><i class="material-icons">perm_identity</i>Perfil Personal</a>
        </li>
        <li>
            <a href="./?p=amigos" class="dark-text"><i class="material-icons">timeline</i>Redes (amigos)</a>
        </li>
        <li>
            <a href="" class="dark-text"><i class="material-icons">toys</i>Intereses</a>
        </li>
        <li>
            <a class="subheader">Configuracion</a>
        </li>
        <li>
            <a class="" onclick="test()"><i class="material-icons">warning</i>Panico</a>
        </li>
        <li>
            <a class="" onclick="test2()"><i class="material-icons">lock</i>Privacidad</a>
        </li>
        <li>
            <a class="" onclick="beep.probarGPS()"><i class="material-icons">globe</i>probar GPS</a>
        </li>
        <li>
            <a class="" href="./cerrarsesion.php"><i class="material-icons">close</i>Cerrar sesion</a>
        </li>
    </ul>
    <script>
        $('.button-collapse').sideNav({
                menuWidth: 300, // Default is 240
                edge: 'left', // Choose the horizontal origin
                closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
                draggable: true // Choose whether you can drag to open on touch screens
            }
        );
    </script>
    <form action="" id="busqueda" style="display: none;;">
        <input type="hidden" name="fn" value="busqueda">
        <input type="text" name="query" id="query"

               placeholder="Busqueda de usuario por nombre/correo, mostrar en otro lado">
    </form>
    <div id="resultadosbusqueda"></div>

    <div id="modal1" class="modal bottom-sheet">
        <div class="modal-content">
            <h4>Notificaciones</h4>
            <p>Aqui van las notificaciones</p>
            <p>Se pueden actualizar sin generar cambios y bloquear el renderer</p>
            <p>No cascading y asi</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">Cerrar</a>
        </div>
    </div>


    <script>

        $('#busqueda').on('submit', function (e) {
            e.preventDefault();
            beep.fetchHelper('/', $(this).serialize(), 'parseBusquedaUsuario');
        });
        let activadorBusqueda = $('#query');
        activadorBusqueda.on('keyup', function (e) {
            if ($(this).val() != '') {
                $('#busqueda').submit();
            } else {
                $('#resultadosbusqueda').html('');
            }
        });
        activadorBusqueda.on('blur', function (e) {
            e.preventDefault();
            $('#busqueda').slideUp();
        });

        function showBusqueda() {
            $('#busqueda').slideDown();
            $('#query').focus();
        }

        $(document).ready(function () {
            // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
            $('.modal').modal();
        });

        function showNotificaciones() {

        }
    </script>

    <?php
}

?>