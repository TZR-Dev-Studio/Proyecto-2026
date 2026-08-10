<?php

class Usuario {
    private string $usuario;
    private string $claveHash;
    private bool $activo;
    private array $roles;

    public function __construct(string $usuario, string $claveHash, bool $activo, array $roles) {
        $this->usuario = $usuario;
        $this->claveHash = $claveHash;
        $this->activo = $activo;
        $this->roles = $roles;
    }

    public function getUsuario(): string {
        return $this->usuario;
    }

    public function getClaveHash(): string {
        return $this->claveHash;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    public function getRoles(): array {
        return $this->roles;
    }

    public function esAdministrador(): bool {
        return in_array("administrador", $this->roles);
    }

    public function esTecnico(): bool {
        return in_array("tecnico", $this->roles);
    }

    public function esSolicitante(): bool {
        return in_array("solicitante", $this->roles);
    }
}

?>