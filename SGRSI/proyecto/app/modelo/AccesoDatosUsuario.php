<?php
class AccesoDatosUsuario {
    private PDO $conexion;
    public function __construct(PDO $conexion) { $this->conexion = $conexion; }

    public function rolesDeUsuario(int $id): array {
        $q = $this->conexion->prepare('SELECT r.nombre_rol FROM ROL r INNER JOIN USUARIO_ROL ur ON ur.id_rol = r.id_rol WHERE ur.id_usuario = :id AND ur.activo = 1 AND r.activo = 1 ORDER BY r.id_rol');
        $q->execute(['id' => $id]);
        $roles = [];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) $roles[] = $fila['nombre_rol'];
        return $roles;
    }
    public function buscarUsuario(string $nombre): ?Usuario {
        $q = $this->conexion->prepare('SELECT id_usuario, nombre_usuario, password, activo FROM USUARIO WHERE nombre_usuario = :nombre');
        $q->execute(['nombre' => $nombre]);
        $fila = $q->fetch(PDO::FETCH_ASSOC);
        if ($fila === false) return null;
        return new Usuario($fila['nombre_usuario'], $fila['password'], (bool) $fila['activo'], $this->rolesDeUsuario((int) $fila['id_usuario']), (int) $fila['id_usuario']);
    }
    public function listarUsuarios(): array {
        $q = $this->conexion->query('SELECT id_usuario, nombre_usuario, nombre_completo, email, activo FROM USUARIO ORDER BY id_usuario');
        $filas = $q->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $indice => $fila) $filas[$indice]['roles'] = $this->rolesDeUsuario((int) $fila['id_usuario']);
        return $filas;
    }
    public function obtenerUsuario(int $id): ?array {
        $q = $this->conexion->prepare('SELECT id_usuario, nombre_usuario, nombre_completo, email, activo FROM USUARIO WHERE id_usuario = :id');
        $q->execute(['id' => $id]);
        $fila = $q->fetch(PDO::FETCH_ASSOC);
        if ($fila === false) return null;
        $fila['roles'] = $this->rolesDeUsuario($id);
        return $fila;
    }
    public function existeUsuario(string $nombre, string $email, int $excepto = 0): bool {
        $q = $this->conexion->prepare('SELECT id_usuario FROM USUARIO WHERE (nombre_usuario = :nombre OR email = :email) AND id_usuario <> :excepto');
        $q->execute(['nombre' => $nombre, 'email' => $email, 'excepto' => $excepto]);
        return $q->fetch() !== false;
    }
    public function guardarRoles(int $id, array $roles): bool {
        // Revocar conserva las asociaciones anteriores como inactivas.
        $q = $this->conexion->prepare('UPDATE USUARIO_ROL SET activo = 0 WHERE id_usuario = :id');
        $q->execute(['id' => $id]);
        foreach ($roles as $nombre) {
            $q = $this->conexion->prepare('SELECT id_rol FROM ROL WHERE nombre_rol = :nombre AND activo = 1');
            $q->execute(['nombre' => $nombre]);
            $rol = $q->fetch(PDO::FETCH_ASSOC);
            if ($rol === false) return false;
            $q = $this->conexion->prepare('SELECT id_usuario FROM USUARIO_ROL WHERE id_usuario = :usuario AND id_rol = :rol');
            $q->execute(['usuario' => $id, 'rol' => $rol['id_rol']]);
            if ($q->fetch() === false) {
                $q = $this->conexion->prepare('INSERT INTO USUARIO_ROL (id_usuario, id_rol, activo) VALUES (:usuario, :rol, 1)');
            } else {
                $q = $this->conexion->prepare('UPDATE USUARIO_ROL SET activo = 1 WHERE id_usuario = :usuario AND id_rol = :rol');
            }
            $q->execute(['usuario' => $id, 'rol' => $rol['id_rol']]);
        }
        return true;
    }
    public function registrarUsuarioConRoles(string $nombre, string $usuario, string $hash, string $email, array $roles): int {
        try {
            $this->conexion->beginTransaction();
            $q = $this->conexion->prepare('INSERT INTO USUARIO (nombre_completo, nombre_usuario, password, email) VALUES (:nombre, :usuario, :hash, :email)');
            $q->execute(['nombre' => $nombre, 'usuario' => $usuario, 'hash' => $hash, 'email' => $email]);
            $q = $this->conexion->prepare('SELECT id_usuario FROM USUARIO WHERE nombre_usuario = :valor');
        $q->execute(['valor' => $usuario]);
        $nuevo = $q->fetch(PDO::FETCH_ASSOC);
            $id = (int) $nuevo['id_usuario'];
            if (!$this->guardarRoles($id, $roles)) {
                $this->conexion->rollBack(); return 0;
            }
            $this->conexion->commit(); return $id;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $e;
        }
    }
    public function modificarUsuario(int $id, string $nombre, string $email, string $hash, array $roles, int $activo): bool {
        try {
            $this->conexion->beginTransaction();
            $q = $this->conexion->prepare('UPDATE USUARIO SET nombre_completo = :nombre, email = :email, activo = :activo WHERE id_usuario = :id');
            $q->execute(['nombre' => $nombre, 'email' => $email, 'activo' => $activo, 'id' => $id]);
            if ($hash !== '') {
                $q = $this->conexion->prepare('UPDATE USUARIO SET password = :hash WHERE id_usuario = :id');
                $q->execute(['hash' => $hash, 'id' => $id]);
            }
            if (!$this->guardarRoles($id, $roles)) {
                $this->conexion->rollBack(); return false;
            }
            $this->conexion->commit(); return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $e;
        }
    }
    public function bajaUsuario(int $id): void {
        $q = $this->conexion->prepare('UPDATE USUARIO SET activo = 0 WHERE id_usuario = :id');
        $q->execute(['id' => $id]);
    }
}
