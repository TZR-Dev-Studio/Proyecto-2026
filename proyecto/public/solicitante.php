<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion(); verificarRol('solicitante');
header('Location: inicio.php'); exit;
