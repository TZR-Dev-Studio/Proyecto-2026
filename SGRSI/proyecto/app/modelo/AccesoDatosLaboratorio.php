<?php
class AccesoDatosLaboratorio {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(): array {
        return $this->conexion->query('SELECT id_laboratorio, nombre, ubicacion, activo FROM LABORATORIO ORDER BY id_laboratorio')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT id_laboratorio, nombre, ubicacion, activo FROM LABORATORIO WHERE id_laboratorio = :id');
        $q->execute(['id' => $id]); $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
    public function crear(array $d): int {
        $q = $this->conexion->prepare('INSERT INTO LABORATORIO (nombre, ubicacion, activo) VALUES (:nombre, :ubicacion, :activo)');
        $q->execute(['activo' => $d['activo'], 'nombre' => $d['nombre'], 'ubicacion' => $d['ubicacion']]);
        $q = $this->conexion->prepare('SELECT id_laboratorio FROM LABORATORIO WHERE nombre = :valor');
        $q->execute(['valor' => $d['nombre']]);
        $nuevo = $q->fetch(PDO::FETCH_ASSOC);
        return (int) $nuevo['id_laboratorio'];
    }
    public function modificar(int $id, array $d): void {
        $q = $this->conexion->prepare('UPDATE LABORATORIO SET nombre = :nombre, ubicacion = :ubicacion, activo = :activo WHERE id_laboratorio = :id');
        $q->execute(['nombre' => $d['nombre'], 'ubicacion' => $d['ubicacion'], 'activo' => $d['activo'], 'id' => $id]);
    }
    public function baja(int $id): void {
        $q = $this->conexion->prepare('UPDATE LABORATORIO SET activo = 0 WHERE id_laboratorio = :id');
        $q->execute(['id' => $id]);
    }
}
