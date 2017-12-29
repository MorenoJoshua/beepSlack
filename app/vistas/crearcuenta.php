<div class="container vert-center vh-100">

    <div class="col-xs-12">
        <div class="row ">
            <div class="col-xs-4 offset-xs-4 p-b-2">
                <div class="embed-responsive embed-responsive-1by1">
                    <img src="img/beepLogo.png" alt="" class="embed-responsive-item">
                </div>
            </div>
        </div>
        <div class="row">
            <form action="" id="crearForma" method="post">
                <div class="">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" placeholder="Nombre" class="form-control" id="nombre" required>
                </div>
                <div class="">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" placeholder="Apellido" class="form-control" id="apellido"
                           required>
                </div>
                <div class="">
                    <label for="correo">Correo</label>
                    <input type="text" name="correo" placeholder="Correo" class="form-control" id="correo" required>
                </div>
                <div class="">
                    <label for="nacimiento">Fecha de Nacimiento</label>
                    <input type="date" name="nacimiento" placeholder="Nacimiento" class="form-control" id="nacimiento"
                           required>
                </div>
                <div class="">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" placeholder="Contraseña" class="form-control" id="password"
                           required>
                </div>
                <div class="text-xs-center p-t-2">
                    <input type="checkbox" id="deacuerdo" required/>
                    <label for="deacuerdo">Acepto los <a href="" data-toggle="modal" data-target="#terminos">Terminos y
                            condiciones</a></label>
                </div>
                <div class=" p-t-1">
                    <input type="submit" class="btn btn-success form-control" id="crear" value="Crear cuenta">
                </div>
                <input type="hidden" name="fn" value="crear_usuario">
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="terminos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Terminos Y Condiciones</h4>
            </div>
            <div class="modal-body">
                Texto de terminos y condiciones
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $('#crearForma').on('submit', function (e) {
        e.preventDefault();
        datos = $(this).serialize();
        $('#crear').attr('disabled', true);
        $.post('/', datos, function (data) {
            data = JSON.parse(data);
            if (data.status == 'ok') {
                window.location = '?p=cuentacreada';
            } else {
                window.location = '?p=errorcrearcuenta';
            }
        })
    })
</script>