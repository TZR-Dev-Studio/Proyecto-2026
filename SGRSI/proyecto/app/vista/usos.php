<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<title>SGRSI - Uso de laboratorios</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Uso de laboratorios">Uso de laboratorios</h1>
<div id="mensaje" role="status">
</div>
<form id="formulario" class="card card-body mb-3">
<input type="hidden" id="registro_id">
<label class="form-label" for="id_laboratorio" data-i18n="Laboratorio">Laboratorio</label>
<select class="form-select mb-2" id="id_laboratorio" required>
<option value="">—</option>
</select>
<label class="form-label" for="id_responsable" data-i18n="Responsable">Responsable</label>
<select class="form-select mb-2" id="id_responsable" required>
<option value="">—</option>
</select>
<label class="form-label" for="grupo" data-i18n="Grupo">Grupo</label>
<input class="form-control mb-2" id="grupo" type="text" required>
<label class="form-label" for="actividad" data-i18n="Actividad">Actividad</label>
<textarea class="form-control mb-2" id="actividad" maxlength="255" required>
</textarea>
<label class="form-label" for="fecha" data-i18n="Fecha">Fecha</label>
<input class="form-control mb-2" id="fecha" type="date" required>
<label class="form-label" for="hora_inicio" data-i18n="Hora de inicio">Hora de inicio</label>
<input class="form-control mb-2" id="hora_inicio" type="time" required>
<label class="form-label" for="hora_fin" data-i18n="Hora de fin">Hora de fin</label>
<input class="form-control mb-2" id="hora_fin" type="time" required>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button>
<button id="cancelar" type="button" class="btn btn-secondary mt-2" data-i18n="Cancelar">Cancelar</button>
</form>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="id_uso">id_uso</th>
<th data-i18n="id_laboratorio">id_laboratorio</th>
<th data-i18n="id_responsable">id_responsable</th>
<th data-i18n="grupo">grupo</th>
<th data-i18n="actividad">actividad</th>
<th data-i18n="fecha">fecha</th>
<th data-i18n="hora_inicio">hora_inicio</th>
<th data-i18n="hora_fin">hora_fin</th>
<th data-i18n="activo">activo</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="filas">
</tbody>
</table>
</div>
<section id="historial">
</section>
</main>
<script>const puedeEditar = <?php echo ($_SESSION['administrador'] || $_SESSION['tecnico']) ? 'true' : 'false'; ?>; const esAdministrador = <?php echo $_SESSION['administrador'] ? 'true' : 'false'; ?>;</script>
<script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/usos.js"></script></body>
</html>
