function editar(fila) {
    document.getElementById('registro_id').value = fila.id_rol;
    document.getElementById('nombre_rol').value = fila.nombre_rol;
    document.getElementById('nombre_rol').disabled = true;
    document.getElementById('activo').value = Number(fila.activo);
    document.getElementById('formulario').scrollIntoView();
}
async function cargar() {
    const filas = (await llamarApi('roles')).data;
    const listado = document.getElementById('listado'); listado.replaceChildren();
    filas.forEach(function(fila) {
        const tr = document.createElement('tr'); agregarCelda(tr, fila.id_rol);
        agregarCelda(tr, fila.nombre_rol);
        agregarCelda(tr, traducir(Number(fila.activo) ? 'Sí' : 'No'));
        const acciones = document.createElement('td');
        if (document.getElementById('formulario')) {
            botonFila(acciones, 'Editar', function() { editar(fila); });
            if (Number(fila.activo)) botonFila(acciones, 'Dar de baja', async function() {
                if (!confirm(traducir('¿Registrar baja lógica?'))) return;
                try { const r = await llamarApi('roles', 'DELETE', {}, fila.id_rol); mostrarMensaje(r.message); await cargar(); } catch(e) { mostrarMensaje(e.message, true); }
            });
        }
        tr.appendChild(acciones); listado.appendChild(tr);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario');
    if (form) {
        document.getElementById('nuevo').addEventListener('click', function() { form.reset(); document.getElementById('registro_id').value = ''; document.getElementById('nombre_rol').disabled = false; });
        form.addEventListener('submit', async function(evento) {
            evento.preventDefault(); if (!form.reportValidity()) return;
            const datos = {
        nombre_rol: document.getElementById('nombre_rol').value.trim(),
        activo: Number(document.getElementById('activo').value)
            };
            const id = document.getElementById('registro_id').value;
            try {
                const r = await llamarApi('roles', id ? 'PUT' : 'POST', datos, id || null);
                mostrarMensaje(r.message); form.reset(); document.getElementById('registro_id').value = '';
    document.getElementById('nombre_rol').disabled = false;
                await cargar();
            } catch(e) { mostrarMensaje(e.message, true); }
        });
    }
    cargar().catch(function(e) { mostrarMensaje(e.message, true); });
});
