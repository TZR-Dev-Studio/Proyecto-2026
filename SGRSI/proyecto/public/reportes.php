<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php'; verificarSesion(); verificarRolDisponible(['administrador']);
require_once __DIR__ . '/../app/vista/reportes.php';
