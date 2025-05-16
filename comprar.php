<?php
session_start();

// Función para conectar a la base de datos
function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error al conectar a la base de datos");
    }
    return $conexion;
}

// Función para obtener el nombre de un producto por su id
function obtenerProducto($conexion, $id_producto) {
    $sql = "SELECT nombre FROM productos WHERE id_producto = $id_producto";
    $resultado = mysqli_query($conexion, $sql);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        return $fila['nombre'];
    }
    return null;
}

// Función para registrar la compra en la tabla datos
function registrarCompra($conexion, $nombre_producto, $fecha_compra) {
    $sql = "INSERT INTO datos (nombre_producto, fecha_compra) VALUES ('$nombre_producto', '$fecha_compra')";
    return mysqli_query($conexion, $sql);
}

// Función para eliminar el producto comprado
function eliminarProducto($conexion, $id_producto) {
    $sql = "DELETE FROM productos WHERE id_producto = $id_producto";
    return mysqli_query($conexion, $sql);
}

// Función principal para realizar la compra
function realizarCompra() {
    if (!isset($_SESSION['usuario'])) {
        die("Error: Debes iniciar sesión.");
    }

    if (!isset($_GET['id_producto'])) {
        die("Error: No se seleccionó ningún producto.");
    }

    $id_producto = (int)$_GET['id_producto'];
    $conexion = conectarBD();

    $nombre_producto = obtenerProducto($conexion, $id_producto);
    if (!$nombre_producto) {
        mysqli_close($conexion);
        die("Error: Producto no encontrado.");
    }

    date_default_timezone_set('Europe/Madrid');
    $fecha_compra = date('Y-m-d H:i:s');

    if (registrarCompra($conexion, $nombre_producto, $fecha_compra)) {
        if (eliminarProducto($conexion, $id_producto)) {
            $mensaje = "Compra realizada con éxito.";
        } else {
            $mensaje = "Error al eliminar el producto.";
        }
    } else {
        $mensaje = "Error al registrar la compra.";
    }

    mysqli_close($conexion);
    return $mensaje;
}

$mensaje = realizarCompra();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra</title>
    <link rel="stylesheet" href="style-comprar.css">
</head>
<body>
    <h1><?php echo $mensaje; ?></h1>
    <a href="index.php"><button>Volver al inicio</button></a>
</body>
</html>
