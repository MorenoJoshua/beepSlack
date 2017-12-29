class BeepHelper {
    constructor() {
        this.apiUrl = '/';
        this.api = this.api.bind(this);
        this.parseResponse = this.parseResponse.bind(this);
        this.wall = this.wall.bind(this);

        navigator.bh = this;
    }

    api(fn, payload = []) {
        let req = 'fn=' + fn;
        for (let param in payload) {
            req += `&${param}=${payload[param]}`;
        }
        let posting = $.post(this.apiUrl, req);
        posting.done(function (data) {
            data = JSON.parse(data);
            navigator.bh.parseResponse(fn, data);
        });
    }

    parseResponse(fn, data) {
        data.fn = fn;
        this[fn](data);
    }

    wall(data) {
        let posts = data.wall;
        for (let post in posts) {
            post = posts[post];
            let comentarios = post.comentarios;
            for (let comentario in comentarios) {
                comentario = comentarios[comentario];
            }
            beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post()[post.tipo](post));
            // beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post().imagen(post));
            // beepclient.container.insertAdjacentHTML('afterBegin', beeptemplates.post().texto_imagen(post));
        }
    }
}