<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/UsoDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    $dao = new UsoDAO($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
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
        $d = $actual; unset($d['id_uso']); $d['activo'] = 0; $dao->modificar($id, $d); $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Baja lógica registrada.', null);
    }
    if ($actual !== null && !(int) $actual['activo']) RespuestaJson::enviar(409, 'error', 'Registro dado de baja.', null);
    $d = [];
    $d['id_laboratorio'] = idApi($entrada['id_laboratorio'] ?? null); referenciaActiva($conexion, 'laboratorio', $d['id_laboratorio']);
    $d['id_responsable'] = idApi($entrada['id_responsable'] ?? null); referenciaActiva($conexion, 'usuario', $d['id_responsable']);
    $d['grupo'] = textoApi($entrada, 'grupo', 50); $d['actividad'] = textoApi($entrada, 'actividad', 255);
    $d['fecha'] = fechaDominio($entrada, 'fecha'); $d['hora_inicio'] = horaDominio($entrada, 'hora_inicio'); $d['hora_fin'] = horaDominio($entrada, 'hora_fin');
    if ($d['hora_fin'] <= $d['hora_inicio']) RespuestaJson::enviar(422, 'error', 'Fin debe ser posterior al inicio.', null);

    $d['activo'] = 1;
    $conexion->beginTransaction();
    if ($metodo === 'POST') $dao->crear($d); else { $dao->registrarHistorial($id, $fecha, $hora, $actual); $dao->modificar($id, $d); }
    $conexion->commit(); RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Registro guardado.', null);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
