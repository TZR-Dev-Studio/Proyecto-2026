<?php
require_once __DIR__ . '/../../app/controlador/ValidacionesDominio.php';
require_once __DIR__ . '/../../app/modelo/ReporteDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador']);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') RespuestaJson::enviar(405, 'error', 'Reporte de solo lectura.', null);
    $desde = fechaDominio($_GET, 'desde'); $hasta = fechaDominio($_GET, 'hasta');
    if ($hasta < $desde) RespuestaJson::enviar(422, 'error', 'Período inválido.', null);
    RespuestaJson::enviar(200, 'success', 'Reporte del período.', (new ReporteDAO($conexion))->obtener($desde, $hasta));
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
