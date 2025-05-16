<?php
// Función que muestra el menú de navegación dinámico según el rol del usuario
function mostrarMenu() {
    // Llama a una función externa que obtiene el rol del usuario (por ejemplo: 'admin', 'usuario', 'anonimo')
    $rol = obtenerRol();
    ?>
    
    <!-- Contenedor principal del menú -->
    <div class="menu" id="menu">
        <div class="menu-header">
            <!-- Enlace al archivo CSS del menú -->
            <link rel="stylesheet" href="style-menu.css">
            <h2>Menú</h2>
        </div>
        
        <!-- Lista de enlaces del menú -->
        <ul>
            <li><a href="index.php">Inicio<br></a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="productos.php">Catálogo de productos</a></li>
            <li><a href="historial.php">nuestros productos vendidos</a></li>

            <!-- Solo permite publicar si el usuario NO es anónimo -->
            <?php if ($rol != 'anonimo'): ?>
                <li><a href="publicar.php">Publicar producto</a></li>
            <?php else: ?>
                <!-- Si es anónimo, muestra el enlace desactivado -->
                <li><a href="#" style="color: gray; pointer-events: none; cursor: default;">Publicar producto</a></li>
            <?php endif; ?>

            <!-- Solo los administradores pueden ver el enlace para subir categorías -->
            <?php if ($rol == 'admin'): ?>
                <li><a href="subir_categoria.php">Subir Categoría</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Botón hamburguesa para mostrar/ocultar el menú -->
    <div class="menu-toggle" id="menu-toggle">☰</div>

    <!-- Estilos CSS inline (comentario: se mantiene igual que lo que ya usabas) -->
    <style>
    /* Mantener el estilo que ya tenías, no se modifica */
    </style>

    <!-- Script para mostrar u ocultar el menú al hacer clic en el icono ☰ -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menu = document.getElementById("menu");
        const menuToggle = document.getElementById("menu-toggle");
        const mainContent = document.querySelector("main");

        // Alterna la clase 'hidden' en el menú al hacer clic
        menuToggle.addEventListener("click", function () {
            menu.classList.toggle("hidden");
            if (mainContent) {
                mainContent.classList.toggle("expanded");
            }
        });
    });
    </script>
    <?php
}

// Llamada a la función para que se muestre el menú cuando se incluya este archivo
mostrarMenu();
