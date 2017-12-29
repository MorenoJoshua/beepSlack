<div id="formalogin"></div>

<!--<form action="" id="login">
    <input type="hidden" name="fn" value="iniciar_sesion">
    <input type="email" name="correo" placeholder="Correo">
    <input type="password" name="password" placeholder="Contrasenia">
    <input type="submit">
</form>
<div class="">No tienes cuenta? <a href="./?p=crearcuenta" class="red-text">Crear Una</a></div>
-->
<script>
    $('#formalogin').html(beep.iniciarSesionHTML());
    $('#login').on('submit', function (e) {
        e.preventDefault();
        beep.log($(this).serialize());
        beep.fetchHelper('/', $(this).serialize(), 'doLogin');
    });
</script>