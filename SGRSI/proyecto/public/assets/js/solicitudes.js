const formulario = document.getElementById('formulario');
async function cargar() {
    const respuesta = await llamarApi('solicitudes'); const filas = document.getElementById('filas'); filas.replaceChildren();
    for (const dato of respuesta.data) {
        const fila = document.createElement('tr'); const prioridad = dato.prioridad || dato.urgencia; fila.className = prioridad === 'alta' ? 'table-danger' : prioridad === 'media' ? 'table-warning' : 'table-success';
        for (const campo of ["id_solicitud", "tipo", "descripcion", "laboratorio", "fecha_solicitada", "estado", "activo"]) agregarCelda(fila, typeof dato[campo] === 'string' ? traducir(dato[campo]) : dato[campo]);
        if (puedeEditar && Number(dato.activo) !== 0) botonFila(fila, 'Editar', () => editar(dato));
        if (esAdministrador && Number(dato.activo) !== 0) botonFila(fila, 'Dar de baja', () => baja(dato.id_solicitud));
        botonFila(fila, 'Historial', () => historial(dato.id_solicitud));
        filas.appendChild(fila);
    }
}
function editar(dato) {
    formulario.reset(); document.getElementById('registro_id').value = dato.id_solicitud;
    document.getElementById('tipo').value = dato.tipo ?? '';
    document.getElementById('descripcion').value = dato.descripcion ?? '';
    document.getElementById('id_laboratorio').value = dato.id_laboratorio ?? '';
    if (dato.id_laboratorio && document.getElementById('id_laboratorio').value === '') { const o = document.createElement('option'); o.value = dato.id_laboratorio; o.textContent = dato.id_laboratorio + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_laboratorio').appendChild(o); document.getElementById('id_laboratorio').value = dato.id_laboratorio; }
    document.getElementById('fecha_solicitada').value = dato.fecha_solicitada ?? '';
    document.getElementById('hora_solicitada').value = dato.hora_solicitada ?? '';
    document.getElementById('urgencia').value = dato.urgencia ?? '';
    document.getElementById('estado').value = dato.estado ?? '';
    document.getElementById('id_tecnico').value = dato.id_tecnico ?? '';
    if (dato.id_tecnico && document.getElementById('id_tecnico').value === '') { const o = document.createElement('option'); o.value = dato.id_tecnico; o.textContent = dato.id_tecnico + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_tecnico').appendChild(o); document.getElementById('id_tecnico').value = dato.id_tecnico; }
    document.getElementById('nota_resolucion').value = dato.nota_resolucion ?? '';
}
async function baja(id) {
    const motivo = prompt(traducir('Motivo de baja')); if (!motivo) return;
    try { const resultado = await llamarApi('solicitudes', 'DELETE', {motivo}, id); mostrarMensaje(resultado.message); await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
}
async function historial(id) {
    try { const respuesta = await fetch('api/solicitudes.php?id=' + encodeURIComponent(id) + '&historial=1', {credentials:'same-origin'}); const json = await respuesta.json(); if (!respuesta.ok || json.status !== 'success') throw new Error(json.message); const panel = document.getElementById('historial'); panel.replaceChildren(); for (const evento of json.data) { const bloque = document.createElement('div'); bloque.className = 'card card-body mb-2'; for (const campo of ['fecha', 'hora', 'id_actor', 'accion']) { if (evento[campo] !== undefined) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + evento[campo]; bloque.appendChild(linea); } } const antes = JSON.parse(evento.detalle); for (const campo in antes) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + (antes[campo] ?? ''); bloque.appendChild(linea); } panel.appendChild(bloque); } } catch (error) { mostrarMensaje(error.message, true); }
}
formulario.addEventListener('submit', async evento => {
    evento.preventDefault(); const datos = {};
    datos.tipo = document.getElementById('tipo').value;
    datos.descripcion = document.getElementById('descripcion').value;
    datos.id_laboratorio = document.getElementById('id_laboratorio').value === '' ? null : Number(document.getElementById('id_laboratorio').value);
    datos.fecha_solicitada = document.getElementById('fecha_solicitada').value;
    datos.hora_solicitada = document.getElementById('hora_solicitada').value;
    datos.urgencia = document.getElementById('urgencia').value;
    datos.estado = document.getElementById('estado').value;
    datos.id_tecnico = document.getElementById('id_tecnico').value === '' ? null : Number(document.getElementById('id_tecnico').value);
    datos.nota_resolucion = document.getElementById('nota_resolucion').value;
    const id = document.getElementById('registro_id').value;
    try { const resultado = await llamarApi('solicitudes', id ? 'PUT' : 'POST', datos, id || null); mostrarMensaje(resultado.message); formulario.reset(); document.getElementById('registro_id').value = ''; await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
});
document.getElementById('cancelar').addEventListener('click', () => { formulario.reset(); document.getElementById('registro_id').value = ''; });
async function iniciar() {
    try { const catalogos = (await llamarApi('catalogos')).data;
    for (const dato of catalogos.laboratorios) { const o = document.createElement('option'); o.value = dato.id_laboratorio; o.textContent = dato.nombre; document.getElementById('id_laboratorio').appendChild(o); }
    for (const dato of catalogos.tecnicos) { const o = document.createElement('option'); o.value = dato.id_usuario; o.textContent = dato.nombre_completo; document.getElementById('id_tecnico').appendChild(o); }
    if (!puedeEditar) { document.getElementById('estado').disabled = true; document.getElementById('id_tecnico').disabled = true; document.getElementById('nota_resolucion').disabled = true; }
        await cargar();
    } catch (error) { mostrarMensaje(error.message, true); }
}
iniciar();
