function nuevoUsuario() {
    document.getElementById('formulario').reset();
    document.getElementById('registro_id').value = '';
    document.getElementById('nombreUsuario').disabled = false;
    document.getElementById('password').required = true;
}
function editarUsuario(fila) {
    document.getElementById('registro_id').value = fila.id_usuario;
    document.getElementById('nombreUsuario').value = fila.nombre_usuario;
    document.getElementById('nombreUsuario').disabled = true;
    document.getElementById('nombreCompleto').value = fila.nombre_completo;
    document.getElementById('email').value = fila.email;
    document.getElementById('activo').value = Number(fila.activo);
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.querySelectorAll('input[name="roles"]').forEach(function(campo) { campo.checked = fila.roles.includes(campo.value); });
    document.getElementById('formulario').scrollIntoView();
}
async function cargarUsuarios() {
    const filas = (await llamarApi('usuarios')).data;
    const listado = document.getElementById('listado'); listado.replaceChildren();
    filas.forEach(function(fila) {
        const tr = document.createElement('tr');
        agregarCelda(tr, fila.id_usuario); agregarCelda(tr, fila.nombre_usuario);
        agregarCelda(tr, fila.nombre_completo); agregarCelda(tr, fila.email);
        agregarCelda(tr, fila.roles.map(function(rol) { return traducir(rol); }).join(', '));
        agregarCelda(tr, traducir(Number(fila.activo) ? 'Sí' : 'No'));
        const acciones = document.createElement('td');
        botonFila(acciones, 'Editar', function() { editarUsuario(fila); });
        if (Number(fila.activo)) botonFila(acciones, 'Dar de baja', async function() {
            if (!confirm(traducir('¿Registrar baja lógica?'))) return;
            try { const r = await llamarApi('usuarios', 'DELETE', {}, fila.id_usuario); mostrarMensaje(r.message); await cargarUsuarios(); } catch(e) { mostrarMensaje(e.message, true); }
        });
        tr.appendChild(acciones); listado.appendChild(tr);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario');
    document.getElementById('nuevo').addEventListener('click', nuevoUsuario);
    form.addEventListener('submit', async function(evento) {
        evento.preventDefault(); if (!form.reportValidity()) return;
        const roles = [];
        document.querySelectorAll('input[name="roles"]:checked').forEach(function(campo) { roles.push(campo.value); });
        if (!roles.length) { mostrarMensaje('Seleccione al menos un rol.', true); return; }
        const id = document.getElementById('registro_id').value;
        const datos = {nombreUsuario: document.getElementById('nombreUsuario').value.trim(), nombreCompleto: document.getElementById('nombreCompleto').value.trim(), email: document.getElementById('email').value.trim(), password: document.getElementById('password').value, roles: roles, activo: Number(document.getElementById('activo').value)};
        try { const r = await llamarApi('usuarios', id ? 'PUT' : 'POST', datos, id || null); mostrarMensaje(r.message); nuevoUsuario(); await cargarUsuarios(); } catch(e) { mostrarMensaje(e.message, true); }
    });
    cargarUsuarios().catch(function(e) { mostrarMensaje(e.message, true); });
});
