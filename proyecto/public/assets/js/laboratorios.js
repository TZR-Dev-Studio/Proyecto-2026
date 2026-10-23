function editar(fila) {
    document.getElementById('registro_id').value = fila.id_laboratorio;
    document.getElementById('nombre').value = fila.nombre;
    document.getElementById('ubicacion').value = fila.ubicacion;
    document.getElementById('activo').value = Number(fila.activo);
    document.getElementById('formulario').scrollIntoView();
}
async function cargar() {
    const filas = (await llamarApi('laboratorios')).data;
    const listado = document.getElementById('listado'); listado.replaceChildren();
    filas.forEach(function(fila) {
        const tr = document.createElement('tr'); agregarCelda(tr, fila.id_laboratorio);
        agregarCelda(tr, fila.nombre);
        agregarCelda(tr, fila.ubicacion);
        agregarCelda(tr, traducir(Number(fila.activo) ? 'Sí' : 'No'));
        const acciones = document.createElement('td');
        if (document.getElementById('formulario')) {
            botonFila(acciones, 'Editar', function() { editar(fila); });
            if (Number(fila.activo)) botonFila(acciones, 'Dar de baja', async function() {
                if (!confirm(traducir('¿Registrar baja lógica?'))) return;
                try { const r = await llamarApi('laboratorios', 'DELETE', {}, fila.id_laboratorio); mostrarMensaje(r.message); await cargar(); } catch(e) { mostrarMensaje(e.message, true); }
            });
        }
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
        nombre: document.getElementById('nombre').value.trim(),
        ubicacion: document.getElementById('ubicacion').value.trim(),
        activo: Number(document.getElementById('activo').value)
            };
            const id = document.getElementById('registro_id').value;
            try {
                const r = await llamarApi('laboratorios', id ? 'PUT' : 'POST', datos, id || null);
                mostrarMensaje(r.message); form.reset(); document.getElementById('registro_id').value = '';
                await cargar();
            } catch(e) { mostrarMensaje(e.message, true); }
        });
    }
    cargar().catch(function(e) { mostrarMensaje(e.message, true); });
});
