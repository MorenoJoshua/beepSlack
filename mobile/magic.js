beepclient = new BeepClient();
beephelper = new BeepHelper();
beeptemplates = new BeepTemplates();

demojson = {
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
};


// beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post().texto(demojson));
// beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post().imagen(demojson));
// beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post().texto_imagen(demojson));