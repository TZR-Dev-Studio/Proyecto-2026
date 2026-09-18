<?php
require_once __DIR__ . "/../../app/controlador/SolicitudController.php";

session_start();

if (isset($_SESSION["usuario"]) && !isset($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}

$controlador = new SolicitudController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
