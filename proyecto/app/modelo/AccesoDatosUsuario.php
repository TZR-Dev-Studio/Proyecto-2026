<?php

/**
 * Clase que recupera credenciales y roles del usuario desde la base de datos.
 */
class AccesoDatosUsuario {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct (PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Busca un usuario por su nombre de usuario y recupera sus roles.
     * @param string $usuario El nombre de usuario.
     * @return Usuario|null Los datos del usuario, retorna su objeto si existe, null en caso contrario.
     */
    public function buscarUsuario(string $usuario): ?Usuario
    {
        $sql = "
            SELECT
                u.nombre_usuario,
                u.password AS claveHash,
                u.activo,
                GROUP_CONCAT(r.nombre_rol) AS roles

            FROM USUARIO AS u

            LEFT JOIN USUARIO_ROL AS ur
                ON ur.id_usuario = u.id_usuario

            LEFT JOIN ROL AS r
                ON r.id_rol = ur.id_rol

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
}

?>