<?php
/**
 * Vista: Mesa de Ayuda (Panel Técnico)
 * Recibe la variable $tickets (arreglo asociativo) desde el controlador
 * cargarTickets.php. No realiza consultas SQL.
 */

function badgeEstado(string $estado): string {
    $clases = [
        "pendiente"   => "bg-warning text-dark",
        "en proceso"  => "bg-primary",
        "resuelto"    => "bg-success",
    ];
    $clase = $clases[$estado] ?? "bg-secondary";
    return '<span class="badge ' . $clase . '">' . htmlspecialchars(ucfirst($estado)) . '</span>';
}

function badgePrioridad(string $prioridad): string {
    $clases = [
        "alta"  => "bg-danger",
        "media" => "bg-warning text-dark",
        "baja"  => "bg-success",
    ];
    $clase = $clases[$prioridad] ?? "bg-secondary";
    return '<span class="badge ' . $clase . '">' . htmlspecialchars(ucfirst($prioridad)) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">SGRSI</a>
            <div class="collapse navbar-collapse">
                <span class="navbar-text text-white me-3">
                    Panel Técnico - <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="cerrarSesion.php">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-4">

        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-4 fw-bold">Mesa de Ayuda</h1>
                <p class="text-muted mb-0">Gestión de tickets de incidencias técnicas</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoTicket">
                + Nuevo ticket
            </button>
        </header>

        <!-- Filtros (interactúan sobre las filas ya cargadas por PHP) -->
        <section class="card mb-4">
            <div class="card-body">
                <form id="formFiltros" class="row g-2">
                    <div class="col-12 col-md-4">
                        <label for="filtroEstado" class="form-label">Estado</label>
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en proceso">En proceso</option>
                            <option value="resuelto">Resuelto</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="filtroPrioridad" class="form-label">Prioridad</label>
                        <select class="form-select" id="filtroPrioridad">
                            <option value="">Todas</option>
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Tabla de tickets: generada desde PHP con los datos de la base de datos -->
        <section class="card">
            <div class="card-header">
                <h2 class="fs-6 fw-bold mb-0">Tickets registrados</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Laboratorio</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Prioridad</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Creado por</th>
                            </tr>
                        </thead>
                        <tbody id="tablaTickets">
                            <?php if (empty($tickets)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        No hay tickets registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr
                                        data-estado="<?php echo htmlspecialchars($ticket["estado"]); ?>"
                                        data-prioridad="<?php echo htmlspecialchars($ticket["prioridad"]); ?>"
                                    >
                                        <td><?php echo htmlspecialchars($ticket["id_ticket"]); ?></td>
                                        <td><?php echo htmlspecialchars($ticket["descripcion"]); ?></td>
                                        <td><?php echo htmlspecialchars($ticket["laboratorio"]); ?></td>
                                        <td><?php echo badgeEstado($ticket["estado"]); ?></td>
                                        <td><?php echo badgePrioridad($ticket["prioridad"]); ?></td>
                                        <td><?php echo htmlspecialchars($ticket["fecha_inicio"]); ?></td>
                                        <td><?php echo htmlspecialchars($ticket["creadoPor"]); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- Modal nuevo ticket (formulario visual; el guardado en base de datos se implementará en una próxima entrega) -->
    <div class="modal fade" id="modalNuevoTicket" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="tituloModal">Nuevo ticket</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoTicket" novalidate>

                        <div class="mb-3">
                            <label for="descripcionTicket" class="form-label">Descripción del problema</label>
                            <textarea
                                class="form-control"
                                id="descripcionTicket"
                                rows="3"
                                placeholder="Describí el problema con el mayor detalle posible"
                                required
                                minlength="10"
                            ></textarea>
                            <div class="invalid-feedback">
                                La descripción debe tener al menos 10 caracteres.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="laboratorioTicket" class="form-label">Laboratorio / Aula</label>
                            <input
                                type="text"
                                class="form-control"
                                id="laboratorioTicket"
                                placeholder="Ej: Lab 3, Aula 5"
                                required
                            >
                            <div class="invalid-feedback">
                                Ingresá el laboratorio o aula.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="prioridadTicket" class="form-label">Prioridad</label>
                            <select class="form-select" id="prioridadTicket" required>
                                <option value="">Seleccioná una prioridad</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                            <div class="invalid-feedback">
                                Seleccioná una prioridad.
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarTicket">Guardar ticket</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-3 mt-4 border-top">
        <small>SGRSI &copy; 2026 - TZR DevStudio - ITI CETP</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/validaciones.js"></script>
    <script src="../../public/assets/js/tickets.js"></script>
</body>
</html>
