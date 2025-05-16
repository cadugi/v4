<?php
session_start();

function obtenerDatosHistorial() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    $res = mysqli_query($conexion, "SELECT * FROM datos ORDER BY fecha_compra DESC");
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
        <title>Historial</title>
        <link rel="stylesheet" href="style-historial.css">
    </head>
    <body>
        <h2>Historial de compras</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Fecha</th>
            </tr>
            <?php if (count($datos) > 0): ?>
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <td><?php echo $fila['id_compra']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_producto']); ?></td>
                        <td><?php echo $fila['fecha_compra']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">Sin compras registradas</td></tr>
            <?php endif; ?>
        </table>

        <button><a href="index.php">Volver</a></button>
    </body>
    </html>
    <?php
}

mostrarHistorial();

