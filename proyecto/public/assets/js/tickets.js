const URL_API_TICKETS = 'api/tickets.php';

function crearBadgeEstado(estado) {
    const badges = {
        'pendiente': { texto: 'Pendiente', clase: 'bg-warning text-dark' },
        'en proceso': { texto: 'En proceso', clase: 'bg-primary' },
        'resuelto': { texto: 'Resuelto', clase: 'bg-success' }
    };
    const badge = badges[estado];

    const span = document.createElement('span');
    if (badge) {
        span.className = 'badge ' + badge.clase;
        span.textContent = badge.texto;
    } else {
        span.textContent = estado;
    }
    return span;
}

function crearBadgePrioridad(prioridad) {
    const badges = {
        'alta': { texto: 'Alta', clase: 'bg-danger' },
        'media': { texto: 'Media', clase: 'bg-warning text-dark' },
        'baja': { texto: 'Baja', clase: 'bg-success' }
    };
    const badge = badges[prioridad];

    const span = document.createElement('span');
    if (badge) {
        span.className = 'badge ' + badge.clase;
        span.textContent = badge.texto;
    } else {
        span.textContent = prioridad;
    }
    return span;
}

function mostrarAlertaTickets(tipo, mensaje) {
    const contenedor = document.getElementById('alertaTickets');
    if (!contenedor) return;
    const div = document.createElement('div');
    div.className = 'alert alert-' + tipo;
    div.setAttribute('role', 'alert');
    div.textContent = mensaje;
    contenedor.replaceChildren(div);
}

function renderizarTickets(tickets) {
    const tbody = document.getElementById('tablaTickets');
    if (!tbody) return;

    if (tickets.length === 0) {
        const filaVacia = document.createElement('tr');
        const celdaVacia = document.createElement('td');
        celdaVacia.colSpan = 7;
        celdaVacia.className = 'text-center text-muted py-3';
        celdaVacia.textContent = 'No hay tickets registrados';
        filaVacia.appendChild(celdaVacia);
        tbody.replaceChildren(filaVacia);
        return;
    }

    const filas = tickets.map(function(ticket) {
        const fila = document.createElement('tr');
        fila.setAttribute('data-estado', ticket.estado);
        fila.setAttribute('data-prioridad', ticket.prioridad);

        const celdaId = document.createElement('td');
        celdaId.textContent = ticket.id_ticket;

        const celdaDescripcion = document.createElement('td');
        celdaDescripcion.textContent = ticket.descripcion;

        const celdaLaboratorio = document.createElement('td');
        celdaLaboratorio.textContent = ticket.laboratorio;

        const celdaEstado = document.createElement('td');
        celdaEstado.appendChild(crearBadgeEstado(ticket.estado));

        const celdaPrioridad = document.createElement('td');
        celdaPrioridad.appendChild(crearBadgePrioridad(ticket.prioridad));

        const celdaFecha = document.createElement('td');
        celdaFecha.textContent = ticket.fecha_inicio;

        const celdaCreadoPor = document.createElement('td');
        celdaCreadoPor.textContent = ticket.creadoPor;

        fila.appendChild(celdaId);
        fila.appendChild(celdaDescripcion);
        fila.appendChild(celdaLaboratorio);
        fila.appendChild(celdaEstado);
        fila.appendChild(celdaPrioridad);
        fila.appendChild(celdaFecha);
        fila.appendChild(celdaCreadoPor);

        return fila;
    });

    tbody.replaceChildren(...filas);
}

function obtenerTickets() {
    fetch(URL_API_TICKETS, { method: 'GET', credentials: 'same-origin' })
        .then(function(respuesta) {
            return respuesta.json().then(function(datos) {
                if (!respuesta.ok) throw new Error(datos.error || 'No se pudieron obtener los tickets.');
                return datos;
            });
        })
        .then(function(tickets) {
            renderizarTickets(tickets);
        })
        .catch(function(error) {
            mostrarAlertaTickets('danger', error.message);
        });
}

document.addEventListener('DOMContentLoaded', function() {

    obtenerTickets();

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

        fetch(URL_API_TICKETS, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                descripcion: descripcion.value.trim(),
                laboratorio: laboratorio.value.trim(),
                prioridad: prioridad.value
            })
        })
            .then(function(respuesta) {
                return respuesta.json().then(function(datos) {
                    if (!respuesta.ok) throw new Error(datos.error || 'No se pudo registrar el ticket.');
                    return datos;
                });
            })
            .then(function(datos) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket'));
                modal.hide();
                document.getElementById('formNuevoTicket').reset();
                [descripcion, laboratorio, prioridad].forEach(function(campo) {
                    limpiarValidacion(campo);
                });
                mostrarAlertaTickets('success', datos.mensaje);
                obtenerTickets();
            })
            .catch(function(error) {
                mostrarAlertaTickets('danger', error.message);
            });
    });

});
