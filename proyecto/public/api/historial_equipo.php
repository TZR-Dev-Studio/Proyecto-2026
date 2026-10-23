<?php
require_once __DIR__ . '/../../app/controlador/ApiComun.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') RespuestaJson::enviar(405, 'error', 'El historial es de solo lectura.', null);
    $id = idApi($_GET['id'] ?? null); $datos = [];
    $q = $conexion->prepare('SELECT * FROM EQUIPO WHERE id_equipo = :id'); $q->execute(['id' => $id]); $equipo = $q->fetch(PDO::FETCH_ASSOC);
    if ($equipo === false) RespuestaJson::enviar(404, 'error', 'Equipo no encontrado.', null);
    $datos['equipo'] = $equipo;
    $q = $conexion->prepare('SELECT p.*, u.nombre_completo AS responsable FROM PRESTAMO p INNER JOIN USUARIO u ON u.id_usuario = p.id_responsable WHERE p.id_equipo = :id ORDER BY p.id_prestamo'); $q->execute(['id' => $id]); $datos['prestamos'] = $q->fetchAll(PDO::FETCH_ASSOC);
    $q = $conexion->prepare('SELECT * FROM TICKET WHERE id_equipo = :id ORDER BY id_ticket'); $q->execute(['id' => $id]); $datos['tickets'] = $q->fetchAll(PDO::FETCH_ASSOC);
    $q = $conexion->prepare('SELECT d.*, u.nombre_completo AS autor FROM DIAGNOSTICO d INNER JOIN TICKET t ON t.id_ticket = d.id_ticket INNER JOIN USUARIO u ON u.id_usuario = d.id_actor WHERE t.id_equipo = :id ORDER BY d.id_diagnostico'); $q->execute(['id' => $id]); $datos['diagnosticos'] = $q->fetchAll(PDO::FETCH_ASSOC);
    $q = $conexion->prepare('SELECT * FROM HISTORIAL_EQUIPO WHERE id_equipo = :id ORDER BY id_historial'); $q->execute(['id' => $id]); $datos['cambios'] = $q->fetchAll(PDO::FETCH_ASSOC);
    $q = $conexion->prepare('SELECT h.* FROM HISTORIAL_TICKET h INNER JOIN TICKET t ON t.id_ticket = h.id_ticket WHERE t.id_equipo = :id ORDER BY h.id_historial'); $q->execute(['id' => $id]); $datos['cambios_tickets'] = $q->fetchAll(PDO::FETCH_ASSOC);
    RespuestaJson::enviar(200, 'success', 'Historial del equipo.', $datos);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
