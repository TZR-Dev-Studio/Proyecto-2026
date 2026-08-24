<?php
/**
 * Vista: Gestión de Usuarios (Panel Administrador)
 * Los usuarios se obtienen y se registran desde el frontend mediante fetch()
 * contra proyecto/public/api/usuarios.php. Esta vista no consulta la base de datos.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">SGRSI</a>
            <div class="collapse navbar-collapse">
                <span class="navbar-text text-white me-3">
                    Panel Administrador - <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="cerrarSesion.php">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-4">

        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-4 fw-bold">Gestión de Usuarios</h1>
                <p class="text-muted mb-0">Solo el administrador puede registrar usuarios</p>
            </div>
            <button class="btn btn-primary" id="btnNuevoUsuario">
                + Nuevo usuario
            </button>
        </header>

        <div id="alertaUsuarios"></div>

        <section class="card">
            <div class="card-header">
                <h2 class="fs-6 fw-bold mb-0">Usuarios del sistema</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Nombre completo</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Email</th>
                            </tr>
                        </thead>
                        <tbody id="tablaUsuarios">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Cargando usuarios...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- Modal nuevo usuario (registra en la base de datos mediante fetch a api/usuarios.php) -->
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="tituloModalUsuario" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="tituloModalUsuario">Nuevo usuario</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoUsuario" novalidate>

                        <div class="mb-3">
                            <label for="nombreCompleto" class="form-label">Nombre completo</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombreCompleto"
                                placeholder="Ej: Juan González"
                                required
                            >
                            <div class="invalid-feedback">Ingresá el nombre completo.</div>
                        </div>

                        <div class="mb-3">
                            <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombreUsuario"
                                placeholder="Ej: jgonzalez"
                                required
                                minlength="3"
                            >
                            <div class="invalid-feedback">El usuario debe tener al menos 3 caracteres.</div>
                        </div>

                        <div class="mb-3">
                            <label for="emailUsuario" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="emailUsuario"
                                placeholder="Ej: jgonzalez@iti.edu.uy"
                                required
                            >
                            <div class="invalid-feedback">Ingresá un email válido.</div>
                        </div>

                        <div class="mb-3">
                            <label for="rolUsuario" class="form-label">Rol</label>
                            <select class="form-select" id="rolUsuario" required>
                                <option value="">Seleccioná un rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="tecnico">Técnico / Soporte</option>
                                <option value="solicitante">Solicitante (Docente/Funcionario)</option>
                            </select>
                            <div class="invalid-feedback">Seleccioná un rol.</div>
                        </div>

                        <div class="mb-3">
                            <label for="passwordUsuario" class="form-label">Contraseña</label>
                            <input
                                type="password"
                                class="form-control"
                                id="passwordUsuario"
                                required
                                minlength="6"
                            >
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar usuario</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-3 mt-4 border-top">
        <small>SGRSI &copy; 2026 - TZR DevStudio - ITI CETP</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="assets/js/usuarios.js"></script>
</body>
</html>
