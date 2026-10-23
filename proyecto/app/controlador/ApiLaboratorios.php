<?php
require_once __DIR__ . '/ApiComun.php';
require_once __DIR__ . '/../modelo/AccesoDatosLaboratorio.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion);
    $metodo = $_SERVER['REQUEST_METHOD'];
    permitirApi($metodo === 'GET' ? ['administrador', 'tecnico'] : ['administrador']);
    $dao = new AccesoDatosLaboratorio($conexion);
    if ($metodo === 'GET') {
        if (isset($_GET['id'])) {
            $fila = $dao->obtener(idApi($_GET['id']));
            if ($fila === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
            RespuestaJson::enviar(200, 'success', 'Registro obtenido.', $fila);
        }
        RespuestaJson::enviar(200, 'success', 'Registros obtenidos.', $dao->listar());
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = leerJson(); $id = 0;
    if ($metodo !== 'POST') {
        $id = idApi($_GET['id'] ?? $d['id_laboratorio'] ?? null);
        if ($dao->obtener($id) === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    }
    $activo = $metodo === 'DELETE' ? 0 : ($d['activo'] ?? 1);
    if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
    if ($metodo !== 'POST' && !$activo) {
        $q = $conexion->prepare('SELECT id_equipo FROM EQUIPO WHERE id_laboratorio = :id AND activo = 1');
        $q->execute(['id' => $id]);
        if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Traslade o dé de baja los equipos activos primero.', null);
    }
    if ($metodo === 'DELETE') {
        $dao->baja($id);
        RespuestaJson::enviar(200, 'success', 'Baja lógica registrada. Se conservan las referencias.', null);
    }
    $d['nombre'] = textoApi($d, 'nombre', 80);
    $d['ubicacion'] = textoApi($d, 'ubicacion', 120);
    $q = $conexion->prepare('SELECT id_laboratorio FROM LABORATORIO WHERE nombre = :nombre AND id_laboratorio <> :id');
    $q->execute(['nombre' => $d['nombre'], 'id' => $id]);
    if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Laboratorio duplicado.', null);
    $d['activo'] = $activo;
    if ($metodo === 'POST') $id = $dao->crear($d); else $dao->modificar($id, $d);
    RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Registro guardado.', $dao->obtener($id));
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }
