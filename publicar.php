<?php
session_start();

// Verifica si el usuario ha iniciado sesión, si no, lo redirige al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conexion = conectarBD();

// Si se ha enviado el formulario por POST, se procesa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    procesarFormulario($conexion, $_SESSION['usuario']);
}

// Función para conectar a la base de datos
function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error al conectar con la base de datos: " . mysqli_connect_error());
    }
    return $conexion;
}

// Obtiene el ID del usuario a partir del email
function obtenerIdUsuario($conexion, $email) {
    $res = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE email = '$email'");
    $usuario = mysqli_fetch_assoc($res);
    return $usuario ? $usuario['id_usuario'] : null;
}

// Procesa los datos del formulario y guarda el producto en la base de datos
function procesarFormulario($conexion, $email) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_FILES['imagen'];

    // Intenta subir la imagen
    $ruta_imagen = subirImagen($imagen);
    if (!$ruta_imagen) {
        die("Error al subir la imagen.");
    }

    // Obtiene el ID del vendedor actual
    $id_vendedor = obtenerIdUsuario($conexion, $email);
    if (!$id_vendedor) {
        die("Usuario no encontrado.");
    }

    // Inserta el producto en la base de datos
    if (insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $ruta_imagen, $id_vendedor)) {
        header("Location: productos.php"); // Redirige a productos si todo fue bien
        exit();
    } else {
        echo "Error al insertar el producto.";
    }
}

// Sube la imagen al servidor
function subirImagen($imagen) {
    $permitidas = ['png', 'jpg', 'jpeg', 'webp'];
    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

    // Verifica que la extensión esté permitida
    if (!in_array($extension, $permitidas)) {
        die("Formato de imagen no permitido.");
    }

    // Crea la carpeta 'images' si no existe
    if (!is_dir('images')) {
        mkdir('images');
    }

    // Define la ruta donde se guardará la imagen
    $ruta = 'images/' . basename($imagen['name']);

    // Mueve la imagen a la carpeta y retorna la ruta si fue exitoso
    return move_uploaded_file($imagen['tmp_name'], $ruta) ? $ruta : false;
}

// Inserta el producto en la base de datos
function insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $imagen, $id_vendedor) {
    $stmt = mysqli_prepare($conexion, "INSERT INTO productos (nombre, descripcion, precio, nombre_imagen, categoria_id, id_vendedor) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssdssi", $nombre, $descripcion, $precio, $imagen, $categoria, $id_vendedor);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $resultado;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Producto</title>
    <link rel="stylesheet" href="style-publicar.css">
</head>
<body>
    <h1>Nuevo Producto</h1>
    <!-- Formulario para publicar un nuevo producto -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre" required>

        <!-- Selector de categoría -->
        <select name="categoria" required>
            <option value="">Selecciona una categoría</option>
            <?php
            // Carga las categorías desde la base de datos
            $res = mysqli_query($conexion, "SELECT id_categoria, nombre_categoria FROM categorias");
            while ($cat = mysqli_fetch_assoc($res)) {
                echo "<option value='" . htmlspecialchars($cat['id_categoria']) . "'>" . htmlspecialchars($cat['nombre_categoria']) . "</option>";
            }
            ?>
        </select>

        <!-- Campo para la descripción -->
        <textarea name="descripcion" placeholder="Descripción" required></textarea>

        <!-- Campo para el precio -->
        <input type="number" name="precio" placeholder="Precio" required>

        <!-- Campo para subir imagen -->
        <input type="file" name="imagen" accept=".png,.jpg,.jpeg,.webp" required>

        <!-- Botón de enviar -->
        <button type="submit">Publicar</button>
    </form>
</body>
</html>
