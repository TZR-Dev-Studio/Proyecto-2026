<?php
class AccesoDatosEquipo {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(): array {
        $filas = $this->conexion->query('SELECT id_equipo, codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo FROM EQUIPO ORDER BY id_equipo')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $indice => $fila) {
            $q = $this->conexion->prepare('SELECT id_prestamo FROM PRESTAMO WHERE id_equipo_en_uso = :id'); $q->execute(['id' => $fila['id_equipo']]);
            $filas[$indice]['prestado'] = $q->fetch() !== false;
            $filas[$indice]['disponible'] = (bool) $fila['activo'] && $fila['condicion'] === 'operativo' && !$filas[$indice]['prestado'];
        }
        return $filas;
    }
    public function obtener(int $id): ?array {
        $q = $this->conexion->prepare('SELECT id_equipo, codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo FROM EQUIPO WHERE id_equipo = :id');
        $q->execute(['id' => $id]); $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
    public function crear(array $d): int {
        $q = $this->conexion->prepare('INSERT INTO EQUIPO (codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio, activo) VALUES (:codigo_inventario, :descripcion, :tipo, :marca, :condicion, :id_laboratorio, :activo)');
        $q->execute(['activo' => $d['activo'], 'codigo_inventario' => $d['codigo_inventario'], 'descripcion' => $d['descripcion'], 'tipo' => $d['tipo'], 'marca' => $d['marca'], 'condicion' => $d['condicion'], 'id_laboratorio' => $d['id_laboratorio']]);
        $q = $this->conexion->prepare('SELECT id_equipo FROM EQUIPO WHERE codigo_inventario = :valor');
        $q->execute(['valor' => $d['codigo_inventario']]);
        $nuevo = $q->fetch(PDO::FETCH_ASSOC);
        return (int) $nuevo['id_equipo'];
    }
    public function modificar(int $id, array $d): void {
        $q = $this->conexion->prepare('UPDATE EQUIPO SET codigo_inventario = :codigo_inventario, descripcion = :descripcion, tipo = :tipo, marca = :marca, condicion = :condicion, id_laboratorio = :id_laboratorio, activo = :activo WHERE id_equipo = :id');
        $q->execute(['codigo_inventario' => $d['codigo_inventario'], 'descripcion' => $d['descripcion'], 'tipo' => $d['tipo'], 'marca' => $d['marca'], 'condicion' => $d['condicion'], 'id_laboratorio' => $d['id_laboratorio'], 'activo' => $d['activo'], 'id' => $id]);
    }
    public function baja(int $id): void {
        $q = $this->conexion->prepare('UPDATE EQUIPO SET activo = 0 WHERE id_equipo = :id');
        $q->execute(['id' => $id]);
    }
}
