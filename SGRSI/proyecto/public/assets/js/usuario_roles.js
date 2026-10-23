async function cargarAsociaciones() {
    const usuarios = (await llamarApi('usuarios')).data;
    const roles = (await llamarApi('roles')).data;
    const u = document.getElementById('id_usuario'); const r = document.getElementById('id_rol');
    u.replaceChildren(); r.replaceChildren();
    usuarios.forEach(function(fila) { if (Number(fila.activo)) { const o = document.createElement('option'); o.value = fila.id_usuario; o.textContent = fila.nombre_usuario; u.appendChild(o); } });
    roles.forEach(function(fila) { if (Number(fila.activo)) { const o = document.createElement('option'); o.value = fila.id_rol; o.textContent = traducir(fila.nombre_rol); r.appendChild(o); } });
    const filas = (await llamarApi('usuario_roles')).data;
    const listado = document.getElementById('listado'); listado.replaceChildren();
    filas.forEach(function(fila) {
        const tr = document.createElement('tr'); agregarCelda(tr, fila.nombre_usuario); agregarCelda(tr, traducir(fila.nombre_rol)); agregarCelda(tr, traducir(Number(fila.activo) ? 'Sí' : 'No'));
        const acciones = document.createElement('td');
        botonFila(acciones, Number(fila.activo) ? 'Revocar' : 'Reactivar', async function() {
            const activo = Number(fila.activo) ? 0 : 1;
            if (!confirm(traducir('¿Cambiar esta asociación?'))) return;
            try { const r = await llamarApi('usuario_roles', activo ? 'PUT' : 'DELETE', {id_usuario: Number(fila.id_usuario), id_rol: Number(fila.id_rol), activo: activo}); mostrarMensaje(r.message); await cargarAsociaciones(); } catch(e) { mostrarMensaje(e.message, true); }
        });
        tr.appendChild(acciones); listado.appendChild(tr);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('formulario').addEventListener('submit', async function(evento) {
        evento.preventDefault();
        const datos = {id_usuario: Number(document.getElementById('id_usuario').value), id_rol: Number(document.getElementById('id_rol').value)};
        try { const r = await llamarApi('usuario_roles', 'POST', datos); mostrarMensaje(r.message); await cargarAsociaciones(); } catch(e) { mostrarMensaje(e.message, true); }
    });
    cargarAsociaciones().catch(function(e) { mostrarMensaje(e.message, true); });
});
