<?php
require_once __DIR__ . "/../modelo/SolicitudDAO.php";
require_once __DIR__ . "/../vista/RespuestaJson.php";

class SolicitudController
{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["usuario"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        if (!$this->esGestor() && !($_SESSION["solicitante"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
        try {
            match ($metodo) {
                "GET" => $this->listar(),
                "POST" => $this->alta(),
                "PUT", "PATCH" => $this->modificar(),
                "DELETE" => $this->baja(),
                default => RespuestaJson::error("Método no permitido", 405),
            };
        } catch (PDOException $error) {
            RespuestaJson::error("No se pudo completar la operación en la base de datos", 500);
        }
    }

    private function listar(): void
    {
        $dao = new SolicitudDAO($this->conectar());
        RespuestaJson::exito([
            "solicitudes" => $dao->listarSolicitudes($_SESSION["usuario"], $this->esGestor()),
            "csrfToken" => $_SESSION["csrfToken"]
        ]);
    }

    private function alta(): void
    {
        $this->verificarCsrf();
        $datos = $this->leerDatos();
        $datos["estado"] = "pendiente";
        $datos = $this->validarDatos($datos);
        $dao = new SolicitudDAO($this->conectar());
        if (!$dao->registrarSolicitud($datos, $_SESSION["usuario"])) {
            RespuestaJson::error("No se pudo registrar el dato", 400);
        }
        RespuestaJson::exito(["mensaje" => "Registro ingresado exitosamente"], 201);
    }

    private function modificar(): void
    {
        $this->verificarGestor();
        $this->verificarCsrf();
        $datos = $this->leerDatos();
        $id = $this->validarId($datos);
        $dao = new SolicitudDAO($this->conectar());
        $actual = $dao->buscarSolicitud($id);
        if ($actual === false) {
            RespuestaJson::error("Registro no encontrado", 404);
        }
        if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
            $datos = array_merge($actual, $datos);
        }
        $datos = $this->validarDatos($datos);
        if (!$dao->modificarSolicitud($id, $datos)) {
            RespuestaJson::error("No se pudo modificar el dato", 400);
        }
        RespuestaJson::exito(["mensaje" => "Registro modificado exitosamente"]);
    }

    private function baja(): void
    {
        $this->verificarGestor();
        $this->verificarCsrf();
        $datos = $this->leerDatos();
        $id = $this->validarId($datos);
        $dao = new SolicitudDAO($this->conectar());
        if (!$dao->eliminarSolicitud($id)) {
            RespuestaJson::error("Registro no encontrado", 404);
        }
        RespuestaJson::exito(["mensaje" => "Registro eliminado exitosamente"]);
    }

    private function leerDatos(): array
    {
        $datos = json_decode(file_get_contents("php://input"), true);
        if (!is_array($datos)) {
            RespuestaJson::error("Se esperaba un objeto JSON", 400);
        }
        return $datos;
    }

    private function validarId(array $datos): int
    {
        $id = $datos["id_solicitud"] ?? "";
        if ((!is_int($id) && !is_string($id)) || !preg_match("/^[1-9][0-9]*$/", (string) $id)) {
            RespuestaJson::error("Identificador incorrecto", 422);
        }
        return (int) $id;
    }

    private function validarDatos(array $datos): array
    {
        $validos = [];
        foreach (["tipo", "descripcion", "laboratorio", "fecha_solicitada", "urgencia", "estado"] as $campo) {
            if (!isset($datos[$campo]) || !is_string($datos[$campo]) || trim($datos[$campo]) === "") {
                RespuestaJson::error("Falta o es incorrecto el campo " . $campo, 422);
            }
            $validos[$campo] = trim($datos[$campo]);
        }
        if (strlen($validos["descripcion"]) > 255 || strlen($validos["laboratorio"]) > 50) {
            RespuestaJson::error("La descripción o el laboratorio son demasiado largos", 422);
        }
        if (!in_array($validos["urgencia"], ["alta", "media", "baja"], true)) {
            RespuestaJson::error("urgencia incorrecta", 422);
        }
        if (!in_array($validos["estado"], ["pendiente", "en proceso", "resuelto"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        $this->validarFecha($validos["fecha_solicitada"]);
        if (!in_array($validos["tipo"], ["instalacion", "laboratorio", "configuracion", "otro"], true)) {
            RespuestaJson::error("Tipo de solicitud incorrecto", 422);
        }
        return $validos;
    }

    private function validarFecha(string $fecha): void
    {
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $fecha)) {
            RespuestaJson::error("La fecha debe tener formato AAAA-MM-DD", 422);
        }
        $partes = explode("-", $fecha);
        if (!checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }
    }

    private function esGestor(): bool
    {
        return ($_SESSION["administrador"] ?? false) || ($_SESSION["tecnico"] ?? false);
    }

    private function verificarGestor(): void
    {
        if (!$this->esGestor()) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
    }

    private function verificarCsrf(): void
    {
        $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        if (!isset($_SESSION["csrfToken"]) || !hash_equals($_SESSION["csrfToken"], $token)) {
            RespuestaJson::error("Solicitud rechazada", 403);
        }
    }

    private function conectar(): PDO
    {
        $conexion = new PDO("mysql:host=localhost;dbname=sgrsi;charset=utf8mb4", "root", "");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    }
}
