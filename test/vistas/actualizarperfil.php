<form action="/" id="subirImagen">

    <input type="hidden" name="fn" value="agregar_imagen_perfil">
    <input type="file" id="imagendeperfil" name="imagendeperfil" accept="image/*">
    <input type="submit">
</form>

<script>
    $('#subirImagen').on('submit', function (e) {
        e.preventDefault();
//        var formData = $(this).serialize();
        var formData = new FormData();
        formData.append('fn', 'agregar_imagen_perfil');
        formData.append('file', $('#imagendeperfil')[0].files[0]);

        $.ajax({
            url: '/',
            type: 'POST',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (data) {
                console.log(data);
                data = JSON.parse(data);
                beep.parseResponse(data, 'imagenDePerfilActualizada');
            }
        });

    })
</script>