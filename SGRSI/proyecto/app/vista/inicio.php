<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrfToken']); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SGRSI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
</head>
<body><?php require __DIR__ . '/navegacion.php'; ?><main class="container py-4">
<h1>SGRSI</h1>
<p>
<span data-i18n="Bienvenido">Bienvenido</span>, <?php echo htmlspecialchars($_SESSION['usuario']); ?>.</p>
<p data-i18n="Seleccione un módulo en el menú.">Seleccione un módulo en el menú.</p>
</main><script src="assets/js/idioma.js"></script></body>
</html>
