<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion(); verificarRolDisponible(['administrador', 'tecnico', 'solicitante']);
header('Location: inicio.php'); exit;
