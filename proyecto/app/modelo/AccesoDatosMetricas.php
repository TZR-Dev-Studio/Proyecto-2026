<?php
// Conteos con SELECT y foreach, sin introducir agregaciones SQL no confirmadas.
class AccesoDatosMetricas {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function obtener(): array {
        $datos = ['usuarios_activos' => 0, 'laboratorios_activos' => 0, 'equipos_activos' => 0, 'equipos_reparacion' => 0, 'tickets_pendientes' => 0, 'tickets_en_proceso' => 0, 'tickets_resueltos' => 0];
        foreach ($this->conexion->query('SELECT id_usuario FROM USUARIO WHERE activo = 1')->fetchAll(PDO::FETCH_ASSOC) as $fila) $datos['usuarios_activos']++;
        foreach ($this->conexion->query('SELECT id_laboratorio FROM LABORATORIO WHERE activo = 1')->fetchAll(PDO::FETCH_ASSOC) as $fila) $datos['laboratorios_activos']++;
        foreach ($this->conexion->query('SELECT condicion FROM EQUIPO WHERE activo = 1')->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $datos['equipos_activos']++;
            if ($fila['condicion'] === 'reparacion') $datos['equipos_reparacion']++;
        }
        foreach ($this->conexion->query('SELECT estado FROM TICKET WHERE activo = 1')->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if ($fila['estado'] === 'pendiente') $datos['tickets_pendientes']++;
            if ($fila['estado'] === 'en proceso') $datos['tickets_en_proceso']++;
            if ($fila['estado'] === 'resuelto') $datos['tickets_resueltos']++;
        }
        return $datos;
    }
}
