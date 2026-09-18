<?php
class SolicitudDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarSolicitudes(string $usuario, bool $gestor): array
    {
        $sql = "SELECT d.*, u.nombre_completo AS creadoPor
                FROM SOLICITUD AS d
                INNER JOIN USUARIO AS u ON d.id_usuario = u.id_usuario";
        $parametros = [];
        if (!$gestor) {
            $sql .= " WHERE u.nombre_usuario = :usuario";
            $parametros["usuario"] = $usuario;
        }
        $sql .= " ORDER BY d.id_solicitud DESC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarSolicitud(int $id)
    {
        $consulta = $this->conexion->prepare("SELECT * FROM SOLICITUD WHERE id_solicitud = :id");
        $consulta->execute(["id" => $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarSolicitud(array $datos, string $usuario): bool
    {
        $sql = "INSERT INTO SOLICITUD (tipo, descripcion, laboratorio, fecha_solicitada, urgencia, estado, id_usuario)
                SELECT :tipo, :descripcion, :laboratorio, :fecha_solicitada, :urgencia, :estado, id_usuario
                FROM USUARIO WHERE nombre_usuario = :usuario";
        $datos["usuario"] = $usuario;
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($datos);
        return $consulta->rowCount() === 1;
    }

    public function modificarSolicitud(int $id, array $datos): bool
    {
        $sql = "UPDATE SOLICITUD SET tipo = :tipo, descripcion = :descripcion, laboratorio = :laboratorio, fecha_solicitada = :fecha_solicitada, urgencia = :urgencia, estado = :estado
                WHERE id_solicitud = :id";
        $datos["id"] = $id;
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute($datos);
    }

    public function eliminarSolicitud(int $id): bool
    {
        $consulta = $this->conexion->prepare("DELETE FROM SOLICITUD WHERE id_solicitud = :id");
        $consulta->execute(["id" => $id]);
        return $consulta->rowCount() === 1;
    }
}
