<?php
class DiagnosticoDAO {
    private PDO $conexion;
    public function __construct(PDO $c) { $this->conexion = $c; }
    public function listar(): array {
        return $this->conexion->query('SELECT d.*, t.id_equipo FROM DIAGNOSTICO d INNER JOIN TICKET t ON t.id_ticket = d.id_ticket ORDER BY d.id_diagnostico DESC')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM DIAGNOSTICO WHERE id_diagnostico = :id'); $q->execute(['id' => $id]); $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO DIAGNOSTICO (id_ticket, id_actor, fecha, hora, nota, activo) VALUES (:id_ticket, :id_actor, :fecha, :hora, :nota, 1)'); $q->execute($d);
    }
    public function modificar(int $id, string $nota): void {
        $q = $this->conexion->prepare('UPDATE DIAGNOSTICO SET nota = :nota WHERE id_diagnostico = :id'); $q->execute(['nota' => $nota, 'id' => $id]);
    }
    public function baja(int $id): void {
        $q = $this->conexion->prepare('UPDATE DIAGNOSTICO SET activo = 0 WHERE id_diagnostico = :id'); $q->execute(['id' => $id]);
    }
}
