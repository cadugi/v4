<?php
session_start();

function obtenerDatosHistorial() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");

    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    $res = mysqli_query($conexion, "SELECT id_dato, nombre_producto, fecha_compra, email_comprador, precio_producto, email_vendedor FROM datos ORDER BY fecha_compra DESC");

    if (!$res) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $datos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $datos[] = $fila;
    }

    mysqli_close($conexion);
    return $datos;
}

function mostrarHistorial() {
    $datos = obtenerDatosHistorial();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Historial de Compras</title>
        <link rel="stylesheet" href="style-historial.css">
    </head>
    <body>
        <h2>Historial de compras</h2>

        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Fecha</th>
                <th>Email del Comprador</th>
                <th>Precio (€)</th>
                <th>Email del Vendedor</th>
            </tr>
            <?php if (count($datos) > 0): ?>
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <td><?php echo $fila['id_dato']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_producto']); ?></td>
                        <td>
                            <?php
                            $fecha = $fila['fecha_compra'];
                            // Si es DATETIME, conviértelo
                            
                            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fecha)) {
                                $dt = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
                                echo $dt ? $dt->format('d-m-Y H:i:s') : htmlspecialchars($fecha);
                            } else {
                                echo htmlspecialchars($fecha);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($fila['email_comprador']); ?></td>
                        <td><?php echo number_format($fila['precio_producto'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($fila['email_vendedor'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Sin compras registradas</td></tr>
            <?php endif; ?>
        </table>
        
        <button><a href="index.php">Volver</a></button>
    </body>
    </html>
    <?php
}

mostrarHistorial();
?>
