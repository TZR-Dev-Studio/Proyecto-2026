<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI - Indicadores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?><main class="container py-4">
<h1 data-i18n="Indicadores">Indicadores</h1>
<p data-i18n="Conteos actuales de la base. No representan promedios ni tendencias.">Conteos actuales de la base. No representan promedios ni tendencias.</p>
<div id="mensaje" role="status" aria-live="polite">
</div>
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="Indicador">Indicador</th>
<th data-i18n="Cantidad">Cantidad</th>
</tr>
</thead>
<tbody id="listado">
</tbody>
</table>
</div>
<button class="btn btn-primary" id="actualizar" data-i18n="Actualizar">Actualizar</button>
</main><script src="assets/js/api.js"></script><script src="assets/js/idioma.js"></script><script src="assets/js/metricas.js"></script></body>
</html>
