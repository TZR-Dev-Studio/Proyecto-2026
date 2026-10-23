<?php
class TicketDAO {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(string $usuario, bool $gestor): array {
        $sql = 'SELECT d.*, u.nombre_completo AS creadoPor FROM TICKET d INNER JOIN USUARIO u ON u.id_usuario = d.id_usuario';
        $p = [];
        if (!$gestor) { $sql .= ' WHERE u.nombre_usuario = :usuario'; $p['usuario'] = $usuario; }
        $sql .= ' ORDER BY d.id_ticket DESC';
        $q = $this->conexion->prepare($sql); $q->execute($p); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT * FROM TICKET WHERE id_ticket = :id'); $q->execute(['id' => $id]);
        $d = $q->fetch(PDO::FETCH_ASSOC); return $d === false ? null : $d;
    }
    public function crear(array $d): void {
        $q = $this->conexion->prepare('INSERT INTO TICKET (descripcion, laboratorio, estado, prioridad, fecha_inicio, fecha_limite, id_usuario, activo, id_equipo, id_tecnico, hora_inicio, fecha_resolucion, hora_resolucion, nota_resolucion, espera_insumo) VALUES (:descripcion, :laboratorio, :estado, :prioridad, :fecha_inicio, :fecha_limite, :id_usuario, :activo, :id_equipo, :id_tecnico, :hora_inicio, :fecha_resolucion, :hora_resolucion, :nota_resolucion, :espera_insumo)'); $q->execute($d);
    }
    public function modificar(int $id, array $d): void {
        $d['id'] = $id; $q = $this->conexion->prepare('UPDATE TICKET SET descripcion = :descripcion, laboratorio = :laboratorio, estado = :estado, prioridad = :prioridad, fecha_inicio = :fecha_inicio, fecha_limite = :fecha_limite, id_usuario = :id_usuario, activo = :activo, id_equipo = :id_equipo, id_tecnico = :id_tecnico, hora_inicio = :hora_inicio, fecha_resolucion = :fecha_resolucion, hora_resolucion = :hora_resolucion, nota_resolucion = :nota_resolucion, espera_insumo = :espera_insumo WHERE id_ticket = :id'); $q->execute($d);
    }
    public function registrarHistorial(int $id, string $fecha, string $hora, string $accion, array $antes): void {
        $q = $this->conexion->prepare('INSERT INTO HISTORIAL_TICKET (id_ticket, id_actor, fecha, hora, accion, detalle) VALUES (:id, :actor, :fecha, :hora, :accion, :detalle)');
        $q->execute(['id' => $id, 'actor' => $_SESSION['id_usuario'], 'fecha' => $fecha, 'hora' => $hora, 'accion' => $accion, 'detalle' => json_encode($antes)]);
    }
    public function historial(int $id): array {
        $q = $this->conexion->prepare('SELECT h.*, u.nombre_completo AS actor FROM HISTORIAL_TICKET h INNER JOIN USUARIO u ON u.id_usuario = h.id_actor WHERE h.id_ticket = :id ORDER BY h.id_historial'); $q->execute(['id' => $id]); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
