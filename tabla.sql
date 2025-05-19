CREATE TABLE usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    contraseña VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_usuario)
);
 
-- Tabla: categorias
CREATE TABLE categorias (
    id_categoria INT NOT NULL AUTO_INCREMENT,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (id_categoria)
);
 
-- Tabla: productos
CREATE TABLE productos (
    id_producto INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    nombre_imagen VARCHAR(255),
    categoria_id INT,
    id_vendedor INT,
    PRIMARY KEY (id_producto),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id_categoria),
    FOREIGN KEY (id_vendedor) REFERENCES usuarios(id_usuario)
);
 
-- Tabla: datos
CREATE TABLE datos (
    id_dato INT NOT NULL AUTO_INCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    fecha_compra DATETIME NOT NULL,
    PRIMARY KEY (id_dato)
);