<?php
session_start();

// Solo el admin puede acceder, redirige si no es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin@tienda.com') {
    header("Location: login.php");
    exit();
}

$conexion = mysqli_connect("localhost", "root", "", "tienda");
if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

$error = "";
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_categoria = trim($_POST['nombre_categoria']);

    if ($nombre_categoria == "") {
        $error = "El nombre de la categoría es obligatorio.";
    } else {
        // Verificar si ya existe
        $sql = "SELECT id_categoria FROM categorias WHERE nombre_categoria = '$nombre_categoria'";
        $resultado = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            $error = "La categoría '$nombre_categoria' ya existe.";
        } else {
            // Insertar nueva categoría
            $sql = "INSERT INTO categorias (nombre_categoria) VALUES ('$nombre_categoria')";
            if (mysqli_query($conexion, $sql)) {
                $mensaje = "Categoría '$nombre_categoria' añadida con éxito.";
            } else {
                $error = "Error al añadir la categoría: " . mysqli_error($conexion);
            }
        }
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

<nav>
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="subir_categoria.php">Subir Categoría</a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
</nav>

<h1>Subir Nueva Categoría</h1>

<?php if ($mensaje) echo "<p style='color:green;'>$mensaje</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form action="subir_categoria.php" method="POST">
    <label for="nombre_categoria">Nombre de la categoría:</label><br>
    <input type="text" name="nombre_categoria" id="nombre_categoria" value="<?php echo htmlspecialchars($_POST['nombre_categoria'] ?? ''); ?>" required><br><br>
    <button type="submit">Añadir Categoría</button>
</form>

<p><a href="index.php">Volver al inicio</a></p>

</body>
</html>
