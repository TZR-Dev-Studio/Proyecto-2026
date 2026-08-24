<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI - Seleccionar Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <main class="container mt-4">
        <h1>Seleccioná un panel</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?></p>

        <ul class="list-group" style="max-width: 400px;">
            <?php if ($_SESSION["administrador"]): ?>
            <li class="list-group-item">
                <a href="administrador.php">Panel Administrador</a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION["tecnico"]): ?>
            <li class="list-group-item">
                <a href="tecnico.php">Panel Técnico</a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION["solicitante"]): ?>
            <li class="list-group-item">
                <a href="solicitante.php">Panel Solicitante</a>
            </li>
            <?php endif; ?>
        </ul>
    </main>
</body>
</html>