<?php
require_once __DIR__ . "/../app/controlador/ControlAcceso.php";
verificarSesion();
verificarMultiRol();
require_once __DIR__ . "/../app/vista/panelRoles.php";
?>
