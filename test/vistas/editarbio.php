<div class="tarjeta">
    <div class="titulo">Editar Biografia</div>
    <div class="contenido">
        <form action="/" method="post" id="editarBio">
        <input type="hidden" name="fn" value="agregar_bio">
        <textarea name="bio" id="bio" class="w-100 tarjetaInput materialize-textarea"><?=$_SESSION['bio']?></textarea>
            <input type="submit" value="Actualizar" class="tarjetaInput">
        </form>
    </div>
</div>
<script>
    $('#editarBio').on('submit', function (e) {
        e.preventDefault();
        beep.fetchHelper('/', $(this).serialize(), 'bioEditada')
    })
</script>