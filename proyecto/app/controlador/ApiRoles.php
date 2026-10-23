<?php
require_once __DIR__ . '/ApiComun.php';
require_once __DIR__ . '/../modelo/AccesoDatosRol.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador']);
    $dao = new AccesoDatosRol($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo === 'GET') {
        if (isset($_GET['id'])) {
            $fila = $dao->obtener(idApi($_GET['id']));
            if ($fila === null) RespuestaJson::enviar(404, 'error', 'Rol no encontrado.', null);
            RespuestaJson::enviar(200, 'success', 'Rol obtenido.', $fila);
        }
        RespuestaJson::enviar(200, 'success', 'Roles obtenidos.', $dao->listar());
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = leerJson();
    if ($metodo === 'POST') {
        $nombre = opcionApi($d, 'nombre_rol', ['administrador', 'tecnico', 'solicitante']);
        if ($dao->buscar($nombre) !== null) RespuestaJson::enviar(409, 'error', 'El rol ya existe; puede reactivarlo.', null);
        $activo = $d['activo'] ?? 1;
        if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
        $id = $dao->crear($nombre, $activo);
        RespuestaJson::enviar(201, 'success', 'Rol creado.', $dao->obtener($id));
    }
    $id = idApi($_GET['id'] ?? $d['id_rol'] ?? null);
    $actual = $dao->obtener($id);
    if ($actual === null) RespuestaJson::enviar(404, 'error', 'Rol no encontrado.', null);
    // Los nombres definen permisos en el código; no se renombra un rol.
    if (isset($d['nombre_rol']) && $d['nombre_rol'] !== $actual['nombre_rol']) RespuestaJson::enviar(409, 'error', 'Los identificadores de rol no se renombran.', null);
    $activo = $metodo === 'DELETE' ? 0 : $d['activo'] ?? 1;
    if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
    if (!$activo && $dao->enUso($id)) RespuestaJson::enviar(409, 'error', 'Revoque las asociaciones activas antes de dar de baja el rol.', null);
    $dao->cambiarActivo($id, $activo);
    RespuestaJson::enviar(200, 'success', 'Rol actualizado sin borrar sus asociaciones.', $dao->obtener($id));
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }
