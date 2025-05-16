<?php
function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conexion;
}

function obtenerProductos($conexion) {
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen, p.categoria_id,  u.nombre AS vendedor
            FROM productos p
            LEFT JOIN usuarios u ON p.id_vendedor = u.id_usuario";

    $resultado = mysqli_query($conexion, $sql);
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $productos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $categoria = $fila['categoria_id'] ?: "Sin categoría";
        $productos[$categoria][] = $fila;
    }
    return $productos;
}

$conexion = conectarBD();
$productos = obtenerProductos($conexion);
mysqli_close($conexion);
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda</title>
    <link rel="stylesheet" href="style-productos.css">
</head>
<body>
    <h1>Productos disponibles</h1>

    <?php foreach ($productos as $categoria => $items): ?>
        <h2><?php echo htmlspecialchars($categoria); ?></h2>
        <div class="producto-catalogo">
            <?php foreach ($items as $p): ?>
                <div class="producto">
                    <a href="comprar.php?id_producto=<?php echo $p['id_producto']; ?>">
                        <img src="<?php echo htmlspecialchars($p['imagen']); ?>" alt="">
                        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($p['descripcion']); ?></p>
                        <p><strong><?php echo htmlspecialchars($p['precio']); ?> €</strong></p>
                        <p>Vendedor: <?php echo htmlspecialchars($p['vendedor'] ?? 'No disponible'); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
