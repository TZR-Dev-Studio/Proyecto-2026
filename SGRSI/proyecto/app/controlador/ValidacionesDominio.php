<?php
require_once __DIR__ . '/ApiComun.php';

// Fechas declaradas en formularios. No se introduce reloj, libreria ni funcion SQL nueva.
function fechaDominio(array $d, string $campo, bool $opcional = false): ?string {
    $fecha = textoApi($d, $campo, 10, !$opcional);
    if ($opcional && $fecha === '') return null;
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $fecha)) RespuestaJson::enviar(422, 'error', 'Fecha inválida.', null);
    $partes = explode('-', $fecha);
    $y = (int) $partes[0]; $m = (int) $partes[1]; $dia = (int) $partes[2];
    $dias = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0)) $dias[1] = 29;
    if ($y < 1900 || $y > 2100 || $m < 1 || $m > 12 || $dia < 1 || $dia > $dias[$m - 1]) RespuestaJson::enviar(422, 'error', 'Fecha inválida.', null);
    return $fecha;
}
function horaDominio(array $d, string $campo): string {
    $hora = textoApi($d, $campo, 5);
    if (!preg_match('/^[0-9]{2}:[0-9]{2}$/', $hora)) RespuestaJson::enviar(422, 'error', 'Hora inválida.', null);
    $p = explode(':', $hora);
    if ((int) $p[0] > 23 || (int) $p[1] > 59) RespuestaJson::enviar(422, 'error', 'Hora inválida.', null);
    return $hora;
}
function referenciaActiva(PDO $c, string $tipo, int $id): void {
    // Consultas explicitas, sin recibir nombres de tabla del navegador.
    if ($tipo === 'usuario') $sql = 'SELECT id_usuario FROM USUARIO WHERE id_usuario = :id AND activo = 1';
    elseif ($tipo === 'laboratorio') $sql = 'SELECT id_laboratorio FROM LABORATORIO WHERE id_laboratorio = :id AND activo = 1';
    else $sql = 'SELECT id_equipo FROM EQUIPO WHERE id_equipo = :id AND activo = 1';
    $q = $c->prepare($sql); $q->execute(['id' => $id]);
    if ($q->fetch() === false) RespuestaJson::enviar(409, 'error', 'La referencia debe estar activa.', null);
}
function gestorDominio(): bool {
    return ($_SESSION['administrador'] ?? false) || ($_SESSION['tecnico'] ?? false);
}

// time() aparece en Sesion.php del profesor. La conversion usa solo enteros,
// arrays, condiciones y bucles; no se introduce date(), DateTime ni funciones SQL.
function relojDominio(): array {
    $config = require __DIR__ . '/../../config/config.php';
    $offset = (int) ($config['UTC_OFFSET_HOURS'] ?? -3);
    if ($offset < -12 || $offset > 14) $offset = -3;
    $segundos = time() + $offset * 3600;
    $dias = (int) ($segundos / 86400); $resto = $segundos % 86400; $y = 1970;
    while (true) {
        $cantidad = ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0)) ? 366 : 365;
        if ($dias < $cantidad) break;
        $dias -= $cantidad; $y++;
    }
    $meses = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0)) $meses[1] = 29;
    $m = 1;
    while ($dias >= $meses[$m - 1]) { $dias -= $meses[$m - 1]; $m++; }
    $dia = $dias + 1; $h = (int) ($resto / 3600); $minuto = (int) (($resto % 3600) / 60);
    return ['fecha' => $y . '-' . ($m < 10 ? '0' : '') . $m . '-' . ($dia < 10 ? '0' : '') . $dia,
            'hora' => ($h < 10 ? '0' : '') . $h . ':' . ($minuto < 10 ? '0' : '') . $minuto];
}
