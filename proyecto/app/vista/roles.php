<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI - Roles</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Roles">Roles</h1>
<div id="mensaje" role="status" aria-live="polite">
</div>
<?php if ($_SESSION['administrador']): ?>
<form id="formulario" class="card card-body mb-4">
<input type="hidden" id="registro_id">
<label class="form-label" for="nombre_rol" data-i18n="Rol">Rol</label>
<select class="form-select mb-3" id="nombre_rol" required>
<option value="administrador" data-i18n="administrador">administrador</option>
<option value="tecnico" data-i18n="tecnico">tecnico</option>
<option value="solicitante" data-i18n="solicitante">solicitante</option>
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
<th data-i18n="Rol">Rol</th>
<th data-i18n="Activo">Activo</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="listado">
</tbody>
</table>
</div>
</main><script src="assets/js/api.js"></script><script src="assets/js/idioma.js"></script><script src="assets/js/roles.js"></script></body>
</html>
