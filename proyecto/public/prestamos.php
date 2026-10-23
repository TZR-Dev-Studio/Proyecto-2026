<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion(); verificarRolDisponible(['administrador', 'tecnico', 'solicitante']);
require_once __DIR__ . '/../app/vista/prestamos.php';
