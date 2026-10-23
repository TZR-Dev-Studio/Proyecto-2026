<?php

function verificarSesion(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION["id_usuario"])) {
        $mensaje = "Acceso Denegado: Sesión no iniciada";
        header("Location: login.php?" . "error=" . $mensaje);
        exit;
    }
}

function verificarRol(string $rol): void {
    verificarRolDisponible([$rol]);
}

function verificarRolDisponible(array $roles): void {
    require_once __DIR__ . '/ApiComun.php';
    try {
        iniciarApi(conectarApi()); Token::generar();
    } catch (Throwable $error) {
        error_log((string) $error);
        header('Location: login.php?error=No+se+pudo+verificar+el+acceso'); exit;
    }
    foreach ($roles as $rol) {
        if ($_SESSION[$rol] ?? false) return;
    }
    header('Location: login.php?error=Rol+sin+permiso'); exit;
}
