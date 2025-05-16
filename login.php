<?php
session_start();

function entrarComoAdmin() {
    $_SESSION['usuario'] = 'admin@tienda.com';
    $_SESSION['nombre_completo'] = 'Administrador';
    $_SESSION['rol'] = 'admin';
    header("Location: index.php");
    exit();
}

function entrarComoAnonimo() {
    $_SESSION['usuario'] = 'anonimo';
    $_SESSION['nombre_completo'] = 'Usuario Anónimo';
    $_SESSION['rol'] = 'anonimo';
    header("Location: index.php");
    exit();
}

function procesarLoginNormal() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda");

    if ($conexion) {
        $email_telefono = $_POST['email_telefono'];
        $contrasena = $_POST['contraseña'];

        $sql = "SELECT nombre, apellidos, contraseña FROM Usuarios WHERE email = '$email_telefono' OR telefono = '$email_telefono';";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);
            if ($contrasena == $fila['contraseña']) {
                $_SESSION['usuario'] = $email_telefono;
                $_SESSION['nombre_completo'] = $fila['nombre'] . ' ' . $fila['apellidos'];
                $_SESSION['rol'] = 'usuario';
                header("Location: index.php");
                exit();
            } else {
                echo "<p style='color:red;text-align:center;'>Contraseña incorrecta.</p>";
            }
        } else {
            echo "<p style='color:red;text-align:center;'>El usuario no existe.</p>";
        }

        mysqli_close($conexion);
    }
}

// Lógica de control
if (isset($_POST['admin_directo'])) {
    entrarComoAdmin();
} elseif (isset($_POST['anonimo']) && $_POST['anonimo'] == 'true') {
    entrarComoAnonimo();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_telefono'])) {
    procesarLoginNormal();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="style-login.css">
</head>
<body>
    <h1>Inicio de Sesión</h1>
    <form action="login.php" method="POST" class="login-form">
        <label for="email_telefono">Email o Teléfono:</label><br>
        <input type="text" id="email_telefono" name="email_telefono" required><br><br>

        <label for="contraseña">Contraseña:</label><br>
        <input type="password" id="contraseña" name="contraseña" required><br><br>

        <button type="submit" class="login-boton">Iniciar Sesión</button>
    </form>

    <div class="botones-contenedor">
        <form method="POST" class="anonimo-form">
            <input type="hidden" name="anonimo" value="true">
            <button type="submit" class="anonimo-boton">Modo Anónimo</button>
        </form>

        <form method="POST">
            <input type="hidden" name="admin_directo" value="1">
            <button type="submit" class="registro-boton">Entrar como Administrador</button>
        </form>

        <button type="button" class="registro-boton" onclick="window.location.href='registro.php'">Registrarse</button>
    </div>
</body>
</html>
