<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI - Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Inventario">Inventario</h1>
<div id="mensaje" role="status" aria-live="polite">
</div>
<?php if ($_SESSION['administrador']): ?>
<form id="formulario" class="card card-body mb-4">
<input type="hidden" id="registro_id">
<label class="form-label" for="codigo_inventario" data-i18n="Código">Código</label>
<input class="form-control mb-3" id="codigo_inventario" type="text" maxlength="30" required>
<label class="form-label" for="descripcion" data-i18n="Descripción">Descripción</label>
<input class="form-control mb-3" id="descripcion" type="text" maxlength="200" required>
<label class="form-label" for="tipo" data-i18n="Tipo">Tipo</label>
<input class="form-control mb-3" id="tipo" type="text" maxlength="40" required>
<label class="form-label" for="marca" data-i18n="Marca">Marca</label>
<input class="form-control mb-3" id="marca" type="text" maxlength="80" required>
<label class="form-label" for="condicion" data-i18n="Condición">Condición</label>
<select class="form-select mb-3" id="condicion" required>
<option value="operativo" data-i18n="operativo">operativo</option>
<option value="reparacion" data-i18n="reparacion">reparacion</option>
<option value="baja" data-i18n="Retirado">Retirado</option>
</select>
<label class="form-label" for="id_laboratorio" data-i18n="Laboratorio">Laboratorio</label>
<select class="form-select mb-3" id="id_laboratorio" required>
</select>
<label for="activo" data-i18n="Activo">Activo</label>
<select id="activo" class="form-select mb-3">
<option value="1" data-i18n="Sí">Sí</option>
<option value="0" data-i18n="No">No</option>
</select>
<div>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button> <button type="button" class="btn btn-secondary" id="nuevo" data-i18n="Nuevo">Nuevo</button>
</div>
</form><?php endif; ?>
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>ID</th>
<th data-i18n="Código">Código</th>
<th data-i18n="Descripción">Descripción</th>
<th data-i18n="Tipo">Tipo</th>
<th data-i18n="Marca">Marca</th>
<th data-i18n="Condición">Condición</th>
<th data-i18n="Laboratorio">Laboratorio</th>
<th data-i18n="Activo">Activo</th>
<th data-i18n="Disponible">Disponible</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="listado">
</tbody>
</table>
</div>
</main><script src="assets/js/api.js"></script><script src="assets/js/idioma.js"></script><script src="assets/js/equipos.js"></script></body>
</html>
