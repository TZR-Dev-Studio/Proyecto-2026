document.addEventListener('DOMContentLoaded', function() {

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

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket'));
        modal.hide();
        document.getElementById('formNuevoTicket').reset();
        [descripcion, laboratorio, prioridad].forEach(function(campo) {
            limpiarValidacion(campo);
        });
    });

});
