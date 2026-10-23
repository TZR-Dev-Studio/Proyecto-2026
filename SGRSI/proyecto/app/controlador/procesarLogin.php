<?php
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";
require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/Login.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

if (!isset($_POST['usuario'], $_POST['password']) || !is_string($_POST['usuario']) || !is_string($_POST['password'])) {
    header('Location: login.php?error=Datos+invalidos'); exit;
}
$usuario = trim($_POST["usuario"]);
$clave = $_POST["password"];
if (strlen($usuario) < 3 || strlen($usuario) > 50 || strlen($clave) < 6 || strlen($clave) > 72) {
    header('Location: login.php?error=Datos+invalidos'); exit;
}

try {
    $env = require __DIR__ . '/../../config/config.php';
    $conectorPDO = new ConectorPDO($env["DB_HOST"] ?? "", $env["DB_USER"] ?? "", $env["DB_PASS"] ?? "", $env["DB_NAME"] ?? "");
    $conexion = $conectorPDO->establecerConexion();
    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);

    $usuarioAutenticado = $login->autenticar($usuario, $clave);
    $conectorPDO->desconectar();
} catch (Throwable $e) {
    $mensaje = "Acceso Denegado: Ocurrió un error interno. Intentá nuevamente más tarde.";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

if ($usuarioAutenticado === null) {
    $mensaje = "Acceso Denegado: El usuario o la contraseña son incorrectos.";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

session_start();
session_regenerate_id(true);
$_SESSION["id_usuario"] = $usuarioAutenticado->getIdUsuario();
$_SESSION["usuario"] = $usuarioAutenticado->getUsuario();
$_SESSION["roles"] = $usuarioAutenticado->getRoles();
$_SESSION["administrador"] = $usuarioAutenticado->esAdministrador();
$_SESSION["tecnico"] = $usuarioAutenticado->esTecnico();
$_SESSION["solicitante"] = $usuarioAutenticado->esSolicitante();

if ($_SESSION["administrador"] || $_SESSION["tecnico"] || $_SESSION["solicitante"]) {
    header("Location: inicio.php");
} else {
    header("Location: login.php?error=Usuario+sin+roles+asignados");
}
exit;
