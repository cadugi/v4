<?php
// Función para conectar con la base de datos 'tienda'
function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        // Si la conexión falla, se muestra un mensaje de error y se detiene la ejecución
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conexion; // Devuelve el objeto de conexión
}

// Función para obtener todos los productos junto con su categoría y el correo del vendedor
function obtenerProductos($conexion) {
    // Consulta SQL para obtener los datos de los productos, sus categorías y el vendedor
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.nombre_imagen, 
                   c.nombre_categoria, u.email AS vendedor
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id_categoria
            LEFT JOIN usuarios u ON p.id_vendedor = u.id_usuario";

    // Ejecuta la consulta
    $resultado = mysqli_query($conexion, $sql);
    if (!$resultado) {
        // Si hay un error en la consulta, se detiene la ejecución
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $productos = [];
    // Agrupa los productos por categoría
    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Si no tiene categoría, se asigna "Sin categoría"
        $categoria = $fila['nombre_categoria'] ?: "Sin categoría";
        // Se organiza cada producto en un array por su categoría
        $productos[$categoria][] = $fila;
    }
    return $productos; // Devuelve el array de productos agrupados por categoría
}

// Establece la conexión a la base de datos
$conexion = conectarBD();
// Obtiene los productos usando la conexión
$productos = obtenerProductos($conexion);
// Cierra la conexión a la base de datos
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda</title>
    <!-- Enlace a la hoja de estilos externa -->
    <link rel="stylesheet" href="style-productos.css">
</head>
<body>
    <h1>Productos disponibles</h1>

    <!-- Recorre las categorías -->
    <?php foreach ($productos as $categoria => $items): ?>
        <h2><?php echo htmlspecialchars($categoria); ?></h2>
        <div class="producto-catalogo">
            <!-- Recorre los productos dentro de cada categoría -->
            <?php foreach ($items as $p): ?>
                <div class="producto">
                    <!-- Enlace a la página para comprar el producto -->
                    <a href="comprar.php?id_producto=<?php echo $p['id_producto']; ?>">
                        <!-- Muestra la imagen del producto -->
                        <img src="<?php echo htmlspecialchars($p['nombre_imagen']); ?>" alt="">
                        <!-- Muestra el nombre del producto -->
                        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <!-- Muestra la descripción del producto -->
                        <p><?php echo htmlspecialchars($p['descripcion']); ?></p>
                        <!-- Muestra el precio del producto -->
                        <p><strong><?php echo htmlspecialchars($p['precio']); ?> €</strong></p>
                        <!-- Muestra el correo del vendedor -->
                        <p>Vendedor: <?php echo htmlspecialchars($p['vendedor'] ?? 'No disponible'); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
