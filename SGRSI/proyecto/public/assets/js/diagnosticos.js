const formulario = document.getElementById('formulario');
async function cargar() {
    const respuesta = await llamarApi('diagnosticos'); const filas = document.getElementById('filas'); filas.replaceChildren();
    for (const dato of respuesta.data) {
        const fila = document.createElement('tr');
        for (const campo of ["id_diagnostico", "id_ticket", "id_equipo", "fecha", "hora", "nota", "activo"]) agregarCelda(fila, typeof dato[campo] === 'string' ? traducir(dato[campo]) : dato[campo]);
        if (puedeEditar && Number(dato.activo) !== 0) botonFila(fila, 'Editar', () => editar(dato));
        if (esAdministrador && Number(dato.activo) !== 0) botonFila(fila, 'Dar de baja', () => baja(dato.id_diagnostico));
        filas.appendChild(fila);
    }
}
function editar(dato) {
    formulario.reset(); document.getElementById('registro_id').value = dato.id_diagnostico; document.getElementById('id_ticket').readOnly = true;
    document.getElementById('id_ticket').value = dato.id_ticket ?? '';
    document.getElementById('fecha').value = dato.fecha ?? '';
    document.getElementById('hora').value = dato.hora ?? '';
    document.getElementById('nota').value = dato.nota ?? '';
}
async function baja(id) {
    const motivo = prompt(traducir('Motivo de baja')); if (!motivo) return;
    try { const resultado = await llamarApi('diagnosticos', 'DELETE', {motivo}, id); mostrarMensaje(resultado.message); await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
}
async function historial(id) {
    try { const respuesta = await fetch('api/diagnosticos.php?id=' + encodeURIComponent(id) + '&historial=1', {credentials:'same-origin'}); const json = await respuesta.json(); if (!respuesta.ok || json.status !== 'success') throw new Error(json.message); document.getElementById('historial').textContent = JSON.stringify(json.data, null, 2); } catch (error) { mostrarMensaje(error.message, true); }
}
formulario.addEventListener('submit', async evento => {
    evento.preventDefault(); const datos = {};
    datos.id_ticket = document.getElementById('id_ticket').value === '' ? null : Number(document.getElementById('id_ticket').value);
    datos.fecha = document.getElementById('fecha').value;
    datos.hora = document.getElementById('hora').value;
    datos.nota = document.getElementById('nota').value;
    const id = document.getElementById('registro_id').value;
    try { const resultado = await llamarApi('diagnosticos', id ? 'PUT' : 'POST', datos, id || null); mostrarMensaje(resultado.message); formulario.reset(); document.getElementById('registro_id').value = ''; document.getElementById('id_ticket').readOnly = false; await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
});
document.getElementById('cancelar').addEventListener('click', () => { formulario.reset(); document.getElementById('registro_id').value = ''; document.getElementById('id_ticket').readOnly = false; });
async function iniciar() {
    try { const catalogos = (await llamarApi('catalogos')).data;
        document.getElementById('hora').value = catalogos.reloj.hora;
        document.getElementById('fecha').value = catalogos.reloj.fecha;
        await cargar();
    } catch (error) { mostrarMensaje(error.message, true); }
}
iniciar();
