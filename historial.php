<?php
// Iniciar la sesión para gestionar estados de usuario (aunque en este caso no se usa sesión directamente)
session_start();

/**
 * Establece conexión con la base de datos y obtiene el historial de compras.
 * Consulta la tabla 'datos' y obtiene los campos id_dato, nombre_producto y fecha_compra,
 * ordenados por fecha_compra descendente (las más recientes primero).
 *
 * @return array Devuelve un array con las filas del historial.
 */
function obtenerDatosHistorial() {
    // Conectar con la base de datos local llamada "tienda"
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    
    // Si la conexión falla, mostrar mensaje y detener ejecución
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    // Ejecutar consulta para obtener los datos del historial ordenados por fecha
    $res = mysqli_query($conexion, "SELECT id_dato, nombre_producto, fecha_compra FROM datos ORDER BY fecha_compra DESC");
    
    // Comprobar si la consulta falló, mostrar error y terminar ejecución
    if (!$res) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }
    
    // Crear array vacío para guardar resultados
    $datos = [];
    
    // Recorrer cada fila del resultado y añadirla al array $datos
    while ($fila = mysqli_fetch_assoc($res)) {
        $datos[] = $fila;
    }
    
    // Cerrar la conexión con la base de datos
    mysqli_close($conexion);
    
    // Devolver el array con todos los datos obtenidos
    return $datos;
}

/**
 * Función que muestra el historial de compras en formato tabla HTML.
 * Llama a obtenerDatosHistorial() para conseguir los datos.
 * Muestra un mensaje si no hay registros.
 */
function mostrarHistorial() {
    // Obtener el array con los datos del historial
    $datos = obtenerDatosHistorial();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Historial</title>
        <link rel="stylesheet" href="style-historial.css"> <!-- Archivo CSS para estilo -->
    </head>
    <body>
        <h2>Historial de compras</h2>
        
        <!-- Tabla para mostrar los datos del historial -->
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Fecha</th>
            </tr>
            <?php if (count($datos) > 0): ?>
                <!-- Recorrer el array de datos y mostrar cada fila -->
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <td><?php echo $fila['id_dato']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_producto']); ?></td> <!-- Escapar texto para seguridad -->
                        <td><?php echo $fila['fecha_compra']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Si no hay datos, mostrar mensaje -->
                <tr><td colspan="3">Sin compras registradas</td></tr>
            <?php endif; ?>
        </table>

        <!-- Botón para volver a la página principal -->
        <button><a href="index.php">Volver</a></button>
    </body>
    </html>
    <?php
}

// Llamar a la función para mostrar el historial al cargar este script
mostrarHistorial();
?>
