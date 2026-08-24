<?php

class AccesoDatosUsuario {
    private PDO $conexion;

    public function __construct (PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function buscarUsuario(string $usuario): ?Usuario
    {
        $sql = "
            SELECT
                u.nombre_usuario,
                u.password AS claveHash,
                u.activo,
                GROUP_CONCAT(r.nombre_rol) AS roles
            FROM USUARIO AS u
            LEFT JOIN USUARIO_ROL AS ur ON ur.id_usuario = u.id_usuario
            LEFT JOIN ROL AS r ON r.id_rol = ur.id_rol
            WHERE u.nombre_usuario = :usuario
            GROUP BY u.id_usuario
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["usuario" => $usuario]);
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($datos === false) {
            return null;
        }

        $roles = $datos["roles"] !== null ? explode(",", $datos["roles"]) : [];

        return new Usuario(
            $datos["nombre_usuario"],
            $datos["claveHash"],
            (bool) $datos["activo"],
            $roles
        );
    }

    public function existeUsuario(string $nombreUsuario, string $email): bool
    {
        $sql = "SELECT id_usuario FROM USUARIO WHERE nombre_usuario = :usuario OR email = :email";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["usuario" => $nombreUsuario, "email" => $email]);
        return $consulta->fetch() !== false;
    }

    public function crearUsuario(string $nombreCompleto, string $nombreUsuario, string $claveHash, string $email): int
    {
        $sql = "
            INSERT INTO USUARIO (nombre_completo, nombre_usuario, password, email)
            VALUES (:nombreCompleto, :nombreUsuario, :claveHash, :email)
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "nombreCompleto" => $nombreCompleto,
            "nombreUsuario" => $nombreUsuario,
            "claveHash" => $claveHash,
            "email" => $email,
        ]);
        return (int) $this->conexion->lastInsertId();
    }

    public function asignarRol(int $idUsuario, string $nombreRol): bool
    {
        $sql = "
            INSERT INTO USUARIO_ROL (id_usuario, id_rol)
            SELECT :idUsuario, id_rol FROM ROL WHERE nombre_rol = :nombreRol
        ";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["idUsuario" => $idUsuario, "nombreRol" => $nombreRol]);
    }

    public function listarUsuarios(): array
    {
        $sql = "
            SELECT
                u.id_usuario, u.nombre_usuario, u.nombre_completo, u.email,
                GROUP_CONCAT(r.nombre_rol) AS roles
            FROM USUARIO AS u
            LEFT JOIN USUARIO_ROL AS ur ON ur.id_usuario = u.id_usuario
            LEFT JOIN ROL AS r ON r.id_rol = ur.id_rol
            GROUP BY u.id_usuario
            ORDER BY u.id_usuario
        ";
        $consulta = $this->conexion->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
