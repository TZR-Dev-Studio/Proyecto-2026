<?php
// Ejecutar fuera de la raíz pública. Imprime SQL; no conecta ni modifica bases.
// Valores fijos y ficticios. Técnica password_hash: AltaDatosUsuario del profesor.
echo "USE sgrsi;\n";
echo "INSERT INTO ROL (nombre_rol) VALUES ('administrador'), ('tecnico'), ('solicitante');\n";
$usuarios = [
 ['Administrador Prueba', 'admin', 'PruebaAdmin2026!', 'admin@example.invalid'],
 ['Tecnico Prueba', 'tecnico', 'PruebaTecnico2026!', 'tecnico@example.invalid'],
 ['Solicitante Prueba', 'solicitante', 'PruebaSolicitud2026!', 'solicitante@example.invalid']
];
foreach ($usuarios as $u) {
    $hash = password_hash($u[2], PASSWORD_DEFAULT);
    echo "INSERT INTO USUARIO (nombre_completo, nombre_usuario, password, email) VALUES ('" . $u[0] . "', '" . $u[1] . "', '" . $hash . "', '" . $u[3] . "');\n";
}
echo "INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES (1,1),(2,2),(3,3);\n";
echo "INSERT INTO LABORATORIO (nombre, ubicacion) VALUES ('Laboratorio Prueba 1','Planta baja ficticia'),('Laboratorio Prueba 2','Planta alta ficticia');\n";
echo "INSERT INTO EQUIPO (codigo_inventario, descripcion, tipo, marca, condicion, id_laboratorio) VALUES ('PRUEBA-001','Equipo ficticio de escritorio','PC','Marca ficticia','operativo',1),('PRUEBA-002','Proyector ficticio','Proyector','Marca ficticia','reparacion',2);\n";
echo "INSERT INTO TICKET (descripcion, laboratorio, estado, prioridad, fecha_inicio, id_usuario) VALUES ('Equipo ficticio no enciende','Laboratorio Prueba 1','pendiente','alta','2026-10-06',3);\n";
