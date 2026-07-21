USE sgrsi;

-- 1. Recuperar todos los usuarios
SELECT id_usuario, nombre_completo, nombre_usuario, email, activo
FROM USUARIO;

-- Recuperar un usuario por nombre_usuario
SELECT id_usuario, nombre_completo, nombre_usuario, password, email, activo
FROM USUARIO
WHERE nombre_usuario = 'admin';

-- 2. Consulta equivalente a AccesoDatosUsuario.php::buscarUsuario()
SELECT
    u.nombre_usuario,
    u.password AS claveHash,
    u.activo,
    GROUP_CONCAT(r.nombre_rol) AS roles

FROM USUARIO AS u

LEFT JOIN USUARIO_ROL AS ur
    ON ur.id_usuario = u.id_usuario

LEFT JOIN ROL AS r
    ON r.id_rol = ur.id_rol

WHERE u.nombre_usuario = 'admin'

GROUP BY u.id_usuario;

-- 3. Verificar roles asociados a un usuario
SELECT r.nombre_rol
FROM ROL AS r
INNER JOIN USUARIO_ROL AS ur ON ur.id_rol = r.id_rol
INNER JOIN USUARIO AS u ON u.id_usuario = ur.id_usuario
WHERE u.nombre_usuario = 'admin';

-- Verificar si un usuario tiene un rol específico
SELECT COUNT(*) AS tieneRol
FROM USUARIO_ROL AS ur
INNER JOIN USUARIO AS u ON u.id_usuario = ur.id_usuario
INNER JOIN ROL AS r ON r.id_rol = ur.id_rol
WHERE u.nombre_usuario = 'admin' AND r.nombre_rol = 'administrador';

-- Listar usuarios con múltiples roles
SELECT u.nombre_usuario, COUNT(ur.id_rol) AS cantidadRoles
FROM USUARIO AS u
INNER JOIN USUARIO_ROL AS ur ON ur.id_usuario = u.id_usuario
GROUP BY u.id_usuario
HAVING COUNT(ur.id_rol) > 1;