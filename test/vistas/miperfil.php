<div class="tarjeta">
    <div class="titulo">
        <div class="material-icons icono">face</div>
        Mi Perfil
        <a href="./?p=actualizarperfil" class="material-icons opcion">edit</a>
    </div>
    <div class="contenido">
        <div class="col offset-s4 s4">
            <img src="/imagenes/<?= $_SESSION['imagen'] ?>" alt="" class="w-100">
        </div>
        <div class=""></div>
    </div>
</div>
<div class="tarjeta">
    <div class="titulo">
        <div class="material-icons">person</div>
        Biografia
        <a href="./?p=editarbio" class="material-icons opcion">edit</a></div>
    <div class="contenido">
        <div class="">
            <?= $_SESSION['bio'] ?>
        </div>
        <div class=""></div>
    </div>
</div>
<div id="miFeed">
</div>
<form action="" id="miFeedForma">
    <input type="hidden" name="fn" value="wall">
    <input type="hidden" name="usuario" value="<?= $_SESSION['id'] ?>">
</form>
<script>
    beep.fetchHelper('/', $('#miFeedForma').serialize(), 'parseMiFeed');
</script>