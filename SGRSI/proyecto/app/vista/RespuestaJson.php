<?php
// Adaptación de RespuestaJson del profesor al contrato exigido por la letra.
class RespuestaJson {
    public static function enviar(int $codigo, string $status, string $message, $data): void {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
        exit;
    }
    // Compatibilidad de los controladores existentes con el contrato del curso.
    public static function exito($datos, int $codigo = 200): void {
        $mensaje = is_array($datos) && isset($datos['mensaje']) ? $datos['mensaje'] : 'Operación completada.';
        self::enviar($codigo, 'success', $mensaje, $datos);
    }
    public static function error(string $mensaje, int $codigo): void {
        self::enviar($codigo, 'error', $mensaje, null);
    }
}
