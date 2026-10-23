-- INSTALACION NUEVA exclusivamente. No ejecutar sobre una base existente.
-- Esquema consolidado compatible con develop 5d4cb154: 17 tablas.
-- No incorpora semillas ni elimina registros.

USE tzrdevstudio;

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
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE USUARIO_ROL (
    id_usuario INT NOT NULL,
    id_rol INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_rol) REFERENCES ROL(id_rol)
);

-- Sección: Mesa de Ayuda (Tickets)
-- Relación "crea": un USUARIO (1) crea muchos TICKET (N)
-- Se conserva laboratorio textual por compatibilidad con TicketDAO.
-- La normalizacion completa requiere revisar las dependencias del dominio.
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


CREATE TABLE SOLICITUD (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    laboratorio VARCHAR(50) NOT NULL,
    fecha_solicitada DATE NOT NULL,
    urgencia VARCHAR(10) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario)
);

CREATE TABLE LABORATORIO (
 id_laboratorio INT AUTO_INCREMENT PRIMARY KEY,
 nombre VARCHAR(80) NOT NULL UNIQUE,
 ubicacion VARCHAR(120) NOT NULL,
 activo BOOLEAN NOT NULL DEFAULT TRUE
);
CREATE TABLE EQUIPO (
 id_equipo INT AUTO_INCREMENT PRIMARY KEY,
 codigo_inventario VARCHAR(30) NOT NULL UNIQUE,
 descripcion VARCHAR(200) NOT NULL,
 tipo VARCHAR(40) NOT NULL,
 marca VARCHAR(80) NOT NULL,
 condicion VARCHAR(20) NOT NULL DEFAULT 'operativo',
 id_laboratorio INT NOT NULL,
 activo BOOLEAN NOT NULL DEFAULT TRUE,
 FOREIGN KEY (id_laboratorio) REFERENCES LABORATORIO(id_laboratorio)
);

-- Modulos operativos: forma parte de esta instalacion nueva; no ejecutar migracion 004 despues.
-- 004: aplicar UNA VEZ después de 003; respaldar y revisar columnas existentes.
USE tzrdevstudio;
ALTER TABLE TICKET ADD activo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE TICKET ADD id_equipo INT NULL;
ALTER TABLE TICKET ADD id_tecnico INT NULL;
ALTER TABLE TICKET ADD hora_inicio VARCHAR(5) NULL;
ALTER TABLE TICKET ADD fecha_resolucion DATE NULL;
ALTER TABLE TICKET ADD hora_resolucion VARCHAR(5) NULL;
ALTER TABLE TICKET ADD nota_resolucion VARCHAR(1000) NULL;
ALTER TABLE TICKET ADD espera_insumo BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE TICKET ADD FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo);
ALTER TABLE TICKET ADD FOREIGN KEY (id_tecnico) REFERENCES USUARIO(id_usuario);
ALTER TABLE SOLICITUD ADD activo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE SOLICITUD ADD id_laboratorio INT NULL;
ALTER TABLE SOLICITUD ADD id_tecnico INT NULL;
ALTER TABLE SOLICITUD ADD hora_solicitada VARCHAR(5) NULL;
ALTER TABLE SOLICITUD ADD nota_resolucion VARCHAR(1000) NULL;
ALTER TABLE SOLICITUD ADD FOREIGN KEY (id_laboratorio) REFERENCES LABORATORIO(id_laboratorio);
ALTER TABLE SOLICITUD ADD FOREIGN KEY (id_tecnico) REFERENCES USUARIO(id_usuario);
CREATE TABLE HISTORIAL_TICKET (
 id_historial INT AUTO_INCREMENT PRIMARY KEY,
 id_ticket INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 accion VARCHAR(30) NOT NULL,
 detalle VARCHAR(10000) NOT NULL,
 FOREIGN KEY (id_ticket) REFERENCES TICKET(id_ticket),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE HISTORIAL_SOLICITUD (
 id_historial INT AUTO_INCREMENT PRIMARY KEY,
 id_solicitud INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 accion VARCHAR(30) NOT NULL,
 detalle VARCHAR(10000) NOT NULL,
 FOREIGN KEY (id_solicitud) REFERENCES SOLICITUD(id_solicitud),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE DIAGNOSTICO (
 id_diagnostico INT AUTO_INCREMENT PRIMARY KEY,
 id_ticket INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 nota VARCHAR(1000) NOT NULL,
 activo BOOLEAN NOT NULL DEFAULT TRUE,
 FOREIGN KEY (id_ticket) REFERENCES TICKET(id_ticket),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE PRESTAMO (
 id_prestamo INT AUTO_INCREMENT PRIMARY KEY,
 id_equipo INT NOT NULL,
 -- Referencia unica mientras esta abierto; NULL al devolver o anular.
 id_equipo_en_uso INT NULL UNIQUE,
 id_responsable INT NOT NULL,
 documento VARCHAR(30) NOT NULL,
 grupo VARCHAR(50) NOT NULL,
 fecha_inicio DATE NOT NULL,
 hora_inicio VARCHAR(5) NOT NULL,
 fecha_limite DATE NOT NULL,
 hora_limite VARCHAR(5) NOT NULL,
 fecha_devolucion DATE NULL,
 hora_devolucion VARCHAR(5) NULL,
 condicion_devolucion VARCHAR(20) NULL,
 estado VARCHAR(20) NOT NULL DEFAULT 'abierto',
 nota VARCHAR(1000) NOT NULL,
 motivo_anulacion VARCHAR(1000) NULL,
 id_actor INT NOT NULL,
 id_actor_devolucion INT NULL,
 FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo),
 FOREIGN KEY (id_equipo_en_uso) REFERENCES EQUIPO(id_equipo),
 FOREIGN KEY (id_responsable) REFERENCES USUARIO(id_usuario),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario),
 FOREIGN KEY (id_actor_devolucion) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE TAREA (
 id_tarea INT AUTO_INCREMENT PRIMARY KEY,
 descripcion VARCHAR(255) NOT NULL,
 tipo VARCHAR(20) NOT NULL,
 prioridad VARCHAR(10) NOT NULL,
 fecha_inicio DATE NOT NULL,
 hora_inicio VARCHAR(5) NOT NULL,
 fecha_limite DATE NOT NULL,
 hora_limite VARCHAR(5) NOT NULL,
 id_tecnico INT NOT NULL,
 id_equipo INT NULL,
 estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
 nota VARCHAR(1000) NOT NULL,
 activo BOOLEAN NOT NULL DEFAULT TRUE,
 FOREIGN KEY (id_tecnico) REFERENCES USUARIO(id_usuario),
 FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo)
);
CREATE TABLE HISTORIAL_TAREA (
 id_historial INT AUTO_INCREMENT PRIMARY KEY,
 id_tarea INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 detalle VARCHAR(10000) NOT NULL,
 FOREIGN KEY (id_tarea) REFERENCES TAREA(id_tarea),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE USO_LABORATORIO (
 id_uso INT AUTO_INCREMENT PRIMARY KEY,
 id_laboratorio INT NOT NULL,
 id_responsable INT NOT NULL,
 grupo VARCHAR(50) NOT NULL,
 actividad VARCHAR(255) NOT NULL,
 fecha DATE NOT NULL,
 hora_inicio VARCHAR(5) NOT NULL,
 hora_fin VARCHAR(5) NOT NULL,
 activo BOOLEAN NOT NULL DEFAULT TRUE,
 FOREIGN KEY (id_laboratorio) REFERENCES LABORATORIO(id_laboratorio),
 FOREIGN KEY (id_responsable) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE HISTORIAL_USO (
 id_historial INT AUTO_INCREMENT PRIMARY KEY,
 id_uso INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 detalle VARCHAR(10000) NOT NULL,
 FOREIGN KEY (id_uso) REFERENCES USO_LABORATORIO(id_uso),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE DEVOLUCION (
 id_prestamo INT PRIMARY KEY,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 tipo VARCHAR(20) NOT NULL,
 condicion VARCHAR(20) NULL,
 id_actor INT NOT NULL,
 nota VARCHAR(1000) NOT NULL,
 FOREIGN KEY (id_prestamo) REFERENCES PRESTAMO(id_prestamo),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
CREATE TABLE HISTORIAL_EQUIPO (
 id_historial INT AUTO_INCREMENT PRIMARY KEY,
 id_equipo INT NOT NULL,
 id_actor INT NOT NULL,
 fecha DATE NOT NULL,
 hora VARCHAR(5) NOT NULL,
 detalle VARCHAR(10000) NOT NULL,
 FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo),
 FOREIGN KEY (id_actor) REFERENCES USUARIO(id_usuario)
);
