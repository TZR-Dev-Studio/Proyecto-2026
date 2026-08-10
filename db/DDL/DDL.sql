CREATE DATABASE IF NOT EXISTS sgrsi;
USE sgrsi;

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    activo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE ROL (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE USUARIO_ROL (
    id_usuario INT NOT NULL,
    id_rol INT NOT NULL,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_rol) REFERENCES ROL(id_rol)
);

-- Sección: Mesa de Ayuda (Tickets)
-- Relación "crea": un USUARIO (1) crea muchos TICKET (N)
-- Todos los atributos dependen únicamente de id_ticket (PK) y no hay
-- atributos multivaluados ni dependencias transitivas -> tabla en 3FN.
CREATE TABLE TICKET (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL,
    laboratorio VARCHAR(50) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    prioridad VARCHAR(10) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_limite DATE NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario)
);