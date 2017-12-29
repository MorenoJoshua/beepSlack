<div id="amigos"></div>

<form action="" id="formaMisAmigos">
    <input type="hidden" name="fn" value="amigos">
    <input type="hidden" name="usuario" value="<?= @$_REQUEST['usuario'] ?>">
</form>
<script>
    beep.fetchHelper('/', $('#formaMisAmigos').serialize(), 'parsearAmigos');
</script>