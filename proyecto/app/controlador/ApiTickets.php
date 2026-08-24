<?php
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosTicket.php";

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (!isset($_SESSION["usuario"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesión no iniciada"]);
    exit;
}

if (!isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Rol incorrecto"]);
    exit;
}

try {
    $rutaEnv = __DIR__ . "/../../.env";
    $env = [];
    if (file_exists($rutaEnv)) {
        foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === "" || str_starts_with($linea, "#") || !str_contains($linea, "=")) {
                continue;
            }
            [$clave, $valor] = array_map("trim", explode("=", $linea, 2));
            $env[$clave] = $valor;
        }
    }

    $conectorPDO = new ConectorPDO($env["DB_HOST"] ?? "", $env["DB_USER"] ?? "", $env["DB_PASS"] ?? "", $env["DB_NAME"] ?? "");
    $conexion = $conectorPDO->establecerConexion();
    $accesoDatosTicket = new AccesoDatosTicket($conexion);

    switch ($_SERVER["REQUEST_METHOD"]) {

        case "GET":
            $tickets = $accesoDatosTicket->listarTickets();
            http_response_code(200);
            echo json_encode($tickets);
            break;

        case "POST":
            $datos = json_decode(file_get_contents("php://input"), true);

            if (!is_array($datos)) {
                http_response_code(400);
                echo json_encode(["error" => "El formato de los datos enviados no es válido"]);
                break;
            }

            $camposTexto = ["descripcion", "laboratorio", "prioridad"];
            $tiposValidos = true;
            foreach ($camposTexto as $campo) {
                if (isset($datos[$campo]) && !is_string($datos[$campo])) {
                    $tiposValidos = false;
                }
            }
            if (!$tiposValidos) {
                http_response_code(400);
                echo json_encode(["error" => "El formato de los datos enviados no es válido"]);
                break;
            }

            $descripcion = trim($datos["descripcion"] ?? "");
            $laboratorio = trim($datos["laboratorio"] ?? "");
            $prioridad = trim($datos["prioridad"] ?? "");

            if ($descripcion === "" || $laboratorio === "" || $prioridad === "") {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos del ticket"]);
                break;
            }

            if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
                http_response_code(400);
                echo json_encode(["error" => "La descripción debe tener entre 10 y 255 caracteres"]);
                break;
            }

            if (strlen($laboratorio) > 50) {
                http_response_code(400);
                echo json_encode(["error" => "El laboratorio o aula no puede superar los 50 caracteres"]);
                break;
            }

            if (!in_array($prioridad, ["alta", "media", "baja"])) {
                http_response_code(400);
                echo json_encode(["error" => "La prioridad seleccionada no es válida"]);
                break;
            }

            $creado = $accesoDatosTicket->crearTicket($descripcion, $laboratorio, $prioridad, $_SESSION["usuario"]);

            if ($creado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Ticket registrado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo registrar el ticket"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }

    $conectorPDO->desconectar();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Ocurrió un error interno. Intentá nuevamente más tarde."]);
}
?>
