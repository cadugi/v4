<?php
session_start();
include('menu.php');

function redirigirSiNoHaySesion() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }
}

function obtenerRol() {
    return $_SESSION['rol'] ?? 'anonimo';
}

function obtenerImagenPerfil($rol) {
    $rutas = [
        'anonimo' => 'images/usuarios-prep/anonimo.png',
        'usuario' => 'images/usuarios-prep/0.png',
        'admin'   => 'images/usuarios-prep/admin.png'
    ];
    return $rutas[$rol] ?? 'images/default-profile.png';
}

function mostrarContenidoPrincipal($rol, $imagen_perfil) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Tienda Fonsi</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

    <!-- Parte superior con imagen y logout -->
    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="logout.php"><img src="images/apagado.png" width="30" title="Cerrar sesión"></a>
        <img src="<?php echo $imagen_perfil; ?>" width="50" style="border-radius: 50%;">
    </div>

    <main>
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?>!</h1>
        <p>En nuestra tienda encontrarás muchos productos de calidad. Queremos darte siempre el mejor precio.</p>

        <?php if ($rol == 'admin') : ?>
            <a href="subir_categoria.php"><button>Subir nueva categoría</button></a>
        <?php endif; ?>

        <h2>Algunos productos destacados:</h2>
        <ul>
            <li><a href="productos.php#motor">Motor y accesorios</a><img src="images/motor.png" alt=""></li>
            <li><a href="productos.php#moda">Moda y accesorios</a><img src="images/ropa-limpia.png" alt=""></li>
            <li><a href="productos.php#electrodomesticos">Electrodomésticos</a><img src="images/lavadora.png" alt=""></li>
            <li><a href="productos.php#moviles">Móviles y telefonía</a><img src="images/telefono.png" alt=""></li>
            <li><a href="productos.php#informatica">Informática y electrónica</a><img src="images/computadora.png" alt=""></li>
            <li><a href="productos.php#deportes">Deporte y ocio</a><img src="images/deporte.png" alt=""></li>
            <li><a href="productos.php#tv">TV, audio y fotografía</a><img src="images/camara.png" alt=""></li>
            <li><a href="productos.php#jardin">Hogar y Jardín</a><img src="images/flores.png" alt=""></li>
            <li><a href="productos.php#libros">Cine, libros y música</a><img src="images/libro.png" alt=""></li>
            <li><a href="productos.php#niño">Niños y bebés<img src="images/chico.png" alt=""></a></li>
        </ul>
    </main>

    <footer>
        <p>&copy; 2025 Tienda General Fonsi. Proyecto de práctica.</p>
    </footer>
    </body>
    </html>
    <?php
}

// Lógica principal
redirigirSiNoHaySesion();
$rol = obtenerRol();
$imagen_perfil = obtenerImagenPerfil($rol);
mostrarContenidoPrincipal($rol, $imagen_perfil);
?>



<?php
