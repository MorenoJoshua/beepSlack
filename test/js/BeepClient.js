var beep = {
    renderWall: function (wallData) {
    },
    renderPosts: function (posts) {
        var toreturn = '';
        posts.forEach(function (post) {
            console.log(post);
            toreturn += beep.postHTML(post)
        });
        return toreturn
    },
    renderComentarioHTML: function (comentario) {
        return `<div class="comentarioEnPost small">\n    <span class="red-text" onclick="beep.irAPerfil(${comentario.usuario.id})"><img src="/imagenes/${comentario.usuario.imagen}" alt="" class="imagenPerfilMini">${comentario.usuario.nombre}:</span>\n    <span class="comentarioContenido">${comentario.contenido}</span>\n</div>`;
    },
    postHTML: function (post) {
        var toreturn = '';

        var comentarios = '';
        post.comentarios.forEach(function (i) {
            comentarios += beep.renderComentarioHTML(i);
        });

        if (post.tipo === 'texto') {
            toreturn = `<div class="tarjeta" id="postanchor${post.id}">
    <div class="titulo">${post.usuario.nombre} ${post.usuario.apellido} - ${post.timestamp}</div>
    <div class="contenido">
        <div class="row">
            ${post.contenido}
        </div>
        <div class="row comentarios">
            ${comentarios}
        </div>
        <div class="row" onclick="beep.comentarEnPost(${post.id})">Comentar</div>
    </div>
</div>`;
        } else if (post.tipo === 'imagen') {
            toreturn = `<div class="tarjeta" id="postanchor${post.id}">
    <div class="titulo">${post.usuario.nombre} ${post.usuario.apellido} - ${post.timestamp}</div>
    <div class="contenido">
        <div class="row negpadding">
            <img src="/imagenes/${post.imagen}" alt="" class="w-100">
        </div>
        <div class="row comentarios">
            ${comentarios}
        </div>
        <div class="row" onclick="beep.comentarEnPost(${post.id})">Comentar</div>
    </div>
</div>`;
        } else if (post.tipo === 'texto_imagen') {
            toreturn = `<div class="tarjeta" id="postanchor${post.id}">
    <div class="titulo">${post.usuario.nombre} ${post.usuario.apellido} - ${post.timestamp}</div>
    <div class="contenido">
        <div class="row">
            ${post.contenido}
        </div>
        <div class="row negpadding">
            <img src="/imagenes/${post.imagen}" alt="" class="w-100">
        </div>
        <div class="row comentarios">
            ${comentarios}
        </div>
        <div class="row" onclick="beep.comentarEnPost(${post.id})">Comentar</div>
    </div>
</div>`;
        }
        return toreturn;
    },
    iniciarSesionHTML: function () {
        return '<div class="tarjeta">\n    <div class="titulo"><div class="icono material-icons">lock_outline</div>Iniciar Sesion</div>\n    <div class="contenido">\n        <form action="" id="login">\n            <input class="tarjetaInput" type="hidden" name="fn" value="iniciar_sesion">\n            <input class="tarjetaInput" type="email" name="correo" placeholder="Correo">\n            <input class="tarjetaInput" type="password" name="password" placeholder="Contrasenia">\n            <input class="tarjetaInput" type="submit" value="Iniciar Sesion">\n        </form>\n        <div class="">No tienes cuenta? <a href="./?p=crearcuenta" class="red-text">Crear Una</a></div>\n    </div>\n</div>'
    },
    parseResponse: function (response, funcion) {
        if (response.status == 'ok') {
            // Materialize.toast(response.msg || 'OK', 4000, 'green');
            Materialize.toast(response.msg, 4000, 'green');
            beep[funcion](response)
        } else {
            Materialize.toast('Error - ' + response.msg, 4000, 'red');
        }
    },
    fetchHelper: function (url, data, callback) {
        $.post(url, data, function (data) {
            data = JSON.parse(data);
            beep.parseResponse(data, callback);
        })
    },
    log: function (response) {
        console.log(response);
    },
    doLogin: function (response) {
        beep.log(response);
        if (response.status == 'ok') {
            window.location = '?p=adentro';
        }
    },
    mostrarFeed: function (feed) {

        var torender = beep.renderPosts(feed.feed);
        $('#feed').html(torender);
    },
    parseBusquedaUsuario: function (resultados) {
        var toreturn = '';
        resultados.usuarios.forEach(function (i) {
            toreturn += beep.perfilUsuarioChicoHTML(i);
        });
        $('#resultadosbusqueda').html(toreturn);
    },
    perfilUsuarioChicoHTML: function (perfil) {
        return '<div class="miniUsuario" onclick="beep.irAPerfil(' + perfil.id + ')"> <div class="miniImagen"><img src="/imagenes/' + (perfil.imagen || 'default.png') + '" alt="" class="w-100"></div> <div class="">' + perfil.nombre + ' ' + perfil.apellido + '</div> </div>';

    },
    perfilUsuarioGrandeHTML: function (perfil) {
        return `<div class="tarjeta">
    <div class="row contenido">
        <div class="vert-center ">
            <img class="circle imagenPerfilMed left" src="/imagenes/${perfil.imagen || 'default.png'}">
            <h4 class="white-xtext name big truncate">${perfil.nombre} ${perfil.apellido}</h4>
        </div>
        <div class="row p-t-2">
            <h5 class="">${perfil.bio || 'Este perfil no cuenta con una biografia'}</h5>
        </div>
    </div>
</div>
`;
    },
    irAPerfil: function (idUsuario) {
        window.location = '?p=perfil&usuario=' + idUsuario;
    },
    parseUsuarioWall: function (wall) {
        var posts = wall.wall;
        console.log(wall);
        var torender = '';
        // var torender = beep.renderPosts(posts);

        $('#miniPerfil').html(beep.perfilUsuarioGrandeHTML(wall.usuario));
        console.info(wall.usuario);
        posts.forEach(function (i) {
            torender += beep.postHTML(i);
        });
        // console.log(torender);
        $('#wallAqui').html(torender);
    },
    comentarEnPost: function (postId) {
        window.location = './?p=comentarenpost&regresara=' + beep.get()["usuario"] +
            '&post=' + postId + '#postanchor' + postId;
    },
    get: function () {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
            function (m, key, value) {
                vars[key] = value;
            });
        return vars;
    },
    comentarioOk: function (respuesta) {
        if (respuesta.status == 'ok') {
            window.location = '?p=perfil&usuario=' + beep.get()['regresara'];
        }
    },
    cuentaCreada: function (respuesta) {
        if (respuesta.status == "ok") {
            Materialize.toast('Revisa tu correo <a href="./" class="white-text">Regresar</a>', 5000, 'green');
            // window.location = './';

        }
    },
    imagenDePerfilActualizada: function (data) {
        if (data.status == 'ok') {
            history.back();
        }
    },
    probarGPS: function () {
        beep.gpsobserver = navigator.geolocation.watchPosition(beep.GPSOk, beep.GPSError, {
            enableHighAccuracy: true,
            timeout: 10e3,
            maximumAge: 60e3
        });
    },
    GPSOk: function (geolocation) {
        var coords = geolocation.coords;
        toastText = coords.latitude + ', ' + coords.longitude;
        Materialize.toast(toastText, 1000)

        beep.IfDistancia(coords);


    }, GPSError: function (error) {
        Materialize.toast(error, 1000, 'red');
    },
    ifDistancia: function (coords) {
        if ((Math.abs(coords.latitude - beep.posicion.lat) > 0.00001) ||
            (Math.abs(coords.longitude - beep.posicion.lng) > 0.00001)) {
            // posicion cambio lo sficiente
            beep.posicion.lat = coords.latitude;
            beep.posicion.lng = coords.longitude;

            beep.ping('posicion');
        }
    },
    ping: function (tipo) {
        switch (tipo) {
            case 'posicion':
                beep.fetchHelper('/', 'fn=ping&lat=&' + beep.posicion.lat + 'lng=' + beep.posicion.lng, 'nada');
                break;
        }
        //contactar con servidor remoto, normalmente para actualizar info de GPS;
    },
    nada: function () {
        return true;
    },
    bioEditada: function (data) {
        if (data.status == 'ok') {
            window.location = './?p=miperfil';
        }
    },
    postPosteado: function (data) {
        if (data.status == 'ok') {
            history.back();
        }
    },
    parseMiFeed: function (data) {
        if (data.status == 'ok') {
            posts = '';
            data.wall.forEach(function (post) {
                posts += beep.postHTML(post)
            });
            $('#miFeed').html(posts);
        }
    },
    parsearAmigos: function (amigos) {
        var torender = beep.tarjetaAmigosHTML(amigos.amigos);
        $('#amigos').html(torender);
    },
    tarjetaAmigosHTML: function(amigos){
        var torender = '';
        amigos.forEach(function (amigo) {
            torender += beep.amigoEnTarjetaHTML(amigo);
        });

        var toreturn = `<div class="tarjeta">
<div class="titulo">Amigos</div><div class="contenido">
<div class="row">${torender}</div>
</div>
</div>`;

        return toreturn
    },
    amigoEnTarjetaHTML: function (amigo) {
        return `<div class="col s4" onclick="beep.irAPerfil(${amigo.id})">
    <img src="/imagenes/${amigo.imagen || 'default.png'}" alt="" class="w-100 circle "><div class="w-100 text-center">${amigo.nombre} ${amigo.apellido}</div>
</div>`;
    },
    posicion: {lat: false, lng: false},
};

/*
 function renderPosts(posts) {
 }

 function parseResponse(response, funcion) {

 }
 function renderWall(wall) {
 }
 */

function test() {
    data = 'fn=iniciar_sesion';
    beep.fetchHelper('/', data, 'log')
}
function test2() {
    data = 'fn=sesion';
    beep.fetchHelper('/', data, 'log')
}