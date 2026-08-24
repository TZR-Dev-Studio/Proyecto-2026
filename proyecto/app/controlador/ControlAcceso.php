<?php

function verificarSesion(): void {
    session_start();
    if (!isset($_SESSION["usuario"])) {
        $mensaje = "Acceso Denegado: Sesión no iniciada";
        header("Location: login.php?" . "error=" . $mensaje);
        exit;
    }
}

function verificarRol(string $rol): void {
    if (!isset($_SESSION[$rol]) || $_SESSION[$rol] !== true) {
        $mensaje = "Acceso Denegado: Rol incorrecto";
        header("Location: login.php?" . "error=" . $mensaje);
        exit;
    }
}

function verificarMultiRol(): void {
    $cantidadRoles = count($_SESSION["roles"] ?? []);

    if ($cantidadRoles <= 1) {
        $mensaje = "Acceso Denegado: Rol incorrecto";
        header("Location: login.php?" . "error=" . $mensaje);
        exit;
    }
}

?>
