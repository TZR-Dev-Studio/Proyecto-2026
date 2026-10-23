<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/PrestamoDAO.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion); permitirApi(['administrador', 'tecnico', 'solicitante']);
    $dao = new PrestamoDAO($conexion); $metodo = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? idApi($_GET['id']) : 0; $actual = $id ? $dao->obtener($id) : null;
    if ($id && ($actual === null || (!gestorDominio() && (int) $actual['id_responsable'] !== (int) $_SESSION['id_usuario']))) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    if ($metodo === 'GET') RespuestaJson::enviar(200, 'success', 'Préstamos registrados.', $actual ?? $dao->listar(gestorDominio()));
    permitirApi(['administrador', 'tecnico']);
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $entrada = leerJson(); $d = [];
    if ($metodo === 'POST') {
        $d['id_equipo'] = idApi($entrada['id_equipo'] ?? null); $d['id_responsable'] = idApi($entrada['id_responsable'] ?? null);
        referenciaActiva($conexion, 'equipo', $d['id_equipo']); referenciaActiva($conexion, 'usuario', $d['id_responsable']);
        $q = $conexion->prepare('SELECT condicion FROM EQUIPO WHERE id_equipo = :id'); $q->execute(['id' => $d['id_equipo']]);
        if ($q->fetch(PDO::FETCH_ASSOC)['condicion'] !== 'operativo') RespuestaJson::enviar(409, 'error', 'Equipo no operativo.', null);
        $q = $conexion->prepare('SELECT id_prestamo FROM PRESTAMO WHERE id_equipo_en_uso = :id'); $q->execute(['id' => $d['id_equipo']]);
        if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Equipo ya prestado.', null);
        $d['documento'] = textoApi($entrada, 'documento', 30); $d['grupo'] = textoApi($entrada, 'grupo', 50);
        $reloj = relojDominio(); $d['fecha_inicio'] = $reloj['fecha']; $d['hora_inicio'] = $reloj['hora'];
        $d['fecha_limite'] = fechaDominio($entrada, 'fecha_limite'); $d['hora_limite'] = horaDominio($entrada, 'hora_limite');
        if ($d['fecha_limite'] . ' ' . $d['hora_limite'] < $d['fecha_inicio'] . ' ' . $d['hora_inicio']) RespuestaJson::enviar(422, 'error', 'Límite anterior al préstamo.', null);
        $d['nota'] = textoApi($entrada, 'nota', 1000, false); $d['id_actor'] = $_SESSION['id_usuario'];
        $conexion->beginTransaction(); $dao->crear($d);
        // Revalidar despues del INSERT y dentro de la misma transaccion.
        // FK y UNIQUE preservan las referencias y una sola ocupacion por equipo.
        $q = $conexion->prepare('SELECT e.activo, e.condicion, u.activo AS responsable_activo FROM EQUIPO e INNER JOIN USUARIO u ON u.id_usuario = :usuario WHERE e.id_equipo = :equipo');
        $q->execute(['usuario' => $d['id_responsable'], 'equipo' => $d['id_equipo']]); $vigente = $q->fetch(PDO::FETCH_ASSOC);
        if ($vigente === false || !(int) $vigente['activo'] || !(int) $vigente['responsable_activo'] || $vigente['condicion'] !== 'operativo') {
            $conexion->rollBack(); RespuestaJson::enviar(409, 'error', 'Equipo o responsable no disponible.', null);
        }
        $conexion->commit();
        RespuestaJson::enviar(201, 'success', 'Préstamo registrado.', null);
    }
    if ($actual === null || $actual['estado'] !== 'abierto') RespuestaJson::enviar(409, 'error', 'El préstamo debe estar abierto.', null);
    $reloj = relojDominio(); $fecha = $reloj['fecha']; $hora = $reloj['hora'];
    if ($fecha . ' ' . $hora < $actual['fecha_inicio'] . ' ' . $actual['hora_inicio']) RespuestaJson::enviar(422, 'error', 'Fecha anterior al préstamo.', null);
    if ($metodo === 'DELETE') {
        permitirApi(['administrador']); $motivo = textoApi($entrada, 'motivo', 900);
        $conexion->beginTransaction();
        $q = $conexion->prepare('INSERT INTO DEVOLUCION (id_prestamo, fecha, hora, tipo, condicion, id_actor, nota) VALUES (:id, :fecha, :hora, :tipo, NULL, :actor, :nota)');
        $q->execute(['id' => $id, 'fecha' => $fecha, 'hora' => $hora, 'tipo' => 'anulacion', 'actor' => $_SESSION['id_usuario'], 'nota' => $motivo]);
        $dao->anular($id, $fecha . ' ' . $hora . ' - usuario ' . $_SESSION['id_usuario'] . ': ' . $motivo);
        $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Préstamo anulado; registro conservado.', null);
    }
    $condicion = opcionApi($entrada, 'condicion', ['operativo', 'reparacion', 'baja']);
    $notaDevolucion = textoApi($entrada, 'nota', 1000, false);
    $conexion->beginTransaction();
    $q = $conexion->prepare('INSERT INTO DEVOLUCION (id_prestamo, fecha, hora, tipo, condicion, id_actor, nota) VALUES (:id, :fecha, :hora, :tipo, :condicion, :actor, :nota)');
    $q->execute(['id' => $id, 'fecha' => $fecha, 'hora' => $hora, 'tipo' => 'devolucion', 'condicion' => $condicion, 'actor' => $_SESSION['id_usuario'], 'nota' => $notaDevolucion]);
    $dao->devolver($id, ['fecha' => $fecha, 'hora' => $hora, 'condicion' => $condicion, 'actor' => $_SESSION['id_usuario']]);
    $q = $conexion->prepare('UPDATE EQUIPO SET condicion = :condicion, activo = :activo WHERE id_equipo = :id');
    $q->execute(['condicion' => $condicion, 'activo' => $condicion === 'baja' ? 0 : 1, 'id' => $actual['id_equipo']]);
    $conexion->commit(); RespuestaJson::enviar(200, 'success', 'Devolución registrada.', $dao->obtener($id));
} catch (Throwable $error) { errorApi($error, $conexion ?? null); }
