<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?><main class="container py-4">
<h1 data-i18n="Historial por equipo">Historial por equipo</h1>
<div id="mensaje" role="status">
</div>
<input id="equipo_id" type="hidden" value="<?php echo htmlspecialchars(is_string($_GET['id'] ?? null) ? $_GET['id'] : ''); ?>">
<div id="contenido">
</div>
</main><script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/historial_equipo.js"></script></body>
</html>
