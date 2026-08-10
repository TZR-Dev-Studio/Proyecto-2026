<?php

require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";

class Login {
    private AccesoDatosUsuario $accesoDatosUsuario;

    //Constructor parametrizado
    public function __construct(AccesoDatosUsuario $accesoDatosUsuario) {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    public function autenticar(string $usuario, string $clave): ?Usuario {
        $usuarioEncontrado = $this->accesoDatosUsuario->buscarUsuario($usuario);

        if ($usuarioEncontrado === null) {
            return null;
        }

        if (!$usuarioEncontrado->estaActivo()) {
            return null;
        }

        if (!password_verify($clave, $usuarioEncontrado->getClaveHash())) {
            return null;
        }

        return $usuarioEncontrado;
    }
}

?>