<?php
// Inicia la sesión para poder usar variables de sesión
session_start();

// Función para iniciar sesión como administrador directamente
function entrarComoAdmin() {
    if (!isset($_POST['admin_pass']) || $_POST['admin_pass'] !== '4237') {
        echo "<p style='color:red;text-align:center;'>Contraseña de administrador incorrecta.</p>";
        return;
    }
    $_SESSION['usuario'] = 'admin@tienda.com';              // Guarda email ficticio
    $_SESSION['nombre_completo'] = 'Administrador';         // Nombre a mostrar
    $_SESSION['rol'] = 'admin';                             // Rol asignado
    header("Location: index.php");                          // Redirige al inicio
    exit();
}

// Función para entrar como usuario anónimo
function entrarComoAnonimo() {
    $_SESSION['usuario'] = 'anonimo';                       // Nombre genérico
    $_SESSION['nombre_completo'] = 'Usuario Anónimo';       // Nombre a mostrar
    $_SESSION['rol'] = 'anonimo';                           // Rol anónimo
    header("Location: index.php");                          // Redirige al inicio
    exit();
}

// Función para procesar un login con usuario y contraseña reales
function procesarLoginNormal() {
    $conexion = mysqli_connect("localhost", "root", "", "tienda"); // Conecta a la BD

    if ($conexion) {
        $email_telefono = $_POST['email_telefono'];         // Email o teléfono ingresado
        $contrasena = $_POST['contraseña'];                 // Contraseña ingresada

        // Consulta para buscar el usuario por email o teléfono
        $sql = "SELECT nombre, apellidos, contraseña FROM Usuarios 
                WHERE email = '$email_telefono' OR telefono = '$email_telefono';";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);         // Obtiene datos del usuario

            // Compara contraseñas tal cual (sin hash)
            if ($contrasena == $fila['contraseña']) {
                $_SESSION['usuario'] = $email_telefono;
                $_SESSION['nombre_completo'] = $fila['nombre'] . ' ' . $fila['apellidos'];
                $_SESSION['rol'] = 'usuario';
                header("Location: index.php");              // Acceso permitido
                exit();
            } else {
                // Contraseña incorrecta
                echo "<p style='color:red;text-align:center;'>Contraseña incorrecta.</p>";
            }
        } else {
            // Usuario no encontrado
            echo "<p style='color:red;text-align:center;'>El usuario no existe.</p>";
        }

        mysqli_close($conexion); // Cierra la conexión
    }
}

// Lógica para determinar qué tipo de login se está haciendo
if (isset($_POST['admin_directo'])) {
    entrarComoAdmin();                       // Botón oculto de login directo como admin
} elseif (isset($_POST['anonimo']) && $_POST['anonimo'] == 'true') {
    entrarComoAnonimo();                     // Botón para entrar como usuario anónimo
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_telefono'])) {
    procesarLoginNormal();                   // Login normal con email/teléfono y contraseña
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

        <form method="POST" id="admin-form" style="display:inline;">
            <input type="hidden" name="admin_directo" value="1">
            <button type="button" class="registro-boton" onclick="mostrarAdminPass()">Entrar como Administrador</button>
            <div id="admin-pass-div" style="display:none;margin-top:10px;">
                <input type="password" name="admin_pass" id="admin_pass" placeholder="Contraseña admin">
                <button type="submit" class="registro-boton">Acceder</button>
            </div>
        </form>
        <script>
        function mostrarAdminPass() {
            document.getElementById('admin-pass-div').style.display = 'block';
        }
        </script>

        <button type="button" class="registro-boton" onclick="window.location.href='registro.php'">Registrarse</button>
    </div>
</body>
</html>
