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

    public function crearTicket(string $descripcion, string $laboratorio, string $prioridad, string $nombreUsuario): bool
    {
        $sql = "
            INSERT INTO TICKET (descripcion, laboratorio, estado, prioridad, fecha_inicio, id_usuario)
            VALUES (
                :descripcion, :laboratorio, 'pendiente', :prioridad, CURDATE(),
                (SELECT id_usuario FROM USUARIO WHERE nombre_usuario = :usuario)
            )
        ";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            "descripcion" => $descripcion,
            "laboratorio" => $laboratorio,
            "prioridad" => $prioridad,
            "usuario" => $nombreUsuario,
        ]);
    }
}
?>
