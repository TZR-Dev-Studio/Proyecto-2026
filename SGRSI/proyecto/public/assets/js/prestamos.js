async function cargar() {
    const respuesta = await llamarApi('prestamos'); const tabla = document.getElementById('filas'); tabla.replaceChildren();
    for (const d of respuesta.data) {
        const fila = document.createElement('tr');
        for (const valor of [d.id_prestamo, d.codigo_inventario, d.responsable, d.fecha_inicio + ' ' + d.hora_inicio, d.fecha_limite + ' ' + d.hora_limite, d.fecha_devolucion ? d.fecha_devolucion + ' ' + d.hora_devolucion : '', traducir(d.estado)]) agregarCelda(fila, valor);
        if (d.estado === 'abierto' && puedeEditar) botonFila(fila, 'Registrar devolución', () => { document.getElementById('devolucion').hidden = false; document.getElementById('prestamo_id').value = d.id_prestamo; });
        if (d.estado === 'abierto' && esAdministrador) botonFila(fila, 'Anular', () => anular(d.id_prestamo));
        tabla.appendChild(fila);
    }
}
async function anular(id) {
    const motivo = prompt(traducir('Motivo de baja')); if (!motivo) return;
    try { const r = await llamarApi('prestamos', 'DELETE', {motivo}, id); mostrarMensaje(r.message); await cargar(); } catch (e) { mostrarMensaje(e.message, true); }
}
async function iniciar() {
    try {
        if (puedeEditar) {
            const c = (await llamarApi('catalogos')).data;
            for (const campo of ['fecha_inicio', 'fecha']) document.getElementById(campo).value = c.reloj.fecha;
            for (const campo of ['hora_inicio', 'hora']) document.getElementById(campo).value = c.reloj.hora;
            for (const equipo of c.equipos) { const o = document.createElement('option'); o.value = equipo.id_equipo; o.textContent = equipo.codigo_inventario; document.getElementById('id_equipo').appendChild(o); }
            for (const usuario of c.usuarios) { const o = document.createElement('option'); o.value = usuario.id_usuario; o.textContent = usuario.nombre_completo; document.getElementById('id_responsable').appendChild(o); }
            document.getElementById('formulario').addEventListener('submit', async e => {
                e.preventDefault(); const datos = {};
                for (const campo of ['documento', 'grupo', 'fecha_inicio', 'hora_inicio', 'fecha_limite', 'hora_limite', 'nota']) datos[campo] = document.getElementById(campo).value;
                datos.id_equipo = Number(document.getElementById('id_equipo').value); datos.id_responsable = Number(document.getElementById('id_responsable').value);
                try { const r = await llamarApi('prestamos', 'POST', datos); mostrarMensaje(r.message); document.getElementById('formulario').reset(); await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
            });
            document.getElementById('devolucion').addEventListener('submit', async e => {
                e.preventDefault(); const datos = {fecha: document.getElementById('fecha').value, hora: document.getElementById('hora').value, condicion: document.getElementById('condicion').value, nota: document.getElementById('nota_devolucion').value};
                try { const r = await llamarApi('prestamos', 'PUT', datos, document.getElementById('prestamo_id').value); mostrarMensaje(r.message); document.getElementById('devolucion').reset(); document.getElementById('devolucion').hidden = true; await cargar(); } catch (error) { mostrarMensaje(error.message, true); }
            });
        }
        await cargar();
    } catch (error) { mostrarMensaje(error.message, true); }
}
iniciar();
