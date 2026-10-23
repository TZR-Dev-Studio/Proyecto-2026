async function cargarMetricas() {
    const datos = (await llamarApi('metricas')).data;
    const etiquetas = [{clave: 'usuarios_activos', texto: 'Usuarios activos'}, {clave: 'laboratorios_activos', texto: 'Laboratorios activos'}, {clave: 'equipos_activos', texto: 'Equipos activos'}, {clave: 'equipos_reparacion', texto: 'Equipos en reparación'}, {clave: 'tickets_pendientes', texto: 'Tickets pendientes'}, {clave: 'tickets_en_proceso', texto: 'Tickets en proceso'}, {clave: 'tickets_resueltos', texto: 'Tickets resueltos'}];
    const listado = document.getElementById('listado'); listado.replaceChildren();
    etiquetas.forEach(function(etiqueta) { const tr = document.createElement('tr'); agregarCelda(tr, traducir(etiqueta.texto)); agregarCelda(tr, datos[etiqueta.clave]); listado.appendChild(tr); });
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('actualizar').addEventListener('click', function() { cargarMetricas().catch(function(e) { mostrarMensaje(e.message, true); }); });
    cargarMetricas().catch(function(e) { mostrarMensaje(e.message, true); });
});
