<div id="miniPerfil">
</div>
<div class="">
    <a href="./?p=postearenperfil&usuario=<?= $_REQUEST['usuario'] ?>">Postear algo</a>
</div>

<div id="wallAqui">
    aqui va el wall
</div>
<form action="" id="wallUsuario">
    <input type="hidden" name="fn" value="wall">
    <input type="hidden" name="usuario" value="<?= $_REQUEST['usuario'] ?>">
</form>
<script>
    beep.fetchHelper('/', $('#wallUsuario').serialize(), 'parseUsuarioWall');
</script>


<div id="perfilTarjetas"></div>

<!--<div id="miFeed">
</div>
<form action="" id="miFeedForma">
    <input type="hidden" name="fn" value="wall">
    <input type="hidden" name="usuario" value="<?/*= $_REQUEST['usuario'] */?>">
</form>
<script>
    beep.fetchHelper('/', $('#miFeedForma').serialize(), 'parseMiFeed');
</script>-->