<form action="" id="formaPostearEnPerfil">

    <div class="tarjeta">
        <div class="titulo">Postear en perfil</div>
        <div class="contenido">
            <input type="hidden" name="fn" value="postear">
            <input type="hidden" name="perfil" value="<?= $_REQUEST['usuario'] ?>" id="perfil">
            <textarea name="texto" id="texto" class="materialize-textarea" placeholder="Txto"></textarea>

            <div class="file-field input-field">
                <div class="btn">
                    <span>Imagen</span>
                    <input type="file" accept="image/*" name="imagenEnPost" id="imagenEnPost">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>

            <input type="submit" value="Postear">
        </div>
    </div>


</form>



<script>
    $('#formaPostearEnPerfil').on('submit', function (e) {
        e.preventDefault();
//        var formData = $(this).serialize();
        var formData = new FormData();
        formData.append('fn', 'postear');
        formData.append('texto', $('#texto').val());
        formData.append('usuario', $('#perfil').val());
        if ($('#imagenEnPost')[0].files[0]) {
            formData.append('file', $('#imagenEnPost')[0].files[0]);
        }



        $.ajax({
            url: '/',
            type: 'POST',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (data) {
                console.log(data);
                data = JSON.parse(data);
                beep.parseResponse(data, 'postPosteado');
            }
        });

    })
</script>