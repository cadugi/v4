<?php
session_start();

function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }
    return $conexion;
}

function obtenerProducto($conexion, int $id_producto): ?array {
    $sql = "SELECT nombre FROM productos WHERE id_producto = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_producto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $producto = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    return $producto ?: null;
}

function registrarCompra($conexion, string $nombre_producto, string $fecha_compra): bool {
    $sql = "INSERT INTO datos (nombre_producto, fecha_compra) VALUES (?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nombre_producto, $fecha_compra);
    $exito = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $exito;
}

function eliminarProducto($conexion, int $id_producto): bool {
    $sql = "DELETE FROM productos WHERE id_producto = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_producto);
    $exito = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $exito;
}

function realizarCompra() {
    if (!isset($_SESSION['usuario'])) {
        die("Error: El usuario no ha iniciado sesión.");
    }

    if (!isset($_GET['id_producto'])) {
        die("<p>Error: No se ha seleccionado ningún producto.</p>");
    }

    $id_producto = intval($_GET['id_producto']);
    $conexion = conectarBD();

    $producto = obtenerProducto($conexion, $id_producto);
    if (!$producto) {
        mysqli_close($conexion);
        die("<p>Error: No se encontró el producto en la base de datos.</p>");
    }

    $nombre_producto = $producto['nombre'];

    date_default_timezone_set('Europe/Madrid');
    $fecha_compra = date('Y-m-d H:i:s');

    if (registrarCompra($conexion, $nombre_producto, $fecha_compra)) {
        if (eliminarProducto($conexion, $id_producto)) {
            $mensaje = "¡Compra realizada con éxito!";
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
