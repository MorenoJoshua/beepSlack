<form action="/" method="post" id="postearForma">
    <input type="hidden" name="fn" value="postear">
    <textarea name="texto" id="texto" class="materialize-textarea" placeholder="Txto"></textarea>
    <input type="file" accept="image/*" name="imagenEnPost" id="imagenEnPost">
    <input type="submit" value="Postear">
</form>

<script>
    $('#postearForma').on('submit', function (e) {
        e.preventDefault();
//        var formData = $(this).serialize();
        var formData = new FormData();
        formData.append('fn', 'postear');
        formData.append('texto', $('#texto').val());
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