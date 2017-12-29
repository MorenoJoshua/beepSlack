<form action="" id="crearcuenta">
    <input type="hidden" name="fn" value="crear_usuario">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="email" name="correo" placeholder="Correo">
    <input type="date" name="nacimiento" placeholder="Fecha de nacimiento">
    <input type="password" name="password" placeholder="Contrasenia">
    <input type="submit" class="disableOnSubmit">
</form>
<a href="./">Regresar</a>
<script>
    $('#crearcuenta').on('submit', function (e) {
        $('.disableOnSubmit').attr('disable', true);
        e.preventDefault();
        beep.log($(this).serialize());
        beep.fetchHelper('/', $(this).serialize(), 'cuentaCreada');
    });
</script>