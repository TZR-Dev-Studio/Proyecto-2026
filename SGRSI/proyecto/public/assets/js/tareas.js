const formulario = document.getElementById('formulario');
async function cargar() {
    const respuesta = await llamarApi('tareas'); const filas = document.getElementById('filas'); filas.replaceChildren();
    for (const dato of respuesta.data) {
        const fechaFiltro = document.getElementById('filtro_fecha').value; if (fechaFiltro && dato.fecha_inicio !== fechaFiltro) continue;
        const fila = document.createElement('tr'); fila.className = dato.prioridad === 'alta' ? 'table-danger' : dato.prioridad === 'media' ? 'table-warning' : 'table-success';
        for (const campo of ["id_tarea", "descripcion", "tipo", "fecha_inicio", "hora_inicio", "fecha_limite", "id_tecnico", "estado", "activo"]) agregarCelda(fila, typeof dato[campo] === 'string' ? traducir(dato[campo]) : dato[campo]);
        if (puedeEditar && Number(dato.activo) !== 0) botonFila(fila, 'Editar', () => editar(dato));
        if (esAdministrador && Number(dato.activo) !== 0) botonFila(fila, 'Dar de baja', () => baja(dato.id_tarea));
        botonFila(fila, 'Historial', () => historial(dato.id_tarea));
        filas.appendChild(fila);
    }
}
function editar(dato) {
    formulario.reset(); document.getElementById('registro_id').value = dato.id_tarea;
    document.getElementById('descripcion').value = dato.descripcion ?? '';
    document.getElementById('tipo').value = dato.tipo ?? '';
    document.getElementById('prioridad').value = dato.prioridad ?? '';
    document.getElementById('fecha_inicio').value = dato.fecha_inicio ?? '';
    document.getElementById('hora_inicio').value = dato.hora_inicio ?? '';
    document.getElementById('fecha_limite').value = dato.fecha_limite ?? '';
    document.getElementById('hora_limite').value = dato.hora_limite ?? '';
    document.getElementById('id_tecnico').value = dato.id_tecnico ?? '';
    if (dato.id_tecnico && document.getElementById('id_tecnico').value === '') { const o = document.createElement('option'); o.value = dato.id_tecnico; o.textContent = dato.id_tecnico + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_tecnico').appendChild(o); document.getElementById('id_tecnico').value = dato.id_tecnico; }
    document.getElementById('id_equipo').value = dato.id_equipo ?? '';
    if (dato.id_equipo && document.getElementById('id_equipo').value === '') { const o = document.createElement('option'); o.value = dato.id_equipo; o.textContent = dato.id_equipo + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_equipo').appendChild(o); document.getElementById('id_equipo').value = dato.id_equipo; }
    document.getElementById('estado').value = dato.estado ?? '';
    document.getElementById('nota').value = dato.nota ?? '';
}
async function baja(id) {
    const motivo = prompt(traducir('Motivo de baja')); if (!motivo) return;
    try { const resultado = await llamarApi('tareas', 'DELETE', {motivo}, id); mostrarMensaje(resultado.message); await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
}
async function historial(id) {
    try { const respuesta = await fetch('api/tareas.php?id=' + encodeURIComponent(id) + '&historial=1', {credentials:'same-origin'}); const json = await respuesta.json(); if (!respuesta.ok || json.status !== 'success') throw new Error(json.message); const panel = document.getElementById('historial'); panel.replaceChildren(); for (const evento of json.data) { const bloque = document.createElement('div'); bloque.className = 'card card-body mb-2'; for (const campo of ['fecha', 'hora', 'id_actor', 'accion']) { if (evento[campo] !== undefined) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + evento[campo]; bloque.appendChild(linea); } } const antes = JSON.parse(evento.detalle); for (const campo in antes) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + (antes[campo] ?? ''); bloque.appendChild(linea); } panel.appendChild(bloque); } } catch (error) { mostrarMensaje(error.message, true); }
}
formulario.addEventListener('submit', async evento => {
    evento.preventDefault(); const datos = {};
    datos.descripcion = document.getElementById('descripcion').value;
    datos.tipo = document.getElementById('tipo').value;
    datos.prioridad = document.getElementById('prioridad').value;
    datos.fecha_inicio = document.getElementById('fecha_inicio').value;
    datos.hora_inicio = document.getElementById('hora_inicio').value;
    datos.fecha_limite = document.getElementById('fecha_limite').value;
    datos.hora_limite = document.getElementById('hora_limite').value;
    datos.id_tecnico = document.getElementById('id_tecnico').value === '' ? null : Number(document.getElementById('id_tecnico').value);
    datos.id_equipo = document.getElementById('id_equipo').value === '' ? null : Number(document.getElementById('id_equipo').value);
    datos.estado = document.getElementById('estado').value;
    datos.nota = document.getElementById('nota').value;
    const id = document.getElementById('registro_id').value;
    try { const resultado = await llamarApi('tareas', id ? 'PUT' : 'POST', datos, id || null); mostrarMensaje(resultado.message); formulario.reset(); document.getElementById('registro_id').value = ''; await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
});
document.getElementById('cancelar').addEventListener('click', () => { formulario.reset(); document.getElementById('registro_id').value = ''; });
async function iniciar() {
    try { const catalogos = (await llamarApi('catalogos')).data;
    for (const dato of catalogos.tecnicos) { const o = document.createElement('option'); o.value = dato.id_usuario; o.textContent = dato.nombre_completo; document.getElementById('id_tecnico').appendChild(o); }
    for (const dato of catalogos.equipos) { const o = document.createElement('option'); o.value = dato.id_equipo; o.textContent = dato.codigo_inventario; document.getElementById('id_equipo').appendChild(o); }
        await cargar();
    } catch (error) { mostrarMensaje(error.message, true); }
}
iniciar();

document.getElementById('filtro_fecha').addEventListener('change', () => cargar().catch(e => mostrarMensaje(e.message, true)));
