<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<title>SGRSI - Diagnósticos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Diagnósticos">Diagnósticos</h1>
<div id="mensaje" role="status">
</div>
<form id="formulario" class="card card-body mb-3">
<input type="hidden" id="registro_id">
<label class="form-label" for="id_ticket" data-i18n="Ticket">Ticket</label>
<input class="form-control mb-2" id="id_ticket" type="number" min="1" step="1" required>
<label class="form-label" for="fecha" data-i18n="Fecha">Fecha</label>
<input class="form-control mb-2" id="fecha" type="date" readonly>
<label class="form-label" for="hora" data-i18n="Hora">Hora</label>
<input class="form-control mb-2" id="hora" type="time" readonly>
<label class="form-label" for="nota" data-i18n="Diagnóstico">Diagnóstico</label>
<textarea class="form-control mb-2" id="nota" maxlength="1000" required>
</textarea>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button>
<button id="cancelar" type="button" class="btn btn-secondary mt-2" data-i18n="Cancelar">Cancelar</button>
</form>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="id_diagnostico">id_diagnostico</th>
<th data-i18n="id_ticket">id_ticket</th>
<th data-i18n="id_equipo">id_equipo</th>
<th data-i18n="fecha">fecha</th>
<th data-i18n="hora">hora</th>
<th data-i18n="nota">nota</th>
<th data-i18n="activo">activo</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="filas">
</tbody>
</table>
</div>
<pre id="historial" class="text-wrap">
</pre>
</main>
<script>const puedeEditar = <?php echo ($_SESSION['administrador'] || $_SESSION['tecnico']) ? 'true' : 'false'; ?>; const esAdministrador = <?php echo $_SESSION['administrador'] ? 'true' : 'false'; ?>;</script>
<script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/diagnosticos.js"></script></body>
</html>
