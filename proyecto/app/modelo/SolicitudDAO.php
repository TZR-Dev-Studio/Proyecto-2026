<?php
class SolicitudDAO {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(string $usuario, bool $gestor): array {
        $sql = 'SELECT d.*, u.nombre_completo AS creadoPor FROM SOLICITUD d INNER JOIN USUARIO u ON u.id_usuario = d.id_usuario';
        $p = [];
        if (!$gestor) { $sql .= ' WHERE u.nombre_usuario = :usuario'; $p['usuario'] = $usuario; }
        $sql .= ' ORDER BY d.id_solicitud DESC';
        $q = $this->conexion->prepare($sql); $q->execute($p); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM SOLICITUD WHERE id_solicitud = :id'); $q->execute(['id' => $id]);
        $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO SOLICITUD (tipo, descripcion, laboratorio, fecha_solicitada, urgencia, estado, id_usuario, activo, id_laboratorio, id_tecnico, hora_solicitada, nota_resolucion) VALUES (:tipo, :descripcion, :laboratorio, :fecha_solicitada, :urgencia, :estado, :id_usuario, :activo, :id_laboratorio, :id_tecnico, :hora_solicitada, :nota_resolucion)'); $q->execute($d);
    }
    public function modificar(int $id, array $d): void {
        $d['id'] = $id; $q = $this->conexion->prepare('UPDATE SOLICITUD SET tipo = :tipo, descripcion = :descripcion, laboratorio = :laboratorio, fecha_solicitada = :fecha_solicitada, urgencia = :urgencia, estado = :estado, id_usuario = :id_usuario, activo = :activo, id_laboratorio = :id_laboratorio, id_tecnico = :id_tecnico, hora_solicitada = :hora_solicitada, nota_resolucion = :nota_resolucion WHERE id_solicitud = :id'); $q->execute($d);
    }
    public function registrarHistorial(int $id, string $fecha, string $hora, string $accion, array $antes): void {
        $q = $this->conexion->prepare('INSERT INTO HISTORIAL_SOLICITUD (id_solicitud, id_actor, fecha, hora, accion, detalle) VALUES (:id, :actor, :fecha, :hora, :accion, :detalle)');
        $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'accion' => $accion, 'detalle' => json_encode($antes)]);
    }
    public function historial(int $id): array {
        $q = $this->conexion->prepare('SELECT h.*, u.nombre_completo AS actor FROM HISTORIAL_SOLICITUD h INNER JOIN USUARIO u ON u.id_usuario = h.id_actor WHERE h.id_solicitud = :id ORDER BY h.id_historial'); $q->execute(['id' => $id]); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
