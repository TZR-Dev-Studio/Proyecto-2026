<?php
session_start();
$_SESSION = array();
session_destroy();

$mensaje = "Sesión cerrada correctamente";
header("Location: login.php?" . "mensaje=" . $mensaje);
exit;
?>