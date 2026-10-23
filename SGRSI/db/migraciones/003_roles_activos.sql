-- Aplicar UNA VEZ, sobre las tablas ROL y USUARIO_ROL existentes de develop.
-- Respaldar primero. No borra ni importa semillas. Revisar si se interrumpe.
USE sgrsi;
ALTER TABLE ROL ADD activo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE USUARIO_ROL ADD activo BOOLEAN NOT NULL DEFAULT TRUE;
