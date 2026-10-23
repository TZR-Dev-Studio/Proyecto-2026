<?php
require_once __DIR__ . '/ApiComun.php';
require_once __DIR__ . '/../modelo/AccesoDatosUsuarioRol.php';
require_once __DIR__ . '/../modelo/AccesoDatosUsuario.php';
require_once __DIR__ . '/../modelo/AccesoDatosRol.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador']);
    $dao = new AccesoDatosUsuarioRol($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo === 'GET' && !isset($_GET['id_usuario']) && !isset($_GET['id_rol'])) RespuestaJson::enviar(200, 'success', 'Asociaciones obtenidas.', $dao->listar());
    if (!in_array($metodo, ['GET', 'POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = $metodo === 'GET' ? $_GET : leerJson();
    $usuario = idApi($d['id_usuario'] ?? null); $rol = idApi($d['id_rol'] ?? null);
    $actual = $dao->obtener($usuario, $rol);
    if ($metodo === 'GET') {
        if ($actual === null) RespuestaJson::enviar(404, 'error', 'Asociación no encontrada.', null);
        RespuestaJson::enviar(200, 'success', 'Asociación obtenida.', $actual);
    }
    $u = (new AccesoDatosUsuario($conexion))->obtenerUsuario($usuario);
    $r = (new AccesoDatosRol($conexion))->obtener($rol);
    if ($u === null || $r === null) RespuestaJson::enviar(404, 'error', 'Usuario o rol no encontrado.', null);
    $activo = $metodo === 'DELETE' ? 0 : $d['activo'] ?? 1;
    if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
    if ($usuario === (int) $_SESSION['id_usuario'] && $r['nombre_rol'] === 'administrador' && !$activo) RespuestaJson::enviar(409, 'error', 'Conserve su propio rol administrador.', null);
    if ($activo && (!$u['activo'] || !$r['activo'])) RespuestaJson::enviar(409, 'error', 'Usuario y rol deben estar activos.', null);
    if ($metodo === 'POST') {
        if ($actual !== null) RespuestaJson::enviar(409, 'error', 'La asociación existe; use PUT para reactivarla.', null);
        $dao->crear($usuario, $rol);
    } else {
        if ($actual === null) RespuestaJson::enviar(404, 'error', 'Asociación no encontrada.', null);
        if (!$activo && $u['activo']) {
            $roles = (new AccesoDatosUsuario($conexion))->rolesDeUsuario($usuario);
            if ($actual['activo'] && count($roles) <= 1) RespuestaJson::enviar(409, 'error', 'Dé de baja la cuenta o asigne otro rol antes de revocar el último.', null);
        }
        $dao->modificar($usuario, $rol, $activo);
    }
    RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Asociación guardada. No se borra su registro.', $dao->obtener($usuario, $rol));
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }
