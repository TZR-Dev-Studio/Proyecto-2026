const formulario = document.getElementById('formulario');
async function cargar() {
    const respuesta = await llamarApi('usos'); const filas = document.getElementById('filas'); filas.replaceChildren();
    for (const dato of respuesta.data) {
        const fila = document.createElement('tr');
        for (const campo of ["id_uso", "id_laboratorio", "id_responsable", "grupo", "actividad", "fecha", "hora_inicio", "hora_fin", "activo"]) agregarCelda(fila, typeof dato[campo] === 'string' ? traducir(dato[campo]) : dato[campo]);
        if (puedeEditar && Number(dato.activo) !== 0) botonFila(fila, 'Editar', () => editar(dato));
        if (esAdministrador && Number(dato.activo) !== 0) botonFila(fila, 'Dar de baja', () => baja(dato.id_uso));
        botonFila(fila, 'Historial', () => historial(dato.id_uso));
        filas.appendChild(fila);
    }
}
function editar(dato) {
    formulario.reset(); document.getElementById('registro_id').value = dato.id_uso;
    document.getElementById('id_laboratorio').value = dato.id_laboratorio ?? '';
    if (dato.id_laboratorio && document.getElementById('id_laboratorio').value === '') { const o = document.createElement('option'); o.value = dato.id_laboratorio; o.textContent = dato.id_laboratorio + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_laboratorio').appendChild(o); document.getElementById('id_laboratorio').value = dato.id_laboratorio; }
    document.getElementById('id_responsable').value = dato.id_responsable ?? '';
    if (dato.id_responsable && document.getElementById('id_responsable').value === '') { const o = document.createElement('option'); o.value = dato.id_responsable; o.textContent = dato.id_responsable + ' (' + traducir('Inactivo') + ')'; document.getElementById('id_responsable').appendChild(o); document.getElementById('id_responsable').value = dato.id_responsable; }
    document.getElementById('grupo').value = dato.grupo ?? '';
    document.getElementById('actividad').value = dato.actividad ?? '';
    document.getElementById('fecha').value = dato.fecha ?? '';
    document.getElementById('hora_inicio').value = dato.hora_inicio ?? '';
    document.getElementById('hora_fin').value = dato.hora_fin ?? '';
}
async function baja(id) {
    const motivo = prompt(traducir('Motivo de baja')); if (!motivo) return;
    try { const resultado = await llamarApi('usos', 'DELETE', {motivo}, id); mostrarMensaje(resultado.message); await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
}
async function historial(id) {
    try { const respuesta = await fetch('api/usos.php?id=' + encodeURIComponent(id) + '&historial=1', {credentials:'same-origin'}); const json = await respuesta.json(); if (!respuesta.ok || json.status !== 'success') throw new Error(json.message); const panel = document.getElementById('historial'); panel.replaceChildren(); for (const evento of json.data) { const bloque = document.createElement('div'); bloque.className = 'card card-body mb-2'; for (const campo of ['fecha', 'hora', 'id_actor', 'accion']) { if (evento[campo] !== undefined) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + evento[campo]; bloque.appendChild(linea); } } const antes = JSON.parse(evento.detalle); for (const campo in antes) { const linea = document.createElement('p'); linea.textContent = traducir(campo) + ': ' + (antes[campo] ?? ''); bloque.appendChild(linea); } panel.appendChild(bloque); } } catch (error) { mostrarMensaje(error.message, true); }
}
formulario.addEventListener('submit', async evento => {
    evento.preventDefault(); const datos = {};
    datos.id_laboratorio = document.getElementById('id_laboratorio').value === '' ? null : Number(document.getElementById('id_laboratorio').value);
    datos.id_responsable = document.getElementById('id_responsable').value === '' ? null : Number(document.getElementById('id_responsable').value);
    datos.grupo = document.getElementById('grupo').value;
    datos.actividad = document.getElementById('actividad').value;
    datos.fecha = document.getElementById('fecha').value;
    datos.hora_inicio = document.getElementById('hora_inicio').value;
    datos.hora_fin = document.getElementById('hora_fin').value;
    const id = document.getElementById('registro_id').value;
    try { const resultado = await llamarApi('usos', id ? 'PUT' : 'POST', datos, id || null); mostrarMensaje(resultado.message); formulario.reset(); document.getElementById('registro_id').value = ''; await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
});
document.getElementById('cancelar').addEventListener('click', () => { formulario.reset(); document.getElementById('registro_id').value = ''; });
async function iniciar() {
    try { const catalogos = (await llamarApi('catalogos')).data;
    for (const dato of catalogos.laboratorios) { const o = document.createElement('option'); o.value = dato.id_laboratorio; o.textContent = dato.nombre; document.getElementById('id_laboratorio').appendChild(o); }
    for (const dato of catalogos.usuarios) { const o = document.createElement('option'); o.value = dato.id_usuario; o.textContent = dato.nombre_completo; document.getElementById('id_responsable').appendChild(o); }
        await cargar();
    } catch (error) { mostrarMensaje(error.message, true); }
}
iniciar();
