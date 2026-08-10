<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}
if (!isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true ) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}
require_once __DIR__ . "/../app/controlador/cargarTickets.php";
?>