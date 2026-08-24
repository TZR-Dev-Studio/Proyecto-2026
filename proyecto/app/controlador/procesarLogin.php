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

try {
    $rutaEnv = __DIR__ . "/../../.env";
    $env = [];
    if (file_exists($rutaEnv)) {
        foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === "" || str_starts_with($linea, "#") || !str_contains($linea, "=")) {
                continue;
            }
            [$nombreClave, $valor] = array_map("trim", explode("=", $linea, 2));
            $env[$nombreClave] = $valor;
        }
    }

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
