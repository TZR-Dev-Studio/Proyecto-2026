<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<title>SGRSI - Solicitudes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Solicitudes">Solicitudes</h1>
<div id="mensaje" role="status">
</div>
<form id="formulario" class="card card-body mb-3">
<input type="hidden" id="registro_id">
<label class="form-label" for="tipo" data-i18n="Tipo">Tipo</label>
<select class="form-select mb-2" id="tipo" required>
<option value="instalacion" data-i18n="instalacion">instalacion</option>
<option value="laboratorio" data-i18n="laboratorio">laboratorio</option>
<option value="configuracion" data-i18n="configuracion">configuracion</option>
<option value="otro" data-i18n="otro">otro</option>
</select>
<label class="form-label" for="descripcion" data-i18n="Descripción">Descripción</label>
<textarea class="form-control mb-2" id="descripcion" maxlength="255" required>
</textarea>
<label class="form-label" for="id_laboratorio" data-i18n="Laboratorio">Laboratorio</label>
<select class="form-select mb-2" id="id_laboratorio" required>
<option value="">—</option>
</select>
<label class="form-label" for="fecha_solicitada" data-i18n="Fecha solicitada">Fecha solicitada</label>
<input class="form-control mb-2" id="fecha_solicitada" type="date" required>
<label class="form-label" for="hora_solicitada" data-i18n="Hora solicitada">Hora solicitada</label>
<input class="form-control mb-2" id="hora_solicitada" type="time" required>
<label class="form-label" for="urgencia" data-i18n="Prioridad">Prioridad</label>
<select class="form-select mb-2" id="urgencia" required>
<option value="alta" data-i18n="alta">alta</option>
<option value="media" data-i18n="media">media</option>
<option value="baja" data-i18n="baja">baja</option>
</select>
<label class="form-label" for="estado" data-i18n="Estado">Estado</label>
<select class="form-select mb-2" id="estado" required>
<option value="pendiente" data-i18n="pendiente">pendiente</option>
<option value="en proceso" data-i18n="en proceso">en proceso</option>
<option value="resuelto" data-i18n="resuelto">resuelto</option>
</select>
<label class="form-label" for="id_tecnico" data-i18n="Técnico">Técnico</label>
<select class="form-select mb-2" id="id_tecnico">
<option value="">—</option>
</select>
<label class="form-label" for="nota_resolucion" data-i18n="Nota de resolución">Nota de resolución</label>
<textarea class="form-control mb-2" id="nota_resolucion" maxlength="1000">
</textarea>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button>
<button id="cancelar" type="button" class="btn btn-secondary mt-2" data-i18n="Cancelar">Cancelar</button>
</form>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="id_solicitud">id_solicitud</th>
<th data-i18n="tipo">tipo</th>
<th data-i18n="descripcion">descripcion</th>
<th data-i18n="laboratorio">laboratorio</th>
<th data-i18n="fecha_solicitada">fecha_solicitada</th>
<th data-i18n="estado">estado</th>
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
<script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/solicitudes.js"></script></body>
</html>
