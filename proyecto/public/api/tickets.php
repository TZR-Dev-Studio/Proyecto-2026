<?php
require_once __DIR__ . "/../../app/controlador/TicketController.php";

session_start();

if (isset($_SESSION["usuario"]) && !isset($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}

$controlador = new TicketController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
