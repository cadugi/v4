<?php
session_start();
date_default_timezone_set('Europe/Madrid');

function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error al conectar a la base de datos");
    }
    return $conexion;
}

// Obtener detalles del producto (ahora también el correo del vendedor)
function obtenerProducto($conexion, $id_producto) {
    $sql = "SELECT p.nombre, p.precio, u.email AS vendedor_email
            FROM productos p
            LEFT JOIN usuarios u ON p.id_vendedor = u.id_usuario
            WHERE p.id_producto = $id_producto";
    $resultado = mysqli_query($conexion, $sql);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        return $fila;
    }
    return null;
}

// Registrar la compra en la tabla datos (ahora con correo del vendedor y datos de tarjeta)
function registrarCompra($conexion, $nombre_producto, $fecha_compra, $email_comprador, $precio_producto, $email_vendedor, $tarjeta, $caducidad, $cvv) {
    $sql = "INSERT INTO datos (nombre_producto, fecha_compra, email_comprador, precio_producto, email_vendedor, tarjeta, caducidad, cvv) 
            VALUES ('$nombre_producto', '$fecha_compra', '$email_comprador', $precio_producto, '$email_vendedor', '$tarjeta', '$caducidad', '$cvv')";
    return mysqli_query($conexion, $sql);
}

// Eliminar el producto comprado
function eliminarProducto($conexion, $id_producto) {
    $sql = "DELETE FROM productos WHERE id_producto = $id_producto";
    return mysqli_query($conexion, $sql);
}

// Función principal para realizar la compra
function realizarCompra() {
    if (!isset($_SESSION['usuario'])) {
        die("Error: Debes iniciar sesión.");
    }

    // No permitir compras con usuario anónimo
    if ($_SESSION['usuario'] === 'anonimo') {
        die("Error: No puedes comprar productos como usuario anónimo.");
    }

    if (!isset($_POST['id_producto']) || !isset($_POST['tarjeta']) || !isset($_POST['caducidad']) || !isset($_POST['cvv'])) {
        die("Error: Datos incompletos.");
    }

    $id_producto = (int)$_POST['id_producto'];
    $conexion = conectarBD();
    $producto = obtenerProducto($conexion, $id_producto);

    if (!$producto) {
        mysqli_close($conexion);
        die("Error: Producto no encontrado.");
    }

    $nombre_producto = mysqli_real_escape_string($conexion, $producto['nombre']);
    $precio_producto = $producto['precio'];
    $email_vendedor = mysqli_real_escape_string($conexion, $producto['vendedor_email']);
    $email_comprador = mysqli_real_escape_string($conexion, $_SESSION['usuario']);

    // No permitir comprar tu propio producto
    if ($email_comprador === $email_vendedor) {
        mysqli_close($conexion);
        die("Error: No puedes comprar tu propio producto.");
    }

    $fecha_compra = date('Y-m-d H:i:s');
    $tarjeta = mysqli_real_escape_string($conexion, $_POST['tarjeta']);
    $caducidad = mysqli_real_escape_string($conexion, $_POST['caducidad']);
    $cvv = mysqli_real_escape_string($conexion, $_POST['cvv']);

    // Registrar la compra
    if (registrarCompra($conexion, $nombre_producto, $fecha_compra, $email_comprador, $precio_producto, $email_vendedor, $tarjeta, $caducidad, $cvv)) {
        if (eliminarProducto($conexion, $id_producto)) {
            $mensaje = "✅ Compra realizada con éxito.";
        } else {
            $mensaje = "⚠️ Error al eliminar el producto.";
        }
    } else {
        $mensaje = "❌ Error al registrar la compra.";
    }

    mysqli_close($conexion);
    return $mensaje;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_producto'])) {
    $id_producto = (int)$_GET['id_producto'];
    $conexion = conectarBD();
    $producto = obtenerProducto($conexion, $id_producto);
    mysqli_close($conexion);

    if (!$producto) {
        die("Error: Producto no encontrado.");
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar compra</title>
        <link rel="stylesheet" href="style-comprar.css">
    </head>
    <body>
        <h2>Confirmar compra de: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
        <form method="post" action="comprar.php">
            <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
            <label for="tarjeta">Tarjeta de crédito (16 dígitos):</label>
            <input type="text" name="tarjeta" id="tarjeta" pattern="\d{16}" maxlength="16" required><br>
            <label for="caducidad">Fecha de caducidad (MM/AA):</label>
            <input type="text" name="caducidad" id="caducidad" pattern="(0[1-9]|1[0-2])\/\d{2}" maxlength="5" placeholder="MM/AA" required><br>
            <label for="cvv">CVV (3 dígitos):</label>
            <input type="text" name="cvv" id="cvv" pattern="\d{3}" maxlength="3" required><br>
            <button type="submit">Comprar</button>
        </form>
        <a href="productos.php"><button>Cancelar</button></a>
    </body>
    </html>
    <?php
    exit;
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
