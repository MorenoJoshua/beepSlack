<?php

class Beep
{
    public $db;
    public $eh;
    public $img;
    public $dist;
    public $templates;
    public $requests;
    public $usuario;
    public $baseFolder;
    public $imagenesFolder;
    public $cvFolder;

    public function __construct($sql)
    {
        if (!isset($_SESSION['nombre'])) {
            session_start();
        }
//        ya
        $this->usuario = isset($_SESSION['id']) ? $_SESSION['id'] : null;

        $this->baseFolder = '/var/www/html/';
        $this->imagenesFolder = 'imagenes/';
        $this->cvFolder = 'cv/';

        require_once $this->baseFolder . '/clases/MysqliDb.php';
        $this->db = new MysqliDb($sql['host'], $sql['user'], $sql['pwd'], $sql['db']);

        require_once $this->baseFolder . '/EmailHelper.php';
        $this->eh = new EmailHelper();

        require_once $this->baseFolder . '/Templates.php';
        $this->templates = new Templates();

        require_once $this->baseFolder . '/Requests.php';
        $this->requests = new Requests();

        require_once $this->baseFolder . '/clases/SimpleImage.php';
        $this->img = new SimpleImage();

        require_once $this->baseFolder . '/clases/Dist.php';
        $this->dist = new Dist();
//        $this->crear_usuario();
    }

    public function test()
    {
//        ya
//        echo json_encode($arr);
        if ($this->usuario != null) {
            $info = $_SESSION;
            echo json_encode($info, JSON_PRETTY_PRINT);
        } else {
//            $this->eh->sendEmail('joshua200128@gmail.com', 'test@beeprofiles.com', 'testing email', 'body of the email');
            $this->eh->sendEmail('joshua200128+asd@gmail.com', 'test@beeprofiles.com', 'testing email', 'body of the email');
//            $this->eh->sendEmail('j@morenojoshua.com', 'test@beeprofiles.com', 'testing email', 'body of the email');
//            $this->eh->sendEmail('ichos@live.com', 'test@beeprofiles.com', 'testing email', 'body of the email');
//            $this->eh->sendEmail('morenojoshua@outlook.com', 'test@beeprofiles.com', 'testing email', 'body of the email');
        }
    }

    public function crear_usuario()
    {
//        ya
//        $_REQUEST = $this->requests->crear_usuario();
        if (isset($_REQUEST['nombre']) &&
            isset($_REQUEST['apellido']) &&
            isset($_REQUEST['correo']) &&
            isset($_REQUEST['nacimiento']) &&
            isset($_REQUEST['password'])
        ) {

            $randomToken = substr(md5(microtime()), -8, 6);
            if (!$this->_existe_usuario($_REQUEST['correo'])) {
                $this->_email_creacion($_REQUEST['correo'], $randomToken);
                $_REQUEST['token'] = $randomToken;

                $toinsert = $_REQUEST;
                $toinsert['password'] = $this->_enc_pwd($toinsert['password']);

                if ($this->db->insert('usuarios', $toinsert)) {
                    echo $this->_status_ok();
                    return true;
                } else {
                    $this->_status_error('hubo un error al agregar usuario', __LINE__);
                }
            } else {
                echo $this->_status_error('Ya existe usuario', __LINE__);
            }
        } else {
            echo $this->_status_error('hubo un error al agregar usuario', __LINE__);
        }
        return false;
    }

    private function _existe_usuario($usuario)
    {
//        ya
        return count($this->db->where('correo', $usuario)->get('usuarios')) > 0;
    }

    private function _email_creacion($email, $token)
    {
//        ya
        $this->eh->sendEmail(
            $email,
            'hello@beeprofiles.com',
            'Creacion de cuenta en Beeprofiles.com',
            $this->templates->create_email_template($email, $token)
        );
    }

    private function _enc_pwd($password)
    {
//        ya
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function _status_ok($mensaje = '')
    {
//        ya
        $toreturn = ['status' => 'ok'];

        if (is_array($mensaje)) {
            $toreturn = array_merge($toreturn, $mensaje);
        } else {
            $arr = ['msg' => $mensaje];
            $toreturn = array_merge($toreturn, $arr);
        }

        return json_encode($toreturn, 128);
    }

    private function _status_error($mensaje = '', $linea = __LINE__)
    {
//        ya

        $toreturn = ['status' => 'error'];
        $arr = ['msg' => $mensaje . '. e' . __FUNCTION__ . $linea];
        $toreturn = array_merge($toreturn, $arr);
        return json_encode($toreturn, 128);
    }

    public function agregar_profesion()
    {
//        ya
//        no duplicados, indices corregidos
        $toinsert = [
            'usuario' => $this->usuario,
            'profesion' => $_REQUEST['profesion']
        ];
        $res = $this->db
            ->where('usuario', $this->usuario)
            ->where('profesion', $_REQUEST['profesion'])
            ->get('profesiones');
        if (count($res) == 0) {
            if ($this->db->insert('profesiones', $toinsert)) {
                echo $this->_status_ok('se agrego la profesion');
            } else {
                echo $this->_status_error('hubo un error al agregar profesion', __LINE__);
            }
        } else {
            echo $this->_status_error('Profesion ya existente');
        }

    }

    public function borrar_profesion()
    {
//        ya
        $res = $this->db->where('usuario', $this->usuario)->where('id', $_REQUEST['id'])->get('profesiones');
        if (count($res) == 1) {
            if ($this->db
                ->where('usuario', $this->usuario)
                ->where('id', $_REQUEST['id'])
                ->delete('profesiones')
            ) {
                echo $this->_status_ok('profesion eliminada correctamente');
                return true;
            } else {
                echo $this->_status_error('hubo un error al eliminar profesion.', __LINE__);
            }
        } else {
            echo $this->_status_error('No existente', __LINE__);
        }
        return false;
//        echo $this->_status_error('errr');
    }

    public function bio()
    {
//        ya
        if ($this->usuario != null) {
            $res = $this->db->where('id', $_REQUEST['id'])->get('usuarios');
            if (count($res) == 1) {
                $bio = ['bio' => $res[0]['bio']];
                echo $this->_status_ok($bio);
                return true;
            } else {
                echo $this->_status_error('usuario no existe', __LINE__);
            }
        } else {
            echo $this->_status_error('no logind', __LINE__);
        }
        return false;
    }

    public function agregar_bio()
    {
//        ya
        if ($this->db->where('id', $this->usuario)->update('usuarios', ['bio' => $_REQUEST['bio']])) {
            $_SESSION['bio'] = $_REQUEST['bio'];
            echo $this->_status_ok('actualizada correctamente');
        } else {

            echo $this->_status_error('hubo un error al actualizar tu biografia', __LINE__);
        }
    }

    public function profesiones()
    {
//        ya
        if ($this->_existe_usuario_por_id($_REQUEST['id'])) {
            $profesiones = $this->db->where('usuario', $_REQUEST['id'])->get('profesiones', null, 'id, profesion');
            echo $this->_status_ok(['profesiones' => $profesiones]);
        } else {
            echo $this->_status_error('usuario no existe');
        }
    }

    private function _existe_usuario_por_id($usuario)
    {
//        ya
        return count($this->db->where('id', $usuario)->get('usuarios')) > 0;
    }

    public function iniciar_sesion()
    {
//        ya
//        $_REQUEST = $this->requests->crear_usuario();
        if (count($this->db->where('correo', $_REQUEST['correo'])->get('usuarios')) == 1) {
            if ($this->_ver_pwd($_REQUEST['correo'], $_REQUEST['password'])) {
                $_SESSION = $this->db
                    ->where('correo', $_REQUEST['correo'])
                    ->where('activo', 1)
                    ->get('usuarios', 1, 'id, nombre, apellido, correo, imagen, bio')[0];

                if ($_SESSION['imagen'] == null) {
                    $_SESSION['imagen'] = 'default.png';
                }

                echo $this->_status_ok('Inicio de sesion exitoso');
            } else {
                echo $this->_status_error('Usuario o contraseña incorrectos', __LINE__);
            }
        } else {
            echo $this->_status_error('usuario no existe', __LINE__);
        }
    }

    private function _ver_pwd($usuario, $password)
    {
//        ya
        $hash = $this->db
            ->where('correo', $usuario)
            ->where('activo', 1)
            ->get('usuarios', 1, 'password')[0]['password'];
        return password_verify($password, $hash);
    }

    public function nada()
    {
//        ya
        echo $this->_status_error('Falto definir una funcion');
    }

    public function sesion()
    {
//        ya
        echo $this->_status_ok($_SESSION);
    }

    public function wall()
    {
//        TODO
//        regresar solo una ista de posts

        if (isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != '') {
            $posts = $this->_wallposts($_REQUEST['usuario']);
            $perfil = $this->_perfil_por_id($_REQUEST['usuario']);

            echo $this->_status_ok(['wall' => $posts, 'usuario' => $perfil]);
        } else {
            echo $this->_status_error('hubo un error al obtener los posts', __LINE__);
        }

//        if (isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != '') {
//            if ($posts = $this->db->where('usuario', $_REQUEST['usuario'])->orderBy('id', 'desc')->get('posts', 30)) {
//
//                $wall = [];
//                foreach ($posts as $post) {
//                    $tempPost = $post;
//                    $tempPost['comentarios'] = $this->db->where('post', $post['id'])->get('comentarios');
//                    $wall[] = $tempPost;
//                }
//                echo $this->_status_ok(['wall' => $wall]);
//            } else {
//                echo $this->_status_error('hubo un error al obtener el walld e la persona');
//            }
//        } else {
//            echo $this->_status_error('no usuario especificado', __LINE__);
//        }
    }

    public function _wallposts($usuario)
    {
        $desde = isset($_REQUEST['ultimo']) && $_REQUEST['ultimo'] != '' ? 'id < ' . $_REQUEST['ultimo'] : '1=1';
        $posts = $this->db
            ->where($desde)
            ->where('usuario', $usuario)
            ->orWhere('perfil', $usuario)
            ->orderBy('timestamp', 'desc')->get('posts', 30);

        $usuariosCache = []; // evitar queries y usar usuarios en memoria

        $postsLimpios = [];
        foreach ($posts as $post) {
            $comentarios = $this->db->where('post', $post['id'])->get('comentarios');
            $comentariosFinal = [];
            foreach ($comentarios as $comentario) {
                $comentarioLimpio = $comentario;
//                $comentarioLimpio['usuario'] = $this->_perfil_por_id($comentario['usuario']);
                if (isset($usuariosCache[$comentario['usuario']])) {
                    $usuario = $usuariosCache[$comentario['usuario']];
                } else {
                    $usuario = $this->_usuario_info($comentario['usuario']);
                    $usuariosCache[$usuario['id']] = $usuario;
                }
                $comentarioLimpio['usuario'] = $usuario;
                $comentariosFinal[] = $comentarioLimpio;
            }
            $postLimpio = $post;
            if (isset($post['usuario'])) {
                if (isset($usuariosCache[$post['usuario']])) {
                    $poster = $usuariosCache[$post['usuario']];
                } else {
                    $poster = $this->_usuario_info($post['usuario']);
                    $usuariosCache[$post['usuario']] = $poster;
                }
            } else {
                $poster = $this->_usuario_info($post['usuario']);
                $usuariosCache[$post['usuario']] = $poster;
            }

            $postLimpio['comentarios'] = $comentariosFinal;
            $postLimpio['usuario'] = $poster;
            $postsLimpios[] = $postLimpio;
        }
        return $postsLimpios;
    }

    public function _usuario_info($id)
    {
        return $this->db->where('id', $id)->get('usuarios', 1, 'imagen, nombre, apellido, id, bio')[0];

    }

    public function _perfil_por_id($id)
    {
//        ya
        return $this->db->where('id', $id)->get('usuarios', 1, 'id, nombre, apellido, imagen, bio')[0];

    }

    public function feed()
    {
        if ($amigos = $this->db->where('amigo', $this->usuario)->get('amigos')) {
            $amigosLimpio = [];
            foreach ($amigos as $amigo) {
                $amigosLimpio[] = $amigo['main'];
            }

            $amigosArray = join(',', $amigosLimpio);

            if ($posts = $this->db->where('usuario in (' . $amigosArray . ')')->get('posts')) {
                $feed = [];
                foreach ($posts as $post) {
                    $tempPost = $post;
                    $tempPost['comentarios'] = $this->db->where('post', $post['id'])->orderBy('id', 'desc')->get('comentarios', 100);
                    $feed[] = $tempPost;
                }
                echo $this->_status_ok(['feed' => $feed]);
            } else {
//                echo $this->_status_error('hubo un problema al obtener tu feed', __LINE__);
                echo $this->_status_ok(['feed' => [], 'msg' => 'no hay contenido para mostrar']);
            }
        } else {
//            echo $this->_status_error('hubo un problema al obtener tu lista de amigos', __LINE__);
            echo $this->_status_ok('Agrega amigos para ver sus posts');
        }

    }

    public function comentar()
    {
//ya
        if ($this->_existe_post($_REQUEST['post'])) {
            if (isset($_REQUEST['post'])) {
                if (isset($_REQUEST['comentario'])) {
                    $insert = [
                        'post' => $_REQUEST['post'],
                        'usuario' => $this->usuario,
                        'contenido' => $_REQUEST['comentario']
                    ];
                    if ($this->db->insert('comentarios', $insert)) {
                        echo $this->_status_ok('comentario posteado exitosamente');
                    } else {
                        echo $this->_status_error('hubo un error al postear tu comentario');
                    }
                } else {
                    echo $this->_status_error('y el comentario mi chavo?', __LINE__);
                }
            } else {
                echo $this->_status_error('falto post a cual comentar', __LINE__);
            }
        } else {
            echo $this->_status_error('no existe tal post', __FILE__);
        }
    }

    private function _existe_post($post)
    {
//        ya

        return count($this->db->where('id', $post)->get('posts')) > 0;
    }

    public function borrar_comentario()
    {
//        ya
        if (isset($_REQUEST['comentario']) && $_REQUEST['comentario'] != '') {
            if ($this->_existe_comentario($_REQUEST['comentario'])) {
                if ($this->db->where('id', $_REQUEST['comentario'])->where('usuario', $this->usuario)->delete('comentarios')) {
                    echo $this->_status_ok('comentario eliminado satisfactoriamente');
                } else {
                    echo $this->_status_error('hubo un problema al eliminar tu comentario', __LINE__);
                }
            } else {
                echo $this->_status_error('comentario no existente', __LINE__);
            }
        } else {
            echo $this->_status_error('falta id de comentario', __LINE__);
        }
    }

//    public function get_post($id)
//    {
//        $post = $this->db->where('id', $id)->get('posts', 1)[0];
//        $post['comentarios'] = $this->post_comentarios($id);
//        return $post;
//    }

    private function _existe_comentario($comentario)
    {
//        ya
        return count($this->db->where('id', $comentario)->get('comentarios')) > 0;
    }

    public function agregar_usuario()
    {
//        ya
        $_REQUEST['usuario'] != $this->usuario ?: die($this->_status_error('no te puedes agregar a ti mismo', __LINE__));
        if (isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != '') {
            if ($this->_existe_usuario_por_id($_REQUEST['usuario'])) {
                if (!$this->_son_amigos($this->usuario, $_REQUEST['usuario']) || !$this->_existe_solicitud($this->usuario, $_REQUEST['usuario'])) {
                    $insert = [
                        'main' => $this->usuario,
                        'amigo' => $_REQUEST['usuario'],
                        'permisos' => 99
                    ];
                    if ($this->db->insert('amigos', $insert)) {
                        echo $this->_status_ok('solicitud enviada');
                    } else {
                        echo $this->_status_error('hubo un error al enviar tu solicitud', __LINE__);
                    }
                } else {
                    echo $this->_status_error('Ya existe solicitud, o ya son amigos', __LINE__);
                }
            } else {
                echo $this->_status_error('usuario no existente', __LINE__);
            }
        } else {
            echo $this->_status_error('error en el usuario a agregar', __LINE__);
        }
    }

    private function _son_amigos($a, $b)
    {
//        ya
        $res = $this->db->where('main', $a)->where('amigo', $b)->get('amigos');
        return count($res) > 0;

    }

    /*    public function aceptar_usuario()
        {
            isset($_REQUEST['u']) && $_REQUEST['u'] != '' ?: die($this->_status_error());
            $_REQUEST['u'] != $this->usuario ?: die($this->_status_error());

            if ($this->_existe_usuario_por_id($_REQUEST['u'])) {
                $ver = $this->db
                    ->where('main', $this->usuario)
                    ->where('amigo', $_REQUEST['u'])
                    ->get('amigos');
                if (count($ver) != 1) {
                    echo "no hay solicitud";
                } else {
                    $insert = [
                        'main' => $this->usuario,
                        'amigo' => $_REQUEST['u'],
                        'permisos' => 0
                    ];
                    $this->db->where('main', $this->usuario)->where('amigo', $_REQUEST['u'])->update('amigos', $insert);
                    $insert = [
                        'amigo' => $this->usuario,
                        'main' => $_REQUEST['u'],
                        'permisos' => 0
                    ];
                    $this->db->insert('amigos', $insert);
                    echo $this->_status_ok();
                }
            } else {
                echo 'usuario no existente';
            };
        }*/

    public function _existe_solicitud($a, $b)
    {
//        ya
        $res = $this->db->where('main', $a)->where('amigo', $b)->where('permisos', '99')->get('amigos');
        if (count($res) == 0) {
            $res = $this->db->where('amigo', $a)->where('main', $b)->where('permisos', '99')->get('amigos');
        }
        return count($res) > 0;
    }

    public function aceptar_solicitud()
    {
//        ya
        if ($this->_existe_solicitud($this->usuario, $_REQUEST['usuario'])) {
            $this->db->where('main', $this->usuario)->where('amigo', $_REQUEST['usuario'])->delete('amigos');
            $this->db->where('amigo', $this->usuario)->where('main', $_REQUEST['usuario'])->delete('amigos');
            $insert = [
                'main' => $this->usuario,
                'amigo' => $_REQUEST['usuario'],
                'permisos' => '1'
            ];
            $this->db->insert('amigos', $insert);
            $insert = [
                'amigo' => $this->usuario,
                'main' => $_REQUEST['usuario'],
                'permisos' => '1'
            ];
            $this->db->insert('amigos', $insert);
            echo $this->_status_ok('tienes un nuevo amigo');
        } else {
            echo $this->_status_error('solicitud no existente');
        }
    }

    public function postear()
    {
//        ya
//        $testTemp = 'test_jpg';

        $testTemp = count($_FILES) == 1 ? array_keys($_FILES)[0] : '';

        $texto = false;
        $imagen = false;
//        var_dump($testTemp);

        if (isset($_REQUEST['texto']) && $_REQUEST['texto'] != '') {
            $texto = true;
        }
        if (isset($_FILES[$testTemp]['tmp_name'])) {
            $imagen = true;
        }

        $perfil = isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != '' ? $_REQUEST['usuario'] : null;

        if ($texto == true && $imagen == true) {
            $this->_post_texto_imagen($_REQUEST['texto'], $_FILES[$testTemp], $perfil);
        } elseif ($texto == false && $imagen == true) {
            $this->_post_imagen($_FILES[$testTemp], $perfil);
        } elseif ($texto == true && $imagen == false) {
            $this->_post_texto($_REQUEST['texto'], $perfil);
        } else {
            echo $this->_status_error('no hay que postear', __LINE__);
        }
    }

    private function _post_texto_imagen($texto, $archivo, $perfil)
    {
//        ya
        $this->img->load($archivo['tmp_name']);
        $archivoFinal = uniqid() . '.' . $this->img->extension();
        $this->_mover_y_cambiar_tamanio_de_imagen($archivo['tmp_name'], $this->imagenesFolder . $archivoFinal);
        $insert = [
            'usuario' => $this->usuario,
            'tipo' => 'texto_imagen',
            'perfil' => $perfil,
            'contenido' => $texto,
            'imagen' => $archivoFinal
        ];
        if ($this->db->insert('posts', $insert)) {
            echo $this->_status_ok('post posteado correctamente');
        } else {
            echo $this->_status_error('hubo un error al publicar tu post', __LINE__);
        }
        return $archivoFinal;
    }

    private function _mover_y_cambiar_tamanio_de_imagen($file, $nombreFinal, $tamanio = 512)
    {
        $this->img->load($file);

        $h = $this->img->getHeight();
        $w = $this->img->getWidth();

        $h > $w ? $this->img->resizeToHeight($tamanio) : $this->img->resizeToWidth($tamanio);

        $target_file = $nombreFinal;
        $this->img->save($target_file);
        return $target_file;
    }

    private function _post_imagen($archivo, $perfil = null)
    {
//        ya
        $this->img->load($archivo['tmp_name']);
        $archivoFinal = uniqid() . '.' . $this->img->extension();
        $this->_mover_y_cambiar_tamanio_de_imagen($archivo['tmp_name'], $this->imagenesFolder . $archivoFinal);
        $insert = [
            'usuario' => $this->usuario,
            'perfil' => $perfil,
            'tipo' => 'imagen',
            'imagen' => $archivoFinal
        ];
        if ($this->db->insert('posts', $insert)) {
            echo $this->_status_ok('imagen posteada correctamente');
        } else {
            echo $this->_status_error('hubo un error al postear tu imagen', __LINE__);
        }
        return $archivoFinal;
    }

    private function _post_texto($texto, $perfil = null)
    {
//        ya
        $insert = [
            'usuario' => $this->usuario,
            'contenido' => $texto,
            'perfil' => $perfil,
            'tipo' => 'texto'
        ];
        if ($this->db->insert('posts', $insert)) {
            echo $this->_status_ok('post posteado correctamente');
        } else {
            echo $this->_status_error('Hubo un error al postear tu post', __FILE__);
        }
    }

    public function post()
    {
//        ya
        if (isset($_REQUEST['post']) && $_REQUEST['post'] != '') {
            if ($this->_existe_post($_REQUEST['post'])) {
                if ($post = $this->db->where('id')->get('posts')[0]) {
                    $post['comentarios'] = $this->post_comentarios($_REQUEST['post']);
                    echo $this->_status_ok(['post' => $post]);
                } else {
                    echo $this->_status_error('hubo un error al obtener el post');
                }
            } else {
                echo $this->_status_error('el post no existe');
            }
        } else {
            echo $this->_status_error('falta post', __LINE__);
        }
    }

    public function post_comentarios($postid = null)
    {
//        ya
        $print = $postid == null;
        if ($postid == null && isset($_REQUEST['post']) && $_REQUEST['post'] != '') {
            $postid = $_REQUEST['post'];
        } elseif ($postid == null && !isset($_REQUEST['post'])) {
            echo $this->_status_error('no hay post definido', __LINE__);
            return false;
        }

        if ($print == true) {
            echo $this->_status_ok(['comentarios' => $this->db->where('post', $postid)->get('comentarios')]);
        } else {
            $comentarios = $this->db->where('post', $postid)->get('comentarios');
//            var_dump($comentarios);
            $comentariosFinal = [];
            foreach ($comentarios as $comentario) {
                $comentarioLimpio = $comentario;
                $comentarioLimpio['usuario'] = $this->_usuario_info($comentario['usuario']);
                $comentariosFinal[] = $comentarioLimpio;
            }
            return $comentariosFinal;
        }
        return true;
    }

    public function amigos()
    {
//        ya

        if (($_REQUEST['usuario'] == $this->usuario) || !isset($_REQUEST['usuario']) || $_REQUEST['usuario'] == '') {

            $amigos = $this->db->where('main', $this->usuario)->get('amigos');
            $queColumaUsar = 'amigo';
        } elseif (isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != $this->usuario) {
            $amigos = $this->db->where('main', $_REQUEST['usuario'])->get('amigos');
        } else {
            echo $this->_status_error('error al obtener la lista de amigos', __LINE__);
            die();
        }

        $amigosLimpio = [];
        foreach ($amigos as $amigo) {
            $amigosLimpio[] = $this->_perfil_por_id($amigo['amigo']);
        }
        echo $this->_status_ok(['amigos' => $amigosLimpio]);

    }

    public function eliminar_amigo()
    {
//        ya
        if (isset($_REQUEST['usuario'])) {
            if ($this->db
                    ->where('main', $_REQUEST['usuario'])
                    ->where('amigo', $this->usuario)
                    ->delete('amigos')
                &&
                $this->db
                    ->where('main', $this->usuario)
                    ->where('amigo', $_REQUEST['usuario'])
                    ->delete('amigos')
            ) {
                echo $this->_status_ok('ya no son amigos');
            } else {
                echo $this->_status_error('hubo algun error al elminar a tu amigo', __LINE__);
            }
        } else {
            $this->_status_error('falta a quien eliminar', __LINE__);
        }
    }

    public function perfil()
    {
//        ya
        if ($this->_existe_usuario_por_id($_REQUEST['usuario'])) {
            $perfil = $this->db->where('id', $_REQUEST['usuario'])->get('usuarios', 1, 'id, nombre, apellido, imagen, bio')[0];
            $perfil['amigos'] = $this->db->where('amigo', $_REQUEST['usuario'])->where('permisos', '1')->get('amigos');
            echo $this->_status_ok(['perfil' => $perfil]);
        } else {
            echo $this->_status_error('usuario no existente', __LINE__);
        }
    }

    public function curriculum()
    {
//        ya
        if ($this->_tiene_curriculum($_REQUEST['usuario'])) {
            $archivo = $this->db->where('usuario', $_REQUEST['usuario'])->get('cv', 1, 'archivo')[0];
            echo $this->_status_ok($archivo);
        } else {
            echo $this->_status_error('el usuario no cuenta con un curriculum publico');
        }
    }

    private function _tiene_curriculum($usuario)
    {
//        ya
        return count($this->db->where('usuario', $usuario)->get('cv', 1, 'archivo')) > 0;
    }

    public function agregar_curriculum()
    {
//        ya

        $cvname = array_keys($_FILES)[0];
        $extension = pathinfo($_FILES[$cvname]['name'])['extension'];
        $nombreFinal = $_SESSION['nombre'] . $_SESSION['apellido'] . '_CV_' . uniqid() . '.' . $extension;

        if ($this->_basic_check($_FILES[$cvname]['name'])) {
            if (move_uploaded_file($_FILES[$cvname]['tmp_name'], $this->cvFolder . $nombreFinal)) {
                $insert = [
                    'archivo' => $nombreFinal,
                    'usuario' => $this->usuario
                ];
                if ($this->_tiene_curriculum($this->usuario)) {
                    if ($this->db->where('usuario', $this->usuario)->update('cv', $insert)) {
                        echo $this->_status_ok('cv actualizado correctamente');
                    } else {
                        echo $this->_status_error('hubo un error al actualizar tu cv', __LINE__);
                    }
                } else {
                    if ($this->db->insert('cv', $insert)) {
                        echo $this->_status_ok('cv agregado exitosamente');
                    } else {
                        echo $this->_status_error('hubo un problema al agregar tu cv', __LINE__);
                    }
                }
            } else {
                echo $this->_status_error('hubo un error al mover tu cv', __LINE__);
            }
        } else {
            echo $this->_status_error('tipo de archivo no permitido', __LINE__);
        }
    }

    /**
     * @param $file : nombre de archivo con extension
     * @return bool
     */
    private function _basic_check($file)
    {
//        ya
//        tal ves agregar tamaño de archivo
        $check = pathinfo($file);
        switch (strtolower($check['extension'])) {
            case 'png':
            case 'jpg':
            case 'gif':
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'tiff':
                return true;
                break;
            case 'php':
            default:
                return false;
                break;
        }
    }

    public function grupo()
    {
        if (isset($_REQUEST['grupo']) && $_REQUEST['grupo'] != '') {
            if ($this->_existe_grupo($_REQUEST['grupo'])) {
                if ($grupo = $this->db->where('id', $_REQUEST['grupo'])->get('grupos')[0]) {
                    echo $this->_status_ok(['grupo' => $grupo]);
                } else {
                    echo $this->_status_error('hubo un error al obtener grupo');
                }
            } else {
                echo $this->_status_error('no existe ese grupo', __LINE__);
            }
        } else {
            echo $this->_status_error('falta grupo', __LINE__);
        }

    }

    public function _existe_grupo($grupoId)
    {
        return count($this->db->where('id', $grupoId)->get('grupos')) > 0;

    }

    public function crear_grupo()
    {
//        ya
        if (
            isset($_REQUEST['nombre']) &&
            isset($_REQUEST['desc']) &&
            $_REQUEST['nombre'] != '' &&
            $_REQUEST['desc'] != ''
        ) {

            if (count($this->db->where('nombre', $_REQUEST['nombre'])->get('grupos')) == 0) {
                $insert = [
                    'nombre' => $_REQUEST['nombre'],
                    'desc' => $_REQUEST['desc']
                ];

                if ($this->db->insert('grupos', $insert)) {
                    $insert = [
                        'grupo' => $this->db->getInsertId(),
                        'usuario' => $this->usuario,
                        'admin' => 1
                    ];
                    if ($this->db->insert('grupos_usuarios', $insert)) {
                        echo $this->_status_ok('grupo creado satisfactoriamente');
                    } else {
                        echo $this->_status_error('hubo un error al asignarte como administrador del grupo', __LINE__);
                    }
                } else {
                    echo $this->_status_error('hubo un error al crear el grupo', __LINE__);
                }
            } else {
                echo $this->_status_error('grupo ya existente');
            }
        }
    }

    public function unirse_a_grupo()
    {
//        ya
        if (isset($_REQUEST['grupo']) && $_REQUEST['grupo'] != '') {
            $insert = [
                'grupo' => $_REQUEST['grupo'],
                'usuario' => $_REQUEST['usuario']
            ];

            if ($this->_existe_grupo($_REQUEST['grupo'])) {
                if ($this->db->insert('grupos_usuarios', $insert)) {
                    echo $this->_status_ok('te has unido al grupo satisfactoriamente');
                } else {
                    echo $this->_status_error('hubo un error al unirte al grupo');
                }
            } else {
                echo $this->_status_error('grupo no existente');
            }
        } else {
            echo $this->_status_error('falta grupo', __LINE__);
        }
    }

    public function buscar_grupo()
    {
//        ya
        if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '' && (strlen($_REQUEST['nombre']) > 3)) {
            if ($grupos = $this->db->where('nombre like "%' . $_REQUEST['nombre'] . '%"')->get('grupos')) {
                echo $this->_status_ok(['grupos' => $grupos]);
            } else {
                echo $this->_status_error('hubo un error al buscar el grupo', __LINE__);
            }
        } else {
            echo $this->_status_error('tu busqueda es incorrecta', __LINE__);
        }
    }

    public function busqueda()
    {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $usuarios = $this->_buscar_usuario($_REQUEST['query']);
            $usuariosLimpios = [];
            foreach ($usuarios as $usuario) {
                $usuariosLimpios[] = $this->_perfil_por_id($usuario['id']);
            }
            echo $this->_status_ok(['msg' => '', 'usuarios' => $usuariosLimpios]);
        } else {
            echo $this->_status_error('falta el query', __LINE__);
        }
    }

    public function _buscar_usuario($query)
    {

        return $this->db
            ->where('id', $this->usuario, '!=')
            ->where('concat(nombre, " ", apellido) like "%' . $query . '%"')
            ->orWhere('correo like "%' . $query . '%"')
            ->orWhere('bio like "%' . $query . '%"')
            ->get('usuarios', 5);
    }

    public function _buscar_grupo($query)
    {
        return $this->db
            ->where('nombre like "%' . $query . '%"')
            ->orWhere('desc like "%' . $query . '%"')
            ->get('usuarios');

    }

//    public function grupoX()
//    {
//        if (isset($_REQUEST['id'])) {
//            $grupo = $this->db->where('id', $_REQUEST['id'])->get('grupo');
//            $grupo['posts'] = $this->db->where('grupo', $_REQUEST['id'])->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->get('posts', 30);
//            echo json_encode($grupo);
//        } else {
//            echo $this->_status_error();
//        }
//    }

    /*    public function equipo()
        {
            if (isset($_REQUEST['id'])) {
    //            $grupo = $this->db->where('id', $_REQUEST['id'])->get('equipo');
    //            $posts = $this->db->where('equipo', $_REQUEST['id'])->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->get('posts');
            } else {
                echo $this->_status_error();
            }
        }

        public function equipos()
        {
            if (isset($_REQUEST['id'])) {
                $equipos = $this->db->where('usuario', $_REQUEST['id'])->get('equipos_usuarios');
                echo json_encode($equipos);
            }
        }

        public function agregar_a_equipo()
        {
            if (isset($_REQUEST['id'])) {
                $toinsert = [
                    'usuario' => $this->usuario,
                    'equipo' => $_REQUEST['id']
                ];
                if ($this->db->insert('equipos_usuarios', $toinsert)) {
                    echo $this->_status_ok();
                } else {
                    echo $this->_status_error();
                }
            }
        }

        public function elimidar_de_equipo()
        {
            $usuario = isset($_REQUEST['usuario']) ? $_REQUEST['usuario'] : $this->usuario;
            if (isset($_REQUEST['id'])) {
                $this->db->where('usuario', $usuario)->where('equipo', $_REQUEST['id'])->delete('equipos_usuarios');
            }
        }*/

    public function hobbies()
    {
        if (isset($_REQUEST['usuario']) && $_REQUEST['usuario'] != '') {
            if ($hobbies = $this->db->where('usuario', $_REQUEST['usuario'])) {
                echo $this->_status_ok(['hobbies' => $hobbies]);
            } else {
                echo $this->_status_error('hubo un error al obtener los hobbies de este usuario', __LINE__);
            }
        } else {
            echo $this->_status_error('falta usuario', __LINE__);
        }
    }

    public function agregar_hobbie()
    {
//        ya
        if (isset($_REQUEST['hobbie']) && $_REQUEST['hobbie'] != '') {
            if (count($this->db->where('usuario', $this->usuario)->where('hobbie', $_REQUEST['hobbie'])->get('hobbies')) == 0) {
                $insert = [
                    'usuario' => $this->usuario,
                    'hobbie' => $_REQUEST['hobbie']
                ];
                if ($this->db->insert('hobies', $insert)) {
                    echo $this->_status_ok('hobbie agregado satisfactoriamente');
                } else {
                    echo $this->_status_error('hub un error al agregar tu hobbie', __LINE__);
                }
            } else {
                echo $this->_status_error('hobbie ya existente', __LINE__);
            }
        } else {
            echo $this->_status_error('falta hobbie', __LINE__);
        }
    }

    public function borrar_hobbie()
    {
//        ya
        if (isset($_REQUEST['hobbie']) && $_REQUEST['hobbie'] != '') {
            if (count($this->db->where('id', $_REQUEST['hobbie'])->get('hobbies')) == 1) {
                if ($this->db->where('usuario', $this->usuario)->where('id')->$_REQUEST['hobbie']) {
                    echo $this->_status_ok('hobbie eliminado correctamente');
                } else {
                    echo $this->_status_error('hubo un problema al eliminar el hobbie', __LINE__);
                }

            } else {
                echo $this->_status_error('hobbie no existente', __LINE__);
            }
        } else {
            echo $this->_status_error('falta hobbie', __LINE__);
        }
    }

    /*    public function experiencia()
        {
            $usuario = isset($_REQUEST['id']) ? $_REQUEST['id'] : $this->usuario;
            echo json_encode($this->db->where('usuario', $usuario)->orderBy('de', 'desc')->get('experiencia'));
        }

        public function agregar_experiencia()
        {
            if ($this->db->insert('experiencia', $_REQUEST)) {
                echo $this->_status_ok();
            } else {
                echo $this->_status_error();
            }
        }*/

    public function cupones_cerca()
    {
        $distanciaBusqueda = isset($_REQUEST['distancia']) && $_REQUEST['distancia'] != '' ? $_REQUEST['distancia'] : 1000;
        $km = $distanciaBusqueda * 1.4;
        $aproximacion = (($km * 0.01) / 0.933);
        if (isset($_REQUEST['lat']) && $_REQUEST['lat'] != '' && isset($_REQUEST['lng']) && $_REQUEST['lng'] != '') {
            $betweenLat = 'lat between ' . ($_REQUEST['lat'] - $aproximacion) . ' and ' . ($_REQUEST['lat'] + $aproximacion);
            $betweenLng = 'lng between ' . ($_REQUEST['lng'] - $aproximacion) . ' and ' . ($_REQUEST['lng'] + $aproximacion);
            $cupones = $this->db->where($betweenLat)->where($betweenLng)->where('cant', '0', '>')->get('cupones');
            $cuponesLimpio = [];
            foreach ($cupones as $cupon) {
                $dist = $this->dist->calc($_REQUEST['lat'], $_REQUEST['lng'], $cupon['lat'], $cupon['lng']);
                if ($dist < $distanciaBusqueda && $dist < $cupon['max_dist']) {
                    $cupon['dist'] = $dist;
                    $cuponesLimpio[] = $cupon;
                }
            }
            echo $this->_status_ok(['cupones' => $cuponesLimpio]);
        } else {
            echo $this->_status_error('no hay informacion de posicion', __LINE__);
        }
    }

    public function ping()
    {
//        deberia regresar cosas nuevas
        $timestamp = isset($_REQUEST['timestamp']) ? $_REQUEST['timestamp'] : null;
        if (isset($_REQUEST['lat']) && $_REQUEST['lat'] != '' && isset($_REQUEST['lng']) && $_REQUEST['lng'] != '') {
            $update = [
                'lat' => $_REQUEST['lat'],
                'lng' => $_REQUEST['lng']
            ];
            if ($this->db->where('id', $this->usuario)->update('usuarios', $update)) {
                $insert = $update;
                $insert['usuario'] = $this->usuario;
                if ($this->db->insert('posiciones', $insert)) {
                    echo $this->_status_ok(['notificaciones', $this->_notificaciones($timestamp)]);
                } else {
                    echo $this->_status_error('hubo un problema al recibir tus notificacions', __LINE__);
                }
            } else {
                echo $this->_status_error('hubo in problema al actualizar tu info', __LINE__);
            }
        } else {
            echo $this->_status_error('falta tu info de posicion homes', __LINE__);
        }
    }

    public function _notificaciones($tiestamp = null)
    {
        if ($timestamp = null) {

        } else {
            if ($this->db
                ->where('usuario', $this->usuario)
                ->where('timestamp', $timestamp, '>=')
                ->get('notificaciones')
            ) {

            }
        }

    }

    public function obtener_cupon()
    {
        if (isset($_REQUEST['cupon']) && $_REQUEST['cupon'] != '') {
            if ($this->_cupon_disponible($_REQUEST['cupon'])) {
                $cupon = $this->db->where('id', $_REQUEST['cupon'])->get('cupones', 1)[0];

                $update = [
                    'cant' => ($cupon['cant'] - 1)
                ];
                if ($this->db->where('id', $_REQUEST['id'])->update('cupones', $update)) {
                    $insert = [
                        'usuario' => $this->usuario,
                        'cupon' => $_REQUEST['cupon']
                    ];
                    if ($this->db->insert('cupones_usuarios', $insert)) {
                        echo $this->_status_ok(['cupon', $cupon]);
                    } else {
                        echo $this->_status_error('hubo un problema al agregar tu cupon', __LINE__);
                    }
                } else {
                    echo $this->_status_error('hubo un error al actualizar el cupon', __LINE__);
                }
            } else {
                echo $this->_status_error('cupon no disponible', __LINE__);
            }
        } else {
            echo $this->_status_error('falta cupon', __LINE__);
        }
    }

    public function _cupon_disponible($cupon)
    {
        $cupon = $this->db->where('id', $cupon)->get('cupones');
        if (count($cupon) == 1) {
            if ($cupon[0]['cant'] > 0) {
                return true;
            }
        }
        return false;

    }

    public function agregar_imagen_perfil()
    {
        if (count($_FILES) == 1) {
            $key = array_keys($_FILES)[0];
            $archivo = $_FILES[$key];
            if ($this->_basic_check($archivo['name'])) {
                $nombre = uniqid() . '.' . pathinfo($archivo['name'])['extension'];

                $this->_mover_y_cambiar_tamanio_de_imagen_perfil($archivo['tmp_name'], $this->imagenesFolder . $nombre, 512);
                $update = [
                    'imagen' => $nombre

                ];

//                if (isset($_REQUEST['']))
                if ($this->db->where('id', $this->usuario)->update('usuarios', $update)) {
                    $_SESSION['imagen'] = $nombre;
                    echo $this->_status_ok('imagen de perfil actualizada correctamente');
                } else {
                    echo $this->_status_error('Hubo un error al actualizar tu imagen de perfil', __LINE__);
                }
            } else {
                echo $this->_status_error('tipo de imagen no soportada', __LINE__);
            }
        } else {
            echo $this->_status_error('no hay imagen definida', __LINE__);
        }
    }

    private function _mover_y_cambiar_tamanio_de_imagen_perfil($file, $nombreFinal, $tamanio = 512)
    {
        $this->img->load($file);

//        $h = $this->img->getHeight();
//        $w = $this->img->getWidth();

//        $h > $w ? $this->img->resizeToHeight($tamanio) : $this->img->resizeToWidth($tamanio);
        $this->img->resize($tamanio, $tamanio);

        $target_file = $nombreFinal;
        $this->img->save($target_file);
        return $target_file;
    }

    private function _user_cookie($usuario)
    {
        $cookie = $this->db->where('correo', $usuario)->get('usuarios', 1, 'nombre, apellido, correo')[0];
        $cookie['imagen'] = md5($cookie['correo'] . $cookie['correo']);

        return $cookie;
    }

    private function _email($to, $body, $from = 'contacto@beeprofiles.com')
    {
        return var_export($to . $body . $from);
    }

    private function _agregar_notificacion($usuario, $mensaje, $tipo = 'general')
    {
        $timestamp = time();
        $insert = [
            'usuario' => $usuario,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'timestamp' => $timestamp
        ];

        if ($this->db->insert('notificaciones', $insert)) {
            return true;
        } else {
            return false;
        }


    }
}
