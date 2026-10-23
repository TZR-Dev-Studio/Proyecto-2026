<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<title>SGRSI - Agenda de tareas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Agenda de tareas">Agenda de tareas</h1>
<div id="mensaje" role="status">
</div>
<form id="formulario" class="card card-body mb-3">
<input type="hidden" id="registro_id">
<label class="form-label" for="descripcion" data-i18n="Descripción">Descripción</label>
<textarea class="form-control mb-2" id="descripcion" maxlength="255" required>
</textarea>
<label class="form-label" for="tipo" data-i18n="Tipo">Tipo</label>
<select class="form-select mb-2" id="tipo" required>
<option value="preventiva" data-i18n="preventiva">preventiva</option>
<option value="reactiva" data-i18n="reactiva">reactiva</option>
</select>
<label class="form-label" for="prioridad" data-i18n="Prioridad">Prioridad</label>
<select class="form-select mb-2" id="prioridad" required>
<option value="alta" data-i18n="alta">alta</option>
<option value="media" data-i18n="media">media</option>
<option value="baja" data-i18n="baja">baja</option>
</select>
<label class="form-label" for="fecha_inicio" data-i18n="Fecha de inicio">Fecha de inicio</label>
<input class="form-control mb-2" id="fecha_inicio" type="date" required>
<label class="form-label" for="hora_inicio" data-i18n="Hora de inicio">Hora de inicio</label>
<input class="form-control mb-2" id="hora_inicio" type="time" required>
<label class="form-label" for="fecha_limite" data-i18n="Fecha límite">Fecha límite</label>
<input class="form-control mb-2" id="fecha_limite" type="date" required>
<label class="form-label" for="hora_limite" data-i18n="Hora límite">Hora límite</label>
<input class="form-control mb-2" id="hora_limite" type="time" required>
<label class="form-label" for="id_tecnico" data-i18n="Técnico">Técnico</label>
<select class="form-select mb-2" id="id_tecnico" required>
<option value="">—</option>
</select>
<label class="form-label" for="id_equipo" data-i18n="Equipo">Equipo</label>
<select class="form-select mb-2" id="id_equipo">
<option value="">—</option>
</select>
<label class="form-label" for="estado" data-i18n="Estado">Estado</label>
<select class="form-select mb-2" id="estado" required>
<option value="pendiente" data-i18n="pendiente">pendiente</option>
<option value="en proceso" data-i18n="en proceso">en proceso</option>
<option value="resuelto" data-i18n="resuelto">resuelto</option>
</select>
<label class="form-label" for="nota" data-i18n="Nota">Nota</label>
<textarea class="form-control mb-2" id="nota" maxlength="1000">
</textarea>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button>
<button id="cancelar" type="button" class="btn btn-secondary mt-2" data-i18n="Cancelar">Cancelar</button>
</form>

<label data-i18n="Fecha">Fecha</label>
<input id="filtro_fecha" type="date" class="form-control mb-2">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="id_tarea">id_tarea</th>
<th data-i18n="descripcion">descripcion</th>
<th data-i18n="tipo">tipo</th>
<th data-i18n="fecha_inicio">fecha_inicio</th>
<th data-i18n="hora_inicio">hora_inicio</th>
<th data-i18n="fecha_limite">fecha_limite</th>
<th data-i18n="id_tecnico">id_tecnico</th>
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
<script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/tareas.js"></script></body>
</html>
