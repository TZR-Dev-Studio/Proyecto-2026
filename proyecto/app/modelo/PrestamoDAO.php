<?php
class PrestamoDAO {
    private PDO $conexion;
    public function __construct(PDO $c) { $this->conexion = $c; }
    public function listar(bool $gestor): array {
        $sql = 'SELECT p.*, e.codigo_inventario, u.nombre_completo AS responsable FROM PRESTAMO p INNER JOIN EQUIPO e ON e.id_equipo = p.id_equipo INNER JOIN USUARIO u ON u.id_usuario = p.id_responsable';
        $d = []; if (!$gestor) { $sql .= ' WHERE p.id_responsable = :id'; $d['id'] = $_SESSION['id_usuario']; }
        $q = $this->conexion->prepare($sql . ' ORDER BY p.id_prestamo DESC'); $q->execute($d); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM PRESTAMO WHERE id_prestamo = :id'); $q->execute(['id' => $id]); $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO PRESTAMO (id_equipo, id_equipo_en_uso, id_responsable, documento, grupo, fecha_inicio, hora_inicio, fecha_limite, hora_limite, nota, id_actor) VALUES (:id_equipo, :ocupado, :id_responsable, :documento, :grupo, :fecha_inicio, :hora_inicio, :fecha_limite, :hora_limite, :nota, :id_actor)');
        $d['ocupado'] = $d['id_equipo']; $q->execute($d);
    }
    public function devolver(int $id, array $d): void {
        $d['id'] = $id;
        $q = $this->conexion->prepare("UPDATE PRESTAMO SET fecha_devolucion = :fecha, hora_devolucion = :hora, condicion_devolucion = :condicion, id_actor_devolucion = :actor, id_equipo_en_uso = NULL, estado = 'devuelto' WHERE id_prestamo = :id AND estado = 'abierto'"); $q->execute($d);
    }
    public function anular(int $id, string $motivo): void {
        $q = $this->conexion->prepare("UPDATE PRESTAMO SET estado = 'anulado', motivo_anulacion = :motivo, id_equipo_en_uso = NULL WHERE id_prestamo = :id AND estado = 'abierto'"); $q->execute(['id' => $id, 'motivo' => $motivo]);
    }
}
