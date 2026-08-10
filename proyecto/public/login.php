<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">

    <main class="container d-flex justify-content-center align-items-center min-vh-100">
        <section class="card shadow p-4" style="width: 100%; max-width: 400px;">

            <header class="text-center mb-4">
                <img src="assets/img/logo.png" alt="Logo TZR DevStudio" height="60" onerror="this.style.display='none'">
                <h1 class="mt-2 fw-bold fs-3">SGRSI</h1>
                <p class="text-muted">Sistema de Gestión de Recursos y Soporte de Informática</p>
            </header>

            <form id="formLogin" action="procesarLogin.php" method="post" novalidate>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input
                        type="text"
                        class="form-control"
                        id="usuario"
                        name="usuario"
                        placeholder="Ingresá tu usuario"
                        required
                        minlength="3"
                    >
                    <div class="invalid-feedback">
                        El usuario debe tener al menos 3 caracteres.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Ingresá tu contraseña"
                        required
                        minlength="6"
                    >
                    <div class="invalid-feedback">
                        La contraseña debe tener al menos 6 caracteres.
                    </div>
                </div>

                <?php if (!empty($_GET["error"])): ?>
                <div class="mb-3">
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($_GET["error"]); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($_GET["mensaje"])): ?>
<div class="mb-3">
    <div class="alert alert-success" role="alert">
        <?php echo htmlspecialchars($_GET["mensaje"]); ?>
    </div>
</div>
<?php endif; ?>

                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>

        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
</body>
</html>