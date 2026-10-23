<?php
require_once __DIR__ . '/ValidacionesDominio.php';
require_once __DIR__ . '/../modelo/AccesoDatosEquipo.php';
try {
    $conexion = conectarApi(); iniciarApi($conexion);
    $metodo = $_SERVER['REQUEST_METHOD'];
    permitirApi($metodo === 'GET' ? ['administrador', 'tecnico'] : ['administrador']);
    $dao = new AccesoDatosEquipo($conexion);
    if ($metodo === 'GET') {
        if (isset($_GET['id'])) {
            $fila = $dao->obtener(idApi($_GET['id']));
            if ($fila === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
            RespuestaJson::enviar(200, 'success', 'Registro obtenido.', $fila);
        }
        RespuestaJson::enviar(200, 'success', 'Registros obtenidos.', $dao->listar());
    }
    if (!in_array($metodo, ['POST', 'PUT', 'DELETE'])) RespuestaJson::enviar(405, 'error', 'Método no permitido.', null);
    $d = leerJson(); $id = 0;
    if ($metodo !== 'POST') {
        $id = idApi($_GET['id'] ?? $d['id_equipo'] ?? null);
        if ($dao->obtener($id) === null) RespuestaJson::enviar(404, 'error', 'Registro no encontrado.', null);
    }
    $activo = $metodo === 'DELETE' ? 0 : ($d['activo'] ?? 1);
    if (!in_array($activo, [0, 1], true)) RespuestaJson::enviar(400, 'error', 'Activo debe ser 0 o 1.', null);
    if ($metodo !== 'POST' && !$activo) {
        // No hay borrado físico: el equipo conserva su identificador.
    }
    $reloj = relojDominio(); $fechaEvento = $reloj['fecha']; $horaEvento = $reloj['hora'];
    if ($metodo !== 'POST') {
        $q = $conexion->prepare('SELECT id_prestamo FROM PRESTAMO WHERE id_equipo_en_uso = :id'); $q->execute(['id' => $id]);
        if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Devuelva el equipo antes de modificarlo o retirarlo.', null);
    }
    if ($metodo === 'DELETE') {
        $conexion->beginTransaction(); registrarHistorialEquipo($conexion, $id, $fechaEvento, $horaEvento, $dao->obtener($id));
        $dao->baja($id); $conexion->commit();
        RespuestaJson::enviar(200, 'success', 'Baja lógica registrada. Se conservan las referencias.', null);
    }
    $d['codigo_inventario'] = textoApi($d, 'codigo_inventario', 30);
    $d['descripcion'] = textoApi($d, 'descripcion', 200);
    $d['tipo'] = textoApi($d, 'tipo', 40);
    $d['marca'] = textoApi($d, 'marca', 80);
    $d['condicion'] = opcionApi($d, 'condicion', ['operativo', 'reparacion', 'baja']);
    $d['id_laboratorio'] = idApi($d['id_laboratorio'] ?? null);
    $q = $conexion->prepare('SELECT id_laboratorio FROM LABORATORIO WHERE id_laboratorio = :id AND activo = 1');
    $q->execute(['id' => $d['id_laboratorio']]);
    if ($q->fetch() === false) RespuestaJson::enviar(409, 'error', 'El laboratorio debe estar activo.', null);
    $q = $conexion->prepare('SELECT id_equipo FROM EQUIPO WHERE codigo_inventario = :codigo AND id_equipo <> :id');
    $q->execute(['codigo' => $d['codigo_inventario'], 'id' => $id]);
    if ($q->fetch() !== false) RespuestaJson::enviar(409, 'error', 'Código de inventario duplicado.', null);
    if ($d['condicion'] === 'baja') $activo = 0;
    $d['activo'] = $activo;
    if ($metodo === 'POST') $id = $dao->crear($d); else {
        $conexion->beginTransaction(); registrarHistorialEquipo($conexion, $id, $fechaEvento, $horaEvento, $dao->obtener($id));
        $dao->modificar($id, $d); $conexion->commit();
    }
    RespuestaJson::enviar($metodo === 'POST' ? 201 : 200, 'success', 'Registro guardado.', $dao->obtener($id));
} catch (Throwable $e) { errorApi($e, $conexion ?? null); }

function registrarHistorialEquipo(PDO $c, int $id, string $fecha, string $hora, array $antes): void {
    $q = $c->prepare('INSERT INTO HISTORIAL_EQUIPO (id_equipo, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle)');
    $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'detalle' => json_encode($antes)]);
}
