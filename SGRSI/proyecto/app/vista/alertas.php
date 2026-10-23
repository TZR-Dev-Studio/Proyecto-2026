<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?><main class="container py-4">
<h1 data-i18n="Alertas">Alertas</h1>
<div id="mensaje" role="status">
</div>
<form id="filtro" class="card card-body mb-3">
<label for="fecha" data-i18n="Fecha de referencia">Fecha de referencia</label>
<input id="fecha" type="date" class="form-control" required>
<label for="hora" data-i18n="Hora de referencia">Hora de referencia</label>
<input id="hora" type="time" class="form-control" required>
<button class="btn btn-primary mt-2" data-i18n="Consultar">Consultar</button>
</form>
<div id="contenido">
</div>
</main><script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/alertas.js"></script></body>
</html>
