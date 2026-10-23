<?php
class AccesoDatosRol {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(): array {
        return $this->conexion->query('SELECT id_rol, nombre_rol, activo FROM ROL ORDER BY id_rol')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT id_rol, nombre_rol, activo FROM ROL WHERE id_rol = :id');
        $q->execute(['id' => $id]); $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
    public function buscar(string $nombre): ?array {
        $q = $this->conexion->prepare('SELECT id_rol, nombre_rol, activo FROM ROL WHERE nombre_rol = :nombre');
        $q->execute(['nombre' => $nombre]); $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
    public function crear(string $nombre, int $activo): int {
        $q = $this->conexion->prepare('INSERT INTO ROL (nombre_rol, activo) VALUES (:nombre, :activo)');
        $q->execute(['nombre' => $nombre, 'activo' => $activo]); $q = $this->conexion->prepare('SELECT id_rol FROM ROL WHERE nombre_rol = :valor');
        $q->execute(['valor' => $nombre]);
        $nuevo = $q->fetch(PDO::FETCH_ASSOC);
        return (int) $nuevo['id_rol'];
    }
    public function cambiarActivo(int $id, int $activo): void {
        $q = $this->conexion->prepare('UPDATE ROL SET activo = :activo WHERE id_rol = :id');
        $q->execute(['activo' => $activo, 'id' => $id]);
    }
    public function enUso(int $id): bool {
        $q = $this->conexion->prepare('SELECT id_usuario FROM USUARIO_ROL WHERE id_rol = :id AND activo = 1');
        $q->execute(['id' => $id]); return $q->fetch() !== false;
    }
}
