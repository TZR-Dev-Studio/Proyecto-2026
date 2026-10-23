<?php
class TareaDAO {
    private PDO $conexion;
    public function __construct(PDO $c) { $this->conexion = $c; }
    public function listar(): array { return $this->conexion->query('SELECT * FROM TAREA ORDER BY fecha_inicio, hora_inicio, id_tarea')->fetchAll(PDO::FETCH_ASSOC); }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM TAREA WHERE id_tarea = :id'); $q->execute(['id' => $id]); $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO TAREA (descripcion, tipo, prioridad, fecha_inicio, hora_inicio, fecha_limite, hora_limite, id_tecnico, id_equipo, estado, nota, activo) VALUES (:descripcion, :tipo, :prioridad, :fecha_inicio, :hora_inicio, :fecha_limite, :hora_limite, :id_tecnico, :id_equipo, :estado, :nota, :activo)'); $q->execute($d);
    }
    public function modificar(int $id, array $d): void {
        $d['id'] = $id; $q = $this->conexion->prepare('UPDATE TAREA SET descripcion = :descripcion, tipo = :tipo, prioridad = :prioridad, fecha_inicio = :fecha_inicio, hora_inicio = :hora_inicio, fecha_limite = :fecha_limite, hora_limite = :hora_limite, id_tecnico = :id_tecnico, id_equipo = :id_equipo, estado = :estado, nota = :nota, activo = :activo WHERE id_tarea = :id'); $q->execute($d);
    }
    public function registrarHistorial(int $id, string $fecha, string $hora, array $antes): void {
        $q = $this->conexion->prepare('INSERT INTO HISTORIAL_TAREA (id_tarea, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle)'); $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'detalle' => json_encode($antes)]);
    }
    public function historial(int $id): array {
        $q = $this->conexion->prepare('SELECT * FROM HISTORIAL_TAREA WHERE id_tarea = :id ORDER BY id_historial'); $q->execute(['id' => $id]); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
