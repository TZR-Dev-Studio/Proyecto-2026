<?php
require_once __DIR__ . '/../../app/controlador/ValidacionesDominio.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico', 'solicitante']);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') RespuestaJson::enviar(405, 'error', 'Catálogo de solo lectura.', null);
    $equipos = $conexion->query('SELECT id_equipo, codigo_inventario, descripcion FROM EQUIPO WHERE activo = 1 ORDER BY codigo_inventario')->fetchAll(PDO::FETCH_ASSOC);
    $laboratorios = $conexion->query('SELECT id_laboratorio, nombre FROM LABORATORIO WHERE activo = 1 ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    $usuarios = []; $tecnicos = [];
    if (($_SESSION['administrador'] ?? false) || ($_SESSION['tecnico'] ?? false)) {
        $usuarios = $conexion->query('SELECT id_usuario, nombre_completo FROM USUARIO WHERE activo = 1 ORDER BY nombre_completo')->fetchAll(PDO::FETCH_ASSOC);
        $q = $conexion->prepare('SELECT u.id_usuario, u.nombre_completo, r.nombre_rol FROM USUARIO u INNER JOIN USUARIO_ROL ur ON ur.id_usuario = u.id_usuario INNER JOIN ROL r ON r.id_rol = ur.id_rol WHERE u.activo = 1 AND ur.activo = 1 AND r.activo = 1'); $q->execute();
        $vistos = [];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (in_array($fila['nombre_rol'], ['administrador', 'tecnico']) && !in_array($fila['id_usuario'], $vistos)) { $tecnicos[] = $fila; $vistos[] = $fila['id_usuario']; }
        }
    }
    RespuestaJson::enviar(200, 'success', 'Catálogos activos.', ['reloj' => relojDominio(), 'equipos' => $equipos, 'laboratorios' => $laboratorios, 'usuarios' => $usuarios, 'tecnicos' => $tecnicos]);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
