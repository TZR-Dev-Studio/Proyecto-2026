<?php
require_once __DIR__ . "/../modelo/TicketDAO.php";
require_once __DIR__ . "/../vista/RespuestaJson.php";

class TicketController
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
        $dao = new TicketDAO($this->conectar());
        RespuestaJson::exito([
            "tickets" => $dao->listarTickets($_SESSION["usuario"], $this->esGestor()),
            "csrfToken" => $_SESSION["csrfToken"]
        ]);
    }

    private function alta(): void
    {
        $this->verificarCsrf();
        $datos = $this->leerDatos();
        $datos["estado"] = "pendiente";
        $datos = $this->validarDatos($datos);
        $dao = new TicketDAO($this->conectar());
        if (!$dao->registrarTicket($datos, $_SESSION["usuario"])) {
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
        $dao = new TicketDAO($this->conectar());
        $actual = $dao->buscarTicket($id);
        if ($actual === false) {
            RespuestaJson::error("Registro no encontrado", 404);
        }
        if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
            $datos = array_merge($actual, $datos);
        }
        $datos = $this->validarDatos($datos);
        if (!$dao->modificarTicket($id, $datos)) {
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
        $dao = new TicketDAO($this->conectar());
        if (!$dao->eliminarTicket($id)) {
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
        $id = $datos["id_ticket"] ?? "";
        if ((!is_int($id) && !is_string($id)) || !preg_match("/^[1-9][0-9]*$/", (string) $id)) {
            RespuestaJson::error("Identificador incorrecto", 422);
        }
        return (int) $id;
    }

    private function validarDatos(array $datos): array
    {
        $validos = [];
        foreach (["descripcion", "laboratorio", "prioridad", "fecha_inicio", "estado"] as $campo) {
            if (!isset($datos[$campo]) || !is_string($datos[$campo]) || trim($datos[$campo]) === "") {
                RespuestaJson::error("Falta o es incorrecto el campo " . $campo, 422);
            }
            $validos[$campo] = trim($datos[$campo]);
        }
        if (strlen($validos["descripcion"]) > 255 || strlen($validos["laboratorio"]) > 50) {
            RespuestaJson::error("La descripción o el laboratorio son demasiado largos", 422);
        }
        if (!in_array($validos["prioridad"], ["alta", "media", "baja"], true)) {
            RespuestaJson::error("prioridad incorrecta", 422);
        }
        if (!in_array($validos["estado"], ["pendiente", "en proceso", "resuelto"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        $this->validarFecha($validos["fecha_inicio"]);
        $limite = $datos["fecha_limite"] ?? null;
        if ($limite === "") {
            $limite = null;
        }
        if ($limite !== null) {
            if (!is_string($limite)) {
                RespuestaJson::error("Fecha límite incorrecta", 422);
            }
            $this->validarFecha($limite);
            if ($limite < $validos["fecha_inicio"]) {
                RespuestaJson::error("La fecha límite no puede ser anterior al inicio", 422);
            }
        }
        $validos["fecha_limite"] = $limite;
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
