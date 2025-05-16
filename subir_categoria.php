
<?php
session_start();
include 'funciones.php';

function funcionrol($email){
    return $email === 'admin@tienda.com' ? 'admin' : 'usuario';}


// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || funcionRol($_SESSION['usuario']) !== 'admin') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "tienda");

if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Inicializar variables
$nombre_categoria = '';
$error = '';
$mensaje_exito = '';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_categoria = trim(mysqli_real_escape_string($conexion, $_POST['nombre_categoria']));

    // Validación
    if (empty($nombre_categoria)) {
        $error = 'El nombre de la categoría es obligatorio.';
    } else {
        // Comprobar si ya existe
        $sql_check = "SELECT id_categoria FROM categorias WHERE nombre_categoria = ?";
        $stmt = mysqli_prepare($conexion, $sql_check);
        mysqli_stmt_bind_param($stmt, "s", $nombre_categoria);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "La categoría '$nombre_categoria' ya existe.";
        } else {
            // Insertar
            $sql_insert = "INSERT INTO categorias (nombre_categoria) VALUES (?)";
            $stmt_insert = mysqli_prepare($conexion, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "s", $nombre_categoria);

            if (mysqli_stmt_execute($stmt_insert)) {
                $mensaje_exito = "Categoría '$nombre_categoria' añadida con éxito.";
                $nombre_categoria = ''; // Limpiar campo
            } else {
                $error = "Error al añadir la categoría: " . mysqli_error($conexion);
            }

            mysqli_stmt_close($stmt_insert);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Categoría - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>

<main>
    <h1>Subir Nueva Categoría</h1>

    <?php if (!empty($mensaje_exito)) { ?>
        <p style="color: green;"><?php echo $mensaje_exito; ?></p>
    <?php } ?>

    <?php if (!empty($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>

    <form action="subir_categoria.php" method="POST">
        <label for="nombre_categoria">Nombre de la categoría:</label><br>
        <input type="text" id="nombre_categoria" name="nombre_categoria"
               value="<?php echo htmlspecialchars($nombre_categoria); ?>" required><br><br>

        <button type="submit">Añadir Categoría</button>
    </form>

    <p><a href="index.php">Volver al inicio</a></p>
</main>

<footer>
    <p>&copy; 2025 Tienda General Fonsi. Proyecto de práctica.</p>
</footer>

</body>
</html>

