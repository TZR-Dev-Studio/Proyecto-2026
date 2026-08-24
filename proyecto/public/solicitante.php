<?php
require_once __DIR__ . "/../app/controlador/ControlAcceso.php";
verificarSesion();
verificarRol("solicitante");
require_once __DIR__ . "/../app/vista/solicitante.php";
?>
