-- Inventario de DML de la aplicacion. No ejecutar como lote.

-- proyecto/app/controlador/ApiEquipos.php
INSERT INTO HISTORIAL_EQUIPO (id_equipo, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle);

-- proyecto/app/controlador/ApiPrestamos.php
INSERT INTO DEVOLUCION (id_prestamo, fecha, hora, tipo, condicion, id_actor, nota) VALUES (:id, :fecha, :hora, :tipo, NULL, :actor, :nota);

-- proyecto/app/controlador/ApiPrestamos.php
INSERT INTO DEVOLUCION (id_prestamo, fecha, hora, tipo, condicion, id_actor, nota) VALUES (:id, :fecha, :hora, :tipo, :condicion, :actor, :nota);

-- proyecto/app/controlador/ApiPrestamos.php
UPDATE EQUIPO SET condicion = :condicion, activo = :activo WHERE id_equipo = :id;

-- proyecto/app/modelo/AccesoDatosEquipo.php
INSERT INTO EQUIPO (codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo) VALUES (:codigo_inventario, :descripcion, :tipo, :marca, :condicion, :id_laboratorio, :activo);

-- proyecto/app/modelo/AccesoDatosEquipo.php
UPDATE EQUIPO SET codigo_inventario = :codigo_inventario, descripcion = :descripcion, tipo = :tipo, marca = :marca, condicion = :condicion, id_laboratorio = :id_laboratorio, activo = :activo WHERE id_equipo = :id;

-- proyecto/app/modelo/AccesoDatosEquipo.php
UPDATE EQUIPO SET activo = 0 WHERE id_equipo = :id;

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
INSERT INTO LABORATORIO (nombre, ubicacion, activo) VALUES (:nombre, :ubicacion, :activo);

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
UPDATE LABORATORIO SET nombre = :nombre, ubicacion = :ubicacion, activo = :activo WHERE id_laboratorio = :id;

-- proyecto/app/modelo/AccesoDatosLaboratorio.php
UPDATE LABORATORIO SET activo = 0 WHERE id_laboratorio = :id;

-- proyecto/app/modelo/AccesoDatosRol.php
INSERT INTO ROL (nombre_rol, activo) VALUES (:nombre, :activo);

-- proyecto/app/modelo/AccesoDatosRol.php
UPDATE ROL SET activo = :activo WHERE id_rol = :id;

-- proyecto/app/modelo/AccesoDatosUsuario.php
UPDATE USUARIO_ROL SET activo = 0 WHERE id_usuario = :id;

-- proyecto/app/modelo/AccesoDatosUsuario.php
INSERT INTO USUARIO_ROL (id_usuario, id_rol, activo) VALUES (:usuario, :rol, 1);

-- proyecto/app/modelo/AccesoDatosUsuario.php
UPDATE USUARIO_ROL SET activo = 1 WHERE id_usuario = :usuario AND id_rol = :rol;

-- proyecto/app/modelo/AccesoDatosUsuario.php
INSERT INTO USUARIO (nombre_completo, nombre_usuario, password, email) VALUES (:nombre, :usuario, :hash, :email);

-- proyecto/app/modelo/AccesoDatosUsuario.php
UPDATE USUARIO SET nombre_completo = :nombre, email = :email, activo = :activo WHERE id_usuario = :id;

-- proyecto/app/modelo/AccesoDatosUsuario.php
UPDATE USUARIO SET password = :hash WHERE id_usuario = :id;

-- proyecto/app/modelo/AccesoDatosUsuario.php
UPDATE USUARIO SET activo = 0 WHERE id_usuario = :id;

-- proyecto/app/modelo/AccesoDatosUsuarioRol.php
INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES (:usuario, :rol);

-- proyecto/app/modelo/AccesoDatosUsuarioRol.php
UPDATE USUARIO_ROL SET activo = :activo WHERE id_usuario = :usuario AND id_rol = :rol;

-- proyecto/app/modelo/DiagnosticoDAO.php
INSERT INTO DIAGNOSTICO (id_ticket, id_actor, fecha, hora, nota, activo) VALUES (:id_ticket, :id_actor, :fecha, :hora, :nota, 1);

-- proyecto/app/modelo/DiagnosticoDAO.php
UPDATE DIAGNOSTICO SET nota = :nota WHERE id_diagnostico = :id;

-- proyecto/app/modelo/DiagnosticoDAO.php
UPDATE DIAGNOSTICO SET activo = 0 WHERE id_diagnostico = :id;

-- proyecto/app/modelo/PrestamoDAO.php
INSERT INTO PRESTAMO (id_equipo, id_equipo_en_uso, id_responsable, documento, grupo, fecha_inicio, hora_inicio, fecha_limite, hora_limite, nota, id_actor) VALUES (:id_equipo, :ocupado, :id_responsable, :documento, :grupo, :fecha_inicio, :hora_inicio, :fecha_limite, :hora_limite, :nota, :id_actor);

-- proyecto/app/modelo/PrestamoDAO.php
UPDATE PRESTAMO SET fecha_devolucion = :fecha, hora_devolucion = :hora, condicion_devolucion = :condicion, id_actor_devolucion = :actor, id_equipo_en_uso = NULL, estado = 'devuelto' WHERE id_prestamo = :id AND estado = 'abierto';

-- proyecto/app/modelo/PrestamoDAO.php
UPDATE PRESTAMO SET estado = 'anulado', motivo_anulacion = :motivo, id_equipo_en_uso = NULL WHERE id_prestamo = :id AND estado = 'abierto';

-- proyecto/app/modelo/SolicitudDAO.php
INSERT INTO SOLICITUD (tipo, descripcion, laboratorio, fecha_solicitada, urgencia, estado, id_usuario, activo, id_laboratorio, id_tecnico, hora_solicitada, nota_resolucion) VALUES (:tipo, :descripcion, :laboratorio, :fecha_solicitada, :urgencia, :estado, :id_usuario, :activo, :id_laboratorio, :id_tecnico, :hora_solicitada, :nota_resolucion);

-- proyecto/app/modelo/SolicitudDAO.php
UPDATE SOLICITUD SET tipo = :tipo, descripcion = :descripcion, laboratorio = :laboratorio, fecha_solicitada = :fecha_solicitada, urgencia = :urgencia, estado = :estado, id_usuario = :id_usuario, activo = :activo, id_laboratorio = :id_laboratorio, id_tecnico = :id_tecnico, hora_solicitada = :hora_solicitada, nota_resolucion = :nota_resolucion WHERE id_solicitud = :id;

-- proyecto/app/modelo/SolicitudDAO.php
INSERT INTO HISTORIAL_SOLICITUD (id_solicitud, id_actor, fecha, hora, accion, detalle) VALUES (:id, :actor, :fecha, :hora, :accion, :detalle);

-- proyecto/app/modelo/TareaDAO.php
INSERT INTO TAREA (descripcion, tipo, prioridad, fecha_inicio, hora_inicio, fecha_limite, hora_limite, id_tecnico, id_equipo, estado, nota, activo) VALUES (:descripcion, :tipo, :prioridad, :fecha_inicio, :hora_inicio, :fecha_limite, :hora_limite, :id_tecnico, :id_equipo, :estado, :nota, :activo);

-- proyecto/app/modelo/TareaDAO.php
UPDATE TAREA SET descripcion = :descripcion, tipo = :tipo, prioridad = :prioridad, fecha_inicio = :fecha_inicio, hora_inicio = :hora_inicio, fecha_limite = :fecha_limite, hora_limite = :hora_limite, id_tecnico = :id_tecnico, id_equipo = :id_equipo, estado = :estado, nota = :nota, activo = :activo WHERE id_tarea = :id;

-- proyecto/app/modelo/TareaDAO.php
INSERT INTO HISTORIAL_TAREA (id_tarea, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle);

-- proyecto/app/modelo/TicketDAO.php
INSERT INTO TICKET (descripcion, laboratorio, estado, prioridad, fecha_inicio, fecha_limite, id_usuario, activo, id_equipo, id_tecnico, hora_inicio, fecha_resolucion, hora_resolucion, nota_resolucion, espera_insumo) VALUES (:descripcion, :laboratorio, :estado, :prioridad, :fecha_inicio, :fecha_limite, :id_usuario, :activo, :id_equipo, :id_tecnico, :hora_inicio, :fecha_resolucion, :hora_resolucion, :nota_resolucion, :espera_insumo);

-- proyecto/app/modelo/TicketDAO.php
UPDATE TICKET SET descripcion = :descripcion, laboratorio = :laboratorio, estado = :estado, prioridad = :prioridad, fecha_inicio = :fecha_inicio, fecha_limite = :fecha_limite, id_usuario = :id_usuario, activo = :activo, id_equipo = :id_equipo, id_tecnico = :id_tecnico, hora_inicio = :hora_inicio, fecha_resolucion = :fecha_resolucion, hora_resolucion = :hora_resolucion, nota_resolucion = :nota_resolucion, espera_insumo = :espera_insumo WHERE id_ticket = :id;

-- proyecto/app/modelo/TicketDAO.php
INSERT INTO HISTORIAL_TICKET (id_ticket, id_actor, fecha, hora, accion, detalle) VALUES (:id, :actor, :fecha, :hora, :accion, :detalle);

-- proyecto/app/modelo/UsoDAO.php
INSERT INTO USO_LABORATORIO (id_laboratorio, id_responsable, grupo, actividad, fecha, hora_inicio, hora_fin, activo) VALUES (:id_laboratorio, :id_responsable, :grupo, :actividad, :fecha, :hora_inicio, :hora_fin, :activo);

-- proyecto/app/modelo/UsoDAO.php
UPDATE USO_LABORATORIO SET id_laboratorio = :id_laboratorio, id_responsable = :id_responsable, grupo = :grupo, actividad = :actividad, fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin, activo = :activo WHERE id_uso = :id;

-- proyecto/app/modelo/UsoDAO.php
INSERT INTO HISTORIAL_USO (id_uso, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle);

-- proyecto/public/api/condicion_equipo.php
UPDATE EQUIPO SET condicion = :condicion WHERE id_equipo = :id;