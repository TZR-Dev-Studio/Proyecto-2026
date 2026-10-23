<?php
// Agregaciones con arrays, foreach y aritmetica; no GROUP BY ni funciones de fecha SQL.
class ReporteDAO {
    private PDO $conexion;
    public function __construct(PDO $c) { $this->conexion = $c; }
    public function obtener(string $desde, string $hasta): array {
        $datos = ['tickets' => 0, 'solicitudes' => 0, 'usos_laboratorios' => 0, 'prestamos' => 0, 'resoluciones_medibles' => 0, 'promedio_minutos_resolucion' => null, 'tickets_por_equipo' => [], 'incidentes_por_laboratorio' => [], 'uso_por_laboratorio' => []];
        $q = $this->conexion->prepare('SELECT t.*, e.codigo_inventario FROM TICKET t LEFT JOIN EQUIPO e ON e.id_equipo = t.id_equipo WHERE t.fecha_inicio >= :desde AND t.fecha_inicio <= :hasta AND t.activo = 1'); $q->execute(['desde' => $desde, 'hasta' => $hasta]);
        $suma = 0;
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $t) {
            $datos['tickets']++;
            $lab = $t['laboratorio']; if (!isset($datos['incidentes_por_laboratorio'][$lab])) $datos['incidentes_por_laboratorio'][$lab] = 0; $datos['incidentes_por_laboratorio'][$lab]++;
            if ($t['id_equipo'] !== null) { $codigo = $t['codigo_inventario']; if (!isset($datos['tickets_por_equipo'][$codigo])) $datos['tickets_por_equipo'][$codigo] = 0; $datos['tickets_por_equipo'][$codigo]++; }
            if ($t['estado'] === 'resuelto' && $t['fecha_resolucion'] !== null && $t['hora_inicio'] !== null && $t['hora_resolucion'] !== null) {
                $minutos = minutosDeclarados($t['fecha_resolucion'], $t['hora_resolucion']) - minutosDeclarados($t['fecha_inicio'], $t['hora_inicio']);
                if ($minutos >= 0) { $suma += $minutos; $datos['resoluciones_medibles']++; }
            }
        }
        if ($datos['resoluciones_medibles']) $datos['promedio_minutos_resolucion'] = $suma / $datos['resoluciones_medibles'];
        $q = $this->conexion->prepare('SELECT id_solicitud FROM SOLICITUD WHERE fecha_solicitada >= :desde AND fecha_solicitada <= :hasta AND activo = 1'); $q->execute(['desde' => $desde, 'hasta' => $hasta]); foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) $datos['solicitudes']++;
        $q = $this->conexion->prepare('SELECT u.id_uso, l.nombre FROM USO_LABORATORIO u INNER JOIN LABORATORIO l ON l.id_laboratorio = u.id_laboratorio WHERE u.fecha >= :desde AND u.fecha <= :hasta AND u.activo = 1'); $q->execute(['desde' => $desde, 'hasta' => $hasta]);
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $uso) { $datos['usos_laboratorios']++; if (!isset($datos['uso_por_laboratorio'][$uso['nombre']])) $datos['uso_por_laboratorio'][$uso['nombre']] = 0; $datos['uso_por_laboratorio'][$uso['nombre']]++; }
        $q = $this->conexion->prepare("SELECT id_prestamo FROM PRESTAMO WHERE fecha_inicio >= :desde AND fecha_inicio <= :hasta AND estado <> 'anulado'"); $q->execute(['desde' => $desde, 'hasta' => $hasta]); foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $fila) $datos['prestamos']++;
        return $datos;
    }
}
function minutosDeclarados(string $fecha, string $hora): int {
    $p = explode('-', $fecha); $dias = 0;
    for ($y = 1900; $y < (int) $p[0]; $y++) $dias += ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0)) ? 366 : 365;
    $meses = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]; $y = (int) $p[0];
    if ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0)) $meses[1] = 29;
    for ($m = 1; $m < (int) $p[1]; $m++) $dias += $meses[$m - 1];
    $dias += (int) $p[2] - 1; $h = explode(':', $hora);
    return $dias * 1440 + (int) $h[0] * 60 + (int) $h[1];
}
