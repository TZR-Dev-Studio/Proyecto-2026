<?php
require_once __DIR__ . '/ApiComun.php';
require_once __DIR__ . '/../modelo/AccesoDatosUsuario.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador']);
    $dao = new AccesoDatosUsuario($conexion);
    $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo === 'GET') {
        if (isset($_GET['id'])) {
            $fila = $dao->obtenerUsuario(idApi($_GET['id']));
            if ($fila === null) RespuestaJson::enviar(404, 'error', 'Usuario no encontrado.', null);
            RespuestaJson::enviar(200, 'success', 'Usuario obtenido.', $fila);
        }
        RespuestaJson::enviar(200, 'success', 'Usuarios obtenidos.', $dao->listarUsuarios());
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = leerJson();
    $id = 0; $actual = null;
    if ($metodo !== 'POST') {
        $id = idApi($_GET['id'] ?? $d['id_usuario'] ?? null);
        $actual = $dao->obtenerUsuario($id);
        if ($actual === null) RespuestaJson::enviar(404, 'error', 'Usuario no encontrado.', null);
    }
    if ($metodo === 'DELETE') {
        if ($id === (int) $_SESSION['id_usuario']) RespuestaJson::enviar(409, 'error', 'No puede dar de baja su propia cuenta.', null);
        $dao->bajaUsuario($id);
        RespuestaJson::enviar(200, 'success', 'Usuario dado de baja; sus registros se conservan.', null);
    }
    $nombre = textoApi($d, 'nombreCompleto', 100);
    $usuario = $metodo === 'POST' ? textoApi($d, 'nombreUsuario', 50) : $actual['nombre_usuario'];
    if (strlen($usuario) < 3) RespuestaJson::enviar(400, 'error', 'Usuario demasiado corto.', null);
    $email = textoApi($d, 'email', 100);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) RespuestaJson::enviar(400, 'error', 'Email inválido.', null);
    $roles = $d['roles'] ?? [];
    if (!is_array($roles) || count($roles) === 0) RespuestaJson::enviar(400, 'error', 'Seleccione al menos un rol.', null);
    foreach ($roles as $rol) {
        if (!is_string($rol) || !in_array($rol, ['administrador', 'tecnico', 'solicitante'])) RespuestaJson::enviar(400, 'error', 'Rol no admitido.', null);
    }
    $vistos = [];
    foreach ($roles as $rol) {
        if (in_array($rol, $vistos)) RespuestaJson::enviar(400, 'error', 'Roles repetidos.', null);
        $vistos[] = $rol;
    }
    // La contraseña no se recorta: sus espacios son parte del valor.
    $clave = $d['password'] ?? '';
    if (!is_string($clave) || strlen($clave) > 72 || (($metodo === 'POST' || $clave !== '') && strlen($clave) < 6)) RespuestaJson::enviar(400, 'error', 'Contraseña entre 6 y 72 bytes.', null);
    $activo = $metodo === 'POST' ? 1 : $d['activo'] ?? 1;
    if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
    if ($id === (int) $_SESSION['id_usuario'] && (!$activo || !in_array('administrador', $roles))) RespuestaJson::enviar(409, 'error', 'Conserve su propia cuenta administradora activa.', null);
    if ($dao->existeUsuario($usuario, $email, $id)) RespuestaJson::enviar(409, 'error', 'Usuario o email duplicado.', null);
    $hash = $clave === '' ? '' : password_hash($clave, PASSWORD_DEFAULT);
    if ($metodo === 'POST') {
        $id = $dao->registrarUsuarioConRoles($nombre, $usuario, $hash, $email, $roles);
        if ($id === 0) RespuestaJson::enviar(409, 'error', 'Algún rol no existe o está inactivo.', null);
    } elseif (!$dao->modificarUsuario($id, $nombre, $email, $hash, $roles, $activo)) {
        RespuestaJson::enviar(409, 'error', 'Algún rol no existe o está inactivo.', null);
    }
    RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Usuario guardado.', $dao->obtenerUsuario($id));
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }
