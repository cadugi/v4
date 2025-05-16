<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$conexion = conectarBD();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    procesarFormulario($conexion, $_SESSION['usuario']);
}

function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error al conectar con la base de datos: " . mysqli_connect_error());
    }
    return $conexion;
}

function obtenerIdUsuario($conexion, $email) {
    $res = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE email = '$email'");
    $usuario = mysqli_fetch_assoc($res);
    return $usuario ? $usuario['id_usuario'] : null;
}

function procesarFormulario($conexion, $email) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_FILES['imagen'];

    $ruta_imagen = subirImagen($imagen);
    if (!$ruta_imagen) {
        die("Error al subir la imagen.");
    }

    $id_vendedor = obtenerIdUsuario($conexion, $email);
    if (!$id_vendedor) {
        die("Usuario no encontrado.");
    }

    if (insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $ruta_imagen, $id_vendedor)) {
        header("Location: productos.php");
        exit();
    } else {
        echo "Error al insertar el producto.";
    }
}

function subirImagen($imagen) {
    $permitidas = ['png', 'jpg', 'jpeg', 'webp'];
    $extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidas)) {
        die("Formato de imagen no permitido.");
    }

    if (!is_dir('images')) {
        mkdir('images');
    }

    $ruta = 'images/' . basename($imagen['name']);
    return move_uploaded_file($imagen['tmp_name'], $ruta) ? $ruta : false;
}

function insertarProducto($conexion, $nombre, $categoria, $descripcion, $precio, $imagen, $id_vendedor) {
    $stmt = mysqli_prepare($conexion, "INSERT INTO productos (nombre, descripcion, precio,imagen, categoria_id, id_vendedor) VALUES (?, ?, ?, ?, ?, ?)");
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
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre" required>
<select name="categoria" required>
    <option value="">Selecciona una categoría</option>
    <?php
    $res = mysqli_query($conexion, "SELECT id_categoria, nombre_categoria FROM categorias");
    while ($cat = mysqli_fetch_assoc($res)) {
        echo "<option value='" . htmlspecialchars($cat['id_categoria']) . "'>" . htmlspecialchars($cat['nombre_categoria']) . "</option>";
    }
    ?>
</select>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" name="precio" placeholder="Precio" required>
        <input type="file" name="imagen" accept=".png,.jpg,.jpeg,.webp" required>
        <button type="submit">Publicar</button>
    </form>
</body>
</html>
