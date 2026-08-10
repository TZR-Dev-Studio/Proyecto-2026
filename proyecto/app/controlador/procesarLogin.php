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

$usuario = trim($_POST["usuario"] ?? "");
$clave = $_POST["password"] ?? "";

$conectorPDO = new ConectorPDO("localhost", "root", "", "sgrsi");
$conexion = $conectorPDO->establecerConexion();
    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);

$usuarioAutenticado = $login->autenticar($usuario, $clave);
$conectorPDO->desconectar();

if ($usuarioAutenticado === null) {
    $mensaje = "Acceso Denegado: El usuario o la contraseña son incorrectos.";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

session_start();
session_regenerate_id(true);
$_SESSION["usuario"] = $usuarioAutenticado->getUsuario();
$_SESSION["roles"] = $usuarioAutenticado->getRoles();
$_SESSION["administrador"] = $usuarioAutenticado->esAdministrador();
$_SESSION["tecnico"] = $usuarioAutenticado->esTecnico();
$_SESSION["solicitante"] = $usuarioAutenticado->esSolicitante();

if ($_SESSION["administrador"] && $_SESSION["tecnico"] && $_SESSION["solicitante"]) {
    header("Location: panelRoles.php");
} elseif ($_SESSION["administrador"] && $_SESSION["tecnico"]) {
    header("Location: panelRoles.php");
} elseif ($_SESSION["administrador"] && $_SESSION["solicitante"]) {
    header("Location: panelRoles.php");
} elseif ($_SESSION["tecnico"] && $_SESSION["solicitante"]) {
    header("Location: panelRoles.php");
} elseif ($_SESSION["administrador"]) {
    header("Location: administrador.php");
} elseif ($_SESSION["tecnico"]) {
    header("Location: tecnico.php");
} elseif ($_SESSION["solicitante"]) {
    header("Location: solicitante.php");
} else {
    $mensaje = "Acceso Denegado: Usuario sin roles asignados";
    header("Location: login.php?" . "error=" . $mensaje);
}
exit;
?>
