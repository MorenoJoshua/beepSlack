<a id="postear" onclick="" href="./?p=postear">
    Postear algo
</a>
<div id="feed">
    aqui va el feed
</div>
<form action="" id="hiddenform">
    <input type="hidden" name="fn" value="feed">
</form>
<script>
    beep.fetchHelper('/', $('#hiddenform').serialize(), 'mostrarFeed')
</script>