<?php
// Mantiene el formato .env ya usado por SGRSI; no incorpora Composer.
$env = [];
$ruta = __DIR__ . '/../.env';
if (file_exists($ruta)) {
    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || strpos($linea, '=') === false) {
            continue;
        }
        $par = explode('=', $linea, 2);
        $env[trim($par[0])] = trim($par[1]);
    }
}
return $env;
