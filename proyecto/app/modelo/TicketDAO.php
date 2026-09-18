<?php
class TicketDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarTickets(string $usuario, bool $gestor): array
    {
        $sql = "SELECT d.*, u.nombre_completo AS creadoPor
                FROM TICKET AS d
                INNER JOIN USUARIO AS u ON d.id_usuario = u.id_usuario";
        $parametros = [];
        if (!$gestor) {
            $sql .= " WHERE u.nombre_usuario = :usuario";
            $parametros["usuario"] = $usuario;
        }
        $sql .= " ORDER BY d.id_ticket DESC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarTicket(int $id)
    {
        $consulta = $this->conexion->prepare("SELECT * FROM TICKET WHERE id_ticket = :id");
        $consulta->execute(["id" => $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarTicket(array $datos, string $usuario): bool
    {
        $sql = "INSERT INTO TICKET (descripcion, laboratorio, prioridad, fecha_inicio, fecha_limite, estado, id_usuario)
                SELECT :descripcion, :laboratorio, :prioridad, :fecha_inicio, :fecha_limite, :estado, id_usuario
                FROM USUARIO WHERE nombre_usuario = :usuario";
        $datos["usuario"] = $usuario;
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($datos);
        return $consulta->rowCount() === 1;
    }

    public function modificarTicket(int $id, array $datos): bool
    {
        $sql = "UPDATE TICKET SET descripcion = :descripcion, laboratorio = :laboratorio, prioridad = :prioridad, fecha_inicio = :fecha_inicio, fecha_limite = :fecha_limite, estado = :estado
                WHERE id_ticket = :id";
        $datos["id"] = $id;
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute($datos);
    }

    public function eliminarTicket(int $id): bool
    {
        $consulta = $this->conexion->prepare("DELETE FROM TICKET WHERE id_ticket = :id");
        $consulta->execute(["id" => $id]);
        return $consulta->rowCount() === 1;
    }
}
