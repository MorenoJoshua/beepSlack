<!--<aside class="sidebar hidden bg-principal" id="sidebar">
    <div class="menu bg-principal" id="menu">
        <div id="menuHeader">
            <div class="material-icons" id="closeButton">close</div>
        </div>
        <div id="menuContent" class="bg-principal">
            <ul id="menuOptions" class="bg-principal">
                <li class="bg-principal">
                    <div class="col-xs-6 offset-xs-3">
                        <div class="embed-responsive embed-responsive-1by1">
                            <div class="embed-responsive-item">
                                <img src="/imagenes/<?/*= $_SESSION['imagen'] */?>" alt="" class="w-100 img-circle">
                            </div>
                            <div class="embed-responsive-item">
                                <form action="/" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="fn" value="agregar_imagen_perfil">
                                    <input type="file" accept="image/*" name="perfil">
                                    <input type="submit">
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="text-xs-center bg-principal font-larger"><a
                            href="?p=perfil&id=<?/*= $_SESSION['id'] */?>" class="white"><?/*= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] */?></a>
                </li>
                <li class="bg-principal">Perfiles</li>
            </ul>
        </div>
    </div>
    <div class="overlaySidebar" id="overlaySidebar"></div>
    <div id="slideMenu"></div>
</aside>
-->

<ul id="slide-out" class="side-nav">
    <li><a href="#!">First Sidebar Link</a></li>
    <li><a href="#!">Second Sidebar Link</a></li>
</ul>
<a href="#" data-activates="slide-out" class="button-collapse show-on-large"><i class="material-icons">menu</i></a>
