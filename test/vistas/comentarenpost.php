<form action="/" method="post" id="comentarEnPost">
    <input type="hidden" name="fn" value="comentar">
    <input type="hidden" name="post" value="<?=$_GET['post']?>">
    <input type="text" name="comentario" placeholder="comentario">
    <input type="submit">
</form>
<script>
    $('#comentarEnPost').on('submit', function (e) {
        e.preventDefault();
        beep.fetchHelper('/', $(this).serialize(), 'comentarioOk')
    })
</script>