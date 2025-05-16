<?php
function mostrarMenu() {
    $rol = obtenerRol();
    ?>
    <div class="menu" id="menu">
        <div class="menu-header">
            <link rel="stylesheet" href="style-menu.css">
            <h2>Menú</h2>
        </div>
        <ul>
            <li><a href="index.php">Inicio<br></a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="productos.php">Catálogo de productos</a></li>
            <li><a href="historial.php">nuestros productos vendidos</a></li>

            <?php if ($rol != 'anonimo'): ?>
                <li><a href="publicar.php">Publicar producto</a></li>
            <?php else: ?>
                <li><a href="#" style="color: gray; pointer-events: none; cursor: default;">Publicar producto</a></li>
            <?php endif; ?>

            <?php if ($rol == 'admin'): ?>
                <li><a href="subir_categoria.php">Subir Categoría</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="menu-toggle" id="menu-toggle">☰</div>

    <style>
    /* Mantener el estilo que ya tenías, no se modifica */
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menu = document.getElementById("menu");
        const menuToggle = document.getElementById("menu-toggle");
        const mainContent = document.querySelector("main");

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

mostrarMenu();
