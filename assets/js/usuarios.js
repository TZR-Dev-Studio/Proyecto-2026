let usuarios = [
    { id: 1, nombreCompleto: 'Jean Zarraga', usuario: 'admin', email: 'jean@iti.edu.uy', rol: 'administrador' },
    { id: 2, nombreCompleto: 'Guzmán Troncone', usuario: 'tecnico', email: 'guzman@iti.edu.uy', rol: 'tecnico' },
    { id: 3, nombreCompleto: 'Joaquín Rodríguez', usuario: 'solicitante', email: 'joaquin@iti.edu.uy', rol: 'solicitante' }
];

let contadorIdUsuario = 4;

function getBadgeRol(rol) {
    const badges = {
        'administrador': '<span class="badge bg-danger">Administrador</span>',
        'tecnico': '<span class="badge bg-primary">Técnico</span>',
        'solicitante': '<span class="badge bg-success">Solicitante</span>'
    };
    return badges[rol] || rol;
}

function renderizarUsuarios() {
    const tbody = document.getElementById('tablaUsuarios');
    if (!tbody) return;

    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No hay usuarios registrados</td></tr>';
        return;
    }

    tbody.innerHTML = usuarios.map(function(u) {
        return '<tr>' +
            '<td>' + u.id + '</td>' +
            '<td>' + u.usuario + '</td>' +
            '<td>' + u.nombreCompleto + '</td>' +
            '<td>' + getBadgeRol(u.rol) + '</td>' +
            '<td>' + u.email + '</td>' +
            '<td>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(' + u.id + ')">Eliminar</button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function eliminarUsuario(id) {
    // No permitimos eliminar al admin principal
    const usuario = usuarios.find(function(u) { return u.id === id; });
    if (usuario && usuario.usuario === 'admin') {
        alert('No podés eliminar al administrador principal.');
        return;
    }
    usuarios = usuarios.filter(function(u) { return u.id !== id; });
    renderizarUsuarios();
}

function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

document.addEventListener('DOMContentLoaded', function() {

    // Verificamos que sea administrador
    const sesion = sessionStorage.getItem('usuarioActivo');
    if (sesion) {
        const usuarioActivo = JSON.parse(sesion);
        if (usuarioActivo.rol !== 'administrador') {
            // Ocultamos contenido y mostramos alerta
            document.getElementById('contenidoAdmin').classList.add('d-none');
            document.getElementById('alertaAcceso').classList.remove('d-none');
            document.getElementById('btnNuevoUsuario').classList.add('d-none');
        }
    }

    renderizarUsuarios();

    // El botón abre el modal solo si es admin
    const btnNuevo = document.getElementById('btnNuevoUsuario');
    if (btnNuevo) {
        btnNuevo.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalNuevoUsuario'));
            modal.show();
        });
    }

    const btnGuardar = document.getElementById('btnGuardarUsuario');
    if (!btnGuardar) return;

    btnGuardar.addEventListener('click', function() {

        const nombreCompleto = document.getElementById('nombreCompleto');
        const nombreUsuario = document.getElementById('nombreUsuario');
        const email = document.getElementById('emailUsuario');
        const rol = document.getElementById('rolUsuario');
        const password = document.getElementById('passwordUsuario');
        let valido = true;

        if (campoVacio(nombreCompleto.value)) {
            mostrarError(nombreCompleto, 'Ingresá el nombre completo.');
            valido = false;
        } else {
            mostrarValido(nombreCompleto);
        }

        if (campoVacio(nombreUsuario.value) || !longitudMinima(nombreUsuario.value, 3)) {
            mostrarError(nombreUsuario, 'El usuario debe tener al menos 3 caracteres.');
            valido = false;
        } else {
            mostrarValido(nombreUsuario);
        }

        if (campoVacio(email.value) || !validarEmail(email.value)) {
            mostrarError(email, 'Ingresá un email válido.');
            valido = false;
        } else {
            mostrarValido(email);
        }

        if (campoVacio(rol.value)) {
            mostrarError(rol, 'Seleccioná un rol.');
            valido = false;
        } else {
            mostrarValido(rol);
        }

        if (campoVacio(password.value) || !longitudMinima(password.value, 6)) {
            mostrarError(password, 'La contraseña debe tener al menos 6 caracteres.');
            valido = false;
        } else {
            mostrarValido(password);
        }

        if (!valido) return;

        const nuevoUsuario = {
            id: contadorIdUsuario++,
            nombreCompleto: nombreCompleto.value.trim(),
            usuario: nombreUsuario.value.trim(),
            email: email.value.trim(),
            rol: rol.value
        };

        usuarios.push(nuevoUsuario);
        renderizarUsuarios();

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario'));
        modal.hide();
        document.getElementById('formNuevoUsuario').reset();
        [nombreCompleto, nombreUsuario, email, rol, password].forEach(function(campo) {
            limpiarValidacion(campo);
        });
    });

});