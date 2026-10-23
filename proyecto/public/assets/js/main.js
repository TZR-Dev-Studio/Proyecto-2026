const usuariosPrueba = [
    { usuario: 'admin', password: 'admin123', rol: 'administrador' },
    { usuario: 'tecnico', password: 'tecnico123', rol: 'tecnico' },
    { usuario: 'solicitante', password: 'sol123', rol: 'solicitante' }
];

document.addEventListener('DOMContentLoaded', function() {

    const formLogin = document.getElementById('formLogin');
    const inputUsuario = document.getElementById('usuario');
    const inputPassword = document.getElementById('password');
    const mensajeError = document.getElementById('mensajeError');

    // Validación en tiempo real mientras el usuario escribe
    inputUsuario.addEventListener('input', function() {
        if (campoVacio(this.value)) {
            mostrarError(this, 'El usuario no puede estar vacío.');
        } else if (!longitudMinima(this.value, 3)) {
            mostrarError(this, 'El usuario debe tener al menos 3 caracteres.');
        } else {
            mostrarValido(this);
        }
    });

    inputPassword.addEventListener('input', function() {
        if (campoVacio(this.value)) {
            mostrarError(this, 'La contraseña no puede estar vacía.');
        } else if (!longitudMinima(this.value, 6)) {
            mostrarError(this, 'La contraseña debe tener al menos 6 caracteres.');
        } else {
            mostrarValido(this);
        }
    });

    formLogin.addEventListener('submit', function(e) {
        e.preventDefault(); // evitamos que el form recargue la página

        const valorUsuario = inputUsuario.value.trim();
        const valorPassword = inputPassword.value.trim();
        let formularioValido = true;

        if (campoVacio(valorUsuario)) {
            mostrarError(inputUsuario, 'El usuario no puede estar vacío.');
            formularioValido = false;
        } else if (!longitudMinima(valorUsuario, 3)) {
            mostrarError(inputUsuario, 'El usuario debe tener al menos 3 caracteres.');
            formularioValido = false;
        }

        if (campoVacio(valorPassword)) {
            mostrarError(inputPassword, 'La contraseña no puede estar vacía.');
            formularioValido = false;
        } else if (!longitudMinima(valorPassword, 6)) {
            mostrarError(inputPassword, 'La contraseña debe tener al menos 6 caracteres.');
            formularioValido = false;
        }

        if (!formularioValido) return;

        const usuarioEncontrado = usuariosPrueba.find(function(u) {
            return u.usuario === valorUsuario && u.password === valorPassword;
        });

        if (usuarioEncontrado) {
            // Guardamos sesión temporalmente hasta integrar backend en 2da entrega
            sessionStorage.setItem('usuarioActivo', JSON.stringify(usuarioEncontrado));
            window.location.href = 'pages/dashboard.html';
        } else {
            mensajeError.style.display = 'block';
            mostrarError(inputUsuario, ' ');
            mostrarError(inputPassword, ' ');
        }
    });

});