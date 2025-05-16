<?php
session_start();

function conectarBD() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");
    if (!$conexion) {
        echo "Error al conectar: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit();
    }
    return $conexion;
}

function validarContrasenas($contraseña, $repetir_contraseña) {
    if ($contraseña !== $repetir_contraseña) {
        $_SESSION['registro_error'] = "Las contraseñas no coinciden.";
        header("Location: registro.php");
        exit();
    }
}

function existeUsuario($conexion, $email, $telefono) {
    $sql = "SELECT * FROM Usuarios WHERE email = '$email' OR telefono = '$telefono'";
    $resultado = mysqli_query($conexion, $sql);
    return ($resultado && mysqli_num_rows($resultado) > 0);
}

function insertarUsuario($conexion, $nombre, $apellidos, $email, $contraseña, $telefono) {
    $sql_insertar = "INSERT INTO Usuarios (nombre, apellidos, email, contraseña, telefono)
                     VALUES ('$nombre', '$apellidos', '$email', '$contraseña', '$telefono')";
    return mysqli_query($conexion, $sql_insertar);
}

function procesarRegistro() {
    $conexion = conectarBD();

    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $repetir_contraseña = $_POST['repetir_contraseña'];
    $telefono = $_POST['telefono'];

    validarContrasenas($contraseña, $repetir_contraseña);

    if (existeUsuario($conexion, $email, $telefono)) {
        $_SESSION['registro_error'] = "El correo o el teléfono ya están registrados.";
        header("Location: registro.php");
        exit();
    }

    if (insertarUsuario($conexion, $nombre, $apellidos, $email, $contraseña, $telefono)) {
        $_SESSION['usuario'] = $email;
        $_SESSION['nombre_completo'] = $nombre . " " . $apellidos;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['registro_error'] = "Error al registrar el usuario.";
        header("Location: registro.php");
        exit();
    }

    mysqli_close($conexion);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    procesarRegistro();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="style-registro.css">
</head>
<body class="registro-body">
    <h1 class="registro-titulo">Registro de Usuario</h1>

    <?php
    if (isset($_SESSION['registro_error'])) {
        echo "<p style='color: red; text-align: center;'>" . $_SESSION['registro_error'] . "</p>";
        unset($_SESSION['registro_error']);
    }
    ?>

    <form action="registro.php" method="POST" class="registro-form">
        <div class="input-group">
            <label class="registro-label">Nombre:</label>
            <input type="text" name="nombre" class="registro-input" required>
        </div>

        <div class="input-group">
            <label class="registro-label">Apellidos:</label>
            <input type="text" name="apellidos" class="registro-input" required>
        </div>

        <div class="input-group">
            <label class="registro-label">Email:</label>
            <input type="email" name="email" class="registro-input" required>
        </div>

        <div class="input-group">
            <label class="registro-label">Contraseña:</label>
            <input type="password" name="contraseña" class="registro-input" required>
        </div>

        <div class="input-group">
            <label class="registro-label">Repetir Contraseña:</label>
            <input type="password" name="repetir_contraseña" class="registro-input" required>
        </div>

        <div class="input-group">
            <label class="registro-label">Teléfono:</label>
            <input type="text" name="telefono" class="registro-input" required>
        </div>

        <div class="botones-contenedor">
            <button type="submit" class="registro-boton">Registrarse</button>
            <button type="button" class="login-boton" onclick="window.location.href='login.php'">Ir a Login</button>
        </div>
    </form>
</body>
</html>


