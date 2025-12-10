CREATE DATABASE app_streaming;

USE app_streaming;


CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS contenidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM('pelicula', 'serie') NOT NULL,
    estado ENUM('vistas', 'viendo', 'por_ver') NOT NULL,
    imagen_url VARCHAR(255),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


INSERT INTO usuarios (correo, password) VALUES ('correo@correo.com', '1234');


