<?php
require_once __DIR__ . '/../../app/controlador/ValidacionesDominio.php';
require_once __DIR__ . '/../../app/modelo/AccesoDatosEquipo.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    $metodo = $_SERVER['REQUEST_METHOD']; $id = idApi($_GET['id'] ?? null); $dao = new AccesoDatosEquipo($conexion); $actual = $dao->obtener($id);
    if ($actual === null) RespuestaJson::enviar(404, 'error', 'Equipo no encontrado.', null);
    if ($metodo === 'GET') RespuestaJson::enviar(200, 'success', 'Equipo obtenido.', $actual);
    if ($metodo !== 'PUT') RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    if (!(int) $actual['activo']) RespuestaJson::enviar(409, 'error', 'Equipo retirado.', null);
    $d = leerJson(); $condicion = opcionApi($d, 'condicion', ['operativo', 'reparacion']);
    $reloj = relojDominio(); $fecha = $reloj['fecha']; $hora = $reloj['hora']; $nota = textoApi($d, 'nota', 1000);
    $q = $conexion->prepare('SELECT id_prestamo FROM PRESTAMO WHERE id_equipo_en_uso = :id'); $q->execute(['id' => $id]);
    if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Devuelva el equipo antes de modificarlo o retirarlo.', null);
    $conexion->beginTransaction(); $antes = $actual; $antes['nota'] = $nota;
    $q = $conexion->prepare('INSERT INTO HISTORIAL_EQUIPO (id_equipo, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle)'); $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'detalle' => json_encode($antes)]);
    $q = $conexion->prepare('UPDATE EQUIPO SET condicion = :condicion WHERE id_equipo = :id'); $q->execute(['condicion' => $condicion, 'id' => $id]);
    $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Condición actualizada.', null);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
