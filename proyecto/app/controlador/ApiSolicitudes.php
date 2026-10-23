<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/SolicitudDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico', 'solicitante']);
    $dao = new SolicitudDAO($conexion); $metodo = $_SERVER['REQUEST_METHOD']; $actual = null; $id = 0;
    if (isset($_GET['id'])) {
        $id = idApi($_GET['id']); $actual = $dao->obtener($id);
        if ($actual === null || (!gestorDominio() && (int) $actual['id_usuario'] !== (int) $_SESSION['id_usuario'])) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    }
    if ($metodo === 'GET') {
        if ($actual !== null && isset($_GET['historial'])) RespuestaJson::enviar(200, 'success', 'Historial conservado.', $dao->historial($id));
        RespuestaJson::enviar(200, 'success', 'Registros obtenidos.', $actual ?? $dao->listar($_SESSION['usuario'], gestorDominio()));
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    if ($metodo !== 'POST') {
        permitirApi(['administrador', 'tecnico']);
        if ($actual === null) RespuestaJson::enviar(400, 'error', 'Falta identificador.', null);
    }
    $entrada = leerJson();
    $reloj = relojDominio(); $fechaEvento = $reloj['fecha']; $horaEvento = $reloj['hora'];
    if ($metodo === 'DELETE') {
        permitirApi(['administrador']);
        $motivo = textoApi($entrada, 'motivo', 500);
        $conexion->beginTransaction();
        $actual['motivo_baja'] = $motivo;
        $dao->registrarHistorial($id, $fechaEvento, $horaEvento, 'baja', $actual);
        unset($actual['motivo_baja']);
        $d = $actual; unset($d['id_solicitud']); $d['activo'] = 0; $dao->modificar($id, $d);
        $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Baja lógica registrada.', null);
    }
    if ($actual !== null && !(int) $actual['activo']) RespuestaJson::enviar(409, 'error', 'Registro dado de baja.', null);
    $d = [];
    $d['descripcion'] = textoApi($entrada, 'descripcion', 255);
    $d['estado'] = $metodo === 'POST' ? 'pendiente' : opcionApi($entrada, 'estado', ['pendiente', 'en proceso', 'resuelto']);
    $d['id_usuario'] = $metodo === 'POST' ? (int) $_SESSION['id_usuario'] : (int) $actual['id_usuario'];
    $d['activo'] = $metodo === 'POST' ? 1 : (int) $actual['activo'];
    $d['id_tecnico'] = $metodo === 'POST' ? null : idApi($entrada['id_tecnico'] ?? null);
    if ($d['id_tecnico'] !== null) {
        referenciaActiva($conexion, 'usuario', $d['id_tecnico']);
        $q = $conexion->prepare('SELECT r.nombre_rol FROM USUARIO_ROL ur INNER JOIN ROL r ON r.id_rol = ur.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1');
        $q->execute(['id' => $d['id_tecnico']]); $esTecnico = false;
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) if (in_array($fila['nombre_rol'], ['administrador', 'tecnico'])) $esTecnico = true;
        if (!$esTecnico) RespuestaJson::enviar(422, 'error', 'Responsable sin rol técnico.', null);
    }
    $d['nota_resolucion'] = textoApi($entrada, 'nota_resolucion', 1000, false);
    if ($d['estado'] === 'resuelto' && $d['nota_resolucion'] === '') RespuestaJson::enviar(422, 'error', 'La resolución requiere una nota.', null);
    if ($actual !== null && $actual['estado'] === 'resuelto' && $d['estado'] !== 'resuelto' && !($_SESSION['administrador'] ?? false)) RespuestaJson::enviar(403, 'error', 'Solo el administrador puede reabrir.', null);
    $d['tipo'] = opcionApi($entrada, 'tipo', ['instalacion', 'laboratorio', 'configuracion', 'otro']);
    $d['urgencia'] = opcionApi($entrada, 'urgencia', ['alta', 'media', 'baja']);
    $d['fecha_solicitada'] = fechaDominio($entrada, 'fecha_solicitada');
    $d['hora_solicitada'] = horaDominio($entrada, 'hora_solicitada');
    $d['id_laboratorio'] = idApi($entrada['id_laboratorio'] ?? null);
    if ($actual === null || (int) $actual['id_laboratorio'] !== $d['id_laboratorio']) referenciaActiva($conexion, 'laboratorio', $d['id_laboratorio']);
    $q = $conexion->prepare('SELECT nombre FROM LABORATORIO WHERE id_laboratorio = :id'); $q->execute(['id' => $d['id_laboratorio']]);
    $d['laboratorio'] = $q->fetch(PDO::FETCH_ASSOC)['nombre'];
    if (strlen($d['laboratorio']) > 50) RespuestaJson::enviar(422, 'error', 'Nombre de laboratorio supera la columna heredada; use hasta 50 caracteres.', null);

    $conexion->beginTransaction();
    if ($metodo === 'POST') $dao->crear($d);
    else { $dao->registrarHistorial($id, $fechaEvento, $horaEvento, 'modificacion', $actual); $dao->modificar($id, $d); }
    $conexion->commit();
    RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Registro guardado.', $metodo === 'POST' ? null : $dao->obtener($id));
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
