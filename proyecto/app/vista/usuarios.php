<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI - Usuarios</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?><main class="container py-4">
<h1 data-i18n="Usuarios">Usuarios</h1>
<div id="mensaje" role="status" aria-live="polite">
</div>
<form id="formulario" class="card card-body mb-4">
<input type="hidden" id="registro_id">
<label for="nombreUsuario" data-i18n="Usuario">Usuario</label>
<input class="form-control mb-3" id="nombreUsuario" minlength="3" maxlength="50" required>
<label for="nombreCompleto" data-i18n="Nombre completo">Nombre completo</label>
<input class="form-control mb-3" id="nombreCompleto" maxlength="100" required>
<label for="email" data-i18n="Email">Email</label>
<input class="form-control mb-3" id="email" type="email" maxlength="100" required>
<fieldset class="mb-3">
<legend class="fs-6" data-i18n="Roles">Roles</legend>
<label>
<input type="checkbox" name="roles" value="administrador"> <span data-i18n="administrador">Administrador</span>
</label>
<label>
<input type="checkbox" name="roles" value="tecnico"> <span data-i18n="tecnico">Técnico</span>
</label>
<label>
<input type="checkbox" name="roles" value="solicitante"> <span data-i18n="solicitante">Solicitante</span>
</label>
</fieldset>
<label for="password" data-i18n="Contraseña">Contraseña</label>
<input class="form-control" id="password" type="password" minlength="6" maxlength="72" required autocomplete="new-password">
<p data-i18n="Vacía al editar conserva la contraseña.">Vacía al editar conserva la contraseña.</p>
<label for="activo" data-i18n="Activo">Activo</label>
<select class="form-select mb-3" id="activo">
<option value="1" data-i18n="Sí">Sí</option>
<option value="0" data-i18n="No">No</option>
</select>
<div>
<button class="btn btn-primary" data-i18n="Guardar">Guardar</button> <button type="button" class="btn btn-secondary" id="nuevo" data-i18n="Nuevo">Nuevo</button>
</div>
</form>
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>ID</th>
<th data-i18n="Usuario">Usuario</th>
<th data-i18n="Nombre completo">Nombre completo</th>
<th data-i18n="Email">Email</th>
<th data-i18n="Roles">Roles</th>
<th data-i18n="Activo">Activo</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="listado">
</tbody>
</table>
</div>
</main><script src="assets/js/api.js"></script><script src="assets/js/idioma.js"></script><script src="assets/js/usuarios.js"></script></body>
</html>
