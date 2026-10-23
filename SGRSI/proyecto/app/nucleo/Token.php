<?php
// Técnica tomada de migracion_api/app/nucleo/Token.php del profesor.
class Token {
    public static function generar(): void {
        if (!isset($_SESSION['csrfToken'])) $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
    }
    public static function verificar(): void {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || !isset($_SESSION['csrfToken']) || !hash_equals($_SESSION['csrfToken'], $token)) {
            RespuestaJson::enviar(403, 'error', 'Solicitud rechazada. Recargue la pantalla.', null);
        }
    }
}
