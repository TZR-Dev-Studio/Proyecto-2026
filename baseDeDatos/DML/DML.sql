USE sgrsi;

INSERT INTO USUARIO (nombre_completo, nombre_usuario, password, email, activo) VALUES
('Administrador Prueba', 'admin', '$2b$12$UYeo51kmRPX.i7eUZvmcfO6fu4Bq2xNVmaKe759R4Hh4OOXaGuUTe', 'admin@iti.edu.uy', 1),
('Tecnico Prueba', 'tecnico', '$2b$12$BM.zGTR50tEbitghc9A9nestXWLh8EL7vAZatow4P/qEITJjQ6/Ra', 'tecnico@iti.edu.uy', 1),
('Solicitante Prueba', 'solicitante', '$2b$12$fqGOrkZ9JhbYbjuagIaUye4gb8GMZglrC2vu8L9grZeMOTHbFDeay', 'solicitante@iti.edu.uy', 1);

INSERT INTO ROL (nombre_rol) VALUES
('administrador'),
('tecnico'),
('solicitante');

INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES
(1, 1),
(2, 2),
(3, 3);