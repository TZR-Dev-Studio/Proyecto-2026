<?php
require_once __DIR__ . '/../app/controlador/ControlAcceso.php';
verificarSesion(); verificarRol('tecnico');
header('Location: inicio.php'); exit;
