-- Consultas literales activas: parametros :nombre se suministran por PDO.
-- Es un inventario, no un script para ejecutar entero. Consultas construidas por partes documentadas al final.

-- proyecto/app/controlador/ApiComun.php
SELECT id_usuario, nombre_usuario, activo FROM USUARIO WHERE id_usuario = :id;

-- proyecto/app/controlador/ApiComun.php
SELECT r.nombre_rol FROM ROL r INNER JOIN USUARIO_ROL ur ON ur.id_rol = r.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1;

-- proyecto/app/controlador/ApiEquipos.php
SELECT id_prestamo FROM PRESTAMO WHERE id_equipo_en_uso = :id;

-- proyecto/app/controlador/ApiEquipos.php
SELECT id_laboratorio FROM LABORATORIO WHERE id_laboratorio = :id AND activo = 1;

-- proyecto/app/controlador/ApiEquipos.php
SELECT id_equipo FROM EQUIPO WHERE codigo_inventario = :codigo AND id_equipo <> :id;

-- proyecto/app/controlador/ApiLaboratorios.php
SELECT id_equipo FROM EQUIPO WHERE id_laboratorio = :id AND activo = 1;

-- proyecto/app/controlador/ApiLaboratorios.php
SELECT id_laboratorio FROM LABORATORIO WHERE nombre = :nombre AND id_laboratorio <> :id;

-- proyecto/app/controlador/ApiPrestamos.php
SELECT condicion FROM EQUIPO WHERE id_equipo = :id;

-- proyecto/app/controlador/ApiPrestamos.php
SELECT e.activo, e.condicion, u.activo AS responsable_activo FROM EQUIPO e INNER JOIN USUARIO u ON u.id_usuario = :usuario WHERE e.id_equipo = :equipo;

-- proyecto/app/controlador/ApiSolicitudes.php
SELECT r.nombre_rol FROM USUARIO_ROL ur INNER JOIN ROL r ON r.id_rol = ur.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1;

-- proyecto/app/controlador/ApiSolicitudes.php
SELECT nombre FROM LABORATORIO WHERE id_laboratorio = :id;

-- proyecto/app/modelo/AccesoDatosEquipo.php
SELECT id_equipo, codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo FROM EQUIPO ORDER BY id_equipo;

-- proyecto/app/modelo/AccesoDatosEquipo.php
SELECT id_equipo, codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo FROM EQUIPO WHERE id_equipo = :id;

-- proyecto/app/modelo/AccesoDatosEquipo.php
SELECT id_equipo FROM EQUIPO WHERE codigo_inventario = :valor;

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
SELECT id_laboratorio, nombre, ubicacion, activo FROM LABORATORIO ORDER BY id_laboratorio;

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
SELECT id_laboratorio, nombre, ubicacion, activo FROM LABORATORIO WHERE id_laboratorio = :id;

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
SELECT id_laboratorio FROM LABORATORIO WHERE nombre = :valor;

-- proyecto/app/modelo/AccesoDatosMetricas.php
SELECT id_usuario FROM USUARIO WHERE activo = 1;

-- proyecto/app/modelo/AccesoDatosMetricas.php
SELECT id_laboratorio FROM LABORATORIO WHERE activo = 1;

-- proyecto/app/modelo/AccesoDatosMetricas.php
SELECT condicion FROM EQUIPO WHERE activo = 1;

-- proyecto/app/modelo/AccesoDatosMetricas.php
SELECT estado FROM TICKET WHERE activo = 1;

-- proyecto/app/modelo/AccesoDatosRol.php
SELECT id_rol, nombre_rol, activo FROM ROL ORDER BY id_rol;

-- proyecto/app/modelo/AccesoDatosRol.php
SELECT id_rol, nombre_rol, activo FROM ROL WHERE id_rol = :id;

-- proyecto/app/modelo/AccesoDatosRol.php
SELECT id_rol, nombre_rol, activo FROM ROL WHERE nombre_rol = :nombre;

-- proyecto/app/modelo/AccesoDatosRol.php
SELECT id_rol FROM ROL WHERE nombre_rol = :valor;

-- proyecto/app/modelo/AccesoDatosRol.php
SELECT id_usuario FROM USUARIO_ROL WHERE id_rol = :id AND activo = 1;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT r.nombre_rol FROM ROL r INNER JOIN USUARIO_ROL ur ON ur.id_rol = r.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1 ORDER BY r.id_rol;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario, nombre_usuario, password, activo FROM USUARIO WHERE nombre_usuario = :nombre;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario, nombre_usuario, nombre_completo, email, activo FROM USUARIO ORDER BY id_usuario;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario, nombre_usuario, nombre_completo, email, activo FROM USUARIO WHERE id_usuario = :id;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario FROM USUARIO WHERE (nombre_usuario = :nombre OR email = :email) AND id_usuario <> :excepto;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_rol FROM ROL WHERE nombre_rol = :nombre AND activo = 1;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario FROM USUARIO_ROL WHERE id_usuario = :usuario AND id_rol = :rol;

-- proyecto/app/modelo/AccesoDatosUsuario.php
SELECT id_usuario FROM USUARIO WHERE nombre_usuario = :valor;

-- proyecto/app/modelo/AccesoDatosUsuarioRol.php
SELECT ur.id_usuario, ur.id_rol, ur.activo, u.nombre_usuario, r.nombre_rol FROM USUARIO_ROL ur INNER JOIN USUARIO u ON u.id_usuario = ur.id_usuario INNER JOIN ROL r ON r.id_rol = ur.id_rol ORDER BY ur.id_usuario, ur.id_rol;

-- proyecto/app/modelo/AccesoDatosUsuarioRol.php
SELECT id_usuario, id_rol, activo FROM USUARIO_ROL WHERE id_usuario = :usuario AND id_rol = :rol;

-- proyecto/app/modelo/DiagnosticoDAO.php
SELECT d.*, t.id_equipo FROM DIAGNOSTICO d INNER JOIN TICKET t ON t.id_ticket = d.id_ticket ORDER BY d.id_diagnostico DESC;

-- proyecto/app/modelo/DiagnosticoDAO.php
SELECT * FROM DIAGNOSTICO WHERE id_diagnostico = :id;

-- proyecto/app/modelo/PrestamoDAO.php
SELECT * FROM PRESTAMO WHERE id_prestamo = :id;

-- proyecto/app/modelo/ReporteDAO.php
SELECT t.*, e.codigo_inventario FROM TICKET t LEFT JOIN EQUIPO e ON e.id_equipo = t.id_equipo WHERE t.fecha_inicio >= :desde AND t.fecha_inicio <= :hasta AND t.activo = 1;

-- proyecto/app/modelo/ReporteDAO.php
SELECT id_solicitud FROM SOLICITUD WHERE fecha_solicitada >= :desde AND fecha_solicitada <= :hasta AND activo = 1;

-- proyecto/app/modelo/ReporteDAO.php
SELECT u.id_uso, l.nombre FROM USO_LABORATORIO u INNER JOIN LABORATORIO l ON l.id_laboratorio = u.id_laboratorio WHERE u.fecha >= :desde AND u.fecha <= :hasta AND u.activo = 1;

-- proyecto/app/modelo/ReporteDAO.php
SELECT id_prestamo FROM PRESTAMO WHERE fecha_inicio >= :desde AND fecha_inicio <= :hasta AND estado <> 'anulado';

-- proyecto/app/modelo/SolicitudDAO.php
SELECT * FROM SOLICITUD WHERE id_solicitud = :id;

-- proyecto/app/modelo/SolicitudDAO.php
SELECT h.*, u.nombre_completo AS actor FROM HISTORIAL_SOLICITUD h INNER JOIN USUARIO u ON u.id_usuario = h.id_actor WHERE h.id_solicitud = :id ORDER BY h.id_historial;

-- proyecto/app/modelo/TareaDAO.php
SELECT * FROM TAREA ORDER BY fecha_inicio, hora_inicio, id_tarea;

-- proyecto/app/modelo/TareaDAO.php
SELECT * FROM TAREA WHERE id_tarea = :id;

-- proyecto/app/modelo/TareaDAO.php
SELECT * FROM HISTORIAL_TAREA WHERE id_tarea = :id ORDER BY id_historial;

-- proyecto/app/modelo/TicketDAO.php
SELECT * FROM TICKET WHERE id_ticket = :id;

-- proyecto/app/modelo/TicketDAO.php
SELECT h.*, u.nombre_completo AS actor FROM HISTORIAL_TICKET h INNER JOIN USUARIO u ON u.id_usuario = h.id_actor WHERE h.id_ticket = :id ORDER BY h.id_historial;

-- proyecto/app/modelo/UsoDAO.php
SELECT * FROM USO_LABORATORIO ORDER BY id_uso DESC;

-- proyecto/app/modelo/UsoDAO.php
SELECT * FROM USO_LABORATORIO WHERE id_uso = :id;

-- proyecto/app/modelo/UsoDAO.php
SELECT * FROM HISTORIAL_USO WHERE id_uso = :id ORDER BY id_historial;

-- proyecto/public/api/alertas.php
SELECT id_ticket, descripcion, fecha_limite, estado, espera_insumo FROM TICKET WHERE activo = 1 AND estado <> 'resuelto';

-- proyecto/public/api/alertas.php
SELECT id_prestamo, id_equipo, id_responsable, fecha_limite, hora_limite FROM PRESTAMO WHERE estado = 'abierto';

-- proyecto/public/api/alertas.php
SELECT id_tarea, descripcion, fecha_limite, hora_limite FROM TAREA WHERE activo = 1 AND estado <> 'resuelto';

-- proyecto/public/api/catalogos.php
SELECT id_equipo, codigo_inventario, descripcion FROM EQUIPO WHERE activo = 1 ORDER BY codigo_inventario;

-- proyecto/public/api/catalogos.php
SELECT id_laboratorio, nombre FROM LABORATORIO WHERE activo = 1 ORDER BY nombre;

-- proyecto/public/api/catalogos.php
SELECT id_usuario, nombre_completo FROM USUARIO WHERE activo = 1 ORDER BY nombre_completo;

-- proyecto/public/api/catalogos.php
SELECT u.id_usuario, u.nombre_completo, r.nombre_rol FROM USUARIO u INNER JOIN USUARIO_ROL ur ON ur.id_usuario = u.id_usuario INNER JOIN ROL r ON r.id_rol = ur.id_rol WHERE u.activo = 1 AND ur.activo = 1 AND r.activo = 1;

-- proyecto/public/api/historial_equipo.php
SELECT * FROM EQUIPO WHERE id_equipo = :id;

-- proyecto/public/api/historial_equipo.php
SELECT p.*, u.nombre_completo AS responsable FROM PRESTAMO p INNER JOIN USUARIO u ON u.id_usuario = p.id_responsable WHERE p.id_equipo = :id ORDER BY p.id_prestamo;

-- proyecto/public/api/historial_equipo.php
SELECT * FROM TICKET WHERE id_equipo = :id ORDER BY id_ticket;

-- proyecto/public/api/historial_equipo.php
SELECT d.*, u.nombre_completo AS autor FROM DIAGNOSTICO d INNER JOIN TICKET t ON t.id_ticket = d.id_ticket INNER JOIN USUARIO u ON u.id_usuario = d.id_actor WHERE t.id_equipo = :id ORDER BY d.id_diagnostico;

-- proyecto/public/api/historial_equipo.php
SELECT * FROM HISTORIAL_EQUIPO WHERE id_equipo = :id ORDER BY id_historial;

-- proyecto/public/api/historial_equipo.php
SELECT h.* FROM HISTORIAL_TICKET h INNER JOIN TICKET t ON t.id_ticket = h.id_ticket WHERE t.id_equipo = :id ORDER BY h.id_historial;

-- TicketDAO/SolicitudDAO: SELECT con JOIN al creador; WHERE nombre_usuario=:usuario
-- solamente para el solicitante, seguido de ORDER BY identificador DESC.
-- PrestamoDAO: SELECT con JOIN a equipo y responsable; WHERE id_responsable=:id
-- para solicitante, seguido de ORDER BY id_prestamo DESC.
