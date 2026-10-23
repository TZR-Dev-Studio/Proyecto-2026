<?php
session_start();
$_SESSION = [];
// Igual que Sesion::cerrar() en la referencia docente: retirar también cookie.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
header('Location: login.php?mensaje=Sesion+cerrada'); exit;
