function editar(fila) {
    document.getElementById('registro_id').value = fila.id_equipo;
    document.getElementById('codigo_inventario').value = fila.codigo_inventario;
    document.getElementById('descripcion').value = fila.descripcion;
    document.getElementById('tipo').value = fila.tipo;
    document.getElementById('marca').value = fila.marca;
    document.getElementById('condicion').value = fila.condicion;
    document.getElementById('id_laboratorio').value = fila.id_laboratorio;
    document.getElementById('activo').value = Number(fila.activo);
    document.getElementById('formulario').scrollIntoView();
}
async function cargar() {
    const laboratorios = (await llamarApi('laboratorios')).data;
    const select = document.getElementById('id_laboratorio');
    if (select) {
        select.replaceChildren();
        laboratorios.forEach(function(lab) {
            const opcion = document.createElement('option');
            opcion.value = lab.id_laboratorio;
            opcion.textContent = lab.nombre + (Number(lab.activo) ? '' : ' (' + traducir('Inactivo') + ')');
            // Conserva opción inactiva para mostrar una relación histórica,
            // pero PHP impide asignarla al guardar.
            select.appendChild(opcion);
        });
    }
    const filas = (await llamarApi('equipos')).data;
    const listado = document.getElementById('listado'); listado.replaceChildren();
    filas.forEach(function(fila) {
        const tr = document.createElement('tr'); agregarCelda(tr, fila.id_equipo);
        agregarCelda(tr, fila.codigo_inventario);
        agregarCelda(tr, fila.descripcion);
        agregarCelda(tr, fila.tipo);
        agregarCelda(tr, fila.marca);
        agregarCelda(tr, traducir(fila.condicion === 'baja' ? 'Retirado' : fila.condicion));
        agregarCelda(tr, fila.id_laboratorio);
        agregarCelda(tr, traducir(Number(fila.activo) ? 'Sí' : 'No'));
        agregarCelda(tr, traducir(fila.disponible ? 'Sí' : 'No'));
        const acciones = document.createElement('td');
        if (document.getElementById('formulario')) {
            botonFila(acciones, 'Editar', function() { editar(fila); });
            if (Number(fila.activo)) botonFila(acciones, 'Dar de baja', async function() {
                if (!confirm(traducir('¿Registrar baja lógica?'))) return;
                try {  const r = await llamarApi('equipos', 'DELETE', {}, fila.id_equipo); mostrarMensaje(r.message); await cargar(); } catch(e) { mostrarMensaje(e.message, true); }
            });
        }
        botonFila(acciones, 'Condición', () => cambiarCondicion(fila.id_equipo));
        botonFila(acciones, 'Historial', () => { window.location.href = 'historial_equipo.php?id=' + encodeURIComponent(fila.id_equipo); });
        tr.appendChild(acciones); listado.appendChild(tr);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario');
    if (form) {
        document.getElementById('nuevo').addEventListener('click', function() { form.reset(); document.getElementById('registro_id').value = '';  });
        form.addEventListener('submit', async function(evento) {
            evento.preventDefault(); if (!form.reportValidity()) return;
            const datos = {
        codigo_inventario: document.getElementById('codigo_inventario').value.trim(),
        descripcion: document.getElementById('descripcion').value.trim(),
        tipo: document.getElementById('tipo').value.trim(),
        marca: document.getElementById('marca').value.trim(),
        condicion: document.getElementById('condicion').value.trim(),
        id_laboratorio: Number(document.getElementById('id_laboratorio').value),
        activo: Number(document.getElementById('activo').value),

            };
            const id = document.getElementById('registro_id').value;
            try {
                const r = await llamarApi('equipos', id ? 'PUT' : 'POST', datos, id || null);
                mostrarMensaje(r.message); form.reset(); document.getElementById('registro_id').value = '';
                await cargar();
            } catch(e) { mostrarMensaje(e.message, true); }
        });
    }
    cargar().catch(function(e) { mostrarMensaje(e.message, true); });
});

async function cambiarCondicion(id) {
    const condicion = prompt(traducir('Condición') + ' (operativo / reparacion)'); if (!condicion) return;
    const nota = prompt(traducir('Nota')); if (!nota) return;
    try { const r = await llamarApi('condicion_equipo', 'PUT', {condicion, nota}, id); mostrarMensaje(r.message); await cargar(); } catch (e) { mostrarMensaje(e.message, true); }
}
