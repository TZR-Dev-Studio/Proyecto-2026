<?php
require_once __DIR__ . '/../../app/controlador/ValidacionesDominio.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') RespuestaJson::enviar(405, 'error', 'Alertas de solo lectura.', null);
    $reloj = relojDominio(); $fecha = isset($_GET['fecha']) ? fechaDominio($_GET, 'fecha') : $reloj['fecha']; $hora = isset($_GET['hora']) ? horaDominio($_GET, 'hora') : $reloj['hora']; $datos = ['referencia' => ['fecha' => $fecha, 'hora' => $hora]];
    $q = $conexion->prepare("SELECT id_ticket, descripcion, fecha_limite, estado, espera_insumo FROM TICKET WHERE activo = 1 AND estado <> 'resuelto'"); $q->execute(); $datos['tickets'] = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $t) if (($t['fecha_limite'] !== null && $t['fecha_limite'] < $fecha) || (int) $t['espera_insumo']) $datos['tickets'][] = $t;
    $q = $conexion->prepare("SELECT id_prestamo, id_equipo, id_responsable, fecha_limite, hora_limite FROM PRESTAMO WHERE estado = 'abierto'"); $q->execute(); $datos['prestamos'] = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $p) if ($p['fecha_limite'] . ' ' . $p['hora_limite'] < $fecha . ' ' . $hora) $datos['prestamos'][] = $p;
    $q = $conexion->prepare("SELECT id_tarea, descripcion, fecha_limite, hora_limite FROM TAREA WHERE activo = 1 AND estado <> 'resuelto'"); $q->execute(); $datos['tareas'] = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $t) if ($t['fecha_limite'] . ' ' . $t['hora_limite'] < $fecha . ' ' . $hora) $datos['tareas'][] = $t;
    RespuestaJson::enviar(200, 'success', 'Alertas para la fecha indicada.', $datos);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
