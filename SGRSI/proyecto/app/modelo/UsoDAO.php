<?php
class UsoDAO {
    private PDO $conexion;
    public function __construct(PDO $c) { $this->conexion = $c; }
    public function listar(): array { return $this->conexion->query('SELECT * FROM USO_LABORATORIO ORDER BY id_uso DESC')->fetchAll(PDO::FETCH_ASSOC); }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM USO_LABORATORIO WHERE id_uso = :id'); $q->execute(['id' => $id]); $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO USO_LABORATORIO (id_laboratorio, id_responsable, grupo, actividad, fecha, hora_inicio, hora_fin, activo) VALUES (:id_laboratorio, :id_responsable, :grupo, :actividad, :fecha, :hora_inicio, :hora_fin, :activo)'); $q->execute($d);
    }
    public function modificar(int $id, array $d): void {
        $d['id'] = $id; $q = $this->conexion->prepare('UPDATE USO_LABORATORIO SET id_laboratorio = :id_laboratorio, id_responsable = :id_responsable, grupo = :grupo, actividad = :actividad, fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin, activo = :activo WHERE id_uso = :id'); $q->execute($d);
    }
    public function registrarHistorial(int $id, string $fecha, string $hora, array $antes): void {
        $q = $this->conexion->prepare('INSERT INTO HISTORIAL_USO (id_uso, id_actor, fecha, hora, detalle) VALUES (:id, :actor, :fecha, :hora, :detalle)'); $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'detalle' => json_encode($antes)]);
    }
    public function historial(int $id): array {
        $q = $this->conexion->prepare('SELECT * FROM HISTORIAL_USO WHERE id_uso = :id ORDER BY id_historial'); $q->execute(['id' => $id]); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
