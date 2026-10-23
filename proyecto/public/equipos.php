<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion();
verificarRolDisponible(['administrador', 'tecnico']);
require_once __DIR__ . '/../app/vista/equipos.php';
