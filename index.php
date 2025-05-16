<?php
// Iniciar la sesión para acceder a datos del usuario logueado
session_start();

// Incluir el menú superior o de navegación (puede tener lógica de sesión o enlaces comunes)
include('menu.php');

/**
 * Redirige al login si no hay sesión iniciada
 */
function redirigirSiNoHaySesion() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit(); // Detiene la ejecución para evitar continuar sin sesión
    }
}

/**
 * Devuelve el rol del usuario desde la sesión
 * Si no hay rol definido, se devuelve 'anonimo'
 */
function obtenerRol() {
    return $_SESSION['rol'] ?? 'anonimo';
}

/**
 * Devuelve la ruta de la imagen de perfil según el rol del usuario
 */
function obtenerImagenPerfil($rol) {
    $rutas = [
        'anonimo' => 'images/usuarios-prep/anonimo.png',
        'usuario' => 'images/usuarios-prep/0.png',
        'admin'   => 'images/usuarios-prep/admin.png'
    ];
    return $rutas[$rol] ?? 'images/default-profile.png';
}

/**
 * Conecta a la base de datos, consulta las categorías y las devuelve en un array
 */
function obtenerCategorias() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");

    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Obtener todas las categorías ordenadas alfabéticamente
    $res = mysqli_query($conexion, "SELECT nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");

    if (!$res) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $categorias = [];

    // Guardar todas las categorías en un array
    while ($fila = mysqli_fetch_assoc($res)) {
        $categorias[] = $fila['nombre_categoria'];
    }

    mysqli_close($conexion); // Cerrar la conexión después de la consulta
    return $categorias;
}

/**
 * Muestra la estructura principal de la página, incluyendo:
 * - Bienvenida personalizada
 * - Botón para subir nueva categoría si el usuario es admin
 * - Lista de categorías con enlaces
 */
function mostrarContenidoPrincipal($rol, $imagen_perfil, $categorias) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Tienda Fonsi</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

    <!-- Parte superior derecha con botón de logout y foto de perfil -->
    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="logout.php"><img src="images/apagado.png" width="30" title="Cerrar sesión"></a>
        <img src="<?php echo $imagen_perfil; ?>" width="50" style="border-radius: 50%;">
    </div>

    <main>
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?>!</h1>
        <p>En nuestra tienda encontrarás muchos productos de calidad. Queremos darte siempre el mejor precio.</p>

        <!-- Si el usuario es administrador, puede subir una nueva categoría -->
        <?php if ($rol == 'admin') : ?>
            <a href="subir_categoria.php"><button>Subir nueva categoría</button></a>
        <?php endif; ?>

        <h2>Categorías disponibles:</h2>
        <ul>
            <?php foreach ($categorias as $categoria): ?>
                <li>
                    <!-- Enlace hacia la sección de esa categoría en productos.php -->
                    <a href="productos.php#<?php echo urlencode(strtolower(str_replace(' ', '', $categoria))); ?>">
                        <?php echo htmlspecialchars($categoria); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <footer>
        <p>&copy; 2025 Tienda General Fonsi. Proyecto de práctica.</p>
    </footer>
    </body>
    </html>
    <?php
}

// ==== EJECUCIÓN PRINCIPAL DEL SCRIPT ====

// Si no hay sesión, redirige
redirigirSiNoHaySesion();

// Obtener datos del usuario
$rol = obtenerRol();
$imagen_perfil = obtenerImagenPerfil($rol);
$categorias = obtenerCategorias();

// Mostrar todo el contenido en pantalla
mostrarContenidoPrincipal($rol, $imagen_perfil, $categorias);
?>
