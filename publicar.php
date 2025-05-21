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
    $imagenes = $_FILES['imagenes'];

    // Sube todas las imágenes y guarda las rutas en un array
    $rutas_imagenes = subirImagenes($imagenes);
    if (!$rutas_imagenes) {
        die("Error al subir las imágenes.");
    }
    $rutas_serializadas = serialize($rutas_imagenes); // Guarda como string serializado

    // Obtiene el ID del vendedor actual
    $id_vendedor = obtenerIdUsuario($conexion, $email);
    if (!$id_vendedor) {
        die("Usuario no encontrado.");
    }

    // Inserta el producto en la base de datos
    if (insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $rutas_serializadas, $id_vendedor)) {
        header("Location: productos.php");
        exit();
    } else {
        echo "Error al insertar el producto.";
    }
}

// Sube varias imágenes al servidor
function subirImagenes($imagenes) {
    $permitidas = ['png', 'jpg', 'jpeg', 'webp'];
    $rutas = [];
    for ($i = 0; $i < count($imagenes['name']); $i++) {
        $extension = strtolower(pathinfo($imagenes['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($extension, $permitidas)) {
            continue; // Salta archivos no permitidos
        }
        if (!is_dir('images')) {
            mkdir('images');
        }
        $nombre_archivo = uniqid() . '_' . basename($imagenes['name'][$i]);
        $ruta = 'images/' . $nombre_archivo;
        if (move_uploaded_file($imagenes['tmp_name'][$i], $ruta)) {
            $rutas[] = $ruta;
        }
    }
    return count($rutas) > 0 ? $rutas : false;
}

// Inserta el producto en la base de datos
function insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $imagenes, $id_vendedor) {
    $stmt = mysqli_prepare($conexion, "INSERT INTO productos (nombre, descripcion, precio, nombre_imagen, categoria_id, id_vendedor) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssdssi", $nombre, $descripcion, $precio, $imagenes, $categoria, $id_vendedor);
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

        <!-- Campo para subir imágenes -->
        <input type="file" name="imagenes[]" accept=".png,.jpg,.jpeg,.webp" multiple required>

        <!-- Botón de enviar -->
        <button type="submit">Publicar</button>
    </form>
</body>
</html>
