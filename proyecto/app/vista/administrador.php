<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <main class="container mt-4">
        <h1>Panel Administrador</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?></p>
        <a class="btn btn-primary" href="usuarios.php">Gestionar usuarios</a>
    </main>
</body>
</html>