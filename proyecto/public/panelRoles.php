<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

$cantidadRoles = count($_SESSION["roles"] ?? []);

if ($cantidadRoles <= 1) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

require_once __DIR__ . "/../app/vista/panelRoles.php";
?>