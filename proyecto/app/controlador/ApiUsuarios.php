<?php
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (!isset($_SESSION["usuario"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesión no iniciada"]);
    exit;
}

if (!isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true) {
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
    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);

    switch ($_SERVER["REQUEST_METHOD"]) {

        case "GET":
            $usuarios = $accesoDatosUsuario->listarUsuarios();
            http_response_code(200);
            echo json_encode($usuarios);
            break;

        case "POST":
            $datos = json_decode(file_get_contents("php://input"), true);

            if (!is_array($datos)) {
                http_response_code(400);
                echo json_encode(["error" => "El formato de los datos enviados no es válido"]);
                break;
            }

            $camposTexto = ["nombreCompleto", "nombreUsuario", "email", "rol", "password"];
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

            $nombreCompleto = trim($datos["nombreCompleto"] ?? "");
            $nombreUsuario = trim($datos["nombreUsuario"] ?? "");
            $email = trim($datos["email"] ?? "");
            $rol = trim($datos["rol"] ?? "");
            $password = $datos["password"] ?? "";

            if ($nombreCompleto === "" || $nombreUsuario === "" || $email === "" || $rol === "" || $password === "") {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos del usuario"]);
                break;
            }

            if (strlen($nombreCompleto) > 100) {
                http_response_code(400);
                echo json_encode(["error" => "El nombre completo no puede superar los 100 caracteres"]);
                break;
            }

            if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 50) {
                http_response_code(400);
                echo json_encode(["error" => "El usuario debe tener entre 3 y 50 caracteres"]);
                break;
            }

            if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(["error" => "El email no es válido"]);
                break;
            }

            if (strlen($password) < 6) {
                http_response_code(400);
                echo json_encode(["error" => "La contraseña debe tener al menos 6 caracteres"]);
                break;
            }

            if (!in_array($rol, ["administrador", "tecnico", "solicitante"])) {
                http_response_code(400);
                echo json_encode(["error" => "El rol seleccionado no es válido"]);
                break;
            }

            if ($accesoDatosUsuario->existeUsuario($nombreUsuario, $email)) {
                http_response_code(409);
                echo json_encode(["error" => "El usuario o el email ya están registrados"]);
                break;
            }

            $claveHash = password_hash($password, PASSWORD_DEFAULT);
            $idUsuario = $accesoDatosUsuario->crearUsuario($nombreCompleto, $nombreUsuario, $claveHash, $email);

            if ($idUsuario && $accesoDatosUsuario->asignarRol($idUsuario, $rol)) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Usuario registrado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo registrar el usuario"]);
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
