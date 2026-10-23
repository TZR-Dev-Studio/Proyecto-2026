<?php
require_once __DIR__ . '/../vista/RespuestaJson.php';
require_once __DIR__ . '/../modelo/ConectorPDO.php';
require_once __DIR__ . '/../nucleo/Token.php';

function conectarApi(): PDO {
    $env = require __DIR__ . '/../../config/config.php';
    $conector = new ConectorPDO($env['DB_HOST'] ?? '', $env['DB_USER'] ?? '', $env['DB_PASS'] ?? '', $env['DB_NAME'] ?? '');
    return $conector->establecerConexion();
}

function iniciarApi(PDO $conexion): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['id_usuario'])) {
        RespuestaJson::enviar(401, 'error', 'Inicie sesión nuevamente.', null);
    }
    // Se consulta la base en cada petición: una baja o revocación no queda
    // escondida por los permisos guardados anteriormente en la sesión.
    $q = $conexion->prepare('SELECT id_usuario, nombre_usuario, activo FROM USUARIO WHERE id_usuario = :id');
    $q->execute(['id' => $_SESSION['id_usuario']]);
    $usuario = $q->fetch(PDO::FETCH_ASSOC);
    if ($usuario === false || !$usuario['activo']) {
        $_SESSION = [];
        session_destroy();
        RespuestaJson::enviar(401, 'error', 'Cuenta inactiva.', null);
    }
    $_SESSION['usuario'] = $usuario['nombre_usuario'];
    $q = $conexion->prepare('SELECT r.nombre_rol FROM ROL r INNER JOIN USUARIO_ROL ur ON ur.id_rol = r.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1');
    $q->execute(['id' => $_SESSION['id_usuario']]);
    $roles = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $rol) {
        $roles[] = $rol['nombre_rol'];
    }
    $_SESSION['roles'] = $roles;
    if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH', 'DELETE'])) Token::verificar();
    foreach (['administrador', 'tecnico', 'solicitante'] as $nombre) {
        $_SESSION[$nombre] = in_array($nombre, $roles);
    }
}

function permitirApi(array $roles): void {
    foreach ($roles as $rol) {
        if (in_array($rol, $_SESSION['roles'])) return;
    }
    RespuestaJson::enviar(403, 'error', 'No tiene permiso para esta operación.', null);
}

function leerJson(): array {
    $entrada = trim(file_get_contents('php://input'));
    $datos = json_decode($entrada, true);
    if ($entrada === '' || $entrada[0] !== '{' || !is_array($datos)) RespuestaJson::enviar(400, 'error', 'JSON inválido.', null);
    return $datos;
}

function textoApi(array $datos, string $campo, int $maximo, bool $obligatorio = true): string {
    if (isset($datos[$campo]) && !is_string($datos[$campo])) {
        RespuestaJson::enviar(400, 'error', 'Formato inválido: ' . $campo, null);
    }
    $valor = trim($datos[$campo] ?? '');
    if (($obligatorio && $valor === '') || strlen($valor) > $maximo) {
        RespuestaJson::enviar(400, 'error', 'Valor inválido: ' . $campo, null);
    }
    return $valor;
}

function idApi($valor): int {
    if ((!is_string($valor) && !is_int($valor)) || !preg_match('/^[1-9][0-9]*$/', (string) $valor)) {
        RespuestaJson::enviar(400, 'error', 'Identificador inválido.', null);
    }
    if (strlen((string) $valor) > 10 || (strlen((string) $valor) === 10 && (string) $valor > '2147483647')) RespuestaJson::enviar(400, 'error', 'Identificador fuera de rango.', null);
    return (int) $valor;
}

function opcionApi(array $datos, string $campo, array $opciones): string {
    $valor = textoApi($datos, $campo, 50);
    if (!in_array($valor, $opciones)) RespuestaJson::enviar(400, 'error', 'Opción inválida: ' . $campo, null);
    return $valor;
}

function errorApi(Throwable $error, ?PDO $conexion): void {
    if ($conexion !== null && $conexion->inTransaction()) $conexion->rollBack();
    error_log((string) $error);
    // No se expone SQL ni credenciales. La clasificación específica del error
    // de integridad se deja para cuando se confirme SQLSTATE en el curso.
    RespuestaJson::enviar(500, 'error', 'No se pudo completar la operación.', null);
}
