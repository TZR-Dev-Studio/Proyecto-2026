<?php

/**
 * Clase que recupera la colección de tickets registrados en la base de datos.
 */
class AccesoDatosTicket {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Recupera todos los tickets registrados, junto con el nombre de quien los creó.
     * No recibe parámetros externos, por lo que se resuelve con query().
     * @return array Arreglo asociativo con la colección de tickets.
     */
    public function listarTickets(): array
    {
        $sql = "
            SELECT
                t.id_ticket,
                t.descripcion,
                t.laboratorio,
                t.estado,
                t.prioridad,
                t.fecha_inicio,
                t.fecha_limite,
                u.nombre_completo AS creadoPor

            FROM TICKET AS t

            INNER JOIN USUARIO AS u
                ON u.id_usuario = t.id_usuario

            ORDER BY t.fecha_inicio DESC
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
