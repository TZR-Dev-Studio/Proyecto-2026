<?php
require_once __DIR__ . "/../app/controlador/ControlAcceso.php";
verificarSesion();
verificarRol("administrador");
require_once __DIR__ . "/../app/vista/usuarios.php";
?>
