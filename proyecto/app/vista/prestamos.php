<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<title>SGRSI - Préstamos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Préstamos y devoluciones">Préstamos y devoluciones</h1>
<div id="mensaje" role="status">
</div>
<?php if ($_SESSION['administrador'] || $_SESSION['tecnico']): ?>
<form id="formulario" class="card card-body mb-3">
<label data-i18n="Equipo">Equipo</label>
<select id="id_equipo" class="form-select" required>
</select>
<label data-i18n="Responsable">Responsable</label>
<select id="id_responsable" class="form-select" required>
</select>
<label data-i18n="Documento">Documento</label>
<input id="documento" class="form-control" maxlength="30" required>
<label data-i18n="Grupo">Grupo</label>
<input id="grupo" class="form-control" maxlength="50" required>
<label data-i18n="Fecha de inicio">Fecha de inicio</label>
<input id="fecha_inicio" type="date" class="form-control" readonly>
<label data-i18n="Hora de inicio">Hora de inicio</label>
<input id="hora_inicio" type="time" class="form-control" readonly>
<label data-i18n="Fecha límite">Fecha límite</label>
<input id="fecha_limite" type="date" class="form-control" required>
<label data-i18n="Hora límite">Hora límite</label>
<input id="hora_limite" type="time" class="form-control" required>
<label data-i18n="Nota">Nota</label>
<textarea id="nota" class="form-control" maxlength="1000">
</textarea>
<button class="btn btn-primary mt-2" data-i18n="Registrar préstamo">Registrar préstamo</button>
</form>
<form id="devolucion" class="card card-body mb-3" hidden>
<h2 data-i18n="Registrar devolución">Registrar devolución</h2>
<input id="prestamo_id" type="hidden">
<label data-i18n="Fecha">Fecha</label>
<input id="fecha" type="date" class="form-control" readonly>
<label data-i18n="Hora">Hora</label>
<input id="hora" type="time" class="form-control" readonly>
<label data-i18n="Condición">Condición</label>
<select id="condicion" class="form-select">
<option value="operativo" data-i18n="operativo">operativo</option>
<option value="reparacion" data-i18n="reparacion">reparacion</option>
<option value="baja" data-i18n="Retirado">Retirado</option>
</select>
<label data-i18n="Nota">Nota</label>
<textarea id="nota_devolucion" class="form-control" maxlength="1000">
</textarea>
<button class="btn btn-primary mt-2" data-i18n="Guardar">Guardar</button>
</form>
<?php endif; ?>
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>ID</th>
<th data-i18n="Equipo">Equipo</th>
<th data-i18n="Responsable">Responsable</th>
<th data-i18n="Inicio">Inicio</th>
<th data-i18n="Límite">Límite</th>
<th data-i18n="Devolución">Devolución</th>
<th data-i18n="Estado">Estado</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="filas">
</tbody>
</table>
</div>
</main>
<script>const puedeEditar = <?php echo ($_SESSION['administrador'] || $_SESSION['tecnico']) ? 'true' : 'false'; ?>; const esAdministrador = <?php echo $_SESSION['administrador'] ? 'true' : 'false'; ?>;</script>
<script src="assets/js/idioma.js"></script><script src="assets/js/api.js"></script><script src="assets/js/prestamos.js"></script></body>
</html>
