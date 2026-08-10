// ---------------------------------------------------------------------
// Los tickets ahora se cargan desde la base de datos a través de PHP
// (ver app/controlador/cargarTickets.php + app/vista/tecnico.php).
// Por eso se desactivan las funciones que antes generaban los datos
// y las filas de la tabla desde JavaScript.
// ---------------------------------------------------------------------

// let tickets = [
//     { id: 1, descripcion: 'PC sin encender', laboratorio: 'Lab 3', estado: 'pendiente', prioridad: 'alta', fecha: '20/06/2026' },
//     { id: 2, descripcion: 'Proyector sin señal', laboratorio: 'Aula 5', estado: 'en proceso', prioridad: 'media', fecha: '19/06/2026' },
//     { id: 3, descripcion: 'Teclado roto', laboratorio: 'Lab 1', estado: 'resuelto', prioridad: 'baja', fecha: '18/06/2026' }
// ];
//
// let contadorId = 4;
//
// function getBadgeEstado(estado) { ... } // ahora resuelto en PHP (badgeEstado())
// function getBadgePrioridad(prioridad) { ... } // ahora resuelto en PHP (badgePrioridad())
//
// function renderizarTickets(lista) {
//     // Ya no se regenera el contenido de <tbody id="tablaTickets">
//     // desde JS: esas filas las genera app/vista/tecnico.php con PHP.
// }
//
// function eliminarTicket(id) {
//     // Desactivado: eliminaría filas creadas por PHP sin tocar la
//     // base de datos. Se implementará como acción de servidor
//     // (DELETE) en una próxima entrega.
// }

document.addEventListener('DOMContentLoaded', function() {

    // Filtra las filas que ya trajo el servidor (renderizadas por PHP),
    // en vez de volver a generarlas desde un arreglo en memoria.
    const formFiltros = document.getElementById('formFiltros');
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();

            const estado = document.getElementById('filtroEstado').value;
            const prioridad = document.getElementById('filtroPrioridad').value;

            const filas = document.querySelectorAll('#tablaTickets tr[data-estado]');

            filas.forEach(function(fila) {
                const coincideEstado = estado === '' || fila.dataset.estado === estado;
                const coincidePrioridad = prioridad === '' || fila.dataset.prioridad === prioridad;
                fila.classList.toggle('d-none', !(coincideEstado && coincidePrioridad));
            });
        });
    }

    // Validación del formulario del modal "Nuevo ticket".
    // El guardado contra la base de datos (INSERT) se implementará en
    // una próxima entrega; por ahora solo se valida el formulario.
    const btnGuardar = document.getElementById('btnGuardarTicket');
    if (!btnGuardar) return;

    btnGuardar.addEventListener('click', function() {

        const descripcion = document.getElementById('descripcionTicket');
        const laboratorio = document.getElementById('laboratorioTicket');
        const prioridad = document.getElementById('prioridadTicket');
        let valido = true;

        if (campoVacio(descripcion.value) || !longitudMinima(descripcion.value, 10)) {
            mostrarError(descripcion, 'La descripción debe tener al menos 10 caracteres.');
            valido = false;
        } else {
            mostrarValido(descripcion);
        }

        if (campoVacio(laboratorio.value)) {
            mostrarError(laboratorio, 'Ingresá el laboratorio o aula.');
            valido = false;
        } else {
            mostrarValido(laboratorio);
        }

        if (campoVacio(prioridad.value)) {
            mostrarError(prioridad, 'Seleccioná una prioridad.');
            valido = false;
        } else {
            mostrarValido(prioridad);
        }

        if (!valido) return;

        // TODO (próxima entrega): enviar estos datos al servidor
        // (por ejemplo mediante un formulario POST o fetch) para que
        // se inserten en la tabla TICKET y luego recargar la página.

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket'));
        modal.hide();
        document.getElementById('formNuevoTicket').reset();
        [descripcion, laboratorio, prioridad].forEach(function(campo) {
            limpiarValidacion(campo);
        });
    });

});
