<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/TareaDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    $dao = new TareaDAO($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? idApi($_GET['id']) : 0; $actual = $id ? $dao->obtener($id) : null;
    if ($id && $actual === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    if ($metodo === 'GET') {
        if ($id && isset($_GET['historial'])) RespuestaJson::enviar(200, 'success', 'Historial conservado.', $dao->historial($id));
        RespuestaJson::enviar(200, 'success', 'Registros obtenidos.', $actual ?? $dao->listar());
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $entrada = leerJson(); $reloj = relojDominio(); $fecha = $reloj['fecha']; $hora = $reloj['hora'];
    if ($metodo !== 'POST' && $actual === null) RespuestaJson::enviar(400, 'error', 'Falta identificador.', null);
    if ($metodo === 'DELETE') {
        permitirApi(['administrador']); $motivo = textoApi($entrada, 'motivo', 500);
        $conexion->beginTransaction(); $antes = $actual; $antes['motivo'] = $motivo; $dao->registrarHistorial($id, $fecha, $hora, $antes);
        $d = $actual; unset($d['id_tarea']); $d['activo'] = 0; $dao->modificar($id, $d); $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Baja lógica registrada.', null);
    }
    if ($actual !== null && !(int) $actual['activo']) RespuestaJson::enviar(409, 'error', 'Registro dado de baja.', null);
    $d = [];
    $d['descripcion'] = textoApi($entrada, 'descripcion', 255);
    $d['tipo'] = opcionApi($entrada, 'tipo', ['preventiva', 'reactiva']);
    $d['prioridad'] = opcionApi($entrada, 'prioridad', ['alta', 'media', 'baja']);
    $d['fecha_inicio'] = fechaDominio($entrada, 'fecha_inicio'); $d['hora_inicio'] = horaDominio($entrada, 'hora_inicio');
    $d['fecha_limite'] = fechaDominio($entrada, 'fecha_limite'); $d['hora_limite'] = horaDominio($entrada, 'hora_limite');
    if ($d['fecha_limite'] . ' ' . $d['hora_limite'] < $d['fecha_inicio'] . ' ' . $d['hora_inicio']) RespuestaJson::enviar(422, 'error', 'Límite anterior al inicio.', null);
    $d['id_tecnico'] = idApi($entrada['id_tecnico'] ?? null); referenciaActiva($conexion, 'usuario', $d['id_tecnico']);
    $q = $conexion->prepare('SELECT r.nombre_rol FROM USUARIO_ROL ur INNER JOIN ROL r ON r.id_rol = ur.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1'); $q->execute(['id' => $d['id_tecnico']]); $valido = false;
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $rol) if (in_array($rol['nombre_rol'], ['tecnico', 'administrador'])) $valido = true;
    if (!$valido) RespuestaJson::enviar(422, 'error', 'Responsable sin rol técnico.', null);
    $d['id_equipo'] = empty($entrada['id_equipo']) ? null : idApi($entrada['id_equipo']);
    if ($d['id_equipo'] !== null && ($actual === null || (int) $actual['id_equipo'] !== $d['id_equipo'])) referenciaActiva($conexion, 'equipo', $d['id_equipo']);
    $d['estado'] = opcionApi($entrada, 'estado', ['pendiente', 'en proceso', 'resuelto']);
    $d['nota'] = textoApi($entrada, 'nota', 1000, false);
    if ($d['estado'] === 'resuelto' && $d['nota'] === '') RespuestaJson::enviar(422, 'error', 'El cierre requiere una nota.', null);

    $d['activo'] = 1;
    $conexion->beginTransaction();
    if ($metodo === 'POST') $dao->crear($d); else { $dao->registrarHistorial($id, $fecha, $hora, $actual); $dao->modificar($id, $d); }
    $conexion->commit(); RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Registro guardado.', null);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
