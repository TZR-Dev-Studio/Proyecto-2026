const URL_API_USUARIOS = 'api/usuarios.php';

function crearBadgeRol(rol) {
    const badges = {
        'administrador': { texto: 'Administrador', clase: 'bg-danger' },
        'tecnico': { texto: 'Técnico', clase: 'bg-primary' },
        'solicitante': { texto: 'Solicitante', clase: 'bg-success' }
    };
    const badge = badges[rol];

    const span = document.createElement('span');
    if (badge) {
        span.className = 'badge ' + badge.clase;
        span.textContent = badge.texto;
    } else {
        span.textContent = rol;
    }
    return span;
}

function mostrarAlertaUsuarios(tipo, mensaje) {
    const contenedor = document.getElementById('alertaUsuarios');
    if (!contenedor) return;
    const div = document.createElement('div');
    div.className = 'alert alert-' + tipo;
    div.setAttribute('role', 'alert');
    div.textContent = mensaje;
    contenedor.replaceChildren(div);
}

function renderizarUsuarios(usuarios) {
    const tbody = document.getElementById('tablaUsuarios');
    if (!tbody) return;

    if (usuarios.length === 0) {
        const filaVacia = document.createElement('tr');
        const celdaVacia = document.createElement('td');
        celdaVacia.colSpan = 5;
        celdaVacia.className = 'text-center text-muted py-3';
        celdaVacia.textContent = 'No hay usuarios registrados';
        filaVacia.appendChild(celdaVacia);
        tbody.replaceChildren(filaVacia);
        return;
    }

    const filas = usuarios.map(function(usuario) {
        const rol = (usuario.roles || '').split(',')[0];

        const fila = document.createElement('tr');

        const celdaId = document.createElement('td');
        celdaId.textContent = usuario.id_usuario;

        const celdaUsuario = document.createElement('td');
        celdaUsuario.textContent = usuario.nombre_usuario;

        const celdaNombre = document.createElement('td');
        celdaNombre.textContent = usuario.nombre_completo;

        const celdaRol = document.createElement('td');
        celdaRol.appendChild(crearBadgeRol(rol));

        const celdaEmail = document.createElement('td');
        celdaEmail.textContent = usuario.email;

        fila.appendChild(celdaId);
        fila.appendChild(celdaUsuario);
        fila.appendChild(celdaNombre);
        fila.appendChild(celdaRol);
        fila.appendChild(celdaEmail);

        return fila;
    });

    tbody.replaceChildren(...filas);
}

function obtenerUsuarios() {
    fetch(URL_API_USUARIOS, { method: 'GET', credentials: 'same-origin' })
        .then(function(respuesta) {
            return respuesta.json().then(function(datos) {
                if (!respuesta.ok) throw new Error(datos.error || 'No se pudieron obtener los usuarios.');
                return datos;
            });
        })
        .then(function(usuarios) {
            renderizarUsuarios(usuarios);
        })
        .catch(function(error) {
            mostrarAlertaUsuarios('danger', error.message);
        });
}

function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

document.addEventListener('DOMContentLoaded', function() {

    obtenerUsuarios();

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

        fetch(URL_API_USUARIOS, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nombreCompleto: nombreCompleto.value.trim(),
                nombreUsuario: nombreUsuario.value.trim(),
                email: email.value.trim(),
                rol: rol.value,
                password: password.value
            })
        })
            .then(function(respuesta) {
                return respuesta.json().then(function(datos) {
                    if (!respuesta.ok) throw new Error(datos.error || 'No se pudo registrar el usuario.');
                    return datos;
                });
            })
            .then(function(datos) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario'));
                modal.hide();
                document.getElementById('formNuevoUsuario').reset();
                [nombreCompleto, nombreUsuario, email, rol, password].forEach(function(campo) {
                    limpiarValidacion(campo);
                });
                mostrarAlertaUsuarios('success', datos.mensaje);
                obtenerUsuarios();
            })
            .catch(function(error) {
                mostrarAlertaUsuarios('danger', error.message);
            });
    });

});
