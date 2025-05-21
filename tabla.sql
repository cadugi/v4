-- Tabla: usuarios
CREATE TABLE usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    contraseña VARCHAR(100) NOT NULL,
    rol VARCHAR(20) DEFAULT 'usuario',
    CONSTRAINT pk_usuarios PRIMARY KEY (id_usuario)
);

-- Tabla: categorias
CREATE TABLE categorias (
    id_categoria INT NOT NULL AUTO_INCREMENT,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT pk_categorias PRIMARY KEY (id_categoria)
);

-- Tabla: productos
CREATE TABLE productos (
    id_producto INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    nombre_imagen TEXT, -- Para almacenar varias imágenes serializadas
    categoria_id INT,
    id_vendedor INT,
    CONSTRAINT pk_productos PRIMARY KEY (id_producto),
    CONSTRAINT fk_productos_categoria_id FOREIGN KEY (categoria_id) REFERENCES categorias(id_categoria),
    CONSTRAINT fk_productos_id_vendedor FOREIGN KEY (id_vendedor) REFERENCES usuarios(id_usuario)
);

-- Tabla: datos (historial de compras)
CREATE TABLE datos (
    id_dato INT NOT NULL AUTO_INCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    fecha_compra DATETIME NOT NULL,
    email_comprador VARCHAR(150) NOT NULL,
    precio_producto DECIMAL(10,2) NOT NULL,
    email_vendedor VARCHAR(150) NOT NULL,
    tarjeta VARCHAR(20),
    caducidad VARCHAR(7),
    cvv VARCHAR(4),
    CONSTRAINT pk_datos PRIMARY KEY (id_dato)
);