<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/DiagnosticoDAO.php';
require_once __DIR__ . '/../modelo/TicketDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico']);
    $dao = new DiagnosticoDAO($conexion); $tickets = new TicketDAO($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? idApi($_GET['id']) : 0; $actual = $id ? $dao->obtener($id) : null;
    if ($id && $actual === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    if ($metodo === 'GET') RespuestaJson::enviar(200, 'success', 'Intervenciones registradas.', $actual ?? $dao->listar());
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = leerJson(); $reloj = relojDominio(); $fecha = $reloj['fecha']; $hora = $reloj['hora'];
    if ($metodo !== 'POST' && $actual === null) RespuestaJson::enviar(400, 'error', 'Falta identificador.', null);
    $ticketId = $actual !== null ? (int) $actual['id_ticket'] : idApi($d['id_ticket'] ?? null);
    $ticket = $tickets->obtener($ticketId);
    if ($ticket === null || !(int) $ticket['activo']) RespuestaJson::enviar(409, 'error', 'Ticket no disponible.', null);
    if ($actual !== null && !($_SESSION['administrador'] ?? false) && (int) $actual['id_actor'] !== (int) $_SESSION['id_usuario']) RespuestaJson::enviar(403, 'error', 'Solo el autor o administrador puede corregir.', null);
    if ($metodo === 'DELETE') permitirApi(['administrador']);
    $nota = $metodo === 'DELETE' ? textoApi($d, 'motivo', 1000) : textoApi($d, 'nota', 1000);
    $conexion->beginTransaction();
    if ($metodo === 'POST') $dao->crear(['id_ticket' => $ticketId, 'id_actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'nota' => $nota]);
    else {
        $antes = $actual; $antes['motivo'] = $metodo === 'DELETE' ? $nota : 'correccion';
        $tickets->registrarHistorial($ticketId, $fecha, $hora, 'diagnostico', $antes);
        if ($metodo === 'DELETE') $dao->baja($id); else $dao->modificar($id, $nota);
    }
    $conexion->commit(); RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Intervención guardada.', null);
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
