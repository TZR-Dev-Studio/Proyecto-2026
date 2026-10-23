<?php
require_once __DIR__ . '/../nucleo/Token.php';
Token::generar();
?>
<nav class="navbar navbar-dark bg-primary p-3 gap-3">
<a class="navbar-brand" href="inicio.php">SGRSI</a>
<a class="text-white" href="tickets.php" data-i18n="Tickets">Tickets</a>
<a class="text-white" href="solicitudes.php" data-i18n="Solicitudes">Solicitudes</a>
<a class="text-white" href="prestamos.php" data-i18n="Préstamos">Préstamos</a>
<?php if ($_SESSION['administrador'] || $_SESSION['tecnico']): ?>
<a class="text-white" href="diagnosticos.php" data-i18n="Diagnósticos">Diagnósticos</a>
<a class="text-white" href="tareas.php" data-i18n="Agenda">Agenda</a>
<a class="text-white" href="usos.php" data-i18n="Uso de laboratorios">Uso de laboratorios</a>
<a class="text-white" href="alertas.php" data-i18n="Alertas">Alertas</a>
<a class="text-white" href="laboratorios.php" data-i18n="Laboratorios">Laboratorios</a>
<a class="text-white" href="equipos.php" data-i18n="Inventario">Inventario</a>
<?php endif; ?>
<?php if ($_SESSION['administrador']): ?>
<a class="text-white" href="usuarios.php" data-i18n="Usuarios">Usuarios</a>
<a class="text-white" href="roles.php" data-i18n="Roles">Roles</a>
<a class="text-white" href="usuario_roles.php" data-i18n="Asociaciones">Asociaciones</a>
<a class="text-white" href="reportes.php" data-i18n="Reportes">Reportes</a>
<a class="text-white" href="metricas.php" data-i18n="Indicadores">Indicadores</a>
<?php endif; ?>
<label class="text-white"><span data-i18n="Idioma">Idioma</span>
<select id="idioma" aria-label="Idioma"><option value="es">Español</option><option value="en">English</option></select></label>
<a class="text-white" href="cerrarSesion.php" data-i18n="Cerrar sesión">Cerrar sesión</a>
</nav>
