<?php
class AccesoDatosUsuarioRol {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }
    public function listar(): array {
        return $this->conexion->query('SELECT ur.id_usuario, ur.id_rol, ur.activo, u.nombre_usuario, r.nombre_rol FROM USUARIO_ROL ur INNER JOIN USUARIO u ON u.id_usuario = ur.id_usuario INNER JOIN ROL r ON r.id_rol = ur.id_rol ORDER BY ur.id_usuario, ur.id_rol')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtener(int $usuario, int $rol): ?array {
        $q = $this->conexion->prepare('SELECT id_usuario, id_rol, activo FROM USUARIO_ROL WHERE id_usuario = :usuario AND id_rol = :rol');
        $q->execute(['usuario' => $usuario, 'rol' => $rol]); $fila = $q->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
    public function crear(int $usuario, int $rol): void {
        $q = $this->conexion->prepare('INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES (:usuario, :rol)');
        $q->execute(['usuario' => $usuario, 'rol' => $rol]);
    }
    public function modificar(int $usuario, int $rol, int $activo): void {
        $q = $this->conexion->prepare('UPDATE USUARIO_ROL SET activo = :activo WHERE id_usuario = :usuario AND id_rol = :rol');
        $q->execute(['activo' => $activo, 'usuario' => $usuario, 'rol' => $rol]);
    }
}
