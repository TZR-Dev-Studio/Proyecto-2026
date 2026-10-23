<?php
require_once __DIR__ . '/ApiComun.php';
require_once __DIR__ . '/../modelo/AccesoDatosMetricas.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador']);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') RespuestaJson::enviar(405, 'error', 'Los indicadores son de solo lectura.', null);
    RespuestaJson::enviar(200, 'success', 'Indicadores de la base actual.', (new AccesoDatosMetricas($conexion))->obtener());
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }
