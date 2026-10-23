<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion(); verificarRol('administrador');
header('Location: inicio.php'); exit;
