class BeepTemplates {
    constructor() {
        console.log('%cTemplates Cargadas', 'color:red; font-size:1em;')
    }

    post() {
        /*json={
         "status": "ok",
         "post": {
         "id": 24,
         "usuario": 2,
         "tipo": "texto",
         "contenido": "textoeste es el texto de la imagen que se supone hize resize",
         "imagen": "584a73fcd9d01.jpg",
         "fecha": null,
         "hora": null,
         "grupo": null,
         "equipo": null,
         "comentarios": [
         {
         "id": 26,
         "post": 24,
         "usuario": {
         "imagen": "584b3ff030b5f.jpg",
         "nombre": "joshua",
         "apellido": "moreno",
         "id": 2
         },
         "contenido": "probando comentario",
         "fecha": "0000-00-00",
         "hora": "00:00:00"
         }
         ]
         }
         };*/
        return {
            texto: function (post) {
                let comentariosHTML = '';
                post.comentarios.forEach(function (i) {
                    comentariosHTML += beeptemplates.comentario(i)
                });
                return `<div class="post">
    <div class="main">
        <div class="">
        ${post.contenido}
        </div>
    </div>
    <ul class="comentarios">
        ${comentariosHTML}
    </ul>
</div>`
            },
            imagen: function (post) {
                let comentariosHTML = '';
                post.comentarios.forEach(function (i) {
                    comentariosHTML += beeptemplates.comentario(i)
                });
                return `<div class="post">
    <div class="main">
        <div class="">
            <img src="/imagenes/${post.imagen}" alt="" class="postimagen">
        </div>
    </div>
    <ul class="comentarios">
        ${comentariosHTML}
    </ul>
</div>`
            },
            texto_imagen: function (post) {
                let comentariosHTML = '';
                post.comentarios.forEach(function (i) {
                    comentariosHTML += beeptemplates.comentario(i)
                });
                return `<div class="post">
    <div class="main">
        <div class="">
            <img src="/imagenes/${post.imagen}" alt="" class="postimagen">
        </div>
        <div class="">
        ${post.contenido}
        </div>
    </div>
    <ul class="comentarios">
        ${comentariosHTML}
    </ul>
</div>`
            }
        }
    }

    comentario(json) {
        /*json=            {
         "id": 26,
         "post": 24,
         "usuario": {
         "imagen": "584b3ff030b5f.jpg",
         "nombre": "joshua",
         "apellido": "moreno",
         "id": 2
         },
         "contenido": "probando comentario",
         "fecha": "0000-00-00",
         "hora": "00:00:00"
         }
         */
        return `<li class="comentario"><img src="/imagenes/${json.usuario.imagen}" class="miniimagen" alt=""><br>${json.usuario.nombre} ${json.usuario.apellido} ${json.contenido}</li>`
    }
}
