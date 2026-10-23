<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI - Asociaciones</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?>
<main class="container py-4">
<h1 data-i18n="Asociaciones">Asociaciones</h1>
<div id="mensaje" role="status" aria-live="polite">
</div>
<form id="formulario" class="card card-body mb-4">
<label for="id_usuario" data-i18n="Usuario">Usuario</label>
<select class="form-select mb-3" id="id_usuario" required>
</select>
<label for="id_rol" data-i18n="Rol">Rol</label>
<select class="form-select mb-3" id="id_rol" required>
</select>
<button class="btn btn-primary" data-i18n="Asignar">Asignar</button>
</form>
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th data-i18n="Usuario">Usuario</th>
<th data-i18n="Rol">Rol</th>
<th data-i18n="Activo">Activo</th>
<th data-i18n="Acciones">Acciones</th>
</tr>
</thead>
<tbody id="listado">
</tbody>
</table>
</div>
</main><script src="assets/js/api.js"></script><script src="assets/js/idioma.js"></script><script src="assets/js/usuario_roles.js"></script></body>
</html>
