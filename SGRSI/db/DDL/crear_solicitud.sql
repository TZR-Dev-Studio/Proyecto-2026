USE sgrsi;

CREATE TABLE IF NOT EXISTS SOLICITUD (
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
