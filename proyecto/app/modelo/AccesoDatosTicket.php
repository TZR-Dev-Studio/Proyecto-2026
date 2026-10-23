<?php

class AccesoDatosTicket {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function listarTickets(): array
    {
        $sql = "
            SELECT
                t.id_ticket, t.descripcion, t.laboratorio, t.estado,
                t.prioridad, t.fecha_inicio, t.fecha_limite,
                u.nombre_completo AS creadoPor
            FROM TICKET AS t
            INNER JOIN USUARIO AS u ON u.id_usuario = t.id_usuario
            ORDER BY t.fecha_inicio DESC
        ";

        $consulta = $this->conexion->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
