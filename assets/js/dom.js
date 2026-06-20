document.addEventListener('DOMContentLoaded', function() {

    // Verificamos que haya una sesión activa
    const sesion = sessionStorage.getItem('usuarioActivo');

    if (!sesion) {
        // Si no hay sesión, redirigimos al login
        window.location.href = '../index.html';
        return;
    }

    const usuario = JSON.parse(sesion);

    // Mostramos el nombre y rol en la navbar
    const nombreUsuario = document.getElementById('nombreUsuario');
    if (nombreUsuario) {
        nombreUsuario.textContent = usuario.usuario + ' (' + usuario.rol + ')';
    }

    // Mostramos mensaje de bienvenida
    const mensajeBienvenida = document.getElementById('mensajeBienvenida');
    if (mensajeBienvenida) {
        mensajeBienvenida.textContent = 'Bienvenido, ' + usuario.usuario + '. Hoy es ' + new Date().toLocaleDateString('es-UY');
    }

    // Evento cerrar sesión
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function() {
            sessionStorage.removeItem('usuarioActivo');
            window.location.href = '../index.html';
        });
    }

});